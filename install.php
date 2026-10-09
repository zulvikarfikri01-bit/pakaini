<?php
/**
 * PAKAINI - 1-Click Database Installer & Supabase/Railway Setup Tool
 * Mengimpor database.sql ke PostgreSQL (Supabase) secara otomatis
 */

define('ROOT_PATH', __DIR__);
require_once ROOT_PATH . '/config/database.php';

$message = null;
$error = null;
$installed = false;

// Cek Ekstensi Server
$requirements = [
    'PHP Version >= 8.1' => version_compare(PHP_VERSION, '8.1.0', '>='),
    'PDO Extension'      => extension_loaded('pdo'),
    'PDO PostgreSQL'     => extension_loaded('pdo_pgsql'),
    'Mbstring'           => extension_loaded('mbstring'),
    'cURL'               => extension_loaded('curl'),
    'OpenSSL'            => extension_loaded('openssl'),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install_db'])) {
    $connUrl = trim($_POST['db_url'] ?? '');
    $host = trim($_POST['db_host'] ?? DB_HOST);
    $port = trim($_POST['db_port'] ?? DB_PORT);
    $name = trim($_POST['db_name'] ?? DB_NAME);
    $user = trim($_POST['db_user'] ?? DB_USER);
    $pass = $_POST['db_pass'] ?? DB_PASS;

    if (!empty($connUrl)) {
        $parsed = parse_url($connUrl);
        if (!empty($parsed['host'])) {
            $host = $parsed['host'];
            $port = $parsed['port'] ?? 5432;
            $name = ltrim($parsed['path'] ?? 'postgres', '/');
            $user = isset($parsed['user']) ? urldecode($parsed['user']) : $user;
            $pass = isset($parsed['pass']) ? urldecode($parsed['pass']) : $pass;
        }
    }

    try {
        if (!extension_loaded('pdo_pgsql')) {
            throw new Exception("Ekstensi PHP 'pdo_pgsql' belum aktif di server ini.");
        }

        $sslPart = ($host !== 'localhost' && $host !== '127.0.0.1') ? ';sslmode=require' : '';
        $dsn = "pgsql:host={$host};port={$port};dbname={$name}{$sslPart}";

        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 15
        ]);

        // Baca file database.sql
        $sqlFile = ROOT_PATH . '/database.sql';
        if (!file_exists($sqlFile)) {
            throw new Exception("File database.sql tidak ditemukan di direktori utama.");
        }

        $sqlContent = file_get_contents($sqlFile);
        $pdo->exec($sqlContent);

        // Update file config/database.php dengan connection string baru jika diisi
        $dbConfigPath = ROOT_PATH . '/config/database.php';
        if (file_exists($dbConfigPath) && is_writable($dbConfigPath)) {
            $cfgContent = file_get_contents($dbConfigPath);
            if (!empty($connUrl)) {
                $cfgContent = preg_replace("/\\\$supabase_url\s*=\s*'.*?';/", "\$supabase_url = '{$connUrl}';", $cfgContent);
            } else {
                $cfgContent = preg_replace("/\\\$db_host\s*=\s*'.*?';/", "\$db_host = '{$host}';", $cfgContent);
                $cfgContent = preg_replace("/\\\$db_port\s*=\s*\d+;/", "\$db_port = {$port};", $cfgContent);
                $cfgContent = preg_replace("/\\\$db_name\s*=\s*'.*?';/", "\$db_name = '{$name}';", $cfgContent);
                $cfgContent = preg_replace("/\\\$db_user\s*=\s*'.*?';/", "\$db_user = '{$user}';", $cfgContent);
                $cfgContent = preg_replace("/\\\$db_pass\s*=\s*'.*?';/", "\$db_pass = '{$pass}';", $cfgContent);
            }
            @file_put_contents($dbConfigPath, $cfgContent);
        }

        $message = "Database Supabase PostgreSQL berhasil diinstal! Semua tabel, indeks, dan akun pengujian bawaan telah siap.";
        $installed = true;
    } catch (Exception $e) {
        $error = "Gagal menginstal database: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalasi Database PostgreSQL — PAKAINI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> body { background-color: #070a13; color: #f1f5f9; font-family: 'Inter', sans-serif; } </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="max-w-xl w-full bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">
        
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-sky-600 via-indigo-600 to-amber-500 flex items-center justify-center shadow-lg shadow-sky-500/30 mx-auto mb-3">
                <i class="fa-solid fa-database text-white text-xl"></i>
            </div>
            <h1 class="font-extrabold text-2xl text-white font-['Outfit']">PAKAINI Setup & Installer</h1>
            <p class="text-xs text-slate-400 mt-1">Inisialisasi Skema PostgreSQL Supabase / Railway</p>
        </div>

        <?php if ($message): ?>
            <div class="p-4 mb-6 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs">
                <i class="fa-solid fa-circle-check text-emerald-400 text-sm mr-1.5"></i>
                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
            </div>

            <div class="p-4 mb-6 rounded-2xl bg-slate-950 border border-slate-800 text-xs space-y-2">
                <span class="font-bold text-amber-400 block uppercase">Akun Pengujian Bawaan:</span>
                <div class="grid grid-cols-2 gap-2 text-slate-300">
                    <div>User: <strong class="text-white">adminzul</strong></div>
                    <div>Pass: <strong class="text-white">admin123</strong> (Super Admin)</div>
                    <div>User: <strong class="text-white">member_biasa</strong></div>
                    <div>Pass: <strong class="text-white">admin123</strong> (Regular)</div>
                </div>
            </div>

            <a href="index.php?page=login" class="w-full py-3.5 bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 font-bold rounded-xl text-center block text-sm shadow-lg shadow-amber-500/20 hover:opacity-90 transition">
                Masuk ke Login Page &rarr;
            </a>
        <?php else: ?>

            <?php if ($error): ?>
                <div class="p-4 mb-6 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs">
                    <i class="fa-solid fa-circle-exclamation text-rose-400 text-sm mr-1.5"></i>
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <!-- Server Compatibility Checklist -->
            <div class="mb-6 bg-slate-950 border border-slate-800/80 rounded-2xl p-4">
                <span class="text-xs font-bold text-slate-400 block mb-2 uppercase tracking-wider">Kesiapan Server:</span>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <?php foreach ($requirements as $req => $ok): ?>
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid <?= $ok ? 'fa-check text-emerald-400' : 'fa-xmark text-rose-400' ?>"></i>
                            <span class="<?= $ok ? 'text-slate-300' : 'text-rose-400 font-bold' ?>"><?= $req ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Supabase Connection URL (URI)
                        <span class="text-sky-400 text-[10px] ml-1">(Paling Cepat & Direkomendasikan)</span>
                    </label>
                    <input type="text" name="db_url" placeholder="postgresql://postgres:[PASSWORD]@db.xxxx.supabase.co:5432/postgres" class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-sky-500">
                </div>

                <div class="relative flex py-2 items-center">
                    <div class="flex-grow border-t border-slate-800"></div>
                    <span class="flex-shrink mx-4 text-[10px] text-slate-500 font-bold uppercase">Atau Parameter Manual</span>
                    <div class="flex-grow border-t border-slate-800"></div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-300 mb-1">DB Host</label>
                        <input type="text" name="db_host" value="<?= htmlspecialchars(DB_HOST, ENT_QUOTES, 'UTF-8') ?>" class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">DB Port</label>
                        <input type="number" name="db_port" value="<?= htmlspecialchars((string)DB_PORT, ENT_QUOTES, 'UTF-8') ?>" class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-sky-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">DB Name</label>
                        <input type="text" name="db_name" value="<?= htmlspecialchars(DB_NAME, ENT_QUOTES, 'UTF-8') ?>" class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">DB User</label>
                        <input type="text" name="db_user" value="<?= htmlspecialchars(DB_USER, ENT_QUOTES, 'UTF-8') ?>" class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-sky-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">DB Password</label>
                    <input type="password" name="db_pass" placeholder="Password Supabase Anda" class="w-full bg-slate-950 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-sky-500">
                </div>

                <button type="submit" name="install_db" class="w-full mt-2 py-3.5 bg-gradient-to-r from-sky-500 via-indigo-600 to-amber-500 text-white font-bold rounded-xl text-sm shadow-lg shadow-sky-500/20 hover:opacity-95 transition">
                    <i class="fa-solid fa-cloud-arrow-up mr-2"></i>Inisialisasi Database Supabase Sekarang
                </button>
            </form>

        <?php endif; ?>

    </div>

</body>
</html>
