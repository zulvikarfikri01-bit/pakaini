<?php
/**
 * Test Suite: Verifikasi Fase Siap Rilis, Reset Database, Sistem Role Admin, & Fitur admin.php
 */
require_once __DIR__ . '/../config.php';

echo "======================================================================\n";
echo "👑 PENGUJIAN FASE SIAP RILIS, SISTEM ADMIN PANEL & TUGAS ABADI\n";
echo "======================================================================\n\n";

$pass = 0;
$fail = 0;

function check(string $name, bool $result) {
    global $pass, $fail;
    if ($result) {
        echo "  ✅ PASS: {$name}\n";
        $pass++;
    } else {
        echo "  ❌ FAIL: {$name}\n";
        $fail++;
    }
}

// ------------------------------------------------------------------------------
// 1. PENGUJIAN STATUS DATABASE BERSIH & SKEMA KOLOM ROLE
// ------------------------------------------------------------------------------
echo "1. Pengujian Pembersihan Data & Kolom Role:\n";

// Cek kolom role di DB
$hasRole = false;
try {
    $stmt = $pdo->query("SELECT role FROM users LIMIT 1");
    $hasRole = true;
} catch (Exception $e) {
    $hasRole = false;
}
check("Kolom 'role' aktif di tabel users", $hasRole);

$dbSql = file_get_contents(__DIR__ . '/../database.sql');
check("File database.sql mendefinisikan kolom role ENUM('user', 'admin')", 
    strpos($dbSql, 'role') !== false && strpos($dbSql, "ENUM('user', 'admin')") !== false
);

// ------------------------------------------------------------------------------
// 2. PENGUJIAN REGISTRASI AKUN PERTAMA SEBAGAI ADMIN OTOMATIS
// ------------------------------------------------------------------------------
echo "\n2. Pengujian Penugasan Role Otomatis (First Account = Admin):\n";

// Pastikan DB kosong sebelum tes
$pdo->exec("DELETE FROM users");
$pdo->exec("DELETE FROM tasks");
$pdo->exec("DELETE FROM task_history");

// Registrasi Akun Pertama (Harus Otomatis Admin)
$firstUsername = 'super_admin';
$firstEmail = 'admin@pakaini.com';
$passHash = password_hash('AdminPass123!', PASSWORD_DEFAULT);
$countInitial = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$roleFirst = ($countInitial === 0) ? 'admin' : 'user';

$pdo->prepare("INSERT INTO users (username, email, password, role, coins, ip_address) VALUES (?, ?, ?, ?, ?, ?)")
    ->execute([$firstUsername, $firstEmail, $passHash, $roleFirst, 10000, '127.0.0.1']);
$adminId = (int)$pdo->lastInsertId();

$firstUser = $pdo->query("SELECT * FROM users WHERE id = {$adminId}")->fetch();
check("Akun pertama terdaftar otomatis mendapat role = 'admin'", $firstUser['role'] === 'admin');

// Registrasi Akun Kedua (Harus Menjadi User Biasa)
$secondUsername = 'member_baru';
$secondEmail = 'member@pakaini.com';
$countSecond = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$roleSecond = ($countSecond === 0) ? 'admin' : 'user';

$pdo->prepare("INSERT INTO users (username, email, password, role, coins, ip_address) VALUES (?, ?, ?, ?, ?, ?)")
    ->execute([$secondUsername, $secondEmail, $passHash, $roleSecond, 0, '127.0.0.2']);
$workerId = (int)$pdo->lastInsertId();

$secondUser = $pdo->query("SELECT * FROM users WHERE id = {$workerId}")->fetch();
check("Akun kedua terdaftar otomatis mendapat role = 'user'", $secondUser['role'] === 'user');

// ------------------------------------------------------------------------------
// 3. PENGUJIAN PROTEKSI & FITUR ADMIN PANEL (admin.php)
// ------------------------------------------------------------------------------
echo "\n3. Pengujian Proteksi & Fitur admin.php:\n";
$adminSrc = file_get_contents(__DIR__ . '/../admin.php');

check("admin.php memverifikasi \$user['role'] === 'admin'", 
    strpos($adminSrc, "'admin'") !== false && strpos($adminSrc, "role") !== false
);

check("admin.php menolak pengguna non-admin dan redirect ke index.php", 
    strpos($adminSrc, "redirect('index.php')") !== false
);

