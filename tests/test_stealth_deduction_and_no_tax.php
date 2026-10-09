<?php
/**
 * Test Suite: Pemotongan Siluman & Pembersihan Total Kata Pajak
 * (Stealth Deduction & No Tax Text Across UI & Backend)
 */
ob_start();
require_once __DIR__ . '/../config.php';
ob_end_clean();

echo "=== TEST SUITE: PEMOTONGAN SILUMAN & PEMBERSIHAN KATA PAJAK ===\n\n";

function assert_test(bool $condition, string $label): void {
    if ($condition) {
        echo "[PASS] {$label}\n";
    } else {
        echo "[FAIL] {$label}\n";
        exit(1);
    }
}

// ------------------------------------------------------------------------------
// TEST 1: Pembersihan Teks di Seluruh UI (dashboard.php, add_task.php, earn_coins.php)
// ------------------------------------------------------------------------------
echo "--- TEST 1: Pembersihan Teks di Seluruh UI ---\n";
$dashboardSrc = file_get_contents(__DIR__ . '/../dashboard.php');
$addTaskSrc   = file_get_contents(__DIR__ . '/../add_task.php');
$earnSrc      = file_get_contents(__DIR__ . '/../earn_coins.php');
$loadTasksSrc = file_get_contents(__DIR__ . '/../load_tasks.php');

assert_test(stripos($dashboardSrc, 'pajak') === false, "dashboard.php 100% bebas dari kata 'pajak'");
assert_test(stripos($dashboardSrc, 'potongan sistem') === false, "dashboard.php bebas dari 'potongan sistem'");

assert_test(stripos($addTaskSrc, 'pajak') === false, "add_task.php 100% bebas dari kata 'pajak'");
assert_test(stripos($addTaskSrc, 'potongan sistem') === false, "add_task.php bebas dari 'potongan sistem'");

assert_test(stripos($earnSrc, 'pajak') === false, "earn_coins.php 100% bebas dari kata 'pajak'");
assert_test(stripos($earnSrc, 'potongan sistem') === false, "earn_coins.php bebas dari 'potongan sistem'");

assert_test(stripos($loadTasksSrc, 'pajak') === false, "load_tasks.php 100% bebas dari kata 'pajak'");

// Verifikasi add_task.php menampilkan "Harga per Klik" dan "Total Biaya"
assert_test(strpos($addTaskSrc, 'Harga per Klik') !== false, "add_task.php menampilkan label 'Harga per Klik'");
assert_test(strpos($addTaskSrc, 'Total Biaya:') !== false, "add_task.php menampilkan label 'Total Biaya:'");

// ------------------------------------------------------------------------------
// TEST 2: Logika Kartu Tugas: Hadiah Bersih (floor(cpc * 0.8)) & Label "Hadiah: [angka] Koin"
// ------------------------------------------------------------------------------
echo "\n--- TEST 2: Kartu Tugas & Pemotongan Siluman (Frontend Rendering) ---\n";

assert_test(strpos($loadTasksSrc, '$hadiah_bersih = (int)floor($cpc * 0.8);') !== false ||
            strpos($loadTasksSrc, 'floor($cpc * 0.8)') !== false, "load_tasks.php menghitung \$hadiah_bersih = floor(\$cpc * 0.8)");

assert_test(strpos($loadTasksSrc, 'Hadiah: <?= number_format($hadiah_bersih) ?> Koin') !== false ||
            strpos($loadTasksSrc, 'Hadiah: ') !== false, "load_tasks.php menampilkan label 'Hadiah: [angka] Koin'");

// Simulasikan render_task_card untuk tugas dengan CPC 100 (YouTube) & 50 (Instagram)
require_once __DIR__ . '/../load_tasks.php';

$mockTask100 = [
    'id' => 9991,
    'platform' => 'youtube',
    'task_type' => 'subscribe',
    'cpc' => 100,
    'task_url' => 'https://youtube.com/@test',
    'owner_username' => 'test_owner',
    'owner_is_vip' => 0,
    'is_admin_task' => 0,
    'is_priority' => 0
];
$cardHtml100 = render_task_card($mockTask100, false);
assert_test(strpos($cardHtml100, 'Hadiah: 80 Koin') !== false, "Tugas CPC 100 ditampilkan sebagai 'Hadiah: 80 Koin' (potongan senyap 20%)");
assert_test(strpos($cardHtml100, '+100 Koin') === false, "Tugas CPC 100 TIDAK menampilkan imbalan kotor +100 Koin");

