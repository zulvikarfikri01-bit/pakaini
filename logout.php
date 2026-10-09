<?php
/**
 * PAKAINI - Social Media Exchange Platform
 * Script Logout / Keluar Sesi Pengguna
 */

require_once __DIR__ . '/config.php';

// Hapus semua data sesi
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Mulai sesi baru hanya untuk pesan notifikasi
session_start();
set_flash('success', 'Anda telah berhasil keluar akun. Sampai jumpa kembali!');
redirect('login.php');