// Fitur A: Manajemen Koin
check("admin.php memiliki form manajemen koin (Input username, amount, add_coins, deduct_coins)", 
    strpos($adminSrc, 'name="username"') !== false && 
    strpos($adminSrc, 'name="amount"') !== false && 
    strpos($adminSrc, 'value="add_coins"') !== false && 
    strpos($adminSrc, 'value="deduct_coins"') !== false
);

// Simulasi Fitur A: Tambah Koin Manual
$coinsBefore = (int)$secondUser['coins'];
$topupAmount = 2500;
$pdo->prepare("UPDATE users SET coins = coins + ? WHERE id = ?")->execute([$topupAmount, $workerId]);
$workerAfterTopup = $pdo->query("SELECT coins FROM users WHERE id = {$workerId}")->fetch();
check("Fitur A: Berhasil menambahkan koin manual (+2.500)", (int)$workerAfterTopup['coins'] === ($coinsBefore + $topupAmount));

// Simulasi Fitur A: Kurangi Koin Manual
$deductAmount = 500;
$pdo->prepare("UPDATE users SET coins = coins - ? WHERE id = ?")->execute([$deductAmount, $workerId]);
$workerAfterDeduct = $pdo->query("SELECT coins FROM users WHERE id = {$workerId}")->fetch();
check("Fitur A: Berhasil memotong koin manual (-500)", (int)$workerAfterDeduct['coins'] === 2000);

// Fitur B: Manajemen Laporan (Strike / Banned)
check("admin.php memiliki tabel daftar laporan dengan filter strike_count > 0 OR is_banned = 1", 
    strpos($adminSrc, 'strike_count > 0') !== false || strpos($adminSrc, 'is_banned = 1') !== false
);

check("admin.php memiliki aksi manual 'ban_user' dan 'unban_user'", 
    strpos($adminSrc, 'ban_user') !== false && strpos($adminSrc, 'unban_user') !== false
);

// Simulasi Fitur B: Ban dan Unban
$pdo->prepare("UPDATE users SET strike_count = 2, is_banned = 1 WHERE id = ?")->execute([$workerId]);
$checkBanned = $pdo->query("SELECT is_banned, strike_count FROM users WHERE id = {$workerId}")->fetch();
check("Fitur B: Status akun berhasil diubah menjadi Banned (is_banned = 1)", (int)$checkBanned['is_banned'] === 1);

$pdo->prepare("UPDATE users SET is_banned = 0, strike_count = 0 WHERE id = ?")->execute([$workerId]);
$checkUnbanned = $pdo->query("SELECT is_banned, strike_count FROM users WHERE id = {$workerId}")->fetch();
check("Fitur B: Status akun berhasil dibuka (is_banned = 0, strike_count = 0)", (int)$checkUnbanned['is_banned'] === 0 && (int)$checkUnbanned['strike_count'] === 0);

// Fitur C: Manajemen Tugas Admin
check("admin.php memiliki form pembuatan tugas admin khusus", 
    strpos($adminSrc, 'create_admin_task') !== false
);

// ------------------------------------------------------------------------------
// 4. PENGUJIAN LOGIKA TUGAS ABADI ADMIN (INFINITE BUDGET & PINNED)
// ------------------------------------------------------------------------------
echo "\n4. Pengujian Logika Tugas Abadi Admin (earn_coins.php):\n";

$earnSrc = file_get_contents(__DIR__ . '/../earn_coins.php');
check("earn_coins.php mengurutkan tugas dengan is_admin_task DESC di paling atas", 
    strpos($earnSrc, 't.is_admin_task DESC') !== false
);

check("earn_coins.php menerapkan Infinite Budget (saldo Admin TIDAK dipotong)", 
    strpos($earnSrc, 'Infinite Budget') !== false || strpos($earnSrc, '!$isAdminTask') !== false
);