$mockTask50 = [
    'id' => 9992,
    'platform' => 'instagram',
    'task_type' => 'follow',
    'cpc' => 50,
    'task_url' => 'https://instagram.com/test',
    'owner_username' => 'test_owner',
    'owner_is_vip' => 0,
    'is_admin_task' => 0,
    'is_priority' => 0
];
$cardHtml50 = render_task_card($mockTask50, false);
assert_test(strpos($cardHtml50, 'Hadiah: 40 Koin') !== false, "Tugas CPC 50 ditampilkan sebagai 'Hadiah: 40 Koin' (potongan senyap 20%)");
assert_test(strpos($cardHtml50, '+50 Koin') === false, "Tugas CPC 50 TIDAK menampilkan imbalan kotor +50 Koin");

// ------------------------------------------------------------------------------
// TEST 3: Klaim Tugas di Backend (AJAX earn_coins.php)
// ------------------------------------------------------------------------------
echo "\n--- TEST 3: Eksekusi Klaim Koin di Backend (Nilai Hadiah Bersih) ---\n";

// Siapkan database fixtures
$pdo->exec("DELETE FROM users WHERE username IN ('stealth_owner', 'stealth_worker')");
$pdo->exec("INSERT INTO users (username, email, password, coins, is_active, daily_clicks) VALUES ('stealth_owner', 'so@test.com', 'pass', 1000, 1, 0)");
$ownerId = (int)$pdo->lastInsertId();

$pdo->exec("INSERT INTO users (username, email, password, coins, is_active, daily_clicks) VALUES ('stealth_worker', 'sw@test.com', 'pass', 100, 1, 0)");
$workerId = (int)$pdo->lastInsertId();

// Buat tugas dengan CPC = 100
$pdo->exec("INSERT INTO tasks (user_id, platform, task_type, task_url, cpc, target_clicks, max_clicks, current_clicks, is_active, status) 
            VALUES ({$ownerId}, 'youtube', 'subscribe', 'https://youtube.com/@stealth', 100, 10, 10, 0, 1, 'active')");
$taskId = (int)$pdo->lastInsertId();

// Simulasikan klaim AJAX via temporary runner
$tempWorker = __DIR__ . '/_temp_stealth_worker.php';
$workerCode = "<?php
session_start();
\$_SESSION['user_id'] = {$workerId};
\$_SESSION['role'] = 'user';
\$_SESSION['task_start_time'] = time() - 35;
\$_SESSION['current_task_id'] = {$taskId};
\$_SERVER['REQUEST_METHOD'] = 'POST';
\$_POST = [
    'action' => 'claim_reward',
    'task_id' => {$taskId},
    'spent_time' => 32
];
chdir(__DIR__ . '/..');
include 'earn_coins.php';
";
file_put_contents($tempWorker, $workerCode);

$output = shell_exec("php " . escapeshellarg($tempWorker));
@unlink($tempWorker);

$res = json_decode($output, true);
assert_test(is_array($res) && ($res['success'] ?? false) === true, "Eksekusi klaim reward berhasil via earn_coins.php");
assert_test(($res['reward'] ?? 0) === 80, "Nilai reward yang dikembalikan AJAX adalah 80 Koin (bukan 100)");

// Cek saldo pekerja di database
$workerCoins = (int)$pdo->query("SELECT coins FROM users WHERE id = {$workerId}")->fetchColumn();
assert_test($workerCoins === 180, "Saldo pekerja bertambah +80 koin (100 -> 180), bukan +100 koin");

// Cek saldo pemilik (harus tetap tidak berubah saat dikerjakan karena dipotong di awal)
$ownerCoins = (int)$pdo->query("SELECT coins FROM users WHERE id = {$ownerId}")->fetchColumn();
assert_test($ownerCoins === 1000, "Saldo pemilik tetap aman 1000 koin");

// Cek catatan transaksi (tidak boleh ada kata pajak)
$txDesc = $pdo->query("SELECT description FROM transactions WHERE user_id = {$workerId} ORDER BY id DESC LIMIT 1")->fetchColumn();
assert_test(stripos($txDesc, 'pajak') === false, "Deskripsi mutasi transaksi bebas dari kata pajak (Hasil: '{$txDesc}')");

// Bersihkan fixtures
$pdo->exec("DELETE FROM tasks WHERE id = {$taskId}");
$pdo->exec("DELETE FROM users WHERE id IN ({$ownerId}, {$workerId})");

echo "\n=== SEMUA PENGUJIAN PEMOTONGAN SILUMAN BERHASIL LULUS 100% ===\n";
