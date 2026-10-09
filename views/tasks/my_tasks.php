<div class="space-y-4 sm:space-y-6">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
        <div>
            <div class="flex items-center space-x-2 text-[10px] sm:text-xs text-slate-400 mb-1">
                <a href="index.php?page=dashboard" class="hover:text-brand-400 transition-colors">Dashboard</a>
                <span>/</span>
                <a href="index.php?page=tasks" class="hover:text-brand-400 transition-colors">Tugas</a>
                <span>/</span>
                <span class="text-white font-medium">Kampanye Saya</span>
            </div>
            <h2 class="font-display font-black text-xl sm:text-3xl text-white flex items-center space-x-2 sm:space-x-2.5">
                <span class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-slate-900 border border-slate-700/80 flex items-center justify-center text-indigo-400 shadow-sm text-xs sm:text-sm">
                    <i class="fa-solid fa-layer-group"></i>
                </span>
                <span>Manajemen Kampanye Saya</span>
            </h2>
            <p class="text-[11px] sm:text-sm text-slate-400 mt-0.5 sm:mt-1">
                Pantau progres penambahan follower/views dan amankan akun Anda dengan sistem Anti-Unfollow.
            </p>
        </div>

        <a href="index.php?page=tasks&action=create" class="inline-flex items-center space-x-2 px-3.5 sm:px-5 py-2 sm:py-3 rounded-xl sm:rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white text-[11px] sm:text-xs font-black shadow-md shadow-indigo-600/30 transition-all active:scale-95">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Pasang Kampanye Baru</span>
        </a>
    </div>

    <!-- Quick Stats Bar -->
    <?php
        $totalCampaigns = count($tasks);
        $totalDelivered = 0;
        $totalCoinsInvested = 0;
        foreach ($tasks as $t) {
            $totalDelivered += (int)$t['current_clicks'];
            $totalCoinsInvested += ((int)$t['current_clicks'] * (int)$t['cpc']);
        }
    ?>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-4">
        <div class="glass-card rounded-xl sm:rounded-2xl p-2.5 sm:p-4 border border-slate-800">
            <div class="flex items-center justify-between text-[10px] sm:text-xs text-slate-400 mb-1">
                <span>Total Kampanye</span>
                <i class="fa-solid fa-bullhorn text-indigo-400 text-xs sm:text-sm"></i>
            </div>
            <span class="text-base sm:text-2xl font-black text-white font-display"><?= $totalCampaigns ?></span>
        </div>

        <div class="glass-card rounded-xl sm:rounded-2xl p-2.5 sm:p-4 border border-slate-800">
            <div class="flex items-center justify-between text-[10px] sm:text-xs text-slate-400 mb-1">
                <span>Interaksi Masuk</span>
                <i class="fa-solid fa-check-double text-emerald-400 text-xs sm:text-sm"></i>
            </div>
            <span class="text-base sm:text-2xl font-black text-emerald-400 font-display"><?= number_format($totalDelivered) ?></span>
        </div>

        <div class="glass-card rounded-xl sm:rounded-2xl p-2.5 sm:p-4 border border-slate-800">
            <div class="flex items-center justify-between text-[10px] sm:text-xs text-slate-400 mb-1">
                <span>Koin Terpakai</span>
                <i class="fa-solid fa-coins text-amber-400 text-xs sm:text-sm"></i>
            </div>
            <span class="text-base sm:text-2xl font-black text-amber-300 font-display"><?= number_format($totalCoinsInvested) ?></span>
        </div>

        <div class="glass-card rounded-xl sm:rounded-2xl p-2.5 sm:p-4 border border-slate-800">
            <div class="flex items-center justify-between text-[10px] sm:text-xs text-slate-400 mb-1">
                <span>Anti-Unfollow Shield</span>
                <i class="fa-solid fa-shield-halved text-rose-400 text-xs sm:text-sm"></i>
            </div>
            <span class="text-[11px] sm:text-sm font-bold text-rose-400 font-display flex items-center mt-1">
                <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-rose-400 animate-pulse mr-1.5"></span>
                Denda 3x Aktif
            </span>
        </div>
    </div>

    <!-- Campaigns Layout -->
    <?php if (empty($tasks)): ?>
        <div class="glass-card rounded-2xl sm:rounded-3xl p-6 sm:p-12 text-center border border-slate-800 max-w-lg mx-auto shadow-2xl">
            <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl sm:rounded-3xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-2xl sm:text-3xl mx-auto mb-3 sm:mb-4 border border-indigo-500/20">
                <i class="fa-solid fa-bullhorn"></i>
            </div>
            <h3 class="font-display font-black text-lg sm:text-xl text-white mb-1.5 sm:mb-2">Belum Ada Kampanye Berjalan</h3>
            <p class="text-[11px] sm:text-xs text-slate-400 mb-4 sm:mb-6 leading-relaxed">
                Anda belum memasang kampanye media sosial. Promosikan channel YouTube, akun TikTok, postingan Instagram, atau Facebook Page Anda sekarang!
            </p>
            <a href="index.php?page=tasks&action=create" class="px-4 sm:px-6 py-2.5 sm:py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl sm:rounded-2xl text-[11px] sm:text-xs font-black inline-flex items-center space-x-2 shadow-md shadow-indigo-600/30 transition-all">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Pasang Kampanye Pertama</span>
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6">
            
            <!-- Left Column: Daftar Kampanye Saya (5 Cols on LG) -->
            <div class="lg:col-span-5 space-y-2.5 sm:space-y-3">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-[11px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">
                        Daftar Kampanye (<?= count($tasks) ?>)
                    </h3>
                    <span class="text-[10px] sm:text-[11px] text-slate-500">Klik kampanye untuk detail</span>
                </div>
                
                <?php foreach ($tasks as $task): 
                    $isSelected = ($selected_task && $selected_task['id'] == $task['id']);
                    $pct = min(100, round(($task['current_clicks'] / max(1, $task['max_clicks'])) * 100));
                    
                    // Platform badge colors
                    $platColor = 'border-slate-800';
                    $badgeBg = 'bg-slate-800 text-slate-300';
                    $icon = 'fa-solid fa-globe';
                    if ($task['platform'] === 'youtube') {
                        $platColor = 'border-red-500/30';
                        $badgeBg = 'bg-red-500/10 text-red-400 border border-red-500/30';
                        $icon = 'fa-brands fa-youtube';
                    } elseif ($task['platform'] === 'tiktok') {
                        $platColor = 'border-cyan-400/30';
                        $badgeBg = 'bg-cyan-400/10 text-cyan-300 border border-cyan-400/30';
                        $icon = 'fa-brands fa-tiktok';
                    } elseif ($task['platform'] === 'instagram') {
                        $platColor = 'border-pink-500/30';
                        $badgeBg = 'bg-pink-500/10 text-pink-400 border border-pink-500/30';
                        $icon = 'fa-brands fa-instagram';
                    } elseif ($task['platform'] === 'facebook') {
                        $platColor = 'border-blue-500/30';
                        $badgeBg = 'bg-blue-500/10 text-blue-400 border border-blue-500/30';
                        $icon = 'fa-brands fa-facebook';
                    }
                ?>
                    <div class="glass-card rounded-xl sm:rounded-2xl p-3 sm:p-5 border transition-all duration-200 <?= $isSelected ? 'border-brand-500 bg-brand-500/[0.08] shadow-xl shadow-brand-500/10 ring-2 ring-brand-500/30' : 'border-slate-800 hover:border-slate-700 bg-slate-950/60' ?>">
                        
                        <!-- Top Meta -->
                        <div class="flex items-center justify-between mb-2 sm:mb-2.5">
                            <div class="flex items-center space-x-1.5 sm:space-x-2">
                                <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-lg sm:rounded-xl text-[10px] sm:text-[11px] font-black uppercase flex items-center space-x-1 sm:space-x-1.5 <?= $badgeBg ?>">
                                    <i class="<?= $icon ?>"></i>
                                    <span><?= htmlspecialchars($task['platform'], ENT_QUOTES, 'UTF-8') ?></span>
                                </span>
                                <span class="text-[11px] sm:text-xs font-semibold text-slate-300 capitalize">
                                    <?= htmlspecialchars($task['task_type'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </div>

                            <span class="text-[10px] sm:text-xs font-black text-amber-300 px-2 sm:px-2.5 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/30 font-mono">
                                <?= $task['cpc'] ?> 🪙 / klik
                            </span>
                        </div>

                        <!-- Target URL -->
                        <div class="mb-2 sm:mb-3">
                            <a href="<?= htmlspecialchars($task['task_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="text-[11px] sm:text-xs text-slate-300 hover:text-brand-300 flex items-center space-x-1.5 truncate group" title="<?= htmlspecialchars($task['task_url'], ENT_QUOTES, 'UTF-8') ?>">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[9px] sm:text-[10px] text-slate-500 group-hover:text-brand-400 flex-shrink-0"></i>
                                <span class="truncate font-mono"><?= htmlspecialchars($task['task_url'], ENT_QUOTES, 'UTF-8') ?></span>
                            </a>
                        </div>

                        <!-- Progress Bar -->
                        <div class="space-y-1 sm:space-y-1.5 mb-3 sm:mb-4">
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-400 text-[10px] sm:text-[11px]">Progres Target Eksekusi:</span>
                                <span class="font-bold text-white font-mono text-[11px] sm:text-xs"><?= $task['current_clicks'] ?> / <?= $task['max_clicks'] ?> <span class="text-slate-500 text-[9px] sm:text-[10px]">(<?= $pct ?>%)</span></span>
                            </div>
                            <div class="w-full bg-slate-900 rounded-full h-1.5 sm:h-2 p-0.5 border border-slate-800 overflow-hidden">
                                <div class="bg-gradient-to-r from-brand-500 to-indigo-500 h-full rounded-full transition-all duration-500" style="width: <?= $pct ?>%"></div>
                            </div>
                        </div>

                        <!-- Actions & Status Row -->
                        <div class="flex items-center justify-between pt-2.5 sm:pt-3 border-t border-slate-800/80 text-xs">
                            <!-- Status Pill -->
                            <div>
                                <?php if ($task['status'] === 'active'): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse mr-1"></span>
                                        Aktif
                                    </span>
                                <?php elseif ($task['status'] === 'paused'): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                        <i class="fa-solid fa-pause text-[8px] mr-1"></i>
                                        Dijeda
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-medium bg-slate-800 text-slate-400">
                                        Selesai
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Buttons -->
                            <div class="flex items-center space-x-1.5 sm:space-x-2">
                                <a href="index.php?page=tasks&action=my_tasks&task_id=<?= $task['id'] ?>" 
                                   class="px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg sm:rounded-xl font-bold text-[10px] sm:text-xs transition-colors <?= $isSelected ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'bg-slate-900 text-slate-300 hover:text-white hover:bg-slate-800 border border-slate-800' ?>">
                                    <i class="fa-solid fa-users text-[9px] sm:text-[10px] mr-1"></i>
                                    <span>Riwayat</span>
                                </a>

                                <?php if ($task['status'] === 'active'): ?>
                                    <a href="index.php?page=tasks&action=toggle_status&id=<?= $task['id'] ?>&status=paused" 
                                       class="px-2 sm:px-2.5 py-1 sm:py-1.5 rounded-lg sm:rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border border-amber-500/30 font-semibold text-[10px] sm:text-xs transition-colors"
                                       title="Jeda sementara kampanye">
                                        <i class="fa-solid fa-pause"></i>
                                    </a>
                                <?php elseif ($task['status'] === 'paused'): ?>
                                    <a href="index.php?page=tasks&action=toggle_status&id=<?= $task['id'] ?>&status=active" 
                                       class="px-2 sm:px-2.5 py-1 sm:py-1.5 rounded-lg sm:rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-semibold text-[10px] sm:text-xs transition-colors"
                                       title="Lanjutkan kampanye">
                                        <i class="fa-solid fa-play"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Right Column: Detail Riwayat Pengerja & Sistem Anti-Unfollow (7 Cols on LG) -->
            <div class="lg:col-span-7 space-y-4">
                <?php if ($selected_task): ?>
                    <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-7 border border-slate-800 shadow-2xl space-y-3.5 sm:space-y-5">
                        
                        <!-- Selected Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-3 sm:pb-4 border-b border-slate-800 gap-2 sm:gap-3">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <h3 class="font-display font-black text-base sm:text-lg text-white">
                                        Riwayat Pengerjaan Kampanye #<?= $selected_task['id'] ?>
                                    </h3>
                                    <span class="px-2 sm:px-2.5 py-0.5 rounded-full text-[9px] sm:text-[10px] font-black uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                        <?= htmlspecialchars($selected_task['platform'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>
                                <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5 sm:mt-1 truncate max-w-md">
                                    Target: <a href="<?= htmlspecialchars($selected_task['task_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="text-indigo-400 hover:underline font-mono"><?= htmlspecialchars($selected_task['task_url'], ENT_QUOTES, 'UTF-8') ?></a>
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] sm:text-[11px] text-slate-400 block uppercase font-bold">Total Pengerja:</span>
                                <span class="text-xl sm:text-2xl font-black text-white font-display"><?= count($completions) ?> Orang</span>
                            </div>
                        </div>

                        <!-- Anti-Unfollow Notice Banner -->
                        <div class="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-gradient-to-r from-rose-500/15 via-rose-500/5 to-transparent border border-rose-500/30 flex items-start space-x-2.5 sm:space-x-3.5 text-[11px] sm:text-xs text-slate-300">
                            <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-lg sm:rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-xs sm:text-base flex-shrink-0 border border-rose-500/30 mt-0.5">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div class="flex-1">
                                <strong class="text-white text-[11px] sm:text-xs block mb-0.5">Sistem Perlindungan Anti-Unfollow:</strong>
                                Jika Anda mendapati pengerja melakukan unfollow / unsub / unlike setelah mengklaim koin Anda, laporkan pengerja tersebut. Admin akan memvalidasi dan mengenakan sanksi <strong class="text-rose-400">Denda 3x Lipat CPC</strong> (saldo pelanggar menjadi minus/hutang), dan koin asli Anda sebesar <strong class="text-emerald-400">1x CPC akan dikembalikan</strong> ke dompet Anda!
                            </div>
                        </div>

                        <!-- Completions Roster Table -->
                        <?php if (empty($completions)): ?>
                            <div class="text-center py-8 sm:py-12 text-slate-500 text-xs">
                                <i class="fa-solid fa-user-clock text-3xl sm:text-4xl mb-2 sm:mb-3 block text-slate-600"></i>
                                <span class="font-bold text-slate-400 block text-xs sm:text-sm mb-1">Belum Ada Riwayat Eksekusi</span>
                                Kampanye ini masih baru atau belum ada member yang mengeksekusinya. Silakan cek berkala.
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-950/80 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                                        <tr>
                                            <th class="py-3 px-3">Username Pengerja</th>
                                            <th class="py-3 px-3">Biaya CPC</th>
                                            <th class="py-3 px-3">Waktu Selesai</th>
                                            <th class="py-3 px-3 text-right">Tindakan Perlindungan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/60">
                                        <?php foreach ($completions as $c): ?>
                                            <tr class="hover:bg-slate-800/30 transition-colors">
                                                <td class="py-2.5 sm:py-3 px-2 sm:px-3 font-semibold text-white flex items-center space-x-2">
                                                    <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg sm:rounded-xl bg-slate-800 text-slate-300 flex items-center justify-center font-bold text-[10px] sm:text-xs uppercase border border-slate-700">
                                                        <?= substr($c['worker_username'], 0, 2) ?>
                                                    </div>
                                                    <div>
                                                        <div class="flex items-center space-x-1 text-[11px] sm:text-xs">
                                                            <span><?= htmlspecialchars($c['worker_username'], ENT_QUOTES, 'UTF-8') ?></span>
                                                            <?php if ((int)$c['worker_is_vip'] === 1): ?>
                                                                <i class="fa-solid fa-crown text-amber-400 text-[9px] sm:text-[10px]" title="VIP Sultan Member"></i>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-2.5 sm:py-3 px-2 sm:px-3 font-black text-amber-300 font-mono text-[11px] sm:text-xs">
                                                    -<?= $c['cpc'] ?> 🪙
                                                </td>
                                                <td class="py-2.5 sm:py-3 px-2 sm:px-3 text-slate-400 text-[10px] sm:text-[11px] whitespace-nowrap">
                                                    <?= date('d M Y, H:i', strtotime($c['completed_at'])) ?>
                                                </td>
                                                <td class="py-2.5 sm:py-3 px-2 sm:px-3 text-right">
                                                    <?php if (!empty($c['report_status'])): ?>
                                                        <?php if ($c['report_status'] === 'pending'): ?>
                                                            <span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[9px] sm:text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                                                <i class="fa-solid fa-clock mr-1 text-[8px] sm:text-[9px]"></i>Laporan Diproses
                                                            </span>
                                                        <?php elseif ($c['report_status'] === 'didenda'): ?>
                                                            <span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[9px] sm:text-[10px] font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                                                <i class="fa-solid fa-gavel mr-1 text-[8px] sm:text-[9px]"></i>Pelanggar Didenda 3x
                                                            </span>
                                                        <?php elseif ($c['report_status'] === 'ditolak'): ?>
                                                            <span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-[9px] sm:text-[10px] font-medium bg-slate-800 text-slate-400">
                                                                Laporan Ditolak
                                                            </span>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <!-- Tombol Lapor Curang -->
                                                        <button type="button" 
                                                                onclick="openReportModal(<?= $c['user_id'] ?>, '<?= htmlspecialchars($c['worker_username'], ENT_QUOTES, 'UTF-8') ?>', <?= $selected_task['id'] ?>, <?= $c['cpc'] ?>)"
                                                                class="px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg sm:rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 text-[10px] sm:text-xs font-bold transition-all inline-flex items-center space-x-1 sm:space-x-1.5 active:scale-95 shadow-sm">
                                                            <i class="fa-solid fa-flag text-[9px] sm:text-[10px]"></i>
                                                            <span>Lapor Curang</span>
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php else: ?>
                    <div class="glass-card rounded-2xl sm:rounded-3xl p-6 sm:p-10 text-center border border-slate-800 text-slate-400">
                        <i class="fa-solid fa-arrow-left text-xl sm:text-2xl text-slate-600 mb-2 block"></i>
                        <span class="font-bold text-white block text-xs sm:text-sm">Pilih Kampanye di Sebelah Kiri</span>
                        Klik salah satu tombol <strong>Riwayat</strong> di daftar kampanye Anda untuk memeriksa siapa saja yang telah mengeksekusi tautan Anda.
                    </div>
                <?php endif; ?>
            </div>

        </div>
    <?php endif; ?>

</div>

<!-- MODAL LAPOR CURANG (MOBILE BOTTOM SHEET / DESKTOP MODAL) -->
<div id="reportModal" class="fixed inset-0 z-50 bg-dark-950/85 backdrop-blur-md flex items-end sm:items-center justify-center p-0 sm:p-4 hidden">
    <div class="glass-card border-t sm:border border-white/[0.12] rounded-t-3xl sm:rounded-3xl p-4 sm:p-8 max-w-md w-full shadow-2xl relative mobile-sheet-enter max-h-[92vh] overflow-y-auto">
        
        <!-- Mobile Pull Handle -->
        <div class="w-12 h-1.5 rounded-full bg-slate-700/80 mx-auto mb-3 sm:hidden"></div>

        <button type="button" onclick="closeReportModal()" class="absolute top-4 sm:top-5 right-4 sm:right-5 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/[0.05] hover:bg-white/[0.1] text-slate-400 hover:text-white flex items-center justify-center transition-colors">
            <i class="fa-solid fa-xmark text-xs sm:text-sm"></i>
        </button>

        <div class="text-center mb-3 sm:mb-5">
            <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-base sm:text-xl mx-auto mb-1.5 sm:mb-2 border border-rose-500/30 shadow-lg shadow-rose-500/10">
                <i class="fa-solid fa-flag text-sm sm:text-base"></i>
            </div>
            <h2 class="font-display font-black text-base sm:text-xl text-white">Laporkan Kecurangan</h2>
            <p class="text-[10px] sm:text-xs text-slate-400 mt-0.5">Lapor jika pengerja terindikasi unfollow, unsub, atau unlike.</p>
        </div>

        <form action="index.php?page=reports&action=store" method="POST" class="space-y-3 sm:space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <input type="hidden" id="report_user_id" name="reported_user_id" value="">
            <input type="hidden" id="report_task_id" name="task_id" value="">

            <div class="p-3 sm:p-3.5 bg-slate-950 rounded-xl sm:rounded-2xl border border-slate-800 text-[11px] sm:text-xs space-y-1 sm:space-y-1.5">
                <div class="flex justify-between text-slate-400">
                    <span>User yang Dilaporkan:</span>
                    <strong id="reportUsername" class="text-white">--</strong>
                </div>
                <div class="flex justify-between text-slate-400">
                    <span>Sanksi Denda Pelanggar:</span>
                    <strong id="reportPenalty" class="text-rose-400 font-bold font-mono">Denda 3x Lipat (-- Koin)</strong>
                </div>
                <div class="flex justify-between text-slate-400">
                    <span>Koin Dikembalikan ke Anda:</span>
                    <strong id="reportRefund" class="text-emerald-400 font-bold font-mono">+-- Koin</strong>
                </div>
            </div>

            <div>
                <label for="reason" class="block text-[11px] sm:text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5 sm:mb-2">Alasan Pelaporan</label>
                <select id="reason" name="reason" class="w-full px-3 sm:px-3.5 py-2 sm:py-3 bg-slate-950 border border-slate-700 rounded-xl sm:rounded-2xl text-white text-[11px] sm:text-xs focus:outline-none focus:border-rose-500">
                    <option value="Unfollow / Unsubscribe akun setelah klaim koin">Unfollow / Unsubscribe akun setelah klaim koin</option>
                    <option value="Unlike postingan atau video setelah klaim koin">Unlike postingan atau video setelah klaim koin</option>
                    <option value="Tidak benar-benar menonton durasi 30 detik">Tidak benar-benar menonton durasi 30 detik</option>
                    <option value="Menggunakan akun bot atau tuyul">Menggunakan akun bot atau tuyul</option>
                </select>
            </div>

            <p class="text-[10px] sm:text-[11px] text-slate-400 leading-relaxed bg-slate-950/60 p-2.5 sm:p-3 rounded-lg sm:rounded-xl border border-slate-800">
                <i class="fa-solid fa-triangle-exclamation text-amber-400 mr-1"></i>
                Admin akan memvalidasi log aktivitas. Pelanggar yang terbukti curang akan dijatuhi <strong class="text-rose-400">denda 3x lipat</strong> hingga saldo koinnya menjadi minus (terhutang).
            </p>

            <div class="flex items-center space-x-2 pt-1 sm:pt-2">
                <button type="button" onclick="closeReportModal()" class="flex-1 py-2.5 sm:py-3 rounded-lg sm:rounded-xl bg-slate-800 text-slate-300 text-[11px] sm:text-xs font-bold hover:bg-slate-700 transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 sm:py-3 rounded-lg sm:rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-[11px] sm:text-xs font-black shadow-lg shadow-rose-600/30 transition-all active:scale-95">
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openReportModal(userId, username, taskId, cpc) {
        document.getElementById('report_user_id').value = userId;
        document.getElementById('report_task_id').value = taskId;
        document.getElementById('reportUsername').innerText = username;
        document.getElementById('reportPenalty').innerText = `-${(cpc * 3).toLocaleString()} Koin (3x)`;
        document.getElementById('reportRefund').innerText = `+${cpc.toLocaleString()} Koin (Penuh)`;
        document.getElementById('reportModal').classList.remove('hidden');
    }

    function closeReportModal() {
        document.getElementById('reportModal').classList.add('hidden');
    }
</script>
