<?php
/**
 * PAKAINI - Social Media Exchange Platform
 * Zona Cari Koin (earn_coins.php)
 * Logika:
 * - Query SQL: Prioritas tugas milik VIP di urutan paling atas (ORDER BY is_vip DESC, cpc DESC).
 * - Batas Klik Harian: Member reguler ditolak jika sudah 50 klik/hari, VIP tanpa batas.
 * - Anti-Cheat JS Timer: Link dibuka di tab baru (target="_blank"), timer 15 detik muncul, tombol klaim aktif setelah 15 detik.
 * - Logika Koin Murni: Imbalan koin 100% masuk ke Saldo Koin Tunggal (coins).
 * - Semua dicatat di tabel transactions.
 */

require_once __DIR__ . '/config.php';

// Wajib Login
$user = require_login($pdo);
$pageTitle = 'Zona Cari Koin';

$isVip = ((int)$user['is_vip'] === 1);
$dailyClicks = (int)$user['daily_clicks'];
$dailyLimit = $isVip ? 999999 : REGULAR_DAILY_MAX_CLICKS;
$dailyReached = (!$isVip && $dailyClicks >= REGULAR_DAILY_MAX_CLICKS);

// ==============================================================================
// 1. ENDPOINT INISIALISASI MULAI TUGAS (ATURAN 1: VALIDASI TIMER BACKEND)
// ==============================================================================
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'start_task') {
    header('Content-Type: application/json; charset=utf-8');
    $taskId = (int)($_POST['task_id'] ?? 0);

    $_SESSION['task_start_time'] = time();
    $_SESSION['current_task_id'] = $taskId;

    echo json_encode([
        'success'    => true,
        'task_id'    => $taskId,
        'start_time' => $_SESSION['task_start_time']
    ]);
    exit;
}

