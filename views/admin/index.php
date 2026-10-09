<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <span>Panel Administrator</span>
                <span>/</span>
                <span class="text-white font-medium">Ringkasan Sistem</span>
            </div>
            <h2 class="font-display font-black text-xl sm:text-3xl text-white flex items-center space-x-2.5">
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-gradient-to-tr from-amber-500 to-yellow-400 flex items-center justify-center text-slate-950 shadow-lg shadow-amber-500/30">
                    <i class="fa-solid fa-chart-pie text-xs sm:text-sm"></i>
                </span>
                <span>Ringkasan Ekonomi & Kontrol PAKAINI</span>
            </h2>
            <p class="text-[11px] sm:text-sm text-slate-400 mt-1">
                Pengawasan perputaran koin virtual, penarikan pajak anti-inflasi, dan penegakan sanksi denda 3x lipat.
            </p>
        </div>

        <div class="flex items-center space-x-2">
            <span class="px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-300 text-[11px] sm:text-xs font-mono font-bold shadow-sm">
                1.000 Koin = Rp <?= number_format(1000 * COIN_RATE_IDR, 0, ',', '.') ?>
            </span>
        </div>
    </div>

    <!-- Stats Grid (4 Top Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        
        <!-- 1. Total Koin Beredar -->
        <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-800 bg-slate-900/90 shadow-xl">
            <div class="flex items-center justify-between mb-2.5 sm:mb-3">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Koin Beredar</span>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs sm:text-sm border border-amber-500/30">
                    <i class="fa-solid fa-coins"></i>
                </div>
            </div>
            <div class="text-xl sm:text-3xl font-black text-amber-300 font-display mb-1">
                <?= number_format($stats['coins_in_circulation']) ?>
            </div>
            <div class="text-[11px] text-slate-400 flex items-center justify-between pt-2 border-t border-slate-800/80">
                <span>Nilai Riil IDR:</span>
                <strong class="text-emerald-400 font-mono">Rp <?= number_format($stats['coins_in_circulation'] * COIN_RATE_IDR, 0, ',', '.') ?></strong>
            </div>
        </div>

        <!-- 2. Pajak Sistem Hangus (Anti-Inflasi) -->
        <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-800 bg-slate-900/90 shadow-xl">
            <div class="flex items-center justify-between mb-2.5 sm:mb-3">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Pajak 20% Dihanguskan</span>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-xs sm:text-sm border border-rose-500/30">
                    <i class="fa-solid fa-fire"></i>
                </div>
            </div>
            <div class="text-xl sm:text-3xl font-black text-rose-400 font-display mb-1">
                <?= number_format($stats['tax_burned']) ?> 🪙
            </div>
            <div class="text-[11px] text-slate-400 flex items-center justify-between pt-2 border-t border-slate-800/80">
                <span>Status Anti-Inflasi:</span>
                <span class="text-emerald-400 font-bold">Koin Terbakar ✓</span>
            </div>
        </div>

        <!-- 3. Hutang Minus User (Denda) -->
        <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-800 bg-slate-900/90 shadow-xl">
            <div class="flex items-center justify-between mb-2.5 sm:mb-3">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Total Hutang Minus</span>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-xs sm:text-sm border border-purple-500/30">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
            </div>
            <div class="text-xl sm:text-3xl font-black text-purple-400 font-display mb-1">
                <?= number_format($stats['user_debt']) ?> 🪙
            </div>
            <div class="text-[11px] text-slate-400 flex items-center justify-between pt-2 border-t border-slate-800/80">
                <span>Akumulasi Pelanggar:</span>
                <span class="text-rose-400 font-mono font-bold">Denda 3x CPC</span>
            </div>
        </div>

        <!-- 4. Laporan Pending -->
        <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-slate-800 bg-slate-900/90 shadow-xl relative overflow-hidden">
            <div class="flex items-center justify-between mb-2.5 sm:mb-3">
                <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Laporan Pending</span>
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-yellow-500/20 text-yellow-400 flex items-center justify-center text-xs sm:text-sm border border-yellow-500/30">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="text-xl sm:text-3xl font-black text-yellow-400 font-display mb-1">
                <?= $pending_reports ?>
            </div>
            <div class="pt-2 border-t border-slate-800/80">
                <a href="index.php?page=admin&action=reports" class="text-[11px] sm:text-xs text-yellow-400 hover:text-yellow-300 font-bold flex items-center space-x-1">
                    <span>Validasi Sanksi</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

    </div>

    <!-- Secondary Stats Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="glass-card bg-slate-950/80 p-4 rounded-2xl border border-slate-800 flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <span class="text-[11px] text-slate-400 block font-medium">Total Akun Pengguna</span>
                <strong class="text-base text-white font-display"><?= $total_users ?> Akun</strong>
            </div>
        </div>

        <div class="glass-card bg-slate-950/80 p-4 rounded-2xl border border-slate-800 flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold">
                <i class="fa-solid fa-crown"></i>
            </div>
            <div>
                <span class="text-[11px] text-slate-400 block font-medium">Member VIP Sultan</span>
                <strong class="text-base text-amber-300 font-display"><?= $total_vip ?> Pengguna</strong>
            </div>
        </div>

        <div class="glass-card bg-slate-950/80 p-4 rounded-2xl border border-slate-800 flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">
                <i class="fa-solid fa-bullhorn"></i>
            </div>
            <div>
                <span class="text-[11px] text-slate-400 block font-medium">Kampanye Aktif</span>
                <strong class="text-base text-white font-display"><?= $active_tasks ?> Tugas</strong>
            </div>
        </div>

        <div class="glass-card bg-slate-950/80 p-4 rounded-2xl border border-slate-800 flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold">
                <i class="fa-solid fa-check-double"></i>
            </div>
            <div>
                <span class="text-[11px] text-slate-400 block font-medium">Tugas Diselesaikan</span>
                <strong class="text-base text-white font-display"><?= $stats['tasks_completed'] ?> Kali</strong>
            </div>
        </div>
    </div>

    <!-- Main Navigation Hub for Admin (5 Master Modules) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4">
        <!-- 1. Kelola User -->
        <a href="index.php?page=admin&action=users" class="glass-card p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-800 hover:border-amber-400/50 transition-all flex items-start space-x-3.5 group shadow-xl">
            <div class="w-11 h-11 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-xl group-hover:scale-105 transition-transform flex-shrink-0 border border-amber-500/30">
                <i class="fa-solid fa-users-gear"></i>
            </div>
            <div>
                <h3 class="font-display font-black text-white text-sm group-hover:text-amber-300">
                    Kelola User & Saldo
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">
                    Top up koin, ubah durasi VIP, reset password, ubah role admin, atau hapus akun pengguna.
                </p>
            </div>
        </a>

        <!-- 2. Kelola Kampanye -->
        <a href="index.php?page=admin&action=tasks" class="glass-card p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-800 hover:border-indigo-400/50 transition-all flex items-start space-x-3.5 group shadow-xl">
            <div class="w-11 h-11 rounded-2xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xl group-hover:scale-105 transition-transform flex-shrink-0 border border-indigo-500/30">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <div>
                <h3 class="font-display font-black text-white text-sm group-hover:text-indigo-300">
                    Kelola Kampanye & Tugas
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">
                    Tinjau link YouTube/TikTok/IG/FB, atur status tayang (pause/aktif), sesuaikan CPC, dan hapus tugas.
                </p>
            </div>
        </a>

        <!-- 3. Validasi Kecurangan -->
        <a href="index.php?page=admin&action=reports" class="glass-card p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-800 hover:border-rose-400/50 transition-all flex items-start space-x-3.5 group shadow-xl">
            <div class="w-11 h-11 rounded-2xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-xl group-hover:scale-105 transition-transform flex-shrink-0 border border-rose-500/30">
                <i class="fa-solid fa-shield-virus"></i>
            </div>
            <div>
                <h3 class="font-display font-black text-white text-sm group-hover:text-rose-300">
                    Validasi Kecurangan
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">
                    Tegakkan sanksi denda 3x lipat pada pelaku unfollow/unsub dan kembalikan refund koin ke pelapor.
                </p>
            </div>
        </a>

        <!-- 4. Buku Kas & Mutasi -->
        <a href="index.php?page=admin&action=transactions" class="glass-card p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-800 hover:border-emerald-400/50 transition-all flex items-start space-x-3.5 group shadow-xl">
            <div class="w-11 h-11 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl group-hover:scale-105 transition-transform flex-shrink-0 border border-emerald-500/30">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <h3 class="font-display font-black text-white text-sm group-hover:text-emerald-300">
                    Buku Kas & Audit Mutasi
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">
                    Audit lengkap seluruh mutasi koin (top up, pengerjaan tugas, penarikan pajak, dan sanksi denda).
                </p>
            </div>
        </a>

        <!-- 5. Pengaturan Sistem -->
        <a href="index.php?page=admin&action=settings" class="glass-card p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-800 hover:border-purple-400/50 transition-all flex items-start space-x-3.5 group shadow-xl sm:col-span-2 lg:col-span-2">
            <div class="w-11 h-11 rounded-2xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-xl group-hover:scale-105 transition-transform flex-shrink-0 border border-purple-500/30">
                <i class="fa-solid fa-sliders"></i>
            </div>
            <div>
                <h3 class="font-display font-black text-white text-sm group-hover:text-purple-300">
                    Konfigurasi & Pengaturan Global Web
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">
                    Ubah nomor WhatsApp pesanan, kurs koin Rupiah (25rb = 1.000 koin), tarif pajak sistem, batas klik, dan banner siaran pengumuman.
                </p>
            </div>
        </a>
    </div>

    <!-- Recent Global Transactions -->
    <div class="glass-card bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-7 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-clock-rotate-left text-brand-400"></i>
                <h3 class="font-display font-black text-base text-white">Log Transaksi Koin Seluruh Sistem</h3>
            </div>
            <span class="text-xs text-slate-500">Real-time Activity Audit</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-3">Pengguna</th>
                        <th class="py-3 px-3">Tipe Mutasi</th>
                        <th class="py-3 px-3">Keterangan Transaksi</th>
                        <th class="py-3 px-3 text-right">Jumlah Koin</th>
                        <th class="py-3 px-3 text-right">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($recent_transactions)): ?>
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500">Belum ada transaksi recorded.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent_transactions as $tx): ?>
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="py-3 px-3 font-semibold text-white">
                                    <div class="flex items-center space-x-1.5">
                                        <span><?= htmlspecialchars($tx['username'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php if ((int)$tx['is_vip'] === 1): ?>
                                            <i class="fa-solid fa-crown text-amber-400 text-[10px]" title="VIP Sultan"></i>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-slate-800 text-slate-300">
                                        <?= htmlspecialchars($tx['type'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-slate-300"><?= htmlspecialchars($tx['description'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="py-3 px-3 text-right font-black font-mono <?= ((int)$tx['amount'] >= 0) ? 'text-emerald-400' : 'text-rose-400' ?>">
                                    <?= ((int)$tx['amount'] >= 0 ? '+' : '') . number_format($tx['amount']) ?> 🪙
                                </td>
                                <td class="py-3 px-3 text-right text-slate-500 text-[11px] whitespace-nowrap">
                                    <?= date('d M, H:i', strtotime($tx['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
