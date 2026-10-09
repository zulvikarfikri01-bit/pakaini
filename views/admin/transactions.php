<div class="space-y-6">

    <!-- Header & Breadcrumbs -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="index.php?page=admin" class="hover:text-amber-400 transition-colors">Admin</a>
                <span>/</span>
                <span class="text-white font-medium">Buku Kas & Mutasi</span>
            </div>
            <h2 class="font-display font-black text-xl sm:text-3xl text-white flex items-center space-x-2.5">
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs sm:text-sm border border-emerald-500/30">
                    <i class="fa-solid fa-receipt"></i>
                </span>
                <span>Buku Kas & Audit Mutasi Koin</span>
            </h2>
            <p class="text-[11px] sm:text-sm text-slate-400 mt-1">
                Jurnal audit lengkap pergerakan koin sistem: pemasukan, pembayaran tugas, pemotongan pajak anti-inflasi, hingga sanksi denda 3x lipat.
            </p>
        </div>
    </div>

    <!-- Economy Stat Badges -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="glass-card bg-slate-900/80 p-3.5 sm:p-5 rounded-2xl border border-slate-800">
            <span class="text-[10px] sm:text-xs text-slate-400 uppercase tracking-wider font-semibold">Koin Beredar</span>
            <div class="text-lg sm:text-2xl font-black text-amber-300 font-display mt-1">
                <?= number_format($stats['coins_in_circulation'] ?? 0) ?> 🪙
            </div>
        </div>
        <div class="glass-card bg-rose-500/10 p-3.5 sm:p-5 rounded-2xl border border-rose-500/20">
            <span class="text-[10px] sm:text-xs text-rose-400 uppercase tracking-wider font-semibold">Hutang Sanksi Denda</span>
            <div class="text-lg sm:text-2xl font-black text-rose-300 font-display mt-1">
                -<?= number_format($stats['user_debt'] ?? 0) ?> 🪙
            </div>
        </div>
        <div class="glass-card bg-purple-500/10 p-3.5 sm:p-5 rounded-2xl border border-purple-500/20">
            <span class="text-[10px] sm:text-xs text-purple-400 uppercase tracking-wider font-semibold">Koin Dibakar Pajak</span>
            <div class="text-lg sm:text-2xl font-black text-purple-300 font-display mt-1">
                <?= number_format($stats['tax_burned'] ?? 0) ?> 🔥
            </div>
        </div>
        <div class="glass-card bg-emerald-500/10 p-3.5 sm:p-5 rounded-2xl border border-emerald-500/20">
            <span class="text-[10px] sm:text-xs text-emerald-400 uppercase tracking-wider font-semibold">Total Top Up</span>
            <div class="text-lg sm:text-2xl font-black text-emerald-300 font-display mt-1">
                +<?= number_format($stats['topups_processed'] ?? 0) ?> 🪙
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="glass-card bg-slate-900/90 border border-slate-800 rounded-2xl p-4 shadow-xl">
        <form action="index.php" method="GET" class="flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
            <input type="hidden" name="page" value="admin">
            <input type="hidden" name="action" value="transactions">

            <!-- Filter Type Dropdown -->
            <div class="flex flex-wrap items-center gap-2">
                <label for="filter_type" class="text-slate-400 font-bold uppercase text-[10px]">Jenis Mutasi:</label>
                <select id="filter_type" name="type" onchange="this.form.submit()" 
                        class="px-3 py-1.5 bg-slate-950 border border-slate-700/80 rounded-xl text-white text-xs focus:outline-none focus:border-emerald-400 font-medium">
                    <?php foreach ($types as $k => $label): ?>
                        <option value="<?= $k ?>" <?= ($selected_type === $k) ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="filter_limit" class="text-slate-400 font-bold uppercase text-[10px] ml-2">Batas:</label>
                <select id="filter_limit" name="limit" onchange="this.form.submit()" 
                        class="px-2.5 py-1.5 bg-slate-950 border border-slate-700/80 rounded-xl text-white text-xs focus:outline-none focus:border-emerald-400 font-medium">
                    <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50 Baris</option>
                    <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100 Baris</option>
                    <option value="200" <?= $limit == 200 ? 'selected' : '' ?>>200 Baris</option>
                </select>
            </div>

            <!-- Search Field -->
            <div class="flex items-center space-x-1.5">
                <div class="relative">
                    <input type="text" name="q" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Cari user / catatan..."
                        class="pl-8 pr-3 py-1.5 sm:py-2 bg-slate-950 border border-slate-700/80 rounded-xl text-white text-xs focus:outline-none focus:border-emerald-400 w-48 sm:w-64 transition-all">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-2 sm:top-2.5 text-[10px]"></i>
                </div>
                <button type="submit" class="px-3 py-1.5 sm:py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition-all shadow-md shadow-emerald-600/20">
                    Cari
                </button>
                <?php if (!empty($search) || $selected_type !== 'all'): ?>
                    <a href="index.php?page=admin&action=transactions" class="px-2.5 py-1.5 sm:py-2 bg-slate-800 text-slate-300 text-xs rounded-xl hover:bg-slate-700 transition-colors">
                        Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Ledger Table -->
    <div class="glass-card bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-3.5 sm:p-7 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-display font-bold text-xs sm:text-sm text-slate-300 uppercase tracking-wider">
                Jurnal Mutasi Koin (<?= count($transactions) ?> Catatan Ditampilkan)
            </h3>
            <span class="text-[10px] sm:text-xs text-slate-500 hidden sm:inline">Pencatatan real-time otomatis dari database</span>
        </div>

        <div class="overflow-x-auto -mx-3.5 sm:mx-0">
            <table class="w-full text-left text-xs min-w-[720px]">
                <thead class="bg-slate-950/80 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-3">ID</th>
                        <th class="py-3 px-3">Waktu</th>
                        <th class="py-3 px-3">Pengguna</th>
                        <th class="py-3 px-3">Jenis Mutasi</th>
                        <th class="py-3 px-3">Nominal Koin</th>
                        <th class="py-3 px-3">Keterangan / Referensi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                <i class="fa-solid fa-receipt text-3xl mb-2 block"></i>
                                Tidak ada catatan transaksi yang cocok dengan kriteria filter.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $tx): 
                            $amt = (int)$tx['amount'];
                            $isPos = $amt >= 0;
                            $isVip = !empty($tx['is_vip']);

                            // Type badge mapping
                            $typeBadges = [
                                'topup'          => ['bg-emerald-500/15 text-emerald-300 border-emerald-500/30', 'fa-wallet', 'Top Up Admin'],
                                'klaim_tugas'    => ['bg-sky-500/15 text-sky-300 border-sky-500/30', 'fa-circle-check', 'Klaim Reward'],
                                'bayar_tugas'    => ['bg-indigo-500/15 text-indigo-300 border-indigo-500/30', 'fa-bullhorn', 'Pasang Iklan'],
                                'pajak_sistem'   => ['bg-purple-500/15 text-purple-300 border-purple-500/30', 'fa-fire', 'Pajak Sistem'],
                                'denda'          => ['bg-rose-500/15 text-rose-300 border-rose-500/30', 'fa-triangle-exclamation', 'Denda Sanksi'],
                                'bonus_daftar'   => ['bg-amber-500/15 text-amber-300 border-amber-500/30', 'fa-gift', 'Bonus Daftar'],
                                'refund_laporan' => ['bg-teal-500/15 text-teal-300 border-teal-500/30', 'fa-rotate-left', 'Refund Laporan']
                            ];

                            $badgeInfo = $typeBadges[$tx['type']] ?? ['bg-slate-800 text-slate-300 border-slate-700', 'fa-coins', $tx['type']];
                        ?>
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="py-3 px-3 text-slate-500 font-mono text-[11px]">#<?= $tx['id'] ?></td>
                                <td class="py-3 px-3 text-slate-400 font-mono text-[11px] whitespace-nowrap">
                                    <?= date('d M Y, H:i', strtotime($tx['created_at'])) ?>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="font-bold text-white flex items-center space-x-1.5">
                                        <span><?= htmlspecialchars($tx['username'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php if ($isVip): ?>
                                            <i class="fa-solid fa-crown text-amber-400 text-[9px]" title="VIP Sultan"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-[10px] text-slate-500 font-mono"><?= htmlspecialchars($tx['email'], ENT_QUOTES, 'UTF-8') ?></div>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase border inline-flex items-center space-x-1 <?= $badgeInfo[0] ?>">
                                        <i class="fa-solid <?= $badgeInfo[1] ?> text-[9px]"></i>
                                        <span><?= $badgeInfo[2] ?></span>
                                    </span>
                                </td>
                                <td class="py-3 px-3 font-mono font-black text-xs whitespace-nowrap <?= $isPos ? 'text-emerald-400' : 'text-rose-400' ?>">
                                    <?= $isPos ? '+' : '' ?><?= number_format($amt) ?> 🪙
                                </td>
                                <td class="py-3 px-3 text-slate-300 text-xs">
                                    <div><?= htmlspecialchars($tx['description'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <?php if (!empty($tx['reference_id'])): ?>
                                        <span class="text-[10px] text-slate-500 font-mono">Ref ID: #<?= $tx['reference_id'] ?></span>
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
