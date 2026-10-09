<?php
/**
 * PAKAINI - Social Media Exchange Platform
 * Konfigurasi Utama & Helper Global
 * 
 * Pengaturan Database Terpusat di: config/database.php
 * Silakan edit host, user, password, dan dbname di file config/database.php!
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set zona waktu Indonesia
date_default_timezone_set('Asia/Jakarta');

defined('ROOT_PATH') or define('ROOT_PATH', __DIR__);

// 1. Muat Database Terpusat
require_once ROOT_PATH . '/config/database.php';

// Pastikan variabel $pdo aktif secara global
if (!isset($pdo) || !($pdo instanceof PDO)) {
    $pdo = Database::getConnection();
}
$conn = null;

try {
    $driver = Database::getDriver();
    if ($driver === 'pgsql') {
        try { $pdo->exec("ALTER TABLE users ADD COLUMN whatsapp_number VARCHAR(30) NULL"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS ip_address VARCHAR(45) NULL"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS is_active SMALLINT NOT NULL DEFAULT 1"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE tasks ADD COLUMN IF NOT EXISTS target_clicks INT NOT NULL DEFAULT 100"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE tasks ADD COLUMN IF NOT EXISTS report_count INT NOT NULL DEFAULT 0"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE tasks ADD COLUMN IF NOT EXISTS is_active SMALLINT NOT NULL DEFAULT 1"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS coins INT NOT NULL DEFAULT 0"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS password VARCHAR(255) NULL"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS password_hash VARCHAR(255) NULL"); } catch (Exception $e) {}
        try { $pdo->exec("UPDATE users SET password = password_hash WHERE (password IS NULL OR password = '') AND password_hash IS NOT NULL"); } catch (Exception $e) {}
        try { $pdo->exec("UPDATE users SET password_hash = password WHERE (password_hash IS NULL OR password_hash = '') AND password IS NOT NULL"); } catch (Exception $e) {}
    } elseif ($driver === 'mysql') {
        try { $pdo->exec("ALTER TABLE users MODIFY COLUMN coins INT UNSIGNED NOT NULL DEFAULT 0"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS whatsapp_number VARCHAR(30) NULL DEFAULT NULL"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS ip_address VARCHAR(45) NULL DEFAULT NULL"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 1"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE tasks ADD COLUMN IF NOT EXISTS target_clicks INT NOT NULL DEFAULT 100"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE tasks ADD COLUMN IF NOT EXISTS report_count INT NOT NULL DEFAULT 0"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE tasks ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 1"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS password VARCHAR(255) NULL DEFAULT NULL"); } catch (Exception $e) {}
        try { $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS password_hash VARCHAR(255) NULL DEFAULT NULL"); } catch (Exception $e) {}
        try { $pdo->exec("UPDATE users SET password = password_hash WHERE (password IS NULL OR password = '') AND password_hash IS NOT NULL"); } catch (Exception $e) {}
        try { $pdo->exec("UPDATE users SET password_hash = password WHERE (password_hash IS NULL OR password_hash = '') AND password IS NOT NULL"); } catch (Exception $e) {}
    }
} catch (Exception $e) {}



// ==============================================================================
// 3. KONSTANTA & PENGATURAN BISNIS SISTEM (NILAI TUKAR MURNI)
// ==============================================================================
defined('SITE_NAME') or define('SITE_NAME', 'PAKAINI');
defined('ADMIN_WHATSAPP') or define('ADMIN_WHATSAPP', '6285143253217');   // Nomor WhatsApp Admin untuk order Koin & VIP
defined('RATE_TOPUP_IDR') or define('RATE_TOPUP_IDR', 50);              // 1.000 Koin Iklan = Rp 50.000 (1 Koin = Rp 50)
defined('RATE_WITHDRAW_IDR') or define('RATE_WITHDRAW_IDR', 30);        // 1.000 Koin Penghasilan = Rp 30.000 (1 Koin = Rp 30)
defined('COIN_RATE_IDR') or define('COIN_RATE_IDR', 30);                // Referensi nilai pencairan (Rp 30)
defined('REGULAR_DAILY_MAX_CLICKS') or define('REGULAR_DAILY_MAX_CLICKS', 50); // Maksimal klik harian member biasa
defined('REGULAR_TAX_RATE') or define('REGULAR_TAX_RATE', 0.0);         // Pajak dihapus
defined('REGULAR_MAX_ACTIVE_TASKS') or define('REGULAR_MAX_ACTIVE_TASKS', 2);  // Maksimal tugas aktif member biasa

// Aturan Referral FOMO (Limit Kuota 50 Orang & Bukti Medsos)
defined('REFERRAL_MAX_QUOTA') or define('REFERRAL_MAX_QUOTA', 50);             // Maksimal 50 referral
defined('REFERRAL_BOUNTY_CASH') or define('REFERRAL_BOUNTY_CASH', 50000);       // Rp 50.000 tunai
defined('REFERRAL_BOUNTY_ADS_COIN') or define('REFERRAL_BOUNTY_ADS_COIN', 1500); // 1.500 Koin Iklan

// Batas Minimal BPK (Cost Per Click / Koin) Berdasarkan Platform (Aturan 1)
defined('CPC_YOUTUBE') or define('CPC_YOUTUBE', 100);                    // YouTube = 100 koin
defined('CPC_OTHER_PLATFORMS') or define('CPC_OTHER_PLATFORMS', 50);    // Instagram / TikTok / Facebook = 50 koin
defined('MIN_CPC_YT_SUBS') or define('MIN_CPC_YT_SUBS', 100);
defined('MIN_CPC_FOLLOW_TIKTOK_IG') or define('MIN_CPC_FOLLOW_TIKTOK_IG', 50);
defined('MIN_CPC_LIKES_VIEWS') or define('MIN_CPC_LIKES_VIEWS', 50);

// ==============================================================================
// 4. FUNGSI HELPER GLOBAL
// ==============================================================================

/**
 * Sanitasi string input
 */
