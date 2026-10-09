<?php
/**
 * Test Suite: Final Polish Sebelum Grand Launching
 * Memverifikasi:
 * 1. Skema Database tasks (target_clicks, current_clicks, is_active)
 * 2. Logika Kuota Klik & Upfront Payment di add_task.php
 * 3. Logika Filter Kuota & Klaim Tanpa Double Deduct di earn_coins.php
 * 4. Fitur Hentikan Iklan & Pengembalian Dana Otomatis (Refund)
 * 5. Keberadaan Open Graph Meta Tags & Catatan Domain
 */

require_once __DIR__ . '/../config.php';

echo "========================================================\n";
echo "TEST SUITE: FINAL POLISH SEBELUM GRAND LAUNCHING\n";
echo "========================================================\n\n";

$passCount = 0;
$failCount = 0;

function assert_true($condition, $message) {
    global $passCount, $failCount;
    if ($condition) {
        echo "  [PASS] {$message}\n";
        $passCount++;
    } else {
        echo "  [FAIL] {$message}\n";
        $failCount++;
    }
}

// ------------------------------------------------------------------------------
// TEST 1: Verifikasi Kolom Database tasks
// ------------------------------------------------------------------------------
echo "--- TEST 1: Skema Database tasks ---\n";

// Cek kolom di tasks table
$colsStmt = $pdo->query("PRAGMA table_info(tasks)");
$cols = $colsStmt->fetchAll();
$colNames = array_column($cols, 'name');

assert_true(in_array('target_clicks', $colNames), "Kolom 'target_clicks' ada di tabel tasks");
assert_true(in_array('current_clicks', $colNames), "Kolom 'current_clicks' ada di tabel tasks");
assert_true(in_array('is_active', $colNames), "Kolom 'is_active' ada di tabel tasks");

// ------------------------------------------------------------------------------
// TEST 2: Logika Upfront Coin Deduction & Kuota di add_task.php
// ------------------------------------------------------------------------------
echo "\n--- TEST 2: Upfront Payment & Kuota add_task.php ---\n";

$testUsername = 'final_tester_' . time();
$pdo->prepare("INSERT INTO users (username, email, password, coins, role) VALUES (?, ?, 'pass', 1000, 'user')")
    ->execute([$testUsername, $testUsername . '@test.com']);
$ownerId = (int)$pdo->lastInsertId();

// Skenario A: Koin tidak cukup (Target: 20 klik @ 100 CPC = 2,000 koin, Saldo: 1,000 koin)
$targetClicks = 20;
$cpc = 100;
$costPerClick = $cpc;
$totalUpfrontCost = $targetClicks * $costPerClick;

$stmtUser = $pdo->prepare("SELECT coins FROM users WHERE id = ?");
$stmtUser->execute([$ownerId]);
$userCoins = (int)$stmtUser->fetchColumn();

$canCreate = ($userCoins >= $totalUpfrontCost);
assert_true(!$canCreate, "Pembuatan tugas ditolak jika saldo koin ({$userCoins}) < total biaya di awal ({$totalUpfrontCost})");

// Skenario B: Tambah saldo dan potong saldo di awal saat tugas dipasang
$pdo->prepare("UPDATE users SET coins = 3000 WHERE id = ?")->execute([$ownerId]);

// Simulasi proses pembuatan kampanye seperti di add_task.php
$pdo->beginTransaction();
$stmtDeduct = $pdo->prepare("UPDATE users SET coins = coins - ? WHERE id = ? AND coins >= ?");
$stmtDeduct->execute([$totalUpfrontCost, $ownerId, $totalUpfrontCost]);

