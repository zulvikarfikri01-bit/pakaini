<?php
/**
 * PAKAINI - Social Media Exchange Platform
 * Konfigurasi Database PostgreSQL (config/database.php)
 * 
 * Siap 100% untuk Supabase & Railway Deployment
 * ==============================================================================
 * CARA MENGHUBUNGKAN KE SUPABASE (PILIH SALAH SATU):
 * ==============================================================================
 * CARA 1 (PALING MUDAH & DIREKOMENDASIKAN):
 * Buka Dashboard Supabase -> Project Settings -> Database -> Connection String (URI),
 * lalu salin dan tempel ke variabel $supabase_url di bawah ini:
 * Contoh:
 * $supabase_url = 'postgresql://postgres.xxx:password@aws-0-ap-southeast-1.pooler.supabase.com:6543/postgres';
 * 
 * CARA 2 (DEPLOY RAILWAY OTOMATIS):
 * Di Railway Dashboard, tambahkan Environment Variable:
 * Key   : DATABASE_URL
 * Value : (Connection String dari Supabase Anda)
 * Jika variable tersebut ada, sistem otomatis menggunakannya!
 * 
 * CARA 3 (MANUAL SATU PER SATU):
 * Masukkan host, user, password, dan port pada variabel di bawah CARA 1.
 * ==============================================================================
 */

defined('ROOT_PATH') or define('ROOT_PATH', dirname(__DIR__));

// ==============================================================================
// 1. KREDENSIAL SUPABASE / POSTGRESQL (TEMPEL URL ANDA DI SINI JIKA INGIN HARDCODE)
// ==============================================================================
$supabase_url = ''; // <-- Bisa diisi URL Supabase atau dibiarkan kosong jika menggunakan Railway Variables

// Parameter default Supabase proyek Anda
$db_host = 'aws-0-ap-northeast-2.pooler.supabase.com';
$db_port = 5432;                                      // Port Supabase (5432 atau 6543)
$db_name = 'postgres';                                 // Database default Supabase adalah 'postgres'
$db_user = 'postgres.tsesxoeipmaqzumfurpw';            // User default Supabase proyek Anda
$db_pass = '';                                         // Password database Supabase Anda

// ==============================================================================
// 2. PARSING OTOMATIS CONNECTION STRING (URL) DARI RAILWAY / ENVIRONMENT / .ENV
// ==============================================================================
$activeUrl = !empty($supabase_url) ? $supabase_url : '';

if (empty($activeUrl)) {
    $activeUrl = getenv('DATABASE_URL') ?: ($_ENV['DATABASE_URL'] ?? ($_SERVER['DATABASE_URL'] ?? ''));
}

// Periksa juga jika ada file .env (yang diekspor oleh entrypoint.sh dari Railway)
if (empty($activeUrl)) {
    $envPaths = [ROOT_PATH . '/.env', '/var/www/html/.env'];
    foreach ($envPaths as $ep) {
        if (file_exists($ep)) {
            $envContent = @file_get_contents($ep);
            if ($envContent && preg_match('/DATABASE_URL=["\']?([^"\'\r\n]+)/', $envContent, $m)) {
                $activeUrl = trim($m[1]);
                break;
            }
        }
    }
}

if (!empty($activeUrl)) {
    $parsed = parse_url($activeUrl);
    if ($parsed !== false && !empty($parsed['host'])) {
        $db_host = $parsed['host'];
        $db_port = !empty($parsed['port']) ? (int)$parsed['port'] : 5432;
        if (!empty($parsed['path'])) {
            $db_name = ltrim($parsed['path'], '/');
        }
        if (isset($parsed['user'])) {
            $db_user = urldecode($parsed['user']);
        }
        if (isset($parsed['pass'])) {
            $db_pass = urldecode($parsed['pass']);
        }
    } elseif (preg_match('#^postgres(?:ql)?://(?:(?<user>[^:]+):(?<pass>.*?)@)?(?<host>[^:/]+)(?::(?<port>\d+))?(?:/(?<dbname>[^?]+))?#', $activeUrl, $matches)) {
        // Fallback jika URL memiliki karakter khusus (#, ?) pada password yang membatalkan parse_url bawaan
        $db_host = $matches['host'];
        $db_port = !empty($matches['port']) ? (int)$matches['port'] : 5432;
        if (!empty($matches['dbname'])) {
            $db_name = $matches['dbname'];
        }
        if (isset($matches['user'])) {
            $db_user = urldecode($matches['user']);
        }
        if (isset($matches['pass'])) {
            $db_pass = urldecode($matches['pass']);
        }
    }
}

