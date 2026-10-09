<?php
/**
 * PAKAINI - Social Media Exchange Platform
 * Dedicated AJAX Endpoint Klaim Koin (claim.php)
 * 
 * Fitur Keamanan & Bisnis:
 * 1. Anti SQL Injection (Prepared Statements 100%)
 * 2. Validasi Timer Backend (Anti Inspect Element / Auto-Clicker)
 * 3. Anti Double-Submit & Anti-Toggle Effect (task_history & task_completions)
 * 4. Pemotongan Siluman (Silent 20% Cut: Hadiah Bersih = floor(CPC * 0.8) tanpa kata pajak)
 * 5. Single Wallet Pure Coins: Langsung menambah kolom coins (INT UNSIGNED)
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/config.php';

// 1. Validasi Autentikasi Login
if (!is_logged_in()) {
    echo json_encode([
        'success' => false,
        'message' => 'Sesi login telah berakhir. Silakan login kembali.'
    ]);
    exit;
}

$user = current_user($pdo);
if (!$user) {
    echo json_encode([
        'success' => false,
        'message' => 'Data pengguna tidak ditemukan.'
    ]);
    exit;
}

// Cek apakah akun diblokir
if ((int)($user['is_banned'] ?? 0) === 1 || (isset($user['is_active']) && (int)$user['is_active'] === 0)) {
    echo json_encode([
        'success' => false,
        'message' => '⛔ AKUN DIBLOKIR: Akses Anda telah dinonaktifkan oleh Administrator.'
    ]);
    exit;
}

// Hanya menerima request POST
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Metode permintaan tidak valid.'
    ]);
    exit;
}

$taskId    = (int)($_POST['task_id'] ?? 0);
$spentTime = (int)($_POST['spent_time'] ?? 0);

if ($taskId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'ID tugas tidak valid.'
    ]);
    exit;
}

$isVip = ((int)($user['is_vip'] ?? 0) === 1);
$dailyClicks = (int)($user['daily_clicks'] ?? 0);

// 2. Cek Kuota Harian Member Biasa (50 Klik/hari)
if (!$isVip && $dailyClicks >= REGULAR_DAILY_MAX_CLICKS) {
    echo json_encode([
        'success' => false,
        'message' => 'Anda telah mencapai batas harian 50 klik untuk member biasa! Upgrade ke VIP Sultan untuk akses klik tanpa batas.'
    ]);
    exit;
}

// 3. Ambil data tugas & pemilik tugas (Prepared Statement)
$stmtT = $pdo->prepare("
    SELECT t.*, u.username AS owner_name, u.coins AS owner_coins, u.is_vip AS owner_is_vip
    FROM tasks t
    JOIN users u ON t.user_id = u.id
    WHERE t.id = ? AND t.status = 'active' AND t.is_active = 1
    LIMIT 1
");
$stmtT->execute([$taskId]);
$task = $stmtT->fetch();

if (!$task) {
    echo json_encode([
        'success' => false,
        'message' => 'Tugas tidak ditemukan atau kampanye telah selesai/dihentikan.'
    ]);
    exit;
}

// 4. Validasi Timer Backend (Anti Inspect Element / Auto-Clicker)
$isYouTube = (strtolower($task['platform']) === 'youtube');
$requiredSeconds = $isYouTube ? 30 : 15;

$taskStartTime  = (int)($_SESSION['task_start_time'] ?? 0);
$sessionTaskId  = (int)($_SESSION['current_task_id'] ?? 0);
$backendElapsed = ($taskStartTime > 0) ? (time() - $taskStartTime) : 0;

if ($taskStartTime <= 0 || $sessionTaskId !== $taskId || $backendElapsed < $requiredSeconds) {
    echo json_encode([
        'success' => false,
        'message' => 'Manipulasi waktu terdeteksi'
    ]);
    exit;
}

$minAllowedSpent = $requiredSeconds - 1;
if ($spentTime < $minAllowedSpent) {
    echo json_encode([
        'success' => false,
        'message' => 'Manipulasi waktu terdeteksi'
    ]);
    exit;
}

// 5. Validasi Kepemilikan: Tidak boleh mengerjakan tugas sendiri
if ((int)$task['user_id'] === (int)$user['id']) {
    echo json_encode([
        'success' => false,
        'message' => 'Anda tidak dapat mengerjakan kampanye tugas milik sendiri.'
    ]);
    exit;
}

// 6. Cek apakah sudah pernah dikerjakan oleh user ini (Anti-Double Claim)
$stmtCheckDone = $pdo->prepare("SELECT id FROM task_history WHERE task_id = ? AND user_id = ? LIMIT 1");
$stmtCheckDone->execute([$taskId, $user['id']]);
if ($stmtCheckDone->fetch()) {
    echo json_encode([
        'success' => false,
        'message' => 'Anda sudah pernah mengerjakan tugas tautan ini sebelumnya. Tugas hanya dapat dikerjakan 1 kali.'
    ]);
    exit;
}

// 7. Cek Kuota Target Klik
$targetClicks = (int)($task['target_clicks'] ?? $task['max_clicks'] ?? 100);
$currentClicks = (int)($task['current_clicks'] ?? 0);
$isAdminTask = ((int)($task['is_admin_task'] ?? 0) === 1);

if (!$isAdminTask && $currentClicks >= $targetClicks) {
    $pdo->prepare("UPDATE tasks SET is_active = 0, status = 'completed' WHERE id = ?")->execute([$taskId]);
    echo json_encode([
        'success' => false,
        'message' => 'Target kuota klik untuk kampanye tugas ini telah terpenuhi.'
    ]);
    exit;
}

// 8. Logika Pemotongan Senyap di Backend:
// Hadiah bersih pekerja = floor(CPC * 0.8) (Sistem mengambil 20% secara senyap tanpa kata pajak)
$cpc = (int)$task['cpc'];
$hadiah_bersih = (int)floor($cpc * 0.8);
$rewardReceived = $hadiah_bersih;
$potonganSenyap = (int)($cpc - $hadiah_bersih);

try {
    $pdo->beginTransaction();

    // A. Tambah saldo koin pengklaim & perbarui daily_clicks +1 (Tabel users: coins INT UNSIGNED)
    $stmtAdd = $pdo->prepare("UPDATE users SET coins = coins + ?, daily_clicks = daily_clicks + 1 WHERE id = ?");
    $stmtAdd->execute([$rewardReceived, $user['id']]);

    // B. UPDATE current_clicks = current_clicks + 1 pada tugas
    $stmtClicks = $pdo->prepare("
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
    ");
    $stmtClicks->execute([$taskId]);

    // C. Catat ke task_history (Anti-Double Claim Permanen)
    $stmtHistory = $pdo->prepare("
        INSERT INTO task_history (user_id, task_id, completed_at)
        VALUES (?, ?, NOW())
    ");
    $stmtHistory->execute([$user['id'], $taskId]);

    // Catat ke task_completions
    $stmtComp = $pdo->prepare("
        INSERT INTO task_completions (task_id, user_id, reward_received, tax_burned, completed_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmtComp->execute([$taskId, $user['id'], $rewardReceived, $potonganSenyap]);

    // D. Catat mutasi kas di tabel transactions
    $descClaimer = "Hadiah tugas {$task['platform']} @{$task['owner_name']} (+{$rewardReceived} Koin)";
    $stmtTx1 = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, date, description) VALUES (?, 'klaim_tugas', ?, NOW(), ?)");
    $stmtTx1->execute([$user['id'], $rewardReceived, $descClaimer]);

    $pdo->commit();

    // Bersihkan sesi timer tugas
    unset($_SESSION['task_start_time']);
    unset($_SESSION['current_task_id']);

    // Ambil saldo terbaru pengklaim
    $stmtBal = $pdo->prepare("SELECT coins, daily_clicks FROM users WHERE id = ?");
    $stmtBal->execute([$user['id']]);
    $updatedUser = $stmtBal->fetch();

    echo json_encode([
        'success'     => true,
        'message'     => "Sukses! Hadiah: +{$rewardReceived} Koin berhasil masuk ke saldo akun Anda.",
        'reward'      => $rewardReceived,
        'new_balance' => (int)$updatedUser['coins'],
        'new_clicks'  => (int)$updatedUser['daily_clicks'],
        'is_vip'      => $isVip
    ]);
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan sistem saat pemrosesan klaim: ' . $e->getMessage()
    ]);
    exit;
}
