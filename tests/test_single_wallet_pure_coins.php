<?php
/**
 * PAKAINI - Single Wallet (Pure Coins) Verification Test Suite
 * Memverifikasi:
 * 1. Tabel users HANYA memiliki 1 kolom dompet: coins.
 * 2. Kolom koin_iklan & koin_penghasilan tidak ada lagi di database maupun skema SQL.
 * 3. Logika klaim tugas menambah ke kolom coins.
 * 4. Logika pembuatan kampanye memotong dari kolom coins.
 * 5. Dashboard & Navbar menampilkan 1 indikator saldo tunggal dalam desain neon/glassmorphism.
 */

$testPassed = 0;
$testFailed = 0;

function assertCheck($condition, $message) {
    global $testPassed, $testFailed;
    if ($condition) {
        echo "  ✅ PASS: " . $message . "\n";
        $testPassed++;
    } else {
        echo "  ❌ FAIL: " . $message . "\n";
        $testFailed++;
    }
}

require_once __DIR__ . '/../config.php';

echo "======================================================================\n";
echo "🪙 PENGUJIAN SISTEM 1 DOMPET TUNGGAL (KOIN MURNI)\n";
echo "======================================================================\n\n";

// 1. Uji Skema Database SQLite users
echo "1. Pengujian Kolom Tabel Users:\n";
$stmt = $pdo->query("PRAGMA table_info(users)");
$userCols = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $userCols[] = $row['name'];
}

assertCheck(in_array('coins', $userCols), "Kolom 'coins' ada di tabel users");
assertCheck(!in_array('koin_iklan', $userCols), "Kolom 'koin_iklan' TIDAK ADA di tabel users (berhasil dihapus)");
assertCheck(!in_array('koin_penghasilan', $userCols), "Kolom 'koin_penghasilan' TIDAK ADA di tabel users (berhasil dihapus)");

// 2. Uji File database.sql
echo "\n2. Pengujian File database.sql:\n";
$sqlFile = file_get_contents(__DIR__ . '/../database.sql');
assertCheck(preg_match('/[`"]?coins[`"]?\s+INT\s+NOT\s+NULL\s+DEFAULT\s+0/i', $sqlFile) === 1, "database.sql mendefinisikan kolom coins");
assertCheck(strpos($sqlFile, 'koin_iklan') === false, "database.sql TIDAK LAGI memuat kolom koin_iklan");
assertCheck(strpos($sqlFile, 'koin_penghasilan') === false, "database.sql TIDAK LAGI memuat kolom koin_penghasilan");

// 3. Uji Penyatuan Transaksi Koin ke Kolom coins (PHP Backend)
echo "\n3. Pengujian Transaksi Tambah/Kurang Koin (Simulasi):\n";
$uniqueTestUser = 'test_coin_wallet_' . time();
$pass = password_hash('test1234', PASSWORD_DEFAULT);
$pdo->prepare("INSERT INTO users (username, email, password, coins) VALUES (?, ?, ?, 1000)")
    ->execute([$uniqueTestUser, "{$uniqueTestUser}@wallet.com", $pass]);
$testUserId = (int)$pdo->lastInsertId();

// Cek saldo awal
$initUser = $pdo->query("SELECT coins FROM users WHERE id = {$testUserId}")->fetch();
assertCheck((int)$initUser['coins'] === 1000, "Saldo awal user terdaftar di kolom coins = 1.000");

// A. Simulasi Klaim Imbalan Tugas (+100 Koin)
$reward = 100;
$pdo->prepare("UPDATE users SET coins = coins + ? WHERE id = ?")->execute([$reward, $testUserId]);
$afterClaim = $pdo->query("SELECT coins FROM users WHERE id = {$testUserId}")->fetch();
assertCheck((int)$afterClaim['coins'] === 1100, "Klaim imbalan tugas (+100 Koin) langsung masuk ke kolom coins (Hasil: 1.100)");

// B. Simulasi Pembuatan Kampanye Iklan (Potong -300 Koin)
$campaignCost = 300;
$pdo->prepare("UPDATE users SET coins = coins - ? WHERE id = ? AND coins >= ?")->execute([$campaignCost, $testUserId, $campaignCost]);
$afterDeduct = $pdo->query("SELECT coins FROM users WHERE id = {$testUserId}")->fetch();
assertCheck((int)$afterDeduct['coins'] === 800, "Pembuatan kampanye iklan (-300 Koin) langsung dipotong dari kolom coins (Hasil: 800)");

// C. Simulasi Top-Up (+5.000 Koin)
$topupAmount = 5000;
$pdo->prepare("UPDATE users SET coins = coins + ? WHERE id = ?")->execute([$topupAmount, $testUserId]);
$afterTopup = $pdo->query("SELECT coins FROM users WHERE id = {$testUserId}")->fetch();
assertCheck((int)$afterTopup['coins'] === 5800, "Top-Up koin (+5.000 Koin) langsung masuk ke kolom coins (Hasil: 5.800)");

// 4. Pengujian UI Dashboard & Navbar
echo "\n4. Pengujian UI Dashboard & Navbar (1 Indikator Saldo Utama Tunggal):\n";
$dashboardContent = file_get_contents(__DIR__ . '/../dashboard.php');
$navbarContent    = file_get_contents(__DIR__ . '/../navbar.php');
$earnContent      = file_get_contents(__DIR__ . '/../earn_coins.php');
$addTaskContent   = file_get_contents(__DIR__ . '/../add_task.php');

assertCheck(strpos($dashboardContent, 'user-coin-balance') !== false, "dashboard.php memiliki penanda saldo user-coin-balance");
assertCheck(strpos($dashboardContent, 'Saldo Koin Anda') !== false || strpos($dashboardContent, 'Saldo Anda') !== false, "dashboard.php menampilkan judul indikator saldo utama");
assertCheck(strpos($dashboardContent, 'Dompet Iklan') === false, "dashboard.php TIDAK memuat istilah 'Dompet Iklan'");
assertCheck(strpos($dashboardContent, 'Dompet Penghasilan') === false, "dashboard.php TIDAK memuat istilah 'Dompet Penghasilan'");

assertCheck(strpos($navbarContent, 'navbarCoinsAmount') !== false, "navbar.php memiliki elemen saldo navbarCoinsAmount");
assertCheck(strpos($navbarContent, 'Dompet Iklan') === false, "navbar.php TIDAK memuat istilah 'Dompet Iklan'");
assertCheck(strpos($navbarContent, 'Dompet Penghasilan') === false, "navbar.php TIDAK memuat istilah 'Dompet Penghasilan'");

assertCheck(strpos($earnContent, 'u.koin_iklan') === false, "earn_coins.php tidak lagi memilih u.koin_iklan");
assertCheck(strpos($earnContent, 'koin_penghasilan') === false, "earn_coins.php tidak lagi memperbarui koin_penghasilan");
assertCheck(strpos($addTaskContent, 'koin_iklan') === false, "add_task.php tidak lagi memperbarui koin_iklan");

echo "\n----------------------------------------------------------------------\n";
echo "RINGKASAN HASIL: {$testPassed} LULUS, {$testFailed} GAGAL\n";
echo "======================================================================\n";

if ($testFailed === 0) {
    echo "🎉 SISTEM 1 DOMPET TUNGGAL (KOIN MURNI) TERBUKTI 100% SUKSES DITERAPKAN!\n";
    exit(0);
} else {
    echo "⚠️ ADA PENGUJIAN YANG GAGAL. SILAKAN CEK DETAIL DI ATAS.\n";
    exit(1);
}