// ==============================================================================
// 3. DEFINISI KONSTANTA GLOBAL
// ==============================================================================
defined('DB_HOST') or define('DB_HOST', getenv('DB_HOST') ?: $db_host);
defined('DB_PORT') or define('DB_PORT', getenv('DB_PORT') ?: $db_port);
defined('DB_NAME') or define('DB_NAME', getenv('DB_NAME') ?: $db_name);
defined('DB_USER') or define('DB_USER', getenv('DB_USER') ?: $db_user);
defined('DB_PASS') or define('DB_PASS', getenv('DB_PASS') ?: $db_pass);

// ==============================================================================
// 4. KELAS KONEKSI DATABASE POSTGRESQL (PDO SINGLETON)
// ==============================================================================
class Database {
    private static ?PDO $instance = null;
    private static string $driver = 'pgsql';

    public static function getInstance(): PDO {
        return self::getConnection();
    }

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $host   = DB_HOST;
            $port   = DB_PORT;
            $dbname = DB_NAME;
            $user   = DB_USER;
            $pass   = DB_PASS;

            // Cek apakah ekstensi pdo_pgsql tersedia
            if (extension_loaded('pdo_pgsql')) {
                try {
                    // Supabase mewajibkan koneksi SSL
                    $isRemote = ($host !== 'localhost' && $host !== '127.0.0.1');
                    $sslPart  = $isRemote ? ";sslmode=require" : "";
                    
                    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}{$sslPart}";
                    $options = [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                        PDO::ATTR_TIMEOUT            => 10,
                    ];

                    self::$instance = new PDO($dsn, $user, $pass, $options);
                    self::$driver   = 'pgsql';
                    return self::$instance;
                } catch (PDOException $e) {
                    // Fallback SQLite HANYA untuk pengujian CLI di komputer lokal
                    // JANGAN aktifkan fallback SQLite di web server agar tidak terjadi error 'no such table: users'
                    if (php_sapi_name() === 'cli' && ($host === 'localhost' || $host === '127.0.0.1')) {
                        $sqliteInstance = self::trySqliteFallback();
                        if ($sqliteInstance !== null) {
                            return $sqliteInstance;
                        }
                    }

                    // Tampilkan panduan elegan jika belum terhubung di web browser
                    self::renderConnectionError($e->getMessage(), $host, $port, $dbname, $user);
                }
            } else {
                // pdo_pgsql belum aktif di PHP runtime lokal
                if (php_sapi_name() === 'cli' && ($host === 'localhost' || $host === '127.0.0.1')) {
                    $sqliteInstance = self::trySqliteFallback();
                    if ($sqliteInstance !== null) {
                        return $sqliteInstance;
                    }
                }

                self::renderExtensionMissingError();
            }
        }

        return self::$instance;
    }

    private static function trySqliteFallback(): ?PDO {
        $sqliteDir = ROOT_PATH . '/storage';
        if (!is_dir($sqliteDir)) {
            @mkdir($sqliteDir, 0777, true);
        }
        $sqlitePath = $sqliteDir . '/pakaini.db';

        try {
            $dsn = "sqlite:" . $sqlitePath;
            $pdo = new PDO($dsn, null, null, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            self::$instance = $pdo;
            self::$driver   = 'sqlite';

            // Registrasi kompatibilitas fungsi SQL
            $pdo->sqliteCreateFunction('NOW', function() { return date('Y-m-d H:i:s'); });
            $pdo->sqliteCreateFunction('CURRENT_TIMESTAMP', function() { return date('Y-m-d H:i:s'); });
            $pdo->sqliteCreateFunction('CURDATE', function() { return date('Y-m-d'); });
            $pdo->sqliteCreateFunction('CURRENT_DATE', function() { return date('Y-m-d'); });

            return self::$instance;
        } catch (PDOException $ex) {
            return null;
        }
    }

    public static function getDriver(): string {
        return self::$driver;
    }

    /**
     * Kompatibilitas mundur untuk kode lama jika ada pemanggilan getMysqli()
     */
    public static function getMysqli(): ?object {
        return null;
    }

    private static function renderConnectionError(string $errMsg, string $host, $port, string $dbname, string $user): void {
        $safeHost = htmlspecialchars($host, ENT_QUOTES, 'UTF-8');
        $safePort = htmlspecialchars((string)$port, ENT_QUOTES, 'UTF-8');
        $safeDb   = htmlspecialchars($dbname, ENT_QUOTES, 'UTF-8');
        $safeUser = htmlspecialchars($user, ENT_QUOTES, 'UTF-8');
        $safeErr  = htmlspecialchars($errMsg, ENT_QUOTES, 'UTF-8');

        die("<!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Koneksi Supabase PostgreSQL Diperlukan — PAKAINI</title>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #070a13; color: #f1f5f9; padding: 24px; margin: 0; display: flex; align-items: center; justify-content: center; min-height: 90vh; }
                .card { background: #0f172a; border: 1px solid rgba(59, 130, 246, 0.4); border-radius: 20px; max-width: 650px; width: 100%; padding: 32px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.7); }
                h2 { color: #38bdf8; margin-top: 0; font-size: 22px; display: flex; align-items: center; gap: 10px; }
                p { line-height: 1.6; color: #94a3b8; font-size: 14px; }
                .info-box { background: #0b1120; border: 1px solid #1e293b; border-radius: 12px; padding: 16px; margin: 20px 0; font-family: monospace; font-size: 13px; color: #cbd5e1; }
                .info-box div { margin-bottom: 6px; }
                .highlight { color: #38bdf8; font-weight: bold; }
                .solution { background: #0b1329; border-left: 4px solid #10b981; padding: 16px 20px; border-radius: 0 12px 12px 0; font-size: 13px; color: #e2e8f0; margin-top: 20px; }
                .solution ol { margin: 10px 0 0 20px; padding: 0; line-height: 1.7; }
                code { background: #1e293b; color: #38bdf8; padding: 3px 8px; border-radius: 6px; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='card'>
                <h2>🐘 Sambungkan Database Supabase (PostgreSQL)</h2>
                <p>Website Pakaini sekarang telah dimigrasikan ke <strong>PostgreSQL (Supabase & Railway)</strong>. Silakan masukkan Connection String Supabase Anda.</p>
                
                <div class='info-box'>
                    <div><strong>Host Target:</strong> <span class='highlight'>{$safeHost}</span> (Port: {$safePort})</div>
                    <div><strong>Database:</strong> <span class='highlight'>{$safeDb}</span></div>
                    <div><strong>User:</strong> <span class='highlight'>{$safeUser}</span></div>
                    <div style='color: #f87171; margin-top: 10px; font-size: 11px;'><strong>Pesan Error:</strong> {$safeErr}</div>
                </div>

                <div class='solution'>
                    <strong>Langkah Menghubungkan ke Supabase:</strong>
                    <ol>
                        <li>Buka Dashboard <strong><a href='https://supabase.com' target='_blank' style='color:#38bdf8;'>Supabase</a></strong> Anda.</li>
                        <li>Buka menu <strong>Project Settings</strong> &rarr; <strong>Database</strong> &rarr; <strong>Connection String</strong> (Pilih tab <em>URI</em>).</li>
                        <li>Salin URI tersebut (contoh: <code>postgresql://postgres:[PASSWORD]@db.xxxx.supabase.co:5432/postgres</code>).</li>
                        <li>Tempelkan ke file <code>config/database.php</code> pada baris variabel <code>\$supabase_url = '...';</code>.</li>
                        <li>(Jika di Railway) Tambahkan Environment Variable <code>DATABASE_URL</code> dengan nilai URI tersebut.</li>
                        <li>Refresh halaman ini.</li>
                    </ol>
                </div>
            </div>
        </body>
        </html>");
    }

    private static function renderExtensionMissingError(): void {
        die("<!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <title>Ekstensi PDO PostgreSQL Diperlukan — PAKAINI</title>
            <style>
                body { font-family: sans-serif; background: #070a13; color: #f1f5f9; padding: 24px; display: flex; align-items: center; justify-content: center; min-height: 90vh; }
                .card { background: #0f172a; border: 1px solid #ef4444; border-radius: 16px; max-width: 600px; padding: 28px; }
                h2 { color: #f87171; margin-top: 0; }
                code { background: #1e293b; color: #38bdf8; padding: 2px 6px; border-radius: 4px; }
            </style>
        </head>
        <body>
            <div class='card'>
                <h2>⚠️ Ekstensi PHP <code>pdo_pgsql</code> Belum Aktif</h2>
                <p>Website Pakaini membutuhkan driver PDO PostgreSQL untuk berkomunikasi dengan Supabase.</p>
                <p>Untuk server Linux/Railway atau Docker, driver ini otomatis terpasang melalui <code>Dockerfile</code> bawaan paket migrasi.</p>
                <p>Untuk server lokal Windows, aktifkan <code>extension=pdo_pgsql</code> di file <code>php.ini</code> Anda.</p>
            </div>
        </body>
        </html>");
    }
}

// Inisialisasi variabel global $pdo dan $conn untuk seluruh script aplikasi
$pdo  = Database::getConnection();
$conn = null;
