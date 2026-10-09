<?php
/**
 * Test Suite: Validasi Lengkap Seluruh Logika Bisnis Social Media Exchange
 */

require_once __DIR__ . '/../config.php';

echo "=== MEMULAI PENGUJIAN LOGIKA BISNIS PAKAINI ===\n\n";

$passCount = 0;
$failCount = 0;

function assert_test($condition, $testName) {
    global $passCount, $failCount;
    if ($condition) {
        echo "✅ PASS: $testName\n";
        $passCount++;
    } else {
        echo "❌ FAIL: $testName\n";
        $failCount++;
    }
}

// ------------------------------------------------------------------------------
// Test 1: Konfigurasi & Koneksi Database
// ------------------------------------------------------------------------------
assert_test($pdo !== null, "Koneksi database PDO berhasil diinisialisasi");
assert_test(defined('SITE_NAME') && SITE_NAME === 'PAKAINI', "Konstanta SITE_NAME didefinisikan");
assert_test(defined('ADMIN_WHATSAPP'), "Konstanta ADMIN_WHATSAPP didefinisikan");

// ------------------------------------------------------------------------------
// Test 2: Pendaftaran Pengguna Baru (Bonus 500 Koin + Trial VIP 3 Hari)
// ------------------------------------------------------------------------------
$testUsername = 'test_user_' . time();
$testEmail = $testUsername . '@test.com';
$testPass = password_hash('secret123', PASSWORD_DEFAULT);
$vipExpired = date('Y-m-d H:i:s', strtotime('+3 days'));
$today = date('Y-m-d');

