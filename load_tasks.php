<?php
/**
 * PAKAINI - Social Media Exchange Platform
 * Endpoint AJAX Pagination Tugas (load_tasks.php)
 * Memuat 20 tugas berikutnya berdasarkan offset dengan Fetch API.
 */

require_once __DIR__ . '/config.php';

/**
 * Render HTML komponen kartu tugas tunggal
 * Dipakai bersama oleh earn_coins.php dan load_tasks.php untuk konsistensi UI 100%.
 */
if (!function_exists('render_task_card')) {
    function render_task_card(array $t, bool $dailyReached): string {
        $ownerIsVip = ((int)($t['owner_is_vip'] ?? 0) === 1);
        $isAdminTask = ((int)($t['is_admin_task'] ?? 0) === 1);
        $isPriority = ((int)($t['is_priority'] ?? 0) === 1);
        $cpc = (int)$t['cpc'];
        // Pemotongan Siluman (Silent 20% cut: pekerja menerima hadiah bersih 80%)
        $hadiah_bersih = (int)floor($cpc * 0.8);
        $cleanReward = $hadiah_bersih;

        $borderClass = $isAdminTask ? 'border-amber-400/50' : ($isPriority ? 'border-purple-500/40' : ($ownerIsVip ? 'border-amber-500/30' : 'border-white/[0.08]'));

        ob_start();
        ?>
        <div id="card-task-<?= (int)$t['id'] ?>" class="glass-card rounded-3xl p-5 border <?= $borderClass ?> bg-[#111111] flex flex-col justify-between hover:scale-[1.01] hover:border-white/20 transition-all relative overflow-hidden group">
            
            <!-- Ribbon Status -->
            <?php if ($isAdminTask): ?>
                <div class="absolute -right-12 top-4 bg-amber-400 text-slate-950 font-black text-[9px] uppercase tracking-wider py-0.5 px-12 rotate-45 shadow-md">
                    ⭐ Wajib Admin
                </div>
            <?php elseif ($isPriority): ?>
                <div class="absolute -right-12 top-4 bg-purple-500 text-white font-black text-[9px] uppercase tracking-wider py-0.5 px-12 rotate-45 shadow-md">
                    🔥 Prioritas #1
                </div>
            <?php elseif ($ownerIsVip): ?>
                <div class="absolute -right-12 top-4 bg-amber-400 text-slate-950 font-black text-[9px] uppercase tracking-wider py-0.5 px-12 rotate-45 shadow-md">
                    👑 VIP Sultan
                </div>
            <?php endif; ?>

            <div>
                <!-- Header Card: Platform & Owner Info -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2.5">
                        <?php if ($t['platform'] === 'youtube'): ?>
                            <div class="w-10 h-10 rounded-2xl bg-red-600/20 text-red-500 flex items-center justify-center text-lg shrink-0">
                                <i class="fa-brands fa-youtube"></i>
                            </div>
                        <?php elseif ($t['platform'] === 'tiktok'): ?>
                            <div class="w-10 h-10 rounded-2xl bg-pink-600/20 text-pink-400 flex items-center justify-center text-lg shrink-0">
                                <i class="fa-brands fa-tiktok"></i>
                            </div>
                        <?php elseif ($t['platform'] === 'instagram'): ?>
                            <div class="w-10 h-10 rounded-2xl bg-purple-600/20 text-purple-400 flex items-center justify-center text-lg shrink-0">
                                <i class="fa-brands fa-instagram"></i>
                            </div>
                        <?php else: ?>
                            <div class="w-10 h-10 rounded-2xl bg-blue-600/20 text-blue-400 flex items-center justify-center text-lg shrink-0">
                                <i class="fa-brands fa-facebook"></i>
                            </div>
                        <?php endif; ?>

                        <div>
                            <h4 class="font-bold text-white text-xs sm:text-sm capitalize flex items-center gap-1.5">
                                <span><?= htmlspecialchars($t['platform'], ENT_QUOTES, 'UTF-8') ?></span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 font-semibold uppercase">
                                    <?= htmlspecialchars($t['task_type'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </h4>
                            <span class="text-[11px] text-slate-400 flex items-center gap-1">
                                <span>Oleh: @<?= htmlspecialchars($t['owner_username'] ?? 'user', ENT_QUOTES, 'UTF-8') ?></span>
                                <?php if ($isAdminTask): ?>
                                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-amber-500/20 text-amber-300 font-bold border border-amber-500/30">Official</span>
                                <?php elseif ($ownerIsVip): ?>
                                    <i class="fa-solid fa-crown text-amber-400 text-[10px]" title="Pemilik VIP Prioritas"></i>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Reward Badge Details: Saldo Koin Bersih -->
                <div class="p-3 rounded-2xl bg-black/50 border border-white/[0.06] mb-4 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-medium">Hadiah</span>
                        <div class="text-base sm:text-lg font-mono font-black text-amber-300 flex items-center space-x-1">
                            <span>🪙</span>
                            <span>Hadiah: <?= number_format($hadiah_bersih) ?> Koin</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-medium">Masuk Saldo Koin</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 block mb-1">Status</span>
                        <?php if ($isAdminTask): ?>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-white/[0.08] text-amber-300 font-bold border border-amber-500/30 inline-block">⭐ Tugas Wajib</span>
                        <?php elseif ($isPriority): ?>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-white/[0.08] text-purple-300 font-bold border border-purple-500/30 inline-block">🔥 Prioritas #1</span>
                        <?php else: ?>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-white/[0.05] text-slate-400 font-medium border border-white/5 inline-block">Reguler</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Action Button -->
            <div>
                <?php if ($dailyReached): ?>
                    <button disabled
                        class="w-full py-2.5 px-4 rounded-xl bg-slate-800 text-slate-500 font-bold text-xs cursor-not-allowed">
                        Limit Harian Tercapai (50 Klik)
                    </button>
                <?php else: ?>
                    <button onclick="startTask(<?= (int)$t['id'] ?>, '<?= htmlspecialchars(addslashes($t['task_url']), ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars(addslashes($t['platform']), ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars(addslashes($t['task_type']), ENT_QUOTES, 'UTF-8') ?>', <?= (int)$hadiah_bersih ?>, <?= (int)$cpc ?>)"
                        class="w-full py-3 px-4 rounded-2xl bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-display font-bold text-xs shadow-lg shadow-indigo-500/25 hover:shadow-[0_0_20px_rgba(139,92,246,0.4)] hover:scale-[1.02] active:scale-95 transition-all duration-300 flex items-center justify-center space-x-1.5 cursor-pointer">
                        <i class="fa-solid fa-play text-[10px]"></i>
                        <span>Kerjakan (Hadiah: <?= number_format($hadiah_bersih) ?> Koin)</span>
                    </button>
                <?php endif; ?>
            </div>

        </div>
        <?php
        return ob_get_clean();
    }
}

// Cek jika dieksekusi langsung sebagai endpoint AJAX HTTP
$currentScript = basename($_SERVER['PHP_SELF'] ?? '');
if ($currentScript === 'load_tasks.php') {
    header('Content-Type: application/json; charset=UTF-8');

    // Wajib Login
    $user = current_user($pdo);
    if (!$user) {
        echo json_encode([
            'success'  => false,
            'message'  => 'Sesi login telah berakhir. Silakan login kembali.',
            'count'    => 0,
            'has_more' => false,
            'html'     => ''
        ]);
        exit;
    }

    $isVip = ((int)$user['is_vip'] === 1);
    $dailyClicks = (int)$user['daily_clicks'];
    $dailyReached = (!$isVip && $dailyClicks >= REGULAR_DAILY_MAX_CLICKS);

    $offset = max(0, (int)($_GET['offset'] ?? $_POST['offset'] ?? 0));
    $limit = 20;
    $platformFilter = strtolower(trim(clean($_GET['platform'] ?? $_POST['platform'] ?? '')));

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

    // Urutan prioritas kueri:
    // ORDER BY is_admin_task DESC, created_at DESC LIMIT 20 OFFSET ?
    $fetchLimit = $limit + 1;
    $sql .= " ORDER BY t.is_admin_task DESC, t.created_at DESC LIMIT " . (int)$fetchLimit . " OFFSET " . (int)$offset;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $tasks = $stmt->fetchAll();

        $hasMore = false;
        if (count($tasks) > $limit) {
            $hasMore = true;
            $tasks = array_slice($tasks, 0, $limit);
        }

        $html = '';
        foreach ($tasks as $t) {
            $html .= render_task_card($t, $dailyReached);
        }

        echo json_encode([
            'success'     => true,
            'count'       => count($tasks),
            'has_more'    => $hasMore,
            'next_offset' => $offset + count($tasks),
            'html'        => $html
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success'  => false,
            'message'  => 'Gagal memuat tugas: ' . $e->getMessage(),
            'count'    => 0,
            'has_more' => false,
            'html'     => ''
        ]);
    }
    exit;
}