// ==============================================================================
// 2. ENDPOINT KLAIM KOIN (AJAX / POST REQUEST)
// ==============================================================================
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'claim_reward') {
    header('Content-Type: application/json; charset=utf-8');

    $taskId     = (int)($_POST['task_id'] ?? 0);
    $spentTime  = (int)($_POST['spent_time'] ?? 0); // Detik dari client timer

    // 1. Cek Kuota Harian Member Biasa
    if (!$isVip && $dailyClicks >= REGULAR_DAILY_MAX_CLICKS) {
        echo json_encode([
            'success' => false,
            'message' => 'Anda telah mencapai batas harian 50 klik untuk member biasa! Upgrade ke VIP untuk klik tanpa batas.'
        ]);
        exit;
    }

    // 2. Ambil data tugas & pemilik tugas terlebih dahulu untuk cek platform
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

    // 3. Aturan Keamanan 1: Validasi Timer Backend (Anti Inspect Element / Auto-Clicker)
    // Backend PHP WAJIB memeriksa: time() - $_SESSION['task_start_time'].
    // Jika waktunya kurang dari batas minimal (misal kurang dari 15 atau 30 detik),
    // TOLAK permintaan tersebut dan berikan respon error "Manipulasi waktu terdeteksi".
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

    // 4. Validasi Pemilik: Tidak boleh mengerjakan tugas sendiri
    if ((int)$task['user_id'] === (int)$user['id']) {
        echo json_encode([
            'success' => false,
            'message' => 'Anda tidak dapat mengerjakan kampanye tugas milik sendiri.'
        ]);
        exit;
    }

    // 5. Cek apakah sudah pernah dikerjakan oleh user ini (Anti-Toggle Effect / Anti-Double Claim)
    $stmtCheckDone = $pdo->prepare("SELECT id FROM task_history WHERE task_id = ? AND user_id = ? LIMIT 1");
    $stmtCheckDone->execute([$taskId, $user['id']]);
    if ($stmtCheckDone->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Anda sudah pernah mengerjakan tugas tautan ini sebelumnya. Tugas hanya dapat dikerjakan 1 kali untuk mencegah tindakan ganda.'
        ]);
        exit;
    }

    // 6. Cek Kuota Target Klik (Proses 1)
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

    // 7. Logika Pemotongan Senyap di Backend (earn_coins.php)
    // Sistem mengambil 20% secara senyap, pekerja menerima $hadiah_bersih = floor($task['cpc'] * 0.8)
    $hadiah_bersih = (int)floor($task['cpc'] * 0.8);
    $rewardReceived = $hadiah_bersih;
    $potonganSenyap = (int)($task['cpc'] - $hadiah_bersih);

    try {
        $pdo->beginTransaction();

        // A. Tambah saldo koin pengklaim & tambahkan daily_clicks +1 (1 Dompet Tunggal: coins)
        $stmtAdd = $pdo->prepare("UPDATE users SET coins = coins + ?, daily_clicks = daily_clicks + 1 WHERE id = ?");
        $stmtAdd->execute([$rewardReceived, $user['id']]);

        // B. UPDATE current_clicks = current_clicks + 1 pada tugas tersebut (Proses 1)
        // Jika target tercapai (current_clicks + 1 >= target_clicks), otomatis nonaktifkan (is_active = 0, status = 'completed')
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

        // C. Catat riwayat ke tabel task_history (Anti-Toggle Effect / Anti-Double Claim)
        $stmtHistory = $pdo->prepare("
            INSERT INTO task_history (user_id, task_id, completed_at)
            VALUES (?, ?, NOW())
        ");
        $stmtHistory->execute([$user['id'], $taskId]);

        // Catat ke tabel task_completions
        $stmtComp = $pdo->prepare("
            INSERT INTO task_completions (task_id, user_id, reward_received, tax_burned, completed_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmtComp->execute([$taskId, $user['id'], $rewardReceived, $potonganSenyap]);

        // D. Catat transaksi untuk pengklaim (masuk ke Saldo Koin)
        $descClaimer = "Hadiah tugas {$task['platform']} @{$task['owner_name']} (+{$rewardReceived} Koin)";
        $stmtTx1 = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, date, description) VALUES (?, 'klaim_tugas', ?, NOW(), ?)");
        $stmtTx1->execute([$user['id'], $rewardReceived, $descClaimer]);

        $pdo->commit();

        // Bersihkan sesi timer tugas agar tidak dapat digunakan ulang (Anti-Replay)
        unset($_SESSION['task_start_time']);
        unset($_SESSION['current_task_id']);

        // Ambil saldo terbaru pengklaim
        $stmtBal = $pdo->prepare("SELECT coins, daily_clicks FROM users WHERE id = ?");
        $stmtBal->execute([$user['id']]);
        $updatedUser = $stmtBal->fetch();

        echo json_encode([
            'success'        => true,
            'message'        => "Sukses! Hadiah: +{$rewardReceived} Koin berhasil masuk ke saldo akun Anda.",
            'reward'         => $rewardReceived,
            'new_balance'    => (int)$updatedUser['coins'],
            'new_clicks'     => (int)$updatedUser['daily_clicks'],
            'is_vip'         => $isVip
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
}

// ==============================================================================
// 2. QUERY DAFTAR TUGAS & LOGIKA MONOPOLI TUGAS WAJIB ADMIN
// Aturan Bisnis 4: User baru TIDAK BISA melihat tugas dari user lain sebelum
// ==============================================================================
// LOGIKA QUERY TUGAS:
// Sesuai Aturan 3: Tugas dengan is_admin_task = 1 SELALU berada di urutan paling atas (Pinned),
// TETAPI bersifat opsional (TIDAK memblokir tugas lain).
// Infinite Budget: Tugas admin tidak memerlukan saldo pemilik dan saldo admin tidak berkurang.
// ==============================================================================
$platformFilter = clean($_GET['platform'] ?? '');

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
";

$params = [':current_user_id' => $user['id']];

if (!empty($platformFilter)) {
    $sql .= " AND t.platform = :platform ";
    $params[':platform'] = $platformFilter;
}

require_once __DIR__ . '/load_tasks.php';

// Urutan prioritas kueri sesuai instruksi:
// ORDER BY is_admin_task DESC, created_at DESC LIMIT 20 OFFSET 0
$initialLimit = 20;
$fetchLimit = $initialLimit + 1;
$sql .= " ORDER BY t.is_admin_task DESC, t.created_at DESC LIMIT " . (int)$fetchLimit . " OFFSET 0 ";

$stmtTasks = $pdo->prepare($sql);
$stmtTasks->execute($params);
$rawTasks = $stmtTasks->fetchAll();

$hasMoreTasks = false;
if (count($rawTasks) > $initialLimit) {
    $hasMoreTasks = true;
    $availableTasks = array_slice($rawTasks, 0, $initialLimit);
} else {
    $availableTasks = $rawTasks;
}

require_once __DIR__ . '/navbar.php';
?>

<!-- ============================================================================== -->
<!-- BANNER ANCAMAN UI: PEMANTAUAN MANUAL & SISTEM STRIKE (Aturan 3)                 -->
<!-- ============================================================================== -->
<div class="mb-6 p-4 sm:p-5 rounded-3xl bg-gradient-to-r from-red-950 via-rose-900 to-red-950 border-2 border-red-500 shadow-2xl shadow-red-950/60 relative overflow-hidden flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div class="flex items-start sm:items-center space-x-3.5">
        <div class="w-12 h-12 rounded-2xl bg-red-600/30 border border-red-500/50 text-red-400 flex items-center justify-center text-2xl shrink-0 animate-pulse">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-red-600 text-white font-black text-[10px] tracking-wider uppercase shadow-md">
                    ⚠️ PENGAWASAN KETAT
                </span>
                <span class="text-xs font-bold text-red-200">Sistem Deteksi Kecurangan Aktif</span>
            </div>
            <p class="text-xs sm:text-sm font-extrabold text-white leading-relaxed">
                PERHATIAN: Pemasang Iklan memantau pekerjaan Anda secara manual! Mengambil koin tanpa melakukan tugas = LAPORAN. 3x Laporan = Akun Diblokir Permanen & Koin Hangus!
            </p>
        </div>
    </div>
</div>

<!-- Header Halaman & Kuota Klik -->
<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl sm:text-2xl font-display font-extrabold text-white flex items-center gap-2">
            <i class="fa-solid fa-hand-holding-dollar text-amber-400"></i>
            <span>Zona Cari Koin Gratis</span>
        </h1>
        <p class="text-xs sm:text-sm text-slate-400 mt-1">
            Kerjakan tugas follow, like, atau subscribe dari member lain untuk mengumpulkan koin dengan cepat.
        </p>
    </div>

    <!-- Status Kuota Harian & VIP Info -->
    <div class="glass-card px-4 py-2.5 rounded-2xl border border-white/[0.08] flex items-center space-x-3 shrink-0">
        <div class="w-9 h-9 rounded-xl <?= $isVip ? 'bg-amber-500/20 text-amber-400' : 'bg-slate-800 text-slate-300' ?> flex items-center justify-center text-base">
            <i class="fa-solid <?= $isVip ? 'fa-crown text-amber-400' : 'fa-mouse-pointer' ?>"></i>
        </div>
        <div>
            <div class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Kuota Klik Hari Ini</div>
            <div class="text-xs font-bold text-white flex items-center gap-1.5" id="userClickTracker">
                <?php if ($isVip): ?>
                    <span class="text-amber-300 font-black">👑 Tanpa Batas (VIP Sultan)</span>
                <?php else: ?>
                    <span class="<?= $dailyReached ? 'text-rose-400 font-extrabold' : 'text-slate-200' ?>">
                        <span id="dailyClicksCount"><?= $dailyClicks ?></span> / <?= REGULAR_DAILY_MAX_CLICKS ?> Klik
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Alert Batas Klik Harian Tercapai (Khusus Member Biasa) -->
<?php if ($dailyReached): ?>
    <div class="mb-6 p-4 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-200 text-xs sm:text-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg">
        <div class="flex items-center space-x-3">
            <i class="fa-solid fa-hand text-rose-400 text-xl"></i>
            <div>
                <strong>Batas Harian 50 Klik Tercapai!</strong>
                <p class="text-xs text-rose-300/80 mt-0.5">Sebagai member reguler, Anda hanya dapat mengerjakan 50 tugas per hari. Kembali lagi besok saat kuota direset, atau upgrade ke VIP Sultan untuk akses klik sepuasnya tanpa batas!</p>
            </div>
        </div>
        <a href="topup.php" class="px-4 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-400 hover:to-yellow-400 text-slate-950 font-black text-xs transition-all shrink-0 text-center shadow-md">
            <i class="fa-solid fa-crown mr-1"></i>Bypass Limit VIP (Rp 30rb)
        </a>
    </div>
<?php endif; ?>

<!-- Info Banner: Sistem 1 Dompet Koin Murni -->
<div class="mb-6 p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 text-xs text-slate-400 flex flex-col sm:flex-row sm:items-center justify-between gap-2 shadow-sm">
    <div class="flex items-center space-x-2.5">
        <i class="fa-solid fa-coins text-amber-400 text-sm"></i>
        <span>
            <strong>Sistem Koin Murni:</strong> 
            Setiap tugas yang Anda selesaikan langsung menambah saldo koin Anda. Koin dapat langsung digunakan untuk memasang tugas promosi Anda sendiri!
        </span>
    </div>
    <a href="topup.php" class="text-amber-400 hover:text-amber-300 font-bold shrink-0 text-right flex items-center gap-1">
        <span>Beli Koin / Top-Up</span>
        <i class="fa-solid fa-arrow-right text-[10px]"></i>
    </a>
</div>

<!-- Banner Monopoli Tugas Wajib Admin -->
<?php if ($isLockedByAdminTask): ?>
    <div class="mb-6 p-4 rounded-3xl bg-[#111111] border border-amber-500/40 text-slate-200 shadow-xl relative overflow-hidden">
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-2xl bg-amber-500/15 text-amber-400 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 font-black text-[10px] uppercase tracking-wider">Tugas Wajib Admin</span>
                    <span class="text-xs font-bold text-amber-300">Selesaikan Dulu Untuk Membuka Tugas Komunitas!</span>
                </div>
                <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                    Sebagai verifikasi akun member, Anda wajib menyelesaikan semua tugas resmi Admin di bawah ini terlebih dahulu. Setelah seluruh tugas wajib selesai, ratusan tugas dari sesama member akan otomatis terbuka!
                </p>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Filter Platform Chips -->
<div class="flex items-center space-x-2 overflow-x-auto pb-3 mb-6 scrollbar-none">
    <a href="earn_coins.php" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 <?= empty($platformFilter) ? 'bg-white/10 text-white border border-white/20' : 'bg-[#141414] border border-white/[0.08] text-slate-400 hover:text-white' ?>">
        <i class="fa-solid fa-layer-group mr-1.5 text-xs"></i>Semua Platform
    </a>
    <a href="earn_coins.php?platform=youtube" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 <?= ($platformFilter === 'youtube') ? 'bg-white/10 text-white border border-white/20' : 'bg-[#141414] border border-white/[0.08] text-slate-400 hover:text-white' ?>">
        <i class="fa-brands fa-youtube mr-1.5 text-xs text-red-400"></i>YouTube
    </a>
    <a href="earn_coins.php?platform=tiktok" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 <?= ($platformFilter === 'tiktok') ? 'bg-white/10 text-white border border-white/20' : 'bg-[#141414] border border-white/[0.08] text-slate-400 hover:text-white' ?>">
        <i class="fa-brands fa-tiktok mr-1.5 text-xs text-pink-400"></i>TikTok
    </a>
    <a href="earn_coins.php?platform=instagram" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 <?= ($platformFilter === 'instagram') ? 'bg-white/10 text-white border border-white/20' : 'bg-[#141414] border border-white/[0.08] text-slate-400 hover:text-white' ?>">
        <i class="fa-brands fa-instagram mr-1.5 text-xs text-purple-400"></i>Instagram
    </a>
    <a href="earn_coins.php?platform=facebook" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all shrink-0 <?= ($platformFilter === 'facebook') ? 'bg-white/10 text-white border border-white/20' : 'bg-[#141414] border border-white/[0.08] text-slate-400 hover:text-white' ?>">
        <i class="fa-brands fa-facebook mr-1.5 text-xs text-blue-400"></i>Facebook
    </a>
</div>

<!-- Daftar Grid Tugas -->
<?php if (empty($availableTasks)): ?>
    <div class="glass-card rounded-3xl p-12 text-center border border-white/[0.08] bg-[#111111]">
        <div class="w-16 h-16 mx-auto rounded-3xl bg-white/[0.04] border border-white/10 flex items-center justify-center text-3xl mb-4 text-slate-500">
            <i class="fa-solid fa-check-double"></i>
        </div>
        <h3 class="font-display font-bold text-lg text-white">Hebat! Semua Tugas Sudah Anda Selesaikan</h3>
        <p class="text-xs text-slate-400 max-w-md mx-auto mt-2 leading-relaxed">
            Tidak ada tugas baru yang tersedia saat ini. Silakan pasang tugas Anda sendiri atau periksa kembali beberapa saat lagi.
        </p>
        <div class="mt-6 flex justify-center gap-3">
            <a href="add_task.php" class="px-4 py-2 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 text-white font-bold text-xs shadow-md">
                Pasang Tugas Saya
            </a>
            <a href="referral.php" class="px-4 py-2 rounded-xl bg-white/[0.06] hover:bg-white/[0.1] text-slate-200 border border-white/10 font-bold text-xs">
                Klaim Bounty Referral
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="taskGrid">
        <?php foreach ($availableTasks as $t): ?>
            <?= render_task_card($t, $dailyReached) ?>
        <?php endforeach; ?>
    </div>

    <!-- Tombol Muat Lebih Banyak (Pagination Opsi A) -->
    <div id="loadMoreContainer" class="mt-8 mb-6 flex flex-col items-center justify-center">
        <button id="loadMoreBtn" onclick="loadMoreTasks()"
            class="group px-6 py-2.5 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 text-slate-300 hover:text-white font-display font-bold text-xs sm:text-sm shadow-lg hover:shadow-indigo-500/20 hover:scale-[1.02] active:scale-95 transition-all duration-300 flex items-center space-x-2.5 cursor-pointer <?= !$hasMoreTasks ? 'hidden' : '' ?>">
            <i id="loadMoreIcon" class="fa-solid fa-angles-down text-xs text-sky-400 group-hover:translate-y-0.5 transition-transform"></i>
            <span id="loadMoreText">Muat Tugas Lainnya</span>
        </button>
        <p id="noMoreTasksText" class="text-xs text-slate-500 mt-2 font-medium <?= $hasMoreTasks ? 'hidden' : '' ?>">
            <i class="fa-solid fa-circle-check mr-1 text-emerald-400"></i>Semua tugas telah ditampilkan
        </p>
    </div>
<?php endif; ?>

<!-- ============================================================================== -->
<!-- 3. ANTI-CHEAT MODAL TIMER (Hitung Mundur 30 Detik & Validasi Otomatis)           -->
<!-- ============================================================================== -->
<div id="antiCheatModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden flex items-center justify-center p-4">
    <div class="glass-card rounded-3xl p-6 sm:p-8 max-w-md w-full border border-white/[0.12] shadow-2xl relative text-center">
        
        <!-- Platform Icon & Task Header -->
        <div class="w-16 h-16 mx-auto rounded-3xl bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center text-2xl mb-4" id="modalPlatformIcon">
            <i class="fa-solid fa-bolt"></i>
        </div>

        <h3 class="font-display font-bold text-lg text-white" id="modalTitle">
            Tugas Sedang Dikerjakan
        </h3>
        <p class="text-xs text-slate-400 mt-1 mb-5" id="modalDesc">
            Tab baru telah dibuka. Silakan lakukan interaksi (subscribe / follow / like) pada halaman tersebut minimal 30 detik.
        </p>

        <!-- Circular Countdown Visual -->
        <div class="relative w-28 h-28 mx-auto my-3 flex items-center justify-center">
            <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                <path class="text-slate-800" stroke-width="3" stroke="currentColor" fill="none"
                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                <path id="timerCircle" class="text-amber-400 transition-all duration-1000 ease-linear" stroke-width="3" stroke-dasharray="100, 100" stroke="currentColor" fill="none"
                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span id="countdownNumber" class="text-3xl font-display font-black text-amber-300 font-mono">30</span>
                <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold">Detik</span>
            </div>
        </div>

        <!-- Security / Anti-Cheat Notice -->
        <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800 text-[11px] text-slate-400 my-4 text-left space-y-1">
            <div class="flex items-center space-x-1.5 text-amber-300 font-bold">
                <i class="fa-solid fa-shield-halved text-xs"></i>
                <span id="antiCheatBadge">Sistem Anti-Cheat Aktif</span>
            </div>
            <p id="antiCheatNoticeDesc" class="text-[10px] text-slate-400 leading-tight">
                Kerjakan tugas di tab tujuan hingga timer selesai. Jangan kembali ke tab ini lebih cepat agar timer tidak terjeda oleh sistem keamanan.
            </p>
        </div>

        <!-- Indikator Menunggu (Hanya tampil sebelum timer selesai) -->
        <div id="waitingNotice" class="w-full py-3.5 px-5 rounded-2xl bg-slate-900/90 border border-slate-800 text-slate-400 font-display font-bold text-xs tracking-wide flex items-center justify-center space-x-2">
            <i class="fa-solid fa-hourglass-half animate-spin text-amber-400 text-xs"></i>
            <span id="waitingNoticeText">Tunggu Detik di Tab Target...</span>
        </div>

        <!-- Action / Claim Button (HANYA MUNCUL SETELAH TIMER BERAKHIR - Aturan 2) -->
        <button id="claimRewardBtn" class="hidden w-full py-3.5 px-5 rounded-2xl bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-display font-black text-sm tracking-wide shadow-xl shadow-indigo-500/35 hover:shadow-[0_0_20px_rgba(139,92,246,0.4)] hover:scale-[1.02] active:scale-95 cursor-pointer transition-all duration-300 flex items-center justify-center space-x-2 animate-bounce">
            <i class="fa-solid fa-gift text-sm"></i>
            <span>Klaim Hadiah: +<span id="claimRewardAmount">0</span> Koin!</span>
        </button>

        <button onclick="cancelTask()" class="mt-3 text-xs text-slate-500 hover:text-slate-300 transition-colors">
            Batalkan Pengerjaan
        </button>

    </div>
</div>

<!-- ============================================================================== -->
<!-- 4. POP-UP ALERT WARNA MERAH MENGINTIMIDASI (Perang Psikologis - Aturan 2)         -->
<!-- ============================================================================== -->
<div id="cheatAlertModal" class="fixed inset-0 z-[60] bg-black/90 backdrop-blur-md hidden flex items-center justify-center p-4">
    <div class="rounded-3xl p-6 sm:p-8 max-w-lg w-full bg-gradient-to-b from-red-950 via-slate-950 to-slate-950 border-2 border-red-500 shadow-2xl shadow-red-900/60 relative text-center">
        
        <!-- Hazard Icon Flashing -->
        <div class="w-20 h-20 mx-auto rounded-3xl bg-red-600/20 border-2 border-red-500 text-red-500 flex items-center justify-center text-4xl mb-4 animate-bounce">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>

        <div class="inline-block px-3 py-1 rounded-full bg-red-600 text-white font-black text-[11px] uppercase tracking-widest mb-3 shadow-lg shadow-red-600/40">
            ⚠️ DETEKSI KECURANGAN SISTEM
        </div>

        <h3 class="font-display font-black text-xl sm:text-2xl text-red-400 mb-3 tracking-wide">
            PERINGATAN KECURANGAN!
        </h3>

        <!-- Kotak Pesan Intimidasi Warna Merah (Teks Wajib Sesuai Aturan 2) -->
        <div class="p-4 rounded-2xl bg-red-950/80 border-2 border-red-600 text-left mb-6 shadow-inner">
            <p id="cheatWarningMessage" class="text-xs sm:text-sm font-black text-red-100 leading-relaxed">
                ⚠️️ PERINGATAN: Sistem mendeteksi Anda kembali terlalu cepat! Selesaikan tugas dengan benar atau akun Anda akan diblokir.
            </p>
        </div>

        <!-- Tombol Tindakan -->
        <div class="space-y-3">
            <button onclick="resumeTaskTab()" class="w-full py-3.5 px-5 rounded-2xl bg-gradient-to-r from-red-600 via-rose-600 to-red-700 hover:from-red-500 hover:to-rose-500 text-white font-display font-black text-xs sm:text-sm tracking-wide shadow-lg shadow-red-600/40 hover:scale-[1.02] cursor-pointer transition-all flex items-center justify-center space-x-2">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                <span>Lanjutkan Tugas di Tab Baru</span>
            </button>

            <button onclick="cancelTask()" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white font-bold text-xs transition-colors">
                Batalkan Tugas Ini
            </button>
        </div>

    </div>
</div>

<!-- ============================================================================== -->
<!-- 5. JAVASCRIPT: TAB TRACKING & TIMER DINAMIS (Aturan 1 & 2)                      -->
<!-- ============================================================================== -->
<script>
let currentTask = null;
let timerInterval = null;
let timeLeft = 30;
let totalTime = 30;
let taskStartTime = 0;
let isTaskInProgress = false;
let isTimerPaused = false;

function startTask(taskId, taskUrl, platform, taskType, cleanReward, cpc) {
    currentTask = {
        id: taskId,
        url: taskUrl,
        platform: platform,
        type: taskType,
        reward: cleanReward,
        cpc: cpc
    };

    isTaskInProgress = true;
    isTimerPaused = false;
    taskStartTime = Date.now();

    // Aturan 1: Catat waktu mulai tugas di Session Backend PHP via AJAX
    const startData = new FormData();
    startData.append('action', 'start_task');
    startData.append('task_id', taskId);
    fetch('earn_coins.php', {
        method: 'POST',
        body: startData
    }).catch(err => console.error('Gagal inisialisasi timer backend:', err));

    // Aturan 1: Tugas YouTube = 30 detik. Tugas selain YouTube = 15 detik.
    const isYouTube = (platform.toLowerCase() === 'youtube');
    totalTime = isYouTube ? 30 : 15;
    timeLeft = totalTime;

    // 1. Logika Wajib: Buka link di tab baru (target="_blank")
    window.open(taskUrl, '_blank');

    // 2. Siapkan Modal Timer
    document.getElementById('modalTitle').innerText = `Mengerjakan ${platform.toUpperCase()} (${taskType})`;
    document.getElementById('modalDesc').innerText = `Tab baru telah dibuka. Harap tonton / ikuti target selama ${totalTime} detik penuh.`;

    const iconEl = document.getElementById('modalPlatformIcon');
    if (platform === 'youtube') {
        iconEl.innerHTML = '<i class="fa-brands fa-youtube text-red-500 text-3xl"></i>';
    } else if (platform === 'tiktok') {
        iconEl.innerHTML = '<i class="fa-brands fa-tiktok text-pink-400 text-3xl"></i>';
    } else if (platform === 'instagram') {
        iconEl.innerHTML = '<i class="fa-brands fa-instagram text-purple-400 text-3xl"></i>';
    } else {
        iconEl.innerHTML = '<i class="fa-brands fa-facebook text-blue-400 text-3xl"></i>';
    }

    // Reset Visual Timer
    document.getElementById('countdownNumber').innerText = timeLeft;
    document.getElementById('timerCircle').setAttribute('stroke-dasharray', '100, 100');
    document.getElementById('antiCheatBadge').innerText = `Sistem Anti-Cheat ${totalTime} Detik Aktif`;

    // Sembunyikan tombol klaim & tampilkan box menunggu (Aturan 2)
    document.getElementById('waitingNotice').classList.remove('hidden');
    document.getElementById('waitingNoticeText').innerText = `Tunggu ${timeLeft} Detik di Tab Target...`;
    document.getElementById('claimRewardBtn').classList.add('hidden');

    // Tampilkan Modal Anti-Cheat
    document.getElementById('antiCheatModal').classList.remove('hidden');
    document.getElementById('cheatAlertModal').classList.add('hidden');

    // Jalankan Timer
    runTimer();
}

function runTimer() {
    clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        if (isTimerPaused) return;

        timeLeft--;
        const displayTime = Math.max(0, timeLeft);
        document.getElementById('countdownNumber').innerText = displayTime;
        
        const percent = (displayTime / totalTime) * 100;
        document.getElementById('timerCircle').setAttribute('stroke-dasharray', `${percent}, 100`);
        document.getElementById('waitingNoticeText').innerText = `Tunggu ${displayTime} Detik di Tab Target...`;

        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            timerInterval = null;
            enableClaimButton();
        }
    }, 1000);
}

// Deteksi Perpindahan Tab (visibilitychange & window.onfocus) - Aturan 2
function onTabFocusDetected() {
    if (!isTaskInProgress || timeLeft <= 0 || isTimerPaused) return;

    const elapsedMs = Date.now() - taskStartTime;
    // Beri toleransi 1 detik saat tab pertama dibuka
    if (elapsedMs < 1000) return;

    // Perang Psikologis: Jika user kembali fokus SEBELUM timer 30/15 detik habis,
    // JEDA timer tersebut dan tampilkan pop-up alert merah!
    isTimerPaused = true;
    clearInterval(timerInterval);
    timerInterval = null;

    const alertModal = document.getElementById('cheatAlertModal');
    if (alertModal) {
        alertModal.classList.remove('hidden');
    }
}

document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
        onTabFocusDetected();
    }
});

