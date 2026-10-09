<div class="space-y-6">

    <!-- Header & Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="index.php?page=admin" class="hover:text-amber-400 transition-colors">Admin</a>
                <span>/</span>
                <span class="text-white font-medium">Laporan Pelanggaran</span>
            </div>
            <h2 class="font-display font-black text-xl sm:text-3xl text-white flex items-center space-x-2.5">
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-xs sm:text-sm border border-rose-500/30">
                    <i class="fa-solid fa-gavel"></i>
                </span>
                <span>Validasi Kecurangan (Anti-Unfollow)</span>
            </h2>
            <p class="text-[11px] sm:text-sm text-slate-400 mt-1">
                Tegakkan disiplin ekosistem: Sanksi denda <strong class="text-rose-400">3x lipat CPC</strong> (saldo minus/hutang) & kembalikan koin ke pelapor.
            </p>
        </div>

        <!-- Status Filter Tabs -->
        <div class="flex items-center space-x-1.5 bg-slate-900/90 p-1.5 rounded-2xl border border-slate-800 text-xs shadow-sm overflow-x-auto">
            <a href="index.php?page=admin&action=reports&status=all" class="px-3 sm:px-3.5 py-1.5 rounded-xl font-bold transition-all <?= ($selected_status === 'all' || empty($selected_status)) ? 'bg-amber-500 text-slate-950 shadow-sm' : 'text-slate-400 hover:text-white' ?>">
                Semua
            </a>
            <a href="index.php?page=admin&action=reports&status=pending" class="px-3 sm:px-3.5 py-1.5 rounded-xl font-bold transition-all <?= ($selected_status === 'pending') ? 'bg-amber-500 text-slate-950 shadow-sm' : 'text-slate-400 hover:text-white' ?>">
                Pending
            </a>
            <a href="index.php?page=admin&action=reports&status=didenda" class="px-3 sm:px-3.5 py-1.5 rounded-xl font-bold transition-all <?= ($selected_status === 'didenda') ? 'bg-amber-500 text-slate-950 shadow-sm' : 'text-slate-400 hover:text-white' ?>">
                Didenda 3x
            </a>
            <a href="index.php?page=admin&action=reports&status=ditolak" class="px-3 sm:px-3.5 py-1.5 rounded-xl font-bold transition-all <?= ($selected_status === 'ditolak') ? 'bg-amber-500 text-slate-950 shadow-sm' : 'text-slate-400 hover:text-white' ?>">
                Ditolak
            </a>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="glass-card bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-3.5 sm:p-7 shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-3">ID</th>
                        <th class="py-3 px-3">Pelapor (Pembuat Tugas)</th>
                        <th class="py-3 px-3">Pelanggar (Pengklik)</th>
                        <th class="py-3 px-3">Target Kampanye</th>
                        <th class="py-3 px-3">CPC & Nilai Denda (3x)</th>
                        <th class="py-3 px-3">Alasan Laporan</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3 text-right">Aksi Validasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($reports)): ?>
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-shield-check text-4xl mb-2 block text-slate-600"></i>
                                Tidak ada laporan kecurangan yang ditemukan untuk filter ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reports as $r): 
                            $cpc = (int)$r['cpc'];
                            $penalty = $cpc * 3;
                        ?>
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="py-3 px-3 text-slate-500 font-mono">#<?= $r['id'] ?></td>
                                <td class="py-3 px-3">
                                    <strong class="text-white text-xs block"><?= htmlspecialchars($r['reporter_username'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <span class="text-[10px] text-emerald-400 font-mono font-bold block">+<?= $cpc ?> 🪙 (Pengembalian)</span>
                                </td>
                                <td class="py-3 px-3">
                                    <strong class="text-rose-400 text-xs block"><?= htmlspecialchars($r['reported_username'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <span class="text-[10px] text-slate-400 font-mono block">Saldo: <?= number_format($r['reported_coins']) ?> 🪙</span>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="font-bold text-white uppercase text-[10px] block"><?= htmlspecialchars($r['platform'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($r['task_type'], ENT_QUOTES, 'UTF-8') ?>)</span>
                                    <a href="<?= htmlspecialchars($r['task_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="text-indigo-400 hover:underline text-[10px] truncate max-w-[130px] block font-mono">
                                        <?= htmlspecialchars($r['task_url'], ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="text-slate-400 text-[11px]">CPC: <?= $cpc ?> 🪙</div>
                                    <strong class="text-rose-400 font-black font-mono block">Denda: -<?= $penalty ?> 🪙</strong>
                                </td>
                                <td class="py-3 px-3 text-slate-300 max-w-[160px]">
                                    <div class="truncate text-[11px]" title="<?= htmlspecialchars($r['reason'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($r['reason'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <?php if (!empty($r['notes'])): ?>
                                        <div class="text-[10px] text-slate-500 italic mt-0.5"><?= htmlspecialchars($r['notes'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-3">
                                    <?php if ($r['status'] === 'pending'): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                            <i class="fa-solid fa-clock mr-1 text-[9px]"></i>Pending
                                        </span>
                                    <?php elseif ($r['status'] === 'didenda'): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                            <i class="fa-solid fa-gavel mr-1 text-[9px]"></i>Didenda 3x
                                        </span>
                                    <?php elseif ($r['status'] === 'ditolak'): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-medium bg-slate-800 text-slate-400">
                                            Ditolak
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <?php if ($r['status'] === 'pending'): ?>
                                        <div class="flex items-center justify-end space-x-1.5">
                                            <!-- Tombol Validasi Sanksi 3x -->
                                            <form action="index.php?page=admin&action=penalize" method="POST" onsubmit="return confirm('Apakah Anda yakin laporan ini valid? Pelanggar akan didenda 3x lipat (-<?= $penalty ?> Koin) dan pelapor dikembalikan +<?= $cpc ?> Koin!');">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                <input type="hidden" name="report_id" value="<?= $r['id'] ?>">
                                                <input type="hidden" name="notes" value="Laporan valid terbukti unfollow. Dikenakan denda 3x lipat.">
                                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-black text-xs transition-all shadow-md shadow-rose-600/30 active:scale-95">
                                                    <i class="fa-solid fa-gavel mr-1 text-[10px]"></i>Denda 3x
                                                </button>
                                            </form>

                                            <!-- Tombol Tolak Laporan -->
                                            <form action="index.php?page=admin&action=reject_report" method="POST" onsubmit="return confirm('Tolak laporan kecurangan ini?');">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                <input type="hidden" name="report_id" value="<?= $r['id'] ?>">
                                                <input type="hidden" name="notes" value="Bukti tidak mencukupi / laporan tidak valid.">
                                                <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-xs font-semibold transition-colors">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-slate-500 text-[11px] font-mono">Tervalidasi</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
