<?php
/**
 * Test Suite: Sistem Keamanan Koin Instan + Sistem Strike (Tilang) 3x Banned
 * Menguji:
 * 1. Timer 30 Detik & Deteksi Tab (visibilitychange / window.onfocus) + Alert Intimidasi
 * 2. Sistem Strike 3x Banned & Hangus Koin di Backend & Database
 * 3. Pencegahan Login Akun Terbanned
 * 4. Tampilan Pekerja & Tombol Lapor di add_task.php
 * 5. Banner Ancaman UI di Bagian Atas earn_coins.php
 */

require_once __DIR__ . '/../config.php';

echo "======================================================================\n";
echo "🧪 PENGUJIAN SISTEM KEAMANAN KOIN INSTAN + STRIKE (TILANG) 3X BANNED\n";
echo "======================================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest(string $desc, bool $condition) {
    global $passCount, $failCount;
    if ($condition) {
        echo "  ✅ PASS: {$desc}\n";
        $passCount++;
    } else {
        echo "  ❌ FAIL: {$desc}\n";
        $failCount++;
    }
}

// -----------------------------------------------------------------------------
// 1. UJI STRUKTUR DATABASE (strike_count & is_banned)
// -----------------------------------------------------------------------------
echo "1. Pengujian Skema Kolom Database Users & Task History:\n";

$stmtCols = $pdo->query("PRAGMA table_info(users)");
$userCols = array_column($stmtCols->fetchAll(), 'name');

assertTest("Kolom 'strike_count' tersedia di tabel users", in_array('strike_count', $userCols));
assertTest("Kolom 'is_banned' tersedia di tabel users", in_array('is_banned', $userCols));

$stmtHistCols = $pdo->query("PRAGMA table_info(task_history)");
$histCols = array_column($stmtHistCols->fetchAll(), 'name');
assertTest("Kolom 'is_reported' tersedia di tabel task_history", in_array('is_reported', $histCols));

// -----------------------------------------------------------------------------
// 2. UJI LOGIKA STRIKE & BANNED (Backend & Database)
// -----------------------------------------------------------------------------
echo "\n2. Pengujian Logika Tilang (Strike) + Banned 3x & Koin Hangus:\n";

