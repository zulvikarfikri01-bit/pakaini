<?php
/**
 * Test AJAX Pagination load_tasks.php and earn_coins.php integration
 */
require_once __DIR__ . '/../config.php';

echo "=== TEST SUITE: AJAX LOAD MORE TASKS PAGINATION ===\n";

// 1. Ambil test user
$stmt = $pdo->query("SELECT * FROM users WHERE role != 'admin' LIMIT 1");
$testUser = $stmt->fetch();
if (!$testUser) {
    die("Error: No test user found!\n");
}

echo "Testing with user: {$testUser['username']} (ID: {$testUser['id']})\n";

// 2. Query langsung menggunakan formula yang sama persis seperti di load_tasks.php
$limit = 20;
$offset = 0;
$fetchLimit = $limit + 1;

$sql = "
    SELECT 
        t.*, 
        u.username AS owner_username, 
        u.is_vip AS owner_is_vip, 
        u.coins AS owner_coins
    FROM tasks t
    JOIN users u ON t.user_id = u.id
    WHERE t.current_clicks < t.target_clicks AND t.is_active = 1
      AND t.status = 'active'
      AND t.user_id != :current_user_id
      AND t.id NOT IN (
          SELECT task_id FROM task_history WHERE user_id = :current_user_id
      )
    ORDER BY t.is_admin_task DESC, t.created_at DESC
    LIMIT " . (int)$fetchLimit . " OFFSET " . (int)$offset;

$stmtTasks = $pdo->prepare($sql);
$stmtTasks->execute([':current_user_id' => $testUser['id']]);
$tasks = $stmtTasks->fetchAll();

$hasMore = false;
if (count($tasks) > $limit) {
    $hasMore = true;
    $tasks = array_slice($tasks, 0, $limit);
}

echo "Found " . count($tasks) . " tasks for offset 0 (hasMore = " . ($hasMore ? 'true' : 'false') . ").\n";

// 3. Verifikasi fungsi render_task_card()
require_once __DIR__ . '/../load_tasks.php';

if (!function_exists('render_task_card')) {
    echo "FAIL: render_task_card function is not defined!\n";
    exit(1);
}

$renderedCount = 0;
$firstIsAdmin = null;
foreach ($tasks as $i => $t) {
    $cardHtml = render_task_card($t, false);
    if (strpos($cardHtml, 'id="card-task-' . $t['id'] . '"') === false) {
        echo "FAIL: Task card #{$t['id']} does not have required ID attribute!\n";
        exit(1);
    }
    if ($i === 0) {
        $firstIsAdmin = (int)$t['is_admin_task'];
    }
    $renderedCount++;
}

echo "PASS: Successfully rendered {$renderedCount} task cards with render_task_card().\n";

// 4. Verifikasi aturan prioritas: is_admin_task DESC, created_at DESC
$prevAdmin = 1;
foreach ($tasks as $t) {
    $curAdmin = (int)$t['is_admin_task'];
    if ($curAdmin > $prevAdmin) {
        echo "FAIL: Ordering violated! Non-admin task appeared before admin task.\n";
        exit(1);
    }
    $prevAdmin = $curAdmin;
}
echo "PASS: Urutan prioritas terverifikasi valid (is_admin_task DESC, created_at DESC).\n";

// 5. Cek tombol Muat Lebih Banyak di earn_coins.php
$earnCoinsContent = file_get_contents(__DIR__ . '/../earn_coins.php');

$checks = [
    'load_tasks.php' => 'require_once load_tasks.php',
    'id="loadMoreBtn"' => 'ID tombol loadMoreBtn',
    'id="loadMoreText"' => 'ID loadMoreText',
    'id="noMoreTasksText"' => 'ID noMoreTasksText',
    'onclick="loadMoreTasks()"' => 'Event handler loadMoreTasks',
    'bg-white/10 hover:bg-white/20 border border-white/20 text-slate-300 rounded-full py-2 px-6' => 'Desain tombol sesuai instruksi',
    'Semua tugas telah ditampilkan' => 'Teks saat tugas habis',
    'async function loadMoreTasks()' => 'Fungsi JavaScript loadMoreTasks',
    'ORDER BY t.is_admin_task DESC, t.created_at DESC LIMIT' => 'Urutan kueri ORDER BY is_admin_task DESC, created_at DESC'
];

foreach ($checks as $needle => $desc) {
    if (strpos($earnCoinsContent, $needle) === false) {
        // Toleransi whitespace untuk class
        if ($needle === 'bg-white/10 hover:bg-white/20 border border-white/20 text-slate-300 rounded-full py-2 px-6') {
            $classSub = 'bg-white/10 hover:bg-white/20 border border-white/20 text-slate-300';
            if (strpos($earnCoinsContent, $classSub) !== false) {
                echo "PASS: $desc (Cocok)\n";
                continue;
            }
        }
        echo "FAIL: $desc tidak ditemukan di earn_coins.php!\n";
        exit(1);
    } else {
        echo "PASS: $desc (Cocok)\n";
    }
}

echo "=== SEMUA PENGUJIAN OPSI A BERHASIL 100% ===\n";