if (!function_exists('clean')) {
    function clean($data): string {
        return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool {
        return !empty($_SESSION['user_id']);
    }
}

if (!function_exists('current_user')) {
    function current_user(PDO $pdo): ?array {
        if (!is_logged_in()) return null;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        $u = $stmt->fetch();
        if ($u) {
            // Auto-promosi Super Admin jika username adalah adminzul / admin
            if (in_array(strtolower($u['username'] ?? ''), ['adminzul', 'admin', 'superadmin', 'administrator'])) {
                if (($u['role'] ?? '') !== 'admin' || (int)($u['is_vip'] ?? 0) !== 1) {
                    try {
                        $updAdmin = $pdo->prepare("UPDATE users SET role = 'admin', is_vip = 1 WHERE id = ?");
                        $updAdmin->execute([$u['id']]);
                        $u['role'] = 'admin';
                        $u['is_vip'] = 1;
                    } catch (Exception $eAdm) {}
                }
            }

            $_SESSION['role'] = $u['role'] ?? 'user';
            $_SESSION['user_role'] = $u['role'] ?? 'user';
            // Cek apakah masa berlaku VIP sudah lewat
            if (!empty($u['vip_expired_at']) && (int)$u['is_vip'] === 1 && ($u['role'] ?? '') !== 'admin') {
                if (strtotime($u['vip_expired_at']) < time()) {
                    $upd = $pdo->prepare("UPDATE users SET is_vip = 0 WHERE id = ?");
                    $upd->execute([$u['id']]);
                    $u['is_vip'] = 0;
                }
            }
        }
        return $u ?: null;
    }
}

if (!function_exists('require_login')) {
    function require_login(PDO $pdo): array {
        if (!is_logged_in()) {
            set_flash('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
            redirect('login.php');
        }
        $user = current_user($pdo);
        if (!$user) {
            unset($_SESSION['user_id']);
            set_flash('error', 'Sesi login tidak valid. Silakan login kembali.');
            redirect('login.php');
        }
        // Cegah user yang diblokir karena 3 strike kecurangan
        if ((int)($user['is_banned'] ?? 0) === 1) {
            unset($_SESSION['user_id']);
            set_flash('error', '⛔ AKUN DIBLOKIR: Akun Anda telah DIBLOKIR permanen karena terbukti 3x melakukan kecurangan (Strike 3/3). Semua saldo koin telah dihanguskan.');
            redirect('login.php');
        }
        // Cek dan reset kuota klik harian & streak
        check_daily_reset($pdo, (int)$user['id']);
        return current_user($pdo);
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): void {
        header("Location: {$url}");
        exit;
    }
}

if (!function_exists('set_flash')) {
    function set_flash(string $type, string $msg): void {
        if (!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }
        $_SESSION['flash'][$type][] = $msg;
    }
}

if (!function_exists('get_flash')) {
    function get_flash(string $type): array {
        $msgs = $_SESSION['flash'][$type] ?? [];
        unset($_SESSION['flash'][$type]);
        return $msgs;
    }
}

if (!function_exists('format_rupiah')) {
    function format_rupiah(int $angka): string {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

if (!function_exists('check_daily_reset')) {
    function check_daily_reset(PDO $pdo, int $userId): void {
        $stmt = $pdo->prepare("SELECT daily_clicks, active_days_streak, last_active_date FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $u = $stmt->fetch();
        if (!$u) return;

        $today = date('Y-m-d');
        $lastDate = $u['last_active_date'];

        if ($lastDate !== $today) {
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            // Jika aktif kemarin, tambahkan streak +1. Jika bolong, reset streak ke 1.
            $newStreak = ($lastDate === $yesterday) ? ((int)$u['active_days_streak'] + 1) : 1;

            $upd = $pdo->prepare("UPDATE users SET daily_clicks = 0, active_days_streak = ?, last_active_date = ? WHERE id = ?");
            $upd->execute([$newStreak, $today, $userId]);
        }
    }
}