window.addEventListener('focus', () => {
    onTabFocusDetected();
});

function resumeTaskTab() {
    if (!currentTask) return;
    document.getElementById('cheatAlertModal').classList.add('hidden');
    // Buka kembali tab tugas tujuan
    window.open(currentTask.url, '_blank');
    taskStartTime = Date.now(); // reset anchor toleransi
    isTimerPaused = false;
    runTimer();
}

function enableClaimButton() {
    // Tombol "Klaim Koin" HANYA MUNCUL setelah mereka benar-benar menghabiskan waktu 30 detik (Aturan 1)
    document.getElementById('waitingNotice').classList.add('hidden');
    const claimBtn = document.getElementById('claimRewardBtn');
    claimBtn.classList.remove('hidden');
    document.getElementById('claimRewardAmount').innerText = currentTask.reward;
    claimBtn.onclick = claimCoin;
}

function cancelTask() {
    clearInterval(timerInterval);
    timerInterval = null;
    isTaskInProgress = false;
    isTimerPaused = false;
    document.getElementById('antiCheatModal').classList.add('hidden');
    document.getElementById('cheatAlertModal').classList.add('hidden');
    currentTask = null;
}

function claimCoin() {
    if (!currentTask) return;

    const btn = document.getElementById('claimRewardBtn');
    if (!btn) return;

    // 2. Anti Double-Submit pada Tombol Klaim (JavaScript)
    btn.disabled = true;
    btn.innerHTML = 'Memproses...';
    btn.classList.add('opacity-50', 'cursor-not-allowed');

    const taskId = currentTask.id;
    const taskReward = currentTask.reward;

    const formData = new FormData();
    formData.append('action', 'claim_reward');
    formData.append('task_id', taskId);
    formData.append('spent_time', totalTime - timeLeft);

    fetch('earn_coins.php', {
        method: 'POST',
        body: formData
    })
    .then(res => {
        if (!res.ok) {
            throw new Error(`HTTP ${res.status}`);
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            // 1. Tampilkan notifikasi in-app toast sukses yang elegan tanpa reload/blocking alert
            if (typeof window.showToast === 'function') {
                window.showToast('success', `🎉 Hadiah: +${data.reward || taskReward} Koin berhasil masuk ke saldo Anda!`);
            }

            // 2. Perbarui teks saldo koin di pojok layar dan seluruh tampilan secara real-time
            const newBalFormatted = Number(data.new_balance).toLocaleString();
            document.querySelectorAll('.user-coin-balance').forEach(el => {
                el.innerText = newBalFormatted;
            });
            const navCoinsEl = document.getElementById('navbarCoinsAmount');
            if (navCoinsEl) {
                navCoinsEl.innerText = newBalFormatted;
            }

            // Efek kilau / bounce pada pill koin di navbar
            const pillEl = document.getElementById('navbarCoinsPill');
            if (pillEl) {
                pillEl.classList.add('ring-2', 'ring-amber-400', 'scale-105');
                setTimeout(() => {
                    pillEl.classList.remove('ring-2', 'ring-amber-400', 'scale-105');
                }, 800);
            }

            // Perbarui pelacak kuota klik harian jika ada
            if (data.new_clicks !== undefined) {
                const clickCountEl = document.getElementById('dailyClicksCount');
                if (clickCountEl) {
                    clickCountEl.innerText = data.new_clicks;
                }
            }

            // 3. Hapus kartu tugas dari layar secara halus (animasi fade out & slide)
            const cardEl = document.getElementById(`card-task-${taskId}`);
            if (cardEl) {
                cardEl.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                cardEl.style.transform = 'scale(0.85) translateY(-20px)';
                cardEl.style.opacity = '0';
                setTimeout(() => {
                    cardEl.style.maxHeight = '0px';
                    cardEl.style.padding = '0px';
                    cardEl.style.margin = '0px';
                    cardEl.style.overflow = 'hidden';
                    setTimeout(() => {
                        cardEl.remove();
                        // Jika tidak ada lagi kartu tugas di taskGrid, tampilkan empty state dengan mulus
                        const grid = document.getElementById('taskGrid');
                        if (grid && grid.querySelectorAll('[id^="card-task-"]').length === 0) {
                            grid.innerHTML = `
                                <div class="col-span-full glass-card rounded-3xl p-10 text-center border border-white/[0.08] transition-all">
                                    <div class="w-16 h-16 mx-auto rounded-3xl bg-slate-900 border border-slate-800 flex items-center justify-center text-3xl mb-4 text-emerald-400">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </div>
                                    <h3 class="font-display font-bold text-lg text-white">Hebat! Semua Tugas Sudah Anda Selesaikan</h3>
                                    <p class="text-xs text-slate-400 max-w-md mx-auto mt-2 leading-relaxed">
                                        Tidak ada tugas baru yang tersedia saat ini. Silakan pasang kampanye Anda sendiri atau periksa kembali nanti!
                                    </p>
                                    <div class="mt-5 flex justify-center gap-3">
                                        <a href="add_task.php" class="px-4 py-2.5 rounded-2xl bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-bold text-xs shadow-md hover:shadow-[0_0_20px_rgba(139,92,246,0.4)] transition-all duration-300">
                                            Pasang Kampanye Iklan
                                        </a>
                                    </div>
                                </div>
                            `;
                        }
                    }, 300);
                }, 200);
            }

            // 4. Tutup modal secara mulus tanpa reload halaman!
            cancelTask();
        } else {
            if (typeof window.showToast === 'function') {
                window.showToast('error', data.message || 'Gagal mengklaim koin.');
            } else {
                alert('⚠️ ' + data.message);
            }
            btn.disabled = false;
            btn.innerHTML = 'Klaim Koin';
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
            cancelTask();
        }
    })
    .catch(error => {
        // 3. Penanganan Error Jaringan (AJAX Timeout & Catch)
        console.error('Koneksi Error:', error);
        if (typeof window.showToast === 'function') {
            window.showToast('error', 'Koneksi jaringan terputus. Silakan coba klik klaim lagi.');
        } else {
            alert('Koneksi jaringan terputus. Silakan coba klik klaim lagi.');
        }
        btn.disabled = false;
        btn.innerHTML = 'Klaim Koin';
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
    });
}
const submitClaim = claimCoin;

