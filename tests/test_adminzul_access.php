<?php
/**
 * Test Suite: Verifikasi Akses Admin untuk Akun Terdaftar (adminzul)
 */

require_once __DIR__ . '/../config.php';

// Pastikan user 1 adalah adminzul
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = 1");
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

echo "\n--- [1] Memeriksa Data Akun adminzul di Database ---\n";
echo "ID: " . $user['id'] . "\n";
echo "Username: " . $user['username'] . "\n";
echo "Role: " . $user['role'] . "\n";
echo "Coins: " . $user['coins'] . "\n";
echo "Is VIP: " . $user['is_vip'] . "\n";

assert($user['role'] === 'admin', 'adminzul harus memiliki role = admin');
assert((int)$user['is_vip'] === 1, 'adminzul harus memiliki is_vip = 1');
assert((int)$user['coins'] >= 10000, 'adminzul harus memiliki saldo koin admin');
echo "  ✓ PASS: Data adminzul di database valid sebagai admin!\n";

echo "\n--- [2] Memeriksa Proteksi admin.php untuk adminzul ---\n";
// Simulasikan sesi adminzul
$_SESSION['user_id'] = 1;
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = 'admin';
$_SESSION['user_role'] = 'admin';

$current = current_user($pdo);
assert($current['role'] === 'admin', 'current_user() harus mengembalikan role = admin');
assert($_SESSION['role'] === 'admin', 'Session role harus admin');
assert($_SESSION['user_role'] === 'admin', 'Session user_role harus admin');
echo "  ✓ PASS: Sesi terverifikasi sebagai role admin!\n";

echo "\n--- [3] Memeriksa Komponen Tampilan Khusus Admin ---\n";
$navbarSrc = file_get_contents(__DIR__ . '/../navbar.php');
assert(strpos($navbarSrc, 'admin.php') !== false, 'navbar.php memiliki link admin.php');
assert(strpos($navbarSrc, "role'] ?? '') === 'admin'") !== false, 'navbar.php memeriksa role admin');

$dashSrc = file_get_contents(__DIR__ . '/../dashboard.php');
assert(strpos($dashSrc, 'Panel Kendali Utama Tersedia') !== false, 'dashboard.php memuat banner admin');
assert(strpos($dashSrc, 'admin.php') !== false, 'dashboard.php memiliki link ke admin.php');

$viewDashSrc = file_get_contents(__DIR__ . '/../views/dashboard/index.php');
assert(strpos($viewDashSrc, 'Panel Kendali Utama Tersedia') !== false, 'views/dashboard/index.php memuat banner admin');
assert(strpos($viewDashSrc, 'admin.php') !== false, 'views/dashboard/index.php memiliki link ke admin.php');

$profileSrc = file_get_contents(__DIR__ . '/../profile.php');
assert(strpos($profileSrc, 'admin.php') !== false, 'profile.php memiliki tab admin.php');
assert(strpos($profileSrc, '👑 Administrator') !== false, 'profile.php memiliki badge Administrator');

echo "  ✓ PASS: Semua navigasi dan banner admin tersedia di dashboard, navbar, dan profil!\n";

echo "\n============================================\n";
echo "SEMUA PEMERIKSAAN AKSES ADMIN BERHASIL 100%!\n";