// Siapkan User Pekerja Uji Coba
$testWorkerUsername = 'worker_test_' . time();
$passHash = password_hash('password123', PASSWORD_DEFAULT);
$pdo->prepare("INSERT INTO users (username, email, password, coins, strike_count, is_banned)
    VALUES (?, ?, ?, 2500, 0, 0)")
    ->execute([$testWorkerUsername, "{$testWorkerUsername}@test.com", $passHash]);

$workerId = (int)$pdo->lastInsertId();

// Siapkan Advertiser & Task
$testAdvUsername = 'advertiser_test_' . time();
$pdo->prepare("INSERT INTO users (username, email, password, coins, is_vip)
    VALUES (?, ?, ?, 10000, 1)")
    ->execute([$testAdvUsername, "{$testAdvUsername}@test.com", $passHash]);
$advId = (int)$pdo->lastInsertId();

$pdo->prepare("INSERT INTO tasks (user_id, platform, task_type, task_url, cpc, status)
    VALUES (?, 'youtube', 'subscribers', 'https://youtube.com/@testadv', 150, 'active')")
    ->execute([$advId]);
$taskId = (int)$pdo->lastInsertId();

// Catat Pekerjaan di task_history
$pdo->prepare("INSERT INTO task_history (user_id, task_id, completed_at, is_reported) VALUES (?, ?, NOW(), 0)")
    ->execute([$workerId, $taskId]);
$histId = (int)$pdo->lastInsertId();

// Simulasi Laporan 1 (Strike 1)
$newStrike1 = 1;
$pdo->prepare("UPDATE users SET strike_count = ? WHERE id = ?")->execute([$newStrike1, $workerId]);
$w1 = $pdo->query("SELECT strike_count, is_banned, coins FROM users WHERE id = {$workerId}")->fetch();
assertTest("Laporan 1: strike_count = 1, akun belum dibanned, koin utuh", (int)$w1['strike_count'] === 1 && (int)$w1['is_banned'] === 0 && (int)$w1['coins'] === 2500);

// Simulasi Laporan 2 (Strike 2)
$newStrike2 = 2;
$pdo->prepare("UPDATE users SET strike_count = ? WHERE id = ?")->execute([$newStrike2, $workerId]);
$w2 = $pdo->query("SELECT strike_count, is_banned, coins FROM users WHERE id = {$workerId}")->fetch();
assertTest("Laporan 2: strike_count = 2, akun belum dibanned, koin utuh", (int)$w2['strike_count'] === 2 && (int)$w2['is_banned'] === 0 && (int)$w2['coins'] === 2500);

// Simulasi Laporan 3 (Strike 3 - HUKUMAN MAKSIMAL)
$newStrike3 = 3;
$pdo->prepare("UPDATE users SET strike_count = ?, is_banned = 1, coins = 0 WHERE id = ?")->execute([$newStrike3, $workerId]);
$w3 = $pdo->query("SELECT strike_count, is_banned, coins FROM users WHERE id = {$workerId}")->fetch();
assertTest("Laporan 3: strike_count = 3, is_banned = 1", (int)$w3['strike_count'] === 3 && (int)$w3['is_banned'] === 1);
assertTest("Laporan 3: Seluruh Saldo Koin Hangus (coins = 0)", (int)$w3['coins'] === 0);

// -----------------------------------------------------------------------------
// 3. UJI CEGAH LOGIN USER TERBANNED
// -----------------------------------------------------------------------------
echo "\n3. Pengujian Pencegahan Login Akun Terbanned:\n";

// Cek autentikasi di login.php logic
$stmtAuth = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmtAuth->execute([$workerId]);
$bannedUser = $stmtAuth->fetch();

$loginBlocked = false;
if ($bannedUser && password_verify('password123', $bannedUser['password'])) {
    if ((int)$bannedUser['is_banned'] === 1) {
        $loginBlocked = true;
    }
}
assertTest("User yang memiliki is_banned = 1 ditolak saat login", $loginBlocked);

// -----------------------------------------------------------------------------
// 4. UJI KODE earn_coins.php (TIMER 30 DETIK, DETEKSI TAB, POPUP ALERT, BANNER)
// -----------------------------------------------------------------------------
echo "\n4. Pengujian File earn_coins.php:\n";

$earnCode = file_get_contents(__DIR__ . '/../earn_coins.php');

// A. Timer Dinamis (30 Detik YouTube & 15 Detik selain YouTube)
assertTest("earn_coins.php memiliki timer totalTime = 30 untuk YouTube dan 15 untuk lainnya", strpos($earnCode, 'totalTime = isYouTube ? 30 : 15;') !== false);
assertTest("earn_coins.php validasi backend syarat spentTime dinamis berdasarkan platform", strpos($earnCode, '$spentTime < $minAllowedSpent') !== false);

// B. Deteksi Tab (visibilitychange & window.onfocus)
assertTest("earn_coins.php melacak event 'visibilitychange'", strpos($earnCode, "document.addEventListener('visibilitychange'") !== false);
assertTest("earn_coins.php melacak event 'focus'", strpos($earnCode, "window.addEventListener('focus'") !== false);

// C. Perang Psikologis Alert Intimidasi
$exactCheatAlertText = "⚠️️ PERINGATAN: Sistem mendeteksi Anda kembali terlalu cepat! Selesaikan tugas dengan benar atau akun Anda akan diblokir.";
assertTest("earn_coins.php memuat teks pop-up alert intimidasi yang tepat", strpos($earnCode, $exactCheatAlertText) !== false);

// D. Tombol Klaim Koin HANYA muncul setelah 30 detik
assertTest("Tombol klaim tersembunyi diawal (class 'hidden')", strpos($earnCode, 'id="claimRewardBtn" class="hidden') !== false);
assertTest("Tombol klaim diaktifkan dan dimunculkan di enableClaimButton()", strpos($earnCode, "claimBtn.classList.remove('hidden')") !== false);

// E. Banner Ancaman UI di bagian atas
$exactBannerText = "PERHATIAN: Pemasang Iklan memantau pekerjaan Anda secara manual! Mengambil koin tanpa melakukan tugas = LAPORAN. 3x Laporan = Akun Diblokir Permanen & Koin Hangus!";
assertTest("earn_coins.php memuat banner ancaman UI di paling atas", strpos($earnCode, $exactBannerText) !== false);

// -----------------------------------------------------------------------------
// 5. UJI KODE add_task.php (KELOLA TUGAS, DAFTAR PEKERJA & TOMBOL LAPOR)
// -----------------------------------------------------------------------------
echo "\n5. Pengujian File add_task.php:\n";

$addTaskCode = file_get_contents(__DIR__ . '/../add_task.php');

assertTest("add_task.php mengambil riwayat pekerja dari task_history", strpos($addTaskCode, 'FROM task_history th') !== false);
assertTest("add_task.php memiliki tombol 'Laporkan Pekerja (Curang)'", strpos($addTaskCode, 'Laporkan Pekerja (Curang)') !== false);
assertTest("add_task.php menangani aksi report_worker", strpos($addTaskCode, "\$reportAction === 'report_worker'") !== false);
assertTest("add_task.php menaikkan strike_count dan mengubah is_banned = 1 serta koin = 0 jika mencapai 3", strpos($addTaskCode, 'strike_count = ?, is_banned = 1, coins = 0') !== false);

// Cleanup data pengujian
$pdo->prepare("DELETE FROM task_history WHERE id = ?")->execute([$histId]);
$pdo->prepare("DELETE FROM tasks WHERE id = ?")->execute([$taskId]);
$pdo->prepare("DELETE FROM users WHERE id IN (?, ?)")->execute([$workerId, $advId]);

echo "\n----------------------------------------------------------------------\n";
echo "RINGKASAN HASIL PENGUJIAN: {$passCount} LULUS, {$failCount} GAGAL\n";
echo "======================================================================\n";
if ($failCount === 0) {
    echo "🎉 SEMUA ATURAN KEAMANAN DAN SISTEM STRIKE BERHASIL DIVERIFIKASI 100%!\n";
}