// ==============================================================================
// 4. AJAX LOAD MORE PAGINATION (Opsi A)
// ==============================================================================
let currentTaskOffset = <?= (int)count($availableTasks) ?>;
let isLoadingTasks = false;
const currentPlatformFilter = <?= json_encode((string)$platformFilter) ?>;

async function loadMoreTasks() {
    if (isLoadingTasks) return;
    isLoadingTasks = true;

    const btn = document.getElementById('loadMoreBtn');
    const icon = document.getElementById('loadMoreIcon');
    const text = document.getElementById('loadMoreText');
    const noMoreText = document.getElementById('noMoreTasksText');
    const taskGrid = document.getElementById('taskGrid');
    if (!btn || !taskGrid) return;

    btn.disabled = true;
    btn.classList.add('opacity-75', 'cursor-wait');
    if (icon) {
        icon.className = 'fa-solid fa-circle-notch fa-spin text-xs text-sky-400';
    }
    if (text) {
        text.innerText = 'Memuat tugas...';
    }

    try {
        const url = `load_tasks.php?offset=${currentTaskOffset}&platform=${encodeURIComponent(currentPlatformFilter)}`;
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await response.json();

        if (data.success && data.html && data.count > 0) {
            // Render elemen baru secara mulus dengan transisi stagger
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = data.html;
            const newCards = Array.from(tempDiv.children);

            newCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(16px)';
                card.style.transition = `all 0.35s cubic-bezier(0.16, 1, 0.3, 1) ${index * 0.04}s`;
                taskGrid.appendChild(card);
                requestAnimationFrame(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                });
            });

            currentTaskOffset += data.count;

            if (!data.has_more || data.count < 20) {
                btn.classList.add('hidden');
                if (noMoreText) noMoreText.classList.remove('hidden');
            }
        } else {
            btn.classList.add('hidden');
            if (noMoreText) noMoreText.classList.remove('hidden');
        }
    } catch (err) {
        console.error('Gagal memuat tugas:', err);
        if (typeof window.showToast === 'function') {
            window.showToast('error', 'Gagal memuat tugas tambahan. Silakan coba lagi.');
        }
        if (text) text.innerText = 'Coba Lagi';
    } finally {
        isLoadingTasks = false;
        btn.disabled = false;
        btn.classList.remove('opacity-75', 'cursor-wait');
        if (icon) {
            icon.className = 'fa-solid fa-angles-down text-xs text-sky-400 group-hover:translate-y-0.5 transition-transform';
        }
        if (text && text.innerText === 'Memuat tugas...') {
            text.innerText = 'Muat Tugas Lainnya';
        }
    }
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