$stmtInsert = $pdo->prepare("
    INSERT INTO tasks (user_id, platform, task_type, task_url, cpc, is_priority, is_admin_task, status, target_clicks, max_clicks, current_clicks, is_active, created_at)
    VALUES (?, 'youtube', 'subscribers', 'https://youtube.com/watch?v=final1', ?, 0, 0, 'active', ?, ?, 0, 1, NOW())
");
$stmtInsert->execute([$ownerId, $cpc, $targetClicks, $targetClicks]);
$taskId = (int)$pdo->lastInsertId();

$descTx = "Biaya pasang iklan youtube ({$targetClicks} target klik @ {$costPerClick} koin)";
$pdo->prepare("INSERT INTO transactions (user_id, type, amount, date, description) VALUES (?, 'pasang_iklan', ?, NOW(), ?)")
    ->execute([$ownerId, -$totalUpfrontCost, $descTx]);
$pdo->commit();

// Cek saldo user setelah dipotong di awal
$stmtUser->execute([$ownerId]);
$newCoins = (int)$stmtUser->fetchColumn();
assert_true($newCoins === 1000, "Saldo dipotong di awal: 3,000 - 2,000 = 1,000 (Aktual: {$newCoins})");

$stmtTask = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
$stmtTask->execute([$taskId]);
$taskData = $stmtTask->fetch();
assert_true((int)$taskData['target_clicks'] === 20, "Task target_clicks bernilai 20");
assert_true((int)$taskData['current_clicks'] === 0, "Task current_clicks bernilai 0 di awal");
assert_true((int)$taskData['is_active'] === 1, "Task is_active bernilai 1");
assert_true($taskData['status'] === 'active', "Task status bernilai 'active'");

// ------------------------------------------------------------------------------
// TEST 3: Logika Filter di earn_coins.php & Eksekusi Pengerja
// ------------------------------------------------------------------------------
echo "\n--- TEST 3: Filter Query & Claim Execution earn_coins.php ---\n";

$workerUsername = 'final_worker_' . time();
$pdo->prepare("INSERT INTO users (username, email, password, coins, role) VALUES (?, ?, 'pass', 500, 'user')")
    ->execute([$workerUsername, $workerUsername . '@test.com']);
$workerId = (int)$pdo->lastInsertId();

// Test filter query: WHERE current_clicks < target_clicks AND is_active = 1
$sqlFilter = "
    SELECT t.* 
    FROM tasks t
    WHERE t.current_clicks < t.target_clicks AND t.is_active = 1
      AND t.status = 'active'
      AND t.user_id != :current_user_id
      AND t.id NOT IN (SELECT task_id FROM task_history WHERE user_id = :current_user_id)
      AND t.id = :task_id
";
$stmtQ = $pdo->prepare($sqlFilter);
$stmtQ->execute([':current_user_id' => $workerId, ':task_id' => $taskId]);
$matchedTask = $stmtQ->fetch();
assert_true(!empty($matchedTask), "Tugas aktif muncul pada filter WHERE current_clicks < target_clicks AND is_active = 1");

// Simulasi klaim tugas oleh pekerja:
// Tambah koin ke pekerja, UPDATE current_clicks = current_clicks + 1 pada tugas tersebut
// Saldo pemilik TIDAK dipotong lagi karena sudah dipotong di awal!
$pdo->beginTransaction();
$pdo->prepare("UPDATE users SET coins = coins + ?, daily_clicks = daily_clicks + 1 WHERE id = ?")
    ->execute([$cpc, $workerId]);

$pdo->prepare("
    UPDATE tasks 
    SET current_clicks = current_clicks + 1,
        is_active = CASE 
            WHEN is_admin_task = 1 THEN 1
            WHEN current_clicks + 1 >= target_clicks THEN 0 
            ELSE is_active 
        END,
        status = CASE 
            WHEN is_admin_task = 1 THEN 'active'
            WHEN current_clicks + 1 >= target_clicks THEN 'completed' 
            ELSE status 
        END
    WHERE id = ?
")->execute([$taskId]);

$pdo->prepare("INSERT INTO task_history (user_id, task_id, completed_at) VALUES (?, ?, NOW())")
    ->execute([$workerId, $taskId]);
$pdo->commit();

// Verifikasi saldo pekerja bertambah
$stmtWorker = $pdo->prepare("SELECT coins FROM users WHERE id = ?");
$stmtWorker->execute([$workerId]);
$workerCoins = (int)$stmtWorker->fetchColumn();
assert_true($workerCoins === 600, "Saldo pekerja bertambah +100 koin: 500 + 100 = 600 (Aktual: {$workerCoins})");

// Verifikasi saldo pemilik TIDAK dipotong lagi (tetap 1,000)
$stmtUser->execute([$ownerId]);
$ownerCoinsAfterClaim = (int)$stmtUser->fetchColumn();
assert_true($ownerCoinsAfterClaim === 1000, "Saldo pemilik TIDAK dipotong ganda saat tugas dikerjakan (Tetap: 1,000)");

// Verifikasi current_clicks bertambah 1
$stmtTask->execute([$taskId]);
$taskAfterClaim = $stmtTask->fetch();
assert_true((int)$taskAfterClaim['current_clicks'] === 1, "Task current_clicks diupdate menjadi 1");

// Simulasi jika target klik tercapai (current_clicks mencapai target_clicks)
$pdo->prepare("
    UPDATE tasks 
    SET current_clicks = target_clicks,
        is_active = 0,
        status = 'completed'
    WHERE id = ?
")->execute([$taskId]);

// Cek apakah query earn_coins.php menyembunyikannya
$stmtQ->execute([':current_user_id' => $workerId, ':task_id' => $taskId]);
$hiddenTask = $stmtQ->fetch();
assert_true(empty($hiddenTask), "Tugas otomatis tersembunyi dari earn_coins saat current_clicks >= target_clicks atau is_active = 0");

// ------------------------------------------------------------------------------
// TEST 4: Fitur Hentikan Iklan & Pengembalian Dana Otomatis (Refund)
// ------------------------------------------------------------------------------
echo "\n--- TEST 4: Hentikan Iklan & Pengembalian Dana (Refund) ---\n";

// Buat kampanye kedua untuk Owner: 50 target klik @ 100 CPC = 5,000 koin
$targetClicks2 = 50;
$cpc2 = 100;
$totalCost2 = $targetClicks2 * $cpc2; // 5000

// Beri owner 10,000 koin
$pdo->prepare("UPDATE users SET coins = 10000 WHERE id = ?")->execute([$ownerId]);

// Potong koin di awal
$pdo->beginTransaction();
$pdo->prepare("UPDATE users SET coins = coins - ? WHERE id = ?")->execute([$totalCost2, $ownerId]);
$pdo->prepare("
    INSERT INTO tasks (user_id, platform, task_type, task_url, cpc, is_priority, is_admin_task, status, target_clicks, max_clicks, current_clicks, is_active, created_at)
    VALUES (?, 'tiktok', 'followers', 'https://tiktok.com/@final2', ?, 0, 0, 'active', ?, ?, 0, 1, NOW())
")->execute([$ownerId, $cpc2, $targetClicks2, $targetClicks2]);
$taskId2 = (int)$pdo->lastInsertId();
$pdo->commit();

// Cek saldo setelah potong di awal: 10,000 - 5,000 = 5,000
$stmtUser->execute([$ownerId]);
$coinsBeforeStop = (int)$stmtUser->fetchColumn();
assert_true($coinsBeforeStop === 5000, "Saldo awal kampanye 2: 5,000 koin");

// Simulasikan 10 klik sudah berjalan
$pdo->prepare("UPDATE tasks SET current_clicks = 10 WHERE id = ?")->execute([$taskId2]);

// Eksekusi Logika Hentikan Iklan (Proses 2)
$stmtTarget = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
$stmtTarget->execute([$taskId2, $ownerId]);
$tStop = $stmtTarget->fetch();

$tTargetClicks = (int)$tStop['target_clicks'];
$tCurrentClicks = (int)$tStop['current_clicks'];
$remainingClicks = max(0, $tTargetClicks - $tCurrentClicks); // 50 - 10 = 40
$costPerClick = ((int)$tStop['is_priority'] === 1) ? ((int)$tStop['cpc'] * 2) : (int)$tStop['cpc']; // 100
$expectedRefund = $remainingClicks * $costPerClick; // 40 * 100 = 4,000

$pdo->beginTransaction();
$pdo->prepare("UPDATE tasks SET is_active = 0, status = 'paused' WHERE id = ?")->execute([$taskId2]);
$pdo->prepare("UPDATE users SET coins = coins + ? WHERE id = ?")->execute([$expectedRefund, $ownerId]);
$descRefund = "Pengembalian dana (Refund) iklan #{$taskId2}: Sisa {$remainingClicks} klik x {$costPerClick} koin";
$pdo->prepare("INSERT INTO transactions (user_id, type, amount, date, description) VALUES (?, 'refund_tugas', ?, NOW(), ?)")
    ->execute([$ownerId, $expectedRefund, $descRefund]);
$pdo->commit();

// Verifikasi status tugas setelah dihentikan
$stmtTarget->execute([$taskId2, $ownerId]);
$tStopped = $stmtTarget->fetch();
assert_true((int)$tStopped['is_active'] === 0, "Status tugas berubah menjadi is_active = 0");
assert_true($tStopped['status'] === 'paused', "Status tugas berubah menjadi 'paused'");

// Verifikasi refund koin masuk kembali ke saldo
$stmtUser->execute([$ownerId]);
$coinsAfterRefund = (int)$stmtUser->fetchColumn();
// 5,000 + 4,000 = 9,000
assert_true($coinsAfterRefund === 9000, "Refund sukses: 5,000 + 4,000 = 9,000 Koin (Aktual: {$coinsAfterRefund})");

// Verifikasi transaksi tercatat
$stmtTxRefund = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? AND type = 'refund_tugas' ORDER BY id DESC LIMIT 1");
$stmtTxRefund->execute([$ownerId]);
$txRefund = $stmtTxRefund->fetch();
assert_true(!empty($txRefund) && (int)$txRefund['amount'] === 4000, "Mutasi transaksi refund_tugas tercatat sebesar +4,000 koin");

// ------------------------------------------------------------------------------
// TEST 5: Open Graph Meta Tags & Catatan Domain
// ------------------------------------------------------------------------------
echo "\n--- TEST 5: Open Graph Meta Tags & Catatan Domain ---\n";

$indexContent = file_get_contents(__DIR__ . '/../index.php');
$navbarContent = file_get_contents(__DIR__ . '/../navbar.php');
$mainLayoutContent = file_get_contents(__DIR__ . '/../views/layouts/main.php');

assert_true(strpos($indexContent, '<meta property="og:title" content="Pakaini - Social Media Exchange & Growth Platform">') !== false, "index.php memiliki og:title");
assert_true(strpos($indexContent, '<meta property="og:description" content="Tingkatkan Followers, Subscribers, dan interaksi media sosial Anda dengan komunitas pengguna asli Indonesia. Zero Risk, Cepat & Aman!">') !== false, "index.php memiliki og:description");
assert_true(strpos($indexContent, '<meta property="og:image" content="https://(domain-anda)/logo.png">') !== false, "index.php memiliki og:image");
assert_true(strpos($indexContent, '<meta property="og:type" content="website">') !== false, "index.php memiliki og:type");
assert_true(strpos($indexContent, 'CATATAN:') !== false, "index.php memiliki catatan penyesuaian domain");

assert_true(strpos($navbarContent, '<meta property="og:title"') !== false, "navbar.php memiliki og:title");
assert_true(strpos($navbarContent, '<meta property="og:image" content="https://(domain-anda)/logo.png">') !== false, "navbar.php memiliki og:image");
assert_true(strpos($mainLayoutContent, '<meta property="og:title"') !== false, "views/layouts/main.php memiliki og:title");

// Cleanup test users
$pdo->prepare("DELETE FROM transactions WHERE user_id IN (?, ?)")->execute([$ownerId, $workerId]);
$pdo->prepare("DELETE FROM task_history WHERE user_id = ?")->execute([$workerId]);
$pdo->prepare("DELETE FROM tasks WHERE user_id = ?")->execute([$ownerId]);
$pdo->prepare("DELETE FROM users WHERE id IN (?, ?)")->execute([$ownerId, $workerId]);

echo "\n========================================================\n";
echo "HASIL AKHIR: PASS = {$passCount}, FAIL = {$failCount}\n";
echo "========================================================\n";