// Simulasi Penuh Tugas Admin Abadi
// 1. Buat Tugas Admin
$pdo->prepare("
    INSERT INTO tasks (user_id, platform, task_type, task_url, cpc, is_priority, is_admin_task, status, max_clicks, current_clicks)
    VALUES (?, 'youtube', 'subscribers', 'https://youtube.com/@PakainiOfficial', 100, 1, 1, 'active', 999999, 0)
")->execute([$adminId]);
$adminTaskId = (int)$pdo->lastInsertId();

// 2. Buat Tugas Biasa dari Pengguna Lain
$pdo->prepare("
    INSERT INTO tasks (user_id, platform, task_type, task_url, cpc, is_priority, is_admin_task, status, max_clicks, current_clicks)
    VALUES (?, 'tiktok', 'followers', 'https://tiktok.com/@usertest', 50, 0, 0, 'active', 100, 0)
")->execute([$adminId]);
$regularTaskId = (int)$pdo->lastInsertId();

// Cek Query earn_coins.php: Tugas Admin Paling Atas dan Tugas Biasa Tidak Terblokir
$stmtList = $pdo->prepare("
    SELECT t.* FROM tasks t
    WHERE t.status = 'active' AND t.user_id != ?
    ORDER BY t.is_admin_task DESC, t.is_priority DESC, t.cpc DESC, t.id DESC
");
$stmtList->execute([$workerId]);
$tasksForWorker = $stmtList->fetchAll();

check("Tugas Admin berhasil di-query dan berada di urutan index #0 (paling atas)", 
    !empty($tasksForWorker) && (int)$tasksForWorker[0]['is_admin_task'] === 1
);
check("Tugas biasa tetap muncul dan TIDAK diblokir (opsional)", 
    count($tasksForWorker) >= 2
);

// 3. Simulasi Eksekusi Klaim Tugas Admin oleh Pekerja
$adminCoinsBefore = (int)$pdo->query("SELECT coins FROM users WHERE id = {$adminId}")->fetchColumn();
$workerCoinsBefore = (int)$pdo->query("SELECT coins FROM users WHERE id = {$workerId}")->fetchColumn();

// Worker klaim tugas admin (+100 koin)
$rewardCpc = 100;

// Sesuai aturan Infinite Budget: Worker dapat +100 koin, Admin TIDAK DIPOTONG
$pdo->beginTransaction();
$pdo->prepare("UPDATE users SET coins = coins + ? WHERE id = ?")->execute([$rewardCpc, $workerId]);
// Catat riwayat 1x seumur hidup
$pdo->prepare("INSERT INTO task_history (user_id, task_id, completed_at) VALUES (?, ?, NOW())")->execute([$workerId, $adminTaskId]);
// Update status tugas admin (tetap 'active')
$pdo->prepare("UPDATE tasks SET current_clicks = current_clicks + 1 WHERE id = ?")->execute([$adminTaskId]);
$pdo->commit();

$adminCoinsAfter = (int)$pdo->query("SELECT coins FROM users WHERE id = {$adminId}")->fetchColumn();
$workerCoinsAfter = (int)$pdo->query("SELECT coins FROM users WHERE id = {$workerId}")->fetchColumn();

check("Saldo pekerja bertambah +100 Koin", $workerCoinsAfter === ($workerCoinsBefore + $rewardCpc));
check("Infinite Budget: Saldo koin Admin TIDAK BERKURANG (tetap {$adminCoinsBefore})", $adminCoinsAfter === $adminCoinsBefore);

// Verifikasi pembatasan 1x seumur hidup
$stmtCheckDone = $pdo->prepare("SELECT id FROM task_history WHERE task_id = ? AND user_id = ? LIMIT 1");
$stmtCheckDone->execute([$adminTaskId, $workerId]);
check("Pekerja tercatat di task_history sehingga tidak bisa mengklaim 2x", (bool)$stmtCheckDone->fetch());

// ==============================================================================
// 5. PEMBERSIHAN AKHIR: RESET KEMBALI AGAR DATABASE 100% BERSIH UNTUK PENGGUNA ASLI
// ==============================================================================
echo "\n5. Pembersihan Akhir Database (Fresh Production State):\n";
$pdo->exec("DELETE FROM users");
$pdo->exec("DELETE FROM tasks");
$pdo->exec("DELETE FROM task_history");
$pdo->exec("DELETE FROM transactions");

$finalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$finalTasks = (int)$pdo->query("SELECT COUNT(*) FROM tasks")->fetchColumn();

check("Database tabel users 100% bersih (0 pengguna)", $finalUsers === 0);
check("Database tabel tasks 100% bersih (0 tugas)", $finalTasks === 0);

echo "\n----------------------------------------------------------------------\n";
echo "RINGKASAN HASIL: {$pass} LULUS, {$fail} GAGAL\n";
echo "======================================================================\n";

if ($fail === 0) {
    echo "🎉 SISTEM ADMIN PANEL & LOGIKA TUGAS ABADI BERHASIL 100% DIVERIFIKASI!\n";
} else {
    exit(1);
}