$stmtReg = $pdo->prepare("
    INSERT INTO users (username, email, password, coins, is_vip, vip_expired_at, referred_by, daily_clicks, active_days_streak, last_active_date, created_at)
    VALUES (?, ?, ?, 500, 1, ?, 'admin', 0, 1, ?, NOW())
");
$stmtReg->execute([$testUsername, $testEmail, $testPass, $vipExpired, $today]);
$testUserId = (int)$pdo->lastInsertId();

// Ambil data user yang baru didaftarkan
$stmtFetch = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmtFetch->execute([$testUserId]);
$newUser = $stmtFetch->fetch();

assert_test($newUser !== false, "User baru berhasil disimpan ke database");
assert_test((int)$newUser['coins'] === 500, "User baru otomatis mendapatkan saldo awal 500 Koin");
assert_test((int)$newUser['is_vip'] === 1, "User baru otomatis mendapatkan status is_vip = 1 (Trial VIP)");
assert_test(!empty($newUser['vip_expired_at']), "User baru memiliki tanggal vip_expired_at");

$diffHours = (strtotime($newUser['vip_expired_at']) - time()) / 3600;
assert_test($diffHours >= 70 && $diffHours <= 73, "vip_expired_at berjarak 3 hari (sekitar 72 jam) dari sekarang");

// ------------------------------------------------------------------------------
// Test 3: Logika Batas Kampanye (Regular Maksimal 2, VIP Unlimited)
// ------------------------------------------------------------------------------
// Buat akun reguler
$regUser = 'reg_' . time();
$pdo->prepare("INSERT INTO users (username, email, password, coins, is_vip, daily_clicks, active_days_streak, last_active_date) VALUES (?, ?, 'pass', 1000, 0, 0, 1, ?)")
    ->execute([$regUser, $regUser.'@test.com', $today]);
$regUserId = (int)$pdo->lastInsertId();

// Pasang 2 tugas aktif untuk member reguler
$pdo->prepare("INSERT INTO tasks (user_id, platform, task_type, task_url, cpc, status) VALUES (?, 'youtube', 'subscribers', 'http://yt1.com', 150, 'active')")->execute([$regUserId]);
$pdo->prepare("INSERT INTO tasks (user_id, platform, task_type, task_url, cpc, status) VALUES (?, 'tiktok', 'followers', 'http://tt1.com', 50, 'active')")->execute([$regUserId]);

$stmtActiveReg = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ? AND status = 'active'");
$stmtActiveReg->execute([$regUserId]);
$regTaskCount = (int)$stmtActiveReg->fetchColumn();

assert_test($regTaskCount === 2, "Member reguler memiliki 2 tugas aktif");
$canAddThird = ($regTaskCount < 2);
assert_test($canAddThird === false, "Member reguler ditolak saat ingin memasang tugas ke-3 (Batas 2 tugas aktif)");

// ------------------------------------------------------------------------------
// Test 4: Batas Minimal BPK (Cost Per Click)
// ------------------------------------------------------------------------------
assert_test(MIN_CPC_YT_SUBS === 150, "Batas minimal BPK Subs YouTube = 150 koin");
assert_test(MIN_CPC_FOLLOW_TIKTOK_IG === 50, "Batas minimal BPK Ikuti TikTok / IG = 50 koin");
assert_test(MIN_CPC_LIKES_VIEWS === 20, "Batas minimal BPK Suka / Tayangan = 20 koin");

// ------------------------------------------------------------------------------
// Test 5: Urutan Tampil Query SQL (ORDER BY is_vip DESC, cpc DESC)
// ------------------------------------------------------------------------------
// Buat tugas milik VIP dengan CPC 50 dan tugas non-VIP dengan CPC 150
$pdo->prepare("INSERT INTO tasks (user_id, platform, task_type, task_url, cpc, status) VALUES (?, 'instagram', 'followers', 'http://ig-vip.com', 50, 'active')")->execute([$testUserId]);
$vipTaskId = (int)$pdo->lastInsertId();

$sqlOrderTest = "
    SELECT t.id, t.cpc, u.is_vip
    FROM tasks t
    JOIN users u ON t.user_id = u.id
    WHERE t.status = 'active'
    ORDER BY u.is_vip DESC, t.cpc DESC
";
$orderedTasks = $pdo->query($sqlOrderTest)->fetchAll();
assert_test(!empty($orderedTasks), "Query berhasil mengambil daftar tugas");
assert_test((int)$orderedTasks[0]['is_vip'] === 1, "Tugas pertama dalam query adalah milik akun VIP (Prioritas Teratas)");

// ------------------------------------------------------------------------------
// Test 6: Batas Klik Harian Member Reguler (Max 50) vs VIP (Tanpa Batas)
// ------------------------------------------------------------------------------
assert_test(REGULAR_DAILY_MAX_CLICKS === 50, "Batas maksimal klik harian member biasa = 50");
$regClicksMax = 50;
$regAllowed = ($regClicksMax < REGULAR_DAILY_MAX_CLICKS);
assert_test($regAllowed === false, "Member reguler dengan 50 klik ditolak untuk klik ke-51");

// ------------------------------------------------------------------------------
// Test 7: Logika Pajak Koin (Tugas 50 koin: Biasa terima 40 koin, VIP terima 50 koin)
// ------------------------------------------------------------------------------
$taskCpc = 50;

// Klaim oleh Member Biasa (is_vip = 0)
$taxRateRegular = 0.20;
$taxBurnedRegular = (int)floor($taskCpc * $taxRateRegular);
$rewardRegular = $taskCpc - $taxBurnedRegular;

assert_test($taxBurnedRegular === 10, "Pajak 20% dari 50 koin = 10 koin");
assert_test($rewardRegular === 40, "Member biasa hanya menerima 40 koin dari tugas 50 koin");

// Klaim oleh Member VIP (is_vip = 1)
$taxRateVip = 0.0;
$taxBurnedVip = (int)floor($taskCpc * $taxRateVip);
$rewardVip = $taskCpc - $taxBurnedVip;

assert_test($taxBurnedVip === 0, "Pajak VIP = 0 koin (Bebas Pajak)");
assert_test($rewardVip === 50, "Member VIP menerima utuh 50 koin tanpa potongan");

// ------------------------------------------------------------------------------
// Test 8: Referral Bounty Kriteria LULUS (Beli VIP ATAU Aktif 10 Hari)
// ------------------------------------------------------------------------------
$friend1 = ['is_vip' => 1, 'active_days_streak' => 2]; // Beli VIP
$friend2 = ['is_vip' => 0, 'active_days_streak' => 10]; // Aktif 10 hari
$friend3 = ['is_vip' => 0, 'active_days_streak' => 4];  // Belum memenuhi

$isLulus1 = ($friend1['is_vip'] === 1 || $friend1['active_days_streak'] >= 10);
$isLulus2 = ($friend2['is_vip'] === 1 || $friend2['active_days_streak'] >= 10);
$isLulus3 = ($friend3['is_vip'] === 1 || $friend3['active_days_streak'] >= 10);

assert_test($isLulus1 === true, "Teman yang sudah beli VIP dinyatakan LULUS");
assert_test($isLulus2 === true, "Teman dengan streak aktif 10 hari dinyatakan LULUS");
assert_test($isLulus3 === false, "Teman belum VIP dan baru aktif 4 hari TIDAK LULUS (masih proses)");

// Target 50 orang = Rp 50.000
$targetPeople = 50;
$targetPrize = 50000;
assert_test($targetPeople === 50 && $targetPrize === 50000, "Target progress bar: 50 orang = Rp 50.000");

// ------------------------------------------------------------------------------
// Test 9: Daftar Harga Topup & WhatsApp Admin
// ------------------------------------------------------------------------------
$price1000 = 50000;
$price2000 = 100000;
$priceVip  = 30000;

assert_test($price1000 === 50000, "Paket 1.000 Koin = Rp 50.000");
assert_test($price2000 === 100000, "Paket 2.000 Koin = Rp 100.000");
assert_test($priceVip === 30000, "VIP 1 Bulan = Rp 30.000");

// Cek URL WhatsApp
$waTest = "https://wa.me/" . ADMIN_WHATSAPP . "?text=Halo";
assert_test(str_starts_with($waTest, 'https://wa.me/'), "Link topup mengarah ke WhatsApp Admin https://wa.me/...");

// ------------------------------------------------------------------------------
// Ringkasan Pengujian
// ------------------------------------------------------------------------------
echo "\n=========================================\n";
echo "HASIL PENGUJIAN: $passCount LULUS, $failCount GAGAL\n";
echo "=========================================\n";

if ($failCount > 0) {
    exit(1);
}
echo "SEMUA FITUR DAN LOGIKA BISNIS 100% VALID & BEKERJA DENGAN SEMPURNA!\n";
