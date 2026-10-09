<div class="space-y-4 sm:space-y-6">

    <?php if (($user['role'] ?? '') === 'admin' || in_array(strtolower($user['username'] ?? ''), ['admin', 'adminzul', 'superadmin'])): ?>
    <!-- SUPER ADMIN CONTROL HERO BANNER -->
    <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-rose-950/80 via-slate-900 to-red-950/70 border border-rose-500/40 text-white shadow-xl relative overflow-hidden backdrop-blur-md">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start space-x-3.5">
                <div class="w-12 h-12 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-400 flex items-center justify-center text-xl shrink-0 shadow-lg shadow-rose-500/20 mt-0.5">
                    <i class="fa-solid fa-shield-halved animate-pulse"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2.5 py-0.5 rounded-full bg-rose-500/30 text-rose-200 border border-rose-500/40 text-[10px] font-black uppercase tracking-wider">
                            👑 Super Administrator
                        </span>
                        <span class="text-xs text-rose-300/80 font-semibold hidden sm:inline">Hak Akses Penuh Sistem Terverifikasi</span>
                    </div>
                    <h3 class="font-display font-extrabold text-white text-base sm:text-lg">
                        Panel Kendali Utama Tersedia — Halo, <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>!
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 leading-relaxed">
                        Anda login sebagai Administrator. Anda memiliki hak kendali penuh: top-up koin tanpa batas, manajemen tugas, kontrol member, dan reset sistem.
                    </p>
                </div>
            </div>
            <div class="shrink-0 flex items-center gap-2.5">
                <a href="admin.php" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white font-display font-bold text-xs sm:text-sm flex items-center justify-center space-x-2 shadow-lg shadow-rose-600/30 hover:scale-[1.02] active:scale-95 transition-all">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>Buka Panel Administrator</span>
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>


    <?php if (!empty($broadcast_announcement)): ?>
        <!-- Global Admin Broadcast Announcement Banner -->
        <div class="p-3.5 sm:p-4 rounded-2xl bg-gradient-to-r from-purple-950/80 via-indigo-950/80 to-purple-950/80 border border-purple-500/30 text-purple-200 text-xs sm:text-sm flex items-start space-x-3 shadow-xl backdrop-blur-md">
            <div class="w-8 h-8 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center flex-shrink-0 text-sm border border-purple-500/30">
                <i class="fa-solid fa-bullhorn animate-bounce"></i>
            </div>
            <div class="flex-1 min-w-0 pt-0.5">
                <span class="font-black text-amber-300 uppercase tracking-wider text-[10px] sm:text-xs block mb-0.5">Pengumuman Admin:</span>
                <p class="text-white text-xs leading-relaxed"><?= nl2br(htmlspecialchars($broadcast_announcement, ENT_QUOTES, 'UTF-8')) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ((int)($user['has_claimed_wa_vip'] ?? 0) === 0): ?>
        <!-- BANNER KHUSUS KLAIM VIP 3 HARI VIA GRUP WHATSAPP -->
        <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-gradient-to-r from-emerald-950/80 via-slate-900 to-indigo-950/70 border border-emerald-500/30 shadow-lg relative overflow-hidden backdrop-blur-md">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 relative z-10">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-green-600 text-white flex items-center justify-center text-lg shrink-0 shadow-md shadow-emerald-500/20">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[9px] font-bold uppercase tracking-wider">
                                🎁 Bonus Belum Diklaim
                            </span>
                            <span class="text-[11px] text-amber-300 font-semibold hidden sm:inline">• Gratis Koin & VIP 3 Hari</span>
                        </div>
                        <h3 class="font-display font-bold text-white text-sm sm:text-base mt-0.5">
                            Gabung Grup WhatsApp untuk Klaim VIP 3 Hari!
                        </h3>
                        <p class="text-xs text-slate-300 mt-0.5 leading-snug">
                            Dapatkan instan <strong class="text-amber-300 font-semibold">+500 Koin Saldo</strong> dan status <strong class="text-amber-400 font-semibold">VIP Sultan 3 Hari</strong>.
                        </p>
                    </div>
                </div>

                <div class="shrink-0 flex items-center pt-1 sm:pt-0">
                    <a href="claim_vip.php" 
                       class="w-full sm:w-auto px-4 py-2 sm:py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-400 hover:to-green-400 text-slate-950 font-display font-bold text-xs flex items-center justify-center space-x-2 shadow-md hover:scale-[1.02] active:scale-95 transition-all">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>Gabung Grup WA untuk Klaim VIP 3 Hari</span>
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- 1. VIRTUAL FINTECH COIN CARD (KARTU DOMPET UTAMA MOBILE & DESKTOP) -->
    <div class="relative overflow-hidden rounded-xl sm:rounded-2xl p-4 sm:p-5 border border-white/[0.12] bg-gradient-to-br from-slate-900 via-dark-950 to-indigo-950/80 shadow-xl">
        <!-- Ambient Background Sheen -->
        <div class="absolute -top-14 -right-14 w-44 h-44 bg-indigo-600/20 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-14 -left-14 w-44 h-44 bg-amber-500/15 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col justify-between space-y-3.5 sm:space-y-4">
            
            <!-- Card Top Row: User Greeting, Chip Graphic & Status Rank -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-gradient-to-br from-brand-600 via-indigo-600 to-indigo-800 flex items-center justify-center font-bold text-white text-[11px] shadow-sm border border-white/20">
                        <?= strtoupper(substr($user['username'], 0, 2)) ?>
                    </div>
                    <div>
                        <div class="text-[9px] text-slate-400 font-semibold uppercase tracking-wider">Akun Member</div>
                        <div class="text-xs sm:text-sm font-bold text-white flex items-center space-x-1.5">
                            <span><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php if ((int)$user['is_vip'] === 1): ?>
                                <i class="fa-solid fa-crown text-amber-400 text-[10px]" title="VIP Sultan Member"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Right Chip Graphic & Rank Badge -->
                <div class="flex items-center space-x-1.5 sm:space-x-2">
                    <!-- EMV Luxury Gold Chip Icon -->
                    <div class="w-6 h-4 sm:w-7 sm:h-5 rounded bg-gradient-to-tr from-amber-400 via-yellow-200 to-amber-500 p-[1px] shadow-sm flex items-center justify-center opacity-85">
                        <div class="w-full h-full bg-slate-950/60 rounded-[2px] sm:rounded-[3px] flex items-center justify-around px-0.5">
                            <span class="w-[1px] h-2 bg-amber-300/80 rounded-full"></span>
                            <span class="w-[1.5px] h-2.5 bg-amber-400 rounded-sm"></span>
                            <span class="w-[1px] h-2 bg-amber-300/80 rounded-full"></span>
                        </div>
                    </div>

                    <?php if ((int)$user['is_vip'] === 1): ?>
                        <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-gradient-to-r from-amber-500/25 to-yellow-500/15 text-amber-300 border border-amber-500/40 shadow-sm">
                            <i class="fa-solid fa-crown text-amber-400 text-[8px]"></i>
                            <span>VIP (<?= $vip_days_left ?>h)</span>
                        </span>
                    <?php else: ?>
                        <a href="index.php?page=shop" class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[9px] font-bold text-amber-300 bg-amber-500/10 border border-amber-500/30 hover:bg-amber-500/20 active:scale-95 transition-all">
                            <span>Upgrade VIP</span>
                            <i class="fa-solid fa-chevron-right text-[7px]"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card Center: Balance Display (Ukuran Standar Rapi & Elegan - Koin Murni) -->
            <div class="py-0.5">
                <div class="text-[10px] sm:text-[11px] text-slate-400 font-medium mb-0.5">Saldo Koin Murni Aktif</div>
                <div class="flex items-baseline space-x-1.5 sm:space-x-2">
                    <span class="text-xl sm:text-2xl drop-shadow-[0_0_8px_rgba(245,158,11,0.5)]">🪙</span>
                    <span class="font-display font-extrabold text-2xl sm:text-3xl font-mono tracking-tight <?= ((int)$user['coins'] < 0) ? 'text-rose-400' : 'text-amber-300 drop-shadow-[0_0_12px_rgba(245,158,11,0.4)]' ?>">
                        <?= number_format($user['coins']) ?>
                    </span>
                    <span class="text-xs sm:text-sm font-bold text-slate-300 font-sans">Koin</span>
                </div>
                <div class="mt-1 flex items-center space-x-2">
                    <span class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/25 text-amber-300 font-mono font-bold text-[10px] sm:text-[11px] shadow-[0_0_10px_rgba(245,158,11,0.15)]">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                        <span>Ekosistem Koin Murni</span>
                    </span>
                    <span class="text-[9px] text-slate-400 font-medium">Pencairan Instan</span>
                </div>
            </div>

            <!-- Card Bottom: 4 Quick Thumb Actions (Akses Cepat 1-Ketuk Jempol di HP) -->
            <div class="pt-2 sm:pt-2.5 border-t border-white/[0.08] grid grid-cols-4 gap-1.5 sm:gap-3 text-center">
                <!-- Action 1: Top Up -->
                <a href="index.php?page=shop" class="flex flex-col items-center group active:scale-95 transition-transform">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-slate-900 border border-white/[0.08] group-hover:border-amber-500/40 text-amber-400 flex items-center justify-center text-xs sm:text-sm shadow-sm group-hover:scale-105 transition-all">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-semibold text-slate-300 mt-1 group-hover:text-white">Top Up</span>
                </a>

                <!-- Action 2: Cari Koin -->
                <a href="index.php?page=tasks" class="flex flex-col items-center group active:scale-95 transition-transform">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-slate-900 border border-white/[0.08] group-hover:border-indigo-500/40 text-indigo-400 flex items-center justify-center text-xs sm:text-sm shadow-sm group-hover:scale-105 transition-all">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-semibold text-slate-300 mt-1 group-hover:text-white">Cari Koin</span>
                </a>

                <!-- Action 3: Pasang Kampanye -->
                <a href="index.php?page=tasks&action=create" class="flex flex-col items-center group active:scale-95 transition-transform">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-slate-900 border border-white/[0.08] group-hover:border-indigo-500/40 text-indigo-400 flex items-center justify-center text-xs sm:text-sm shadow-sm group-hover:scale-105 transition-all">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-semibold text-slate-300 mt-1 group-hover:text-white">Pasang</span>
                </a>

                <!-- Action 4: Misi 50k -->
                <a href="index.php?page=referral" class="flex flex-col items-center group active:scale-95 transition-transform">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-slate-900 border border-white/[0.08] group-hover:border-indigo-500/40 text-indigo-400 flex items-center justify-center text-xs sm:text-sm shadow-sm group-hover:scale-105 transition-all">
                        <i class="fa-solid fa-gift"></i>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-semibold text-slate-300 mt-1 group-hover:text-white">Afiliasi</span>
                </a>
            </div>

        </div>
    </div>

    <!-- LIVE MEMBER ACTIVITY TICKER (Membuat aplikasi terasa sangat hidup & profesional) -->
    <div class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl sm:rounded-2xl bg-dark-950/80 border border-white/[0.08] flex items-center space-x-2 sm:space-x-2.5 text-xs text-slate-300 overflow-hidden shadow-inner">
        <span class="flex items-center space-x-1 px-1.5 sm:px-2 py-0.5 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 font-bold text-[8px] sm:text-[9px] uppercase tracking-wider flex-shrink-0">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
            <span>LIVE</span>
        </span>
        <div id="liveTickerText" class="truncate text-[10px] sm:text-[11px] text-slate-300 transition-opacity duration-300">
            <strong>@sultan_vip</strong> baru saja menyelesaikan tugas YouTube (+150 Koin)
        </div>
    </div>

    <script>
        // Ticker Berjalan Realistis
        const tickerActivities = [
            "<strong>@sultan_vip</strong> baru saja menyelesaikan tugas YouTube (+150 Koin)",
            "<strong>@budi_pemula</strong> menerima reward streak harian (+50 Koin)",
            "<strong>@cantika_tiktok</strong> memasang kampanye TikTok baru (100 Follower)",
            "<strong>@dimas_pro</strong> mengklaim komisi referral (+200 Koin)",
            "<strong>@raffi_agency</strong> melakukan upgrade ke VIP Sultan 👑"
        ];
        let tickerIdx = 0;
        setInterval(() => {
            const el = document.getElementById('liveTickerText');
            if (el) {
                el.style.opacity = '0';
                setTimeout(() => {
                    tickerIdx = (tickerIdx + 1) % tickerActivities.length;
                    el.innerHTML = tickerActivities[tickerIdx];
                    el.style.opacity = '1';
                }, 300);
            }
        }, 3800);
    </script>

    <!-- 2. 2x2 MOBILE KPI TILES (Ringkas, Padat, Mudah Dibaca) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-4">
        
        <!-- Tile 1: Kuota Klik Harian -->
        <div class="glass-card rounded-xl sm:rounded-2xl p-2.5 sm:p-4 border border-white/[0.08] flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-400 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider mb-0.5 sm:mb-1">
                <span>Klik Hari Ini</span>
                <i class="fa-solid fa-hand-pointer text-indigo-400 text-[10px] sm:text-xs"></i>
            </div>
            <div class="text-base sm:text-2xl font-black text-white font-mono my-0.5 sm:my-1">
                <?= (int)$user['daily_clicks'] ?>
                <span class="text-[10px] sm:text-xs text-slate-400 font-sans font-normal">/ <?= ((int)$user['is_vip'] === 1) ? '∞' : '50' ?></span>
            </div>
            <!-- Progress Line -->
            <div class="w-full bg-dark-950 rounded-full h-1 sm:h-1.5 overflow-hidden border border-white/[0.04]">
                <?php $clickPct = ((int)$user['is_vip'] === 1) ? 100 : min(100, round(((int)$user['daily_clicks'] / 50) * 100)); ?>
                <div class="bg-indigo-500 h-full rounded-full" style="width: <?= $clickPct ?>%"></div>
            </div>
            <span class="text-[8px] sm:text-[9px] text-slate-400 mt-1 sm:mt-1.5 block">
                <?= ((int)$user['is_vip'] === 1) ? 'Bebas Batas (VIP)' : (max(0, 50 - (int)$user['daily_clicks']) . ' klik tersisa') ?>
            </span>
        </div>

        <!-- Tile 2: Kampanye Aktif -->
        <div class="glass-card rounded-xl sm:rounded-2xl p-2.5 sm:p-4 border border-white/[0.08] flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-400 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider mb-0.5 sm:mb-1">
                <span>Kampanye Saya</span>
                <i class="fa-solid fa-bullhorn text-indigo-400 text-[10px] sm:text-xs"></i>
            </div>
            <div class="text-base sm:text-2xl font-black text-white font-mono my-0.5 sm:my-1">
                <?= $active_tasks_count ?>
                <span class="text-[10px] sm:text-xs text-slate-400 font-sans font-normal">/ <?= ((int)$user['is_vip'] === 1) ? '∞' : '2' ?></span>
            </div>
            <div class="w-full bg-dark-950 rounded-full h-1 sm:h-1.5 overflow-hidden border border-white/[0.04]">
                <?php $taskPct = ((int)$user['is_vip'] === 1) ? 100 : min(100, round(($active_tasks_count / 2) * 100)); ?>
                <div class="bg-indigo-500 h-full rounded-full" style="width: <?= $taskPct ?>%"></div>
            </div>
            <span class="text-[8px] sm:text-[9px] text-slate-400 mt-1 sm:mt-1.5 block">
                <?= (!$user['is_vip'] && $active_tasks_count >= 2) ? '<span class="text-rose-400 font-bold">Slot Penuh</span>' : '<span class="text-emerald-400 font-bold">Slot Siap</span>' ?>
            </span>
        </div>

        <!-- Tile 3: Streak Anti-Tuyul -->
        <div class="glass-card rounded-xl sm:rounded-2xl p-2.5 sm:p-4 border border-white/[0.08] flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-400 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider mb-0.5 sm:mb-1">
                <span>Streak Aktif</span>
                <i class="fa-solid fa-fire text-amber-400 text-[10px] sm:text-xs"></i>
            </div>
            <div class="text-base sm:text-2xl font-black text-white font-mono my-0.5 sm:my-1">
                <?= (int)$user['active_days_streak'] ?> <span class="text-[10px] sm:text-xs text-slate-400 font-sans font-normal">Hari</span>
            </div>
            <div class="w-full bg-dark-950 rounded-full h-1 sm:h-1.5 overflow-hidden border border-white/[0.04]">
                <?php $streakPct = min(100, round(((int)$user['active_days_streak'] / 10) * 100)); ?>
                <div class="bg-amber-500/80 h-full rounded-full" style="width: <?= $streakPct ?>%"></div>
            </div>
            <span class="text-[8px] sm:text-[9px] text-slate-400 mt-1 sm:mt-1.5 block">
                Target: 10 Hari (LULUS)
            </span>
        </div>

        <!-- Tile 4: Misi Afiliasi -->
        <div class="glass-card rounded-xl sm:rounded-2xl p-2.5 sm:p-4 border border-white/[0.08] flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-400 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider mb-0.5 sm:mb-1">
                <span>Misi Afiliasi</span>
                <i class="fa-solid fa-gift text-indigo-400 text-[10px] sm:text-xs"></i>
            </div>
            <div class="text-base sm:text-2xl font-black text-white font-mono my-0.5 sm:my-1">
                <?= $referral_data['lulus_count'] ?> <span class="text-[10px] sm:text-xs text-slate-400 font-sans font-normal">/ 50</span>
            </div>
            <div class="w-full bg-dark-950 rounded-full h-1 sm:h-1.5 overflow-hidden border border-white/[0.04]">
                <div class="bg-indigo-500 h-full rounded-full" style="width: <?= max(4, $referral_data['progress_pct']) ?>%"></div>
            </div>
            <span class="text-[8px] sm:text-[9px] text-slate-400 mt-1 sm:mt-1.5 block flex justify-between">
                <span>Progres: <?= $referral_data['progress_pct'] ?>%</span>
                <a href="index.php?page=referral" class="text-indigo-400 font-bold hover:underline">Detail</a>
            </span>
        </div>

    </div>

    <!-- 3. HOT TASKS PREVIEW (TUGAS REKOMENDASI TERATAS - SIAP DIKERJAKAN LANGSUNG) -->
    <div class="glass-card rounded-xl sm:rounded-2xl p-3.5 sm:p-4 border border-white/[0.08]">
        <div class="flex items-center justify-between mb-2.5">
            <div class="flex items-center space-x-2">
                <div class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-[10px]">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <div>
                    <h2 class="font-display font-bold text-xs sm:text-sm text-white">Tugas Rekomendasi Teratas</h2>
                    <span class="text-[9px] text-slate-400 block sm:inline">Kerjakan dan kumpulkan koin instan</span>
                </div>
            </div>
            <a href="index.php?page=tasks" class="text-[10px] sm:text-[11px] font-bold text-indigo-400 hover:text-indigo-300 flex items-center space-x-1">
                <span>Semua (<?= $available_count ?>)</span>
                <i class="fa-solid fa-arrow-right text-[8px]"></i>
            </a>
        </div>

        <?php if (empty($available_tasks)): ?>
            <div class="text-center py-6 text-slate-400 text-xs">
                <i class="fa-solid fa-check-double text-2xl mb-1.5 block text-slate-600"></i>
                Semua tugas saat ini telah Anda selesaikan. Pasang tugas Anda sendiri atau cek berkala!
            </div>
        <?php else: ?>
            <div class="space-y-2 sm:space-y-2.5">
                <?php foreach ($available_tasks as $t): 
                    $isVipTask = ((int)($t['owner_is_vip'] ?? 0) === 1);
                    $iconClass = 'fa-brands fa-youtube text-red-500';
                    if ($t['platform'] === 'tiktok') {
                        $iconClass = 'fa-brands fa-tiktok text-cyan-400';
                    } elseif ($t['platform'] === 'instagram') {
                        $iconClass = 'fa-brands fa-instagram text-pink-500';
                    } elseif ($t['platform'] === 'facebook') {
                        $iconClass = 'fa-brands fa-facebook text-blue-500';
                    }
                    $cpc = (int)$t['cpc'];
                    $netReward = $user['is_vip'] ? $cpc : ($cpc - (int)round($cpc * REGULAR_TAX_RATE));
                ?>
                    <div class="p-2.5 sm:p-4 rounded-xl sm:rounded-2xl bg-dark-950/70 border border-white/[0.06] hover:border-indigo-500/30 flex items-center justify-between gap-2.5 sm:gap-3 transition-all active:scale-[0.99] <?= $isVipTask ? 'border-amber-500/40 bg-amber-500/[0.03]' : '' ?>">
                        <div class="flex items-center space-x-2.5 sm:space-x-3 min-w-0">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-sm sm:text-lg flex-shrink-0">
                                <i class="<?= $iconClass ?>"></i>
                            </div>
                            <div class="truncate">
                                <div class="flex items-center space-x-1.5">
                                    <span class="text-[11px] sm:text-xs font-black text-white uppercase truncate"><?= htmlspecialchars($t['platform'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php if ($isVipTask): ?>
                                        <span class="px-1.5 py-0.2 rounded bg-amber-500/20 text-amber-300 font-mono text-[8px] sm:text-[9px] font-bold">VIP</span>
                                    <?php endif; ?>
                                </div>
                                <span class="text-[10px] sm:text-[11px] text-slate-400 capitalize block truncate"><?= htmlspecialchars($t['task_type'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2 sm:space-x-2.5 flex-shrink-0">
                            <div class="text-right">
                                <div class="text-[11px] sm:text-sm font-black text-amber-300 font-mono">+<?= $cpc ?> 🪙</div>
                                <span class="text-[8px] sm:text-[9px] text-emerald-400 font-mono font-bold block">+<?= $netReward ?> bersih</span>
                            </div>
                            <a href="index.php?page=tasks" class="px-2.5 py-1.5 sm:px-3.5 sm:py-2 rounded-lg sm:rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-[10px] sm:text-xs flex items-center space-x-1 transition-all shadow-sm active:scale-95">
                                <span>Buka</span>
                                <i class="fa-solid fa-chevron-right text-[8px] sm:text-[9px]"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- 4. PANDUAN KERJA & SISTEM PROTEKSI EKONOMI KOIN (ACCORDION / CLEAN CARDS) -->
    <div class="glass-card rounded-xl sm:rounded-2xl p-3.5 sm:p-4 border border-white/[0.08] space-y-2 sm:space-y-2.5">
        <div class="flex items-center justify-between pb-2 border-b border-white/[0.06]">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-shield-halved text-indigo-400 text-xs"></i>
                <h2 class="font-display font-bold text-xs sm:text-sm text-white">Sistem Proteksi & Anti-Kecurangan</h2>
            </div>
            <span class="text-[9px] text-emerald-400 font-bold bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">
                100% Organik
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs text-slate-300 pt-0.5">
            <div class="p-2.5 rounded-xl bg-dark-950/70 border border-white/[0.05]">
                <div class="font-bold text-slate-200 text-[11px] flex items-center space-x-1.5 mb-1">
                    <i class="fa-solid fa-coins text-amber-400 text-[10px]"></i>
                    <span>Sistem 1 Dompet Koin Murni</span>
                </div>
                <p class="text-[10px] text-slate-400 leading-relaxed">
                    Semua koin hasil pengerjaan tugas & top-up masuk ke satu saldo koin utama untuk memasang kampanye promosi Anda.
                </p>
            </div>

            <div class="p-2.5 rounded-xl bg-dark-950/70 border border-white/[0.05]">
                <div class="font-bold text-slate-200 text-[11px] flex items-center space-x-1.5 mb-1">
                    <i class="fa-solid fa-gavel text-amber-400 text-[10px]"></i>
                    <span>Sanksi Denda 3x Lipat</span>
                </div>
                <p class="text-[10px] text-slate-400 leading-relaxed">
                    Pengerja yang terbukti unsub/unfollow akan didenda 3x lipat (saldo minus/hutang) dan koin Anda dikembalikan.
                </p>
            </div>

            <div class="p-2.5 rounded-xl bg-dark-950/70 border border-white/[0.05]">
                <div class="font-bold text-slate-200 text-[11px] flex items-center space-x-1.5 mb-1">
                    <i class="fa-solid fa-shield-virus text-indigo-400 text-[10px]"></i>
                    <span>Sistem Afiliasi Anti-Tuyul</span>
                </div>
                <p class="text-[10px] text-slate-400 leading-relaxed">
                    Teman referral wajib memiliki streak keaktifan minimal 10 hari berturut-turut untuk dinyatakan LULUS kualifikasi bounty bonus.
                </p>
            </div>
        </div>
    </div>

    <!-- 5. MUTASI TERAKHIR -->
    <div class="glass-card rounded-xl sm:rounded-2xl p-3.5 sm:p-4 border border-white/[0.08]">
        <div class="flex items-center justify-between mb-2 sm:mb-2.5">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-receipt text-indigo-400 text-xs"></i>
                <h2 class="font-display font-bold text-xs sm:text-sm text-white">Mutasi Koin Terbaru</h2>
            </div>
            <a href="index.php?page=shop" class="text-[10px] sm:text-[11px] text-indigo-400 hover:underline font-bold">
                Toko Koin &rarr;
            </a>
        </div>

        <?php if (empty($recent_transactions)): ?>
            <div class="text-center py-6 text-slate-500 text-xs">
                Belum ada transaksi recorded.
            </div>
        <?php else: ?>
            <div class="divide-y divide-white/[0.04]">
                <?php foreach ($recent_transactions as $tx): ?>
                    <div class="py-2 sm:py-2.5 flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-2 sm:space-x-2.5 truncate pr-2">
                            <span class="px-1.5 sm:px-2 py-0.5 rounded text-[8px] sm:text-[9px] font-bold font-mono uppercase bg-white/[0.06] text-slate-300">
                                <?= htmlspecialchars($tx['type'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <span class="text-slate-300 truncate font-medium text-[10px] sm:text-[11px]"><?= htmlspecialchars($tx['description'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="font-black font-mono text-[11px] sm:text-xs <?= ((int)$tx['amount'] >= 0) ? 'text-emerald-400' : 'text-rose-400' ?>">
                                <?= ((int)$tx['amount'] >= 0 ? '+' : '') . number_format($tx['amount']) ?> 🪙
                            </span>
                            <span class="text-[9px] sm:text-[10px] text-slate-400 block font-mono"><?= date('d M, H:i', strtotime($tx['created_at'])) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>
