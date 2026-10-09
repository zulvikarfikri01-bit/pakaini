<div class="space-y-3 sm:space-y-6">

    <!-- 1. HEADER & DAILY CLICK LIMIT STATUS -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2.5 sm:gap-4">
        <div>
            <div class="flex items-center space-x-1.5 sm:space-x-2 text-[9px] sm:text-xs font-bold text-amber-400 uppercase tracking-wider mb-0.5 sm:mb-1">
                <i class="fa-solid fa-coins"></i>
                <span>Exchange Hub</span>
            </div>
            <h1 class="font-display font-black text-lg sm:text-3xl text-white tracking-tight">
                Cari Koin & Eksekusi Tugas
            </h1>
            <p class="text-[10px] sm:text-sm text-slate-400 mt-0.5 leading-relaxed">
                Buka tautan tugas media sosial, tunggu timer validasi anti-cheat, dan klaim koin instan ke saldo Anda.
            </p>
        </div>

        <!-- Limit Status Widget -->
        <div class="glass-card p-2 sm:p-4 rounded-xl sm:rounded-2xl border border-white/[0.08] flex items-center justify-between sm:justify-start space-x-2 sm:space-x-3.5 shadow-lg">
            <div class="flex items-center space-x-2 sm:space-x-3">
                <div class="w-7 h-7 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl <?= $is_vip ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-brand-500/20 text-indigo-400 border border-brand-500/30' ?> flex items-center justify-center text-[11px] sm:text-base flex-shrink-0">
                    <i class="fa-solid <?= $is_vip ? 'fa-crown' : 'fa-hand-pointer' ?>"></i>
                </div>
                <div>
                    <div class="text-[8px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kuota Klik Hari Ini</div>
                    <div class="text-[11px] sm:text-sm font-black text-white font-mono">
                        <?= $daily_clicks ?> <span class="text-slate-400 font-normal font-sans text-[10px] sm:text-xs">/ <?= $is_vip ? '<span class="text-amber-300 font-bold">∞ Unlimited</span>' : '50 Maks' ?></span>
                    </div>
                </div>
            </div>
            <?php if (!$is_vip): ?>
                <a href="index.php?page=shop" class="text-[9px] sm:text-[11px] font-bold text-amber-300 hover:text-amber-200 bg-amber-500/15 border border-amber-500/30 px-2 py-0.5 sm:px-3 sm:py-1.5 rounded-md sm:rounded-xl whitespace-nowrap transition-colors">
                    Hapus Batas
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- 2. QUICK HOW-IT-WORKS INFO PILL (BEBAS KATA PAJAK) -->
    <div class="p-2 sm:p-3.5 rounded-lg sm:rounded-2xl bg-white/[0.03] border border-white/[0.06] flex flex-wrap items-center justify-between gap-1.5 sm:gap-3 text-[9px] sm:text-xs text-slate-300">
        <div class="flex items-center space-x-1.5">
            <i class="fa-solid fa-coins text-amber-400 text-[10px] sm:text-sm"></i>
            <span><strong>Sistem Koin Murni:</strong> Imbalan koin 100% langsung masuk ke <span class="text-amber-300 font-bold">Saldo Koin</span> Anda.</span>
        </div>
        <div class="flex items-center space-x-2.5 sm:space-x-4 text-[8px] sm:text-[11px]">
            <span class="flex items-center space-x-1"><i class="fa-solid fa-fire text-amber-400"></i><span>Tugas Prioritas Tampil Paling Atas</span></span>
        </div>
    </div>

    <!-- BANNER MONOPOLI TUGAS WAJIB ADMIN JIKA BELUM SELESAI -->
    <?php if (!empty($is_locked_by_admin_task)): ?>
        <div class="p-4 rounded-2xl bg-amber-500/10 border-2 border-amber-500/40 text-amber-200 shadow-xl">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-base shrink-0">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <span class="px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 font-black text-[9px] uppercase tracking-wider">Tugas Wajib Admin</span>
                    <h3 class="text-xs sm:text-sm font-bold text-amber-300 mt-0.5">Selesaikan Tugas Wajib Admin Dulu!</h3>
                    <p class="text-[11px] text-amber-200/90 mt-0.5 leading-relaxed">
                        Anda harus menyelesaikan semua tugas resmi admin di bawah ini sebelum bisa melihat tugas dari member lain.
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- 3. PLATFORM FILTER TABS -->
    <div class="flex items-center space-x-1.5 sm:space-x-2 overflow-x-auto no-scrollbar pb-1 border-b border-white/[0.06]">
        <a href="index.php?page=tasks&platform=all" 
           class="px-2.5 py-1.5 sm:px-4 sm:py-2.5 rounded-lg sm:rounded-2xl text-[10px] sm:text-xs font-bold whitespace-nowrap transition-all duration-200 flex items-center space-x-1 sm:space-x-2 <?= ($selected_platform === 'all' || empty($selected_platform)) ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'bg-slate-900 border border-slate-800 text-slate-400 hover:text-white hover:bg-slate-800/80' ?>">
            <i class="fa-solid fa-border-all text-[9px] sm:text-xs"></i>
            <span>Semua Platform</span>
        </a>
        <a href="index.php?page=tasks&platform=youtube" 
           class="px-2.5 py-1.5 sm:px-4 sm:py-2.5 rounded-lg sm:rounded-2xl text-[10px] sm:text-xs font-bold whitespace-nowrap transition-all duration-200 flex items-center space-x-1 sm:space-x-2 <?= ($selected_platform === 'youtube') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'bg-slate-900 border border-slate-800 text-slate-400 hover:text-white hover:bg-slate-800/80' ?>">
            <i class="fa-brands fa-youtube text-red-500 text-[10px] sm:text-sm"></i>
            <span>YouTube</span>
        </a>
        <a href="index.php?page=tasks&platform=tiktok" 
           class="px-2.5 py-1.5 sm:px-4 sm:py-2.5 rounded-lg sm:rounded-2xl text-[10px] sm:text-xs font-bold whitespace-nowrap transition-all duration-200 flex items-center space-x-1 sm:space-x-2 <?= ($selected_platform === 'tiktok') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'bg-slate-900 border border-slate-800 text-slate-400 hover:text-white hover:bg-slate-800/80' ?>">
            <i class="fa-brands fa-tiktok text-cyan-400 text-[10px] sm:text-sm"></i>
            <span>TikTok</span>
        </a>
        <a href="index.php?page=tasks&platform=instagram" 
           class="px-2.5 py-1.5 sm:px-4 sm:py-2.5 rounded-lg sm:rounded-2xl text-[10px] sm:text-xs font-bold whitespace-nowrap transition-all duration-200 flex items-center space-x-1 sm:space-x-2 <?= ($selected_platform === 'instagram') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'bg-slate-900 border border-slate-800 text-slate-400 hover:text-white hover:bg-slate-800/80' ?>">
            <i class="fa-brands fa-instagram text-pink-400 text-[10px] sm:text-sm"></i>
            <span>Instagram</span>
        </a>
        <a href="index.php?page=tasks&platform=facebook" 
           class="px-2.5 py-1.5 sm:px-4 sm:py-2.5 rounded-lg sm:rounded-2xl text-[10px] sm:text-xs font-bold whitespace-nowrap transition-all duration-200 flex items-center space-x-1 sm:space-x-2 <?= ($selected_platform === 'facebook') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'bg-slate-900 border border-slate-800 text-slate-400 hover:text-white hover:bg-slate-800/80' ?>">
            <i class="fa-brands fa-facebook text-blue-400 text-[10px] sm:text-sm"></i>
            <span>Facebook</span>
        </a>
    </div>

    <!-- 4. TASK CARDS GRID -->
    <?php if (empty($tasks)): ?>
        <div class="glass-card rounded-2xl sm:rounded-3xl p-6 sm:p-12 text-center border border-white/[0.08] max-w-lg mx-auto shadow-2xl">
            <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-xl sm:rounded-2xl bg-white/[0.04] text-slate-500 flex items-center justify-center text-xl sm:text-3xl mx-auto mb-3 sm:mb-4 border border-white/[0.08]">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <h2 class="font-display font-bold text-base sm:text-xl text-white mb-1.5 sm:mb-2">Belum Ada Tugas Tersedia</h2>
            <p class="text-[11px] sm:text-xs text-slate-400 leading-relaxed mb-4 sm:mb-6">
                Semua tugas untuk kategori ini sudah Anda selesaikan atau kuota klik sedang terpenuhi. Anda bisa menjadi yang pertama memasang kampanye!
            </p>
            <a href="index.php?page=tasks&action=create" class="btn-luxury inline-flex items-center space-x-2 px-4 py-2.5 sm:px-6 sm:py-3 text-white rounded-lg sm:rounded-xl text-[11px] sm:text-xs font-bold">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Pasang Kampanye Anda Sendiri</span>
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5 sm:gap-5">
            <?php foreach ($tasks as $task): 
                $isOwnerVip = (int)($task['owner_is_vip'] ?? 0) === 1;
                $isAdminTask = (int)($task['is_admin_task'] ?? 0) === 1;
                $isPriority = (int)($task['is_priority'] ?? 0) === 1;
                $cpc = (int)$task['cpc'];
                $hadiah_bersih = (int)floor($cpc * 0.8);
                $estReward = $hadiah_bersih;
                $approxIdr = $cpc * RATE_WITHDRAW_IDR;

                // Platform Icon & Styling
                $platformIcon = 'fa-link';
                $platformColor = 'text-slate-400';
                if ($task['platform'] === 'youtube') {
                    $platformIcon = 'fa-youtube';
                    $platformColor = 'text-red-500';
                } elseif ($task['platform'] === 'tiktok') {
                    $platformIcon = 'fa-tiktok';
                    $platformColor = 'text-cyan-400';
                } elseif ($task['platform'] === 'instagram') {
                    $platformIcon = 'fa-instagram';
                    $platformColor = 'text-pink-400';
                } elseif ($task['platform'] === 'facebook') {
                    $platformIcon = 'fa-facebook';
                    $platformColor = 'text-blue-400';
                }

                $duration = ($task['platform'] === 'youtube' && $task['task_type'] === 'views') ? 30 : 15;
            ?>
                <!-- Task Card -->
                <div class="glass-card rounded-xl sm:rounded-2xl p-3 sm:p-5 border transition-all duration-200 relative flex flex-col justify-between hover:translate-y-[-2px] <?= $isAdminTask ? 'border-amber-400/60 bg-amber-500/[0.08]' : ($isPriority ? 'border-indigo-500/50 bg-indigo-500/[0.06]' : ($isOwnerVip ? 'vip-border-glow' : 'border-white/[0.08]')) ?>">
                    
                    <!-- Ribbons -->
                    <?php if ($isAdminTask): ?>
                        <div class="absolute -top-2 sm:-top-3 right-2.5 sm:right-4 px-2 sm:px-3 py-0.5 rounded-full bg-gradient-to-r from-red-500 via-amber-500 to-yellow-400 text-slate-950 text-[8px] sm:text-[10px] font-black uppercase tracking-wider flex items-center space-x-1 shadow-md">
                            <i class="fa-solid fa-shield-halved text-[7px] sm:text-[9px]"></i>
                            <span>WAJIB ADMIN</span>
                        </div>
                    <?php elseif ($isPriority): ?>
                        <div class="absolute -top-2 sm:-top-3 right-2.5 sm:right-4 px-2 sm:px-3 py-0.5 rounded-full bg-gradient-to-r from-amber-400 to-orange-500 text-slate-950 text-[8px] sm:text-[10px] font-black uppercase tracking-wider flex items-center space-x-1 shadow-md">
                            <i class="fa-solid fa-fire text-[7px] sm:text-[9px]"></i>
                            <span>PRIORITAS #1</span>
                        </div>
                    <?php elseif ($isOwnerVip): ?>
                        <div class="absolute -top-2 sm:-top-3 right-2.5 sm:right-4 px-1.5 sm:px-3 py-0.2 sm:py-0.5 rounded-full bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 text-[8px] sm:text-[10px] font-black uppercase tracking-wider flex items-center space-x-1 shadow-md">
                            <i class="fa-solid fa-crown text-[7px] sm:text-[9px]"></i>
                            <span>VIP PRIORITAS</span>
                        </div>
                    <?php endif; ?>

                    <div>
                        <!-- Platform & Task Type Header -->
                        <div class="flex items-center justify-between mb-2 sm:mb-4">
                            <div class="flex items-center space-x-2 sm:space-x-3">
                                <div class="w-7 h-7 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-sm sm:text-lg <?= $platformColor ?>">
                                    <i class="fa-brands <?= $platformIcon ?>"></i>
                                </div>
                                <div>
                                    <div class="flex items-center space-x-1.5">
                                        <span class="text-[10px] sm:text-xs font-black text-white uppercase tracking-wider block">
                                            <?= htmlspecialchars($task['platform'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <span class="text-[8px] sm:text-[10px] text-slate-400 font-mono font-medium px-1 sm:px-1.5 py-0.2 rounded bg-white/[0.06]">
                                            <?= $duration ?>s
                                        </span>
                                    </div>
                                    <span class="text-[10px] sm:text-xs text-slate-300 font-semibold capitalize mt-0.5 block">
                                        <?= htmlspecialchars($task['task_type'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Reward Badge Callout -->
                            <div class="text-right">
                                <div class="text-sm sm:text-lg font-black text-amber-300 font-mono">
                                    Hadiah: <?= $hadiah_bersih ?> <span class="text-[9px] sm:text-xs font-normal text-slate-400 font-sans">Koin</span>
                                </div>
                                <span class="text-[8px] sm:text-[10px] text-slate-400 font-medium block">
                                    🪙 Koin Murni
                                </span>
                            </div>
                        </div>

                        <!-- Progress & Creator Details -->
                        <div class="bg-dark-950/70 rounded-lg sm:rounded-xl p-2 sm:p-3 border border-white/[0.06] mb-2.5 sm:mb-4 space-y-1 sm:space-y-2 text-[10px] sm:text-xs">
                            <div class="flex justify-between items-center text-slate-400 text-[9px] sm:text-[11px]">
                                <span>Pembuat:</span>
                                <span class="font-bold text-slate-200 flex items-center space-x-1">
                                    <span><?= htmlspecialchars($task['owner_username'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php if ($isOwnerVip): ?>
                                        <i class="fa-solid fa-crown text-amber-400 text-[7px] sm:text-[9px]" title="VIP Sultan"></i>
                                    <?php endif; ?>
                                </span>
                            </div>

                            <!-- Progress Bar -->
                            <div>
                                <div class="flex justify-between items-center text-[8px] sm:text-[10px] text-slate-400 mb-0.5 sm:mb-1">
                                    <span>Kemajuan:</span>
                                    <span class="font-bold text-white font-mono"><?= $task['current_clicks'] ?> / <?= $task['max_clicks'] ?></span>
                                </div>
                                <div class="w-full bg-dark-950 rounded-full h-1 sm:h-1.5 overflow-hidden border border-white/[0.04]">
                                    <?php $pct = min(100, round(($task['current_clicks'] / max(1, $task['max_clicks'])) * 100)); ?>
                                    <div class="bg-gradient-to-r from-brand-600 to-indigo-500 h-full rounded-full" style="width: <?= $pct ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <button type="button" 
                            onclick="openTaskModal(<?= $task['id'] ?>, '<?= htmlspecialchars($task['platform'], ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($task['task_type'], ENT_QUOTES, 'UTF-8') ?>', <?= $cpc ?>, <?= $hadiah_bersih ?>, <?= $duration ?>, '<?= htmlspecialchars($task['task_url'], ENT_QUOTES, 'UTF-8') ?>')"
                            class="w-full py-2 sm:py-3 px-3 sm:px-4 rounded-lg sm:rounded-xl font-bold text-[10px] sm:text-xs flex items-center justify-center space-x-1.5 sm:space-x-2 transition-all shadow-md active:scale-98 <?= $isOwnerVip ? 'btn-luxury-gold' : 'btn-luxury text-white' ?>">
                        <i class="fa-solid fa-play text-[9px] sm:text-xs"></i>
                        <span>Kerjakan (Hadiah: <?= $hadiah_bersih ?> Koin)</span>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<!-- 5. INTERACTIVE TASK VALIDATION MODAL (MOBILE NATIVE BOTTOM SHEET / DESKTOP MODAL) -->
<div id="taskModal" class="fixed inset-0 z-50 bg-dark-950/85 backdrop-blur-md flex items-end sm:items-center justify-center p-0 sm:p-4 hidden">
    <div class="glass-card border-t sm:border border-white/[0.12] rounded-t-2xl sm:rounded-3xl p-3.5 sm:p-8 max-w-md w-full shadow-2xl relative mobile-sheet-enter max-h-[92vh] overflow-y-auto">
        
        <!-- Mobile Pull Handle -->
        <div class="w-10 h-1 rounded-full bg-slate-700/80 mx-auto mb-2 sm:hidden"></div>

        <!-- Close Button -->
        <button type="button" onclick="closeTaskModal()" class="absolute top-3 sm:top-5 right-3 sm:right-5 w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-white/[0.05] hover:bg-white/[0.1] text-slate-400 hover:text-white flex items-center justify-center transition-colors">
            <i class="fa-solid fa-xmark text-[11px] sm:text-sm"></i>
        </button>

        <div class="text-center mb-3 sm:mb-5">
            <div id="modalPlatformIcon" class="w-9 h-9 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-base sm:text-2xl mx-auto mb-1.5 sm:mb-2.5 border border-indigo-500/30 shadow-lg shadow-indigo-500/20">
                <i class="fa-solid fa-play"></i>
            </div>
            <h2 id="modalTitle" class="font-display font-black text-sm sm:text-xl text-white">Kerjakan Tugas</h2>
            <p id="modalSubtitle" class="text-[9px] sm:text-xs text-slate-400 mt-0.5">Selesaikan tugas dan klaim koin ke saldo Anda.</p>
        </div>

        <!-- Steps Container -->
        <div class="space-y-2 sm:space-y-3.5 mb-3 sm:mb-6">
            
            <!-- Step 1: Buka Link -->
            <div class="p-2.5 sm:p-4 rounded-lg sm:rounded-2xl bg-dark-950/70 border border-white/[0.07] flex items-center justify-between">
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-md sm:rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-[10px] sm:text-xs flex-shrink-0 font-mono">
                        1
                    </div>
                    <div>
                        <div class="text-[10px] sm:text-xs font-bold text-white">Langkah 1: Buka Tautan</div>
                        <div class="text-[9px] sm:text-[11px] text-slate-400">Target akan terbuka di tab baru</div>
                    </div>
                </div>
                <button type="button" id="btnOpenLink" onclick="triggerTaskLink()" class="px-2.5 py-1 sm:px-4 sm:py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-md sm:rounded-xl text-[10px] sm:text-xs font-bold flex items-center space-x-1 sm:space-x-1.5 transition-colors shadow-md shadow-indigo-600/20">
                    <span>Buka Link</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[8px] sm:text-[10px]"></i>
                </button>
            </div>

            <!-- Step 2: Timer Countdown Visual -->
            <div class="p-2.5 sm:p-5 rounded-lg sm:rounded-2xl bg-dark-950/70 border border-white/[0.07] text-center">
                <div class="text-[10px] sm:text-xs font-semibold text-slate-400 mb-0.5 sm:mb-1">Langkah 2: Timer Validasi Anti-Cheat</div>
                
                <div class="flex items-center justify-center space-x-1.5 sm:space-x-2 my-1 sm:my-2.5">
                    <div class="relative w-10 h-10 sm:w-16 sm:h-16 flex items-center justify-center">
                        <span id="timerText" class="text-xl sm:text-3xl font-black text-amber-300 font-mono">--</span>
                    </div>
                    <span class="text-[10px] sm:text-xs font-bold text-slate-400">Detik</span>
                </div>

                <p id="timerHint" class="text-[9px] sm:text-[11px] text-slate-400">
                    Klik tombol "Buka Link" di atas untuk memulai hitung mundur.
                </p>

                <!-- Progress Bar -->
                <div class="w-full bg-dark-950 rounded-full h-1 sm:h-2 mt-2 sm:mt-3 overflow-hidden border border-white/[0.06]">
                    <div id="timerBar" class="bg-gradient-to-r from-amber-500 to-yellow-400 h-full rounded-full transition-all duration-1000 shadow-sm" style="width: 0%"></div>
                </div>
            </div>

        </div>

        <!-- Reward Info Callout -->
        <div class="p-2 sm:p-3.5 rounded-lg sm:rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-300 text-[10px] sm:text-xs mb-3 sm:mb-6 flex justify-between items-center">
            <span>Estimasi Koin Bersih:</span>
            <span id="modalRewardText" class="font-black text-[11px] sm:text-sm text-amber-300 font-mono">+0 Koin</span>
        </div>

        <!-- Claim Button (Disabled until timer finishes) -->
        <button type="button" id="btnClaim" onclick="executeClaimTask()" disabled
                class="w-full py-2 sm:py-3.5 px-3 sm:px-4 rounded-lg sm:rounded-xl font-bold text-[11px] sm:text-sm flex items-center justify-center space-x-1.5 sm:space-x-2 transition-all bg-white/[0.05] text-slate-500 cursor-not-allowed border border-white/[0.05]">
            <i class="fa-solid fa-gift text-xs"></i>
            <span id="btnClaimText">Menunggu Timer Selesai...</span>
        </button>

    </div>
</div>

<script>
    let currentTask = null;
    let timerInterval = null;
    let remainingTime = 0;
    let totalDuration = 15;
    let taskStarted = false;

    function openTaskModal(id, platform, type, cpc, reward, duration, url) {
        currentTask = { id, platform, type, cpc, reward, duration, url };
        totalDuration = duration;
        remainingTime = duration;
        taskStarted = false;
        clearInterval(timerInterval);

        document.getElementById('modalTitle').innerText = `${platform.toUpperCase()} - ${type.toUpperCase()}`;
        document.getElementById('modalRewardText').innerText = `+${reward} Koin`;
        document.getElementById('timerText').innerText = duration;
        document.getElementById('timerBar').style.width = '0%';
        document.getElementById('timerHint').innerText = 'Klik tombol "Buka Link" di atas untuk memulai hitung mundur.';
        
        // Reset claim button
        const btnClaim = document.getElementById('btnClaim');
        btnClaim.disabled = true;
        btnClaim.className = "w-full py-3.5 px-4 rounded-xl font-bold text-sm flex items-center justify-center space-x-2 transition-all bg-white/[0.05] text-slate-500 cursor-not-allowed border border-white/[0.05]";
        document.getElementById('btnClaimText').innerText = 'Menunggu Timer Selesai...';

        document.getElementById('taskModal').classList.remove('hidden');
    }

    function closeTaskModal() {
        clearInterval(timerInterval);
        document.getElementById('taskModal').classList.add('hidden');
        currentTask = null;
    }

    function triggerTaskLink() {
        if (!currentTask) return;

        // Buka link target di tab baru
        window.open(currentTask.url, '_blank');

        if (taskStarted) return;
        taskStarted = true;

        // Mulai server timer session
        fetch('index.php?page=tasks&action=start_timer', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `task_id=${currentTask.id}`
        }).then(res => res.json()).then(data => {
            if (data.success) {
                totalDuration = data.duration;
                remainingTime = data.duration;
                startCountdown();
            }
        }).catch(() => {
            startCountdown();
        });
    }

    function startCountdown() {
        document.getElementById('timerHint').innerHTML = '<span class="text-amber-300 font-semibold">Tonton / Subscribe akun target di tab baru hingga timer selesai...</span>';
        
        clearInterval(timerInterval);
        timerInterval = setInterval(() => {
            remainingTime--;
            document.getElementById('timerText').innerText = remainingTime;
            
            const progress = ((totalDuration - remainingTime) / totalDuration) * 100;
            document.getElementById('timerBar').style.width = `${progress}%`;

            if (remainingTime <= 0) {
                clearInterval(timerInterval);
                enableClaimButton();
            }
        }, 1000);
    }

    function enableClaimButton() {
        document.getElementById('timerText').innerText = '0';
        document.getElementById('timerHint').innerHTML = '<span class="text-emerald-400 font-bold">✓ Timer selesai! Klik tombol di bawah untuk mengklaim koin.</span>';
        
        const btnClaim = document.getElementById('btnClaim');
        btnClaim.disabled = false;
        btnClaim.className = "w-full py-3.5 px-4 rounded-xl font-bold text-sm flex items-center justify-center space-x-2 transition-all bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600 hover:opacity-95 text-slate-950 shadow-xl shadow-emerald-500/25 active:scale-98 cursor-pointer";
        document.getElementById('btnClaimText').innerHTML = `<i class="fa-solid fa-circle-check mr-1.5"></i> Klaim +${currentTask.reward} Koin Sekarang!`;
    }

    function executeClaimTask() {
        if (!currentTask) return;

        const btnClaim = document.getElementById('btnClaim');
        btnClaim.disabled = true;
        document.getElementById('btnClaimText').innerText = 'Memvalidasi & Mengklaim...';

        fetch('index.php?page=tasks&action=claim', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `task_id=${currentTask.id}`
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                showNotification('success', res.message);
                closeTaskModal();
                setTimeout(() => {
                    window.location.reload();
                }, 1400);
            } else {
                showNotification('error', res.message);
                btnClaim.disabled = false;
                document.getElementById('btnClaimText').innerText = 'Coba Klaim Lagi';
            }
        })
        .catch(() => {
            showNotification('error', 'Gagal memproses klaim. Periksa koneksi Anda.');
            btnClaim.disabled = false;
            document.getElementById('btnClaimText').innerText = 'Coba Klaim Lagi';
        });
    }
</script>
