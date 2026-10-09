<div class="space-y-6">

    <!-- Header & Breadcrumbs -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="index.php?page=admin" class="hover:text-amber-400 transition-colors">Admin</a>
                <span>/</span>
                <span class="text-white font-medium">Pengaturan Sistem</span>
            </div>
            <h2 class="font-display font-black text-xl sm:text-3xl text-white flex items-center space-x-2.5">
                <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-xs sm:text-sm border border-purple-500/30">
                    <i class="fa-solid fa-sliders"></i>
                </span>
                <span>Konfigurasi & Pengaturan Web</span>
            </h2>
            <p class="text-[11px] sm:text-sm text-slate-400 mt-1">
                Ubah nomor WhatsApp admin, kurs konversi koin, tarif pajak deflasi, batas klik harian, dan banner siaran pengumuman.
            </p>
        </div>
    </div>

    <!-- Main Settings Form -->
    <form action="index.php?page=admin&action=save_settings" method="POST" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

        <!-- CARD 1: Kontak WhatsApp & Identitas -->
        <div class="glass-card bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-5 sm:p-7 shadow-xl space-y-5">
            <div class="flex items-center space-x-3 pb-3 border-b border-slate-800">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm border border-emerald-500/30">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>
                <div>
                    <h3 class="font-display font-bold text-sm text-white">Layanan WhatsApp & Identitas Platform</h3>
                    <p class="text-[11px] text-slate-400">Kontak utama untuk pemesanan koin, upgrade VIP, dan konfirmasi bukti bayar.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="wa_admin" class="block text-xs font-bold text-slate-300 mb-1.5 flex items-center space-x-1">
                        <i class="fa-brands fa-whatsapp text-emerald-400"></i>
                        <span>Nomor WhatsApp Admin *</span>
                    </label>
                    <input type="text" id="wa_admin" name="wa_admin" required
                           value="<?= htmlspecialchars($settings['wa_admin'] ?? '6285143253217', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="6285143253217"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-xs font-mono font-bold focus:outline-none focus:border-emerald-400">
                    <p class="text-[10px] text-slate-500 mt-1">Gunakan awalan 628... tanpa spasi/strip.</p>
                </div>

                <div>
                    <label for="site_name" class="block text-xs font-bold text-slate-300 mb-1.5">Nama Platform / Brand</label>
                    <input type="text" id="site_name" name="site_name" required
                           value="<?= htmlspecialchars($settings['site_name'] ?? 'PAKAINI', ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-xs focus:outline-none focus:border-purple-400">
                </div>

                <div>
                    <label for="site_tagline" class="block text-xs font-bold text-slate-300 mb-1.5">Tagline Website</label>
                    <input type="text" id="site_tagline" name="site_tagline"
                           value="<?= htmlspecialchars($settings['site_tagline'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="Platform Social Media Exchange #1"
                           class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-xs focus:outline-none focus:border-purple-400">
                </div>
            </div>

            <!-- WhatsApp Direct Test Link -->
            <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center justify-between text-xs">
                <span class="text-emerald-300 flex items-center space-x-1.5">
                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                    <span>Tautan langsung pemesanan: <strong>https://wa.me/<?= htmlspecialchars($settings['wa_admin'] ?? '6285143253217', ENT_QUOTES, 'UTF-8') ?></strong></span>
                </span>
                <a href="https://wa.me/<?= htmlspecialchars($settings['wa_admin'] ?? '6285143253217', ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" 
                   class="px-2.5 py-1 bg-emerald-500 text-slate-950 font-bold text-[10px] rounded-xl hover:bg-emerald-400 transition-colors">
                    Tes Chat
                </a>
            </div>
        </div>

        <!-- CARD 2: Kebijakan Ekonomi & Nilai Tukar Koin -->
        <div class="glass-card bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-5 sm:p-7 shadow-xl space-y-5">
            <div class="flex items-center space-x-3 pb-3 border-b border-slate-800">
                <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-sm border border-amber-500/30">
                    <i class="fa-solid fa-coins"></i>
                </div>
                <div>
                    <h3 class="font-display font-bold text-sm text-white">Nilai Tukar Koin & Kebijakan Deflasi</h3>
                    <p class="text-[11px] text-slate-400">Atur perbandingan harga koin terhadap Rupiah dan mekanisme pajak anti-inflasi.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="coin_rate_idr" class="block text-xs font-bold text-slate-300 mb-1.5">
                        Harga 1 Koin (Rupiah) *
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-xs text-slate-500 font-mono">Rp</span>
                        <input type="number" id="coin_rate_idr" name="coin_rate_idr" required min="1"
                               value="<?= (int)($settings['coin_rate_idr'] ?? 25) ?>"
                               oninput="updateCalcPreview()"
                               class="w-full pl-9 pr-3.5 py-2.5 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-xs font-mono font-bold focus:outline-none focus:border-amber-400">
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1" id="rateHelper">
                        Nilai 25 = 1.000 Koin seharga Rp 25.000
                    </p>
                </div>

                <div>
                    <label for="regular_tax_rate" class="block text-xs font-bold text-slate-300 mb-1.5">
                        Tarif Pajak Member Biasa *
                    </label>
                    <select id="regular_tax_rate" name="regular_tax_rate" class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-xs focus:outline-none focus:border-amber-400 font-medium">
                        <?php 
                        $taxes = [
                            '0.10' => '10% (Ringan)',
                            '0.15' => '15% (Sedang)',
                            '0.20' => '20% (Standar Rekomendasi)',
                            '0.25' => '25% (Ketat)',
                            '0.30' => '30% (Sangat Ketat)'
                        ];
                        $curTax = number_format((float)($settings['regular_tax_rate'] ?? 0.20), 2);
                        foreach ($taxes as $rateVal => $rateText):
                        ?>
                            <option value="<?= $rateVal ?>" <?= ($curTax === $rateVal) ? 'selected' : '' ?>>
                                <?= $rateText ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-[10px] text-slate-500 mt-1">Koin ditarik hangus dari eksekusi klaim tugas.</p>
                </div>

                <div>
                    <label for="penalty_multiplier" class="block text-xs font-bold text-slate-300 mb-1.5">
                        Kelipatan Denda Unfollow / Curang *
                    </label>
                    <div class="relative">
                        <input type="number" id="penalty_multiplier" name="penalty_multiplier" required min="1" max="10"
                               value="<?= (int)($settings['penalty_multiplier'] ?? 3) ?>"
                               class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-xs font-mono font-bold focus:outline-none focus:border-amber-400">
                        <span class="absolute right-3.5 top-2.5 text-xs text-rose-400 font-bold">x Lipat</span>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1">Denda dipotong dari saldo pelaku dan direfund ke pelapor.</p>
                </div>
            </div>
        </div>

        <!-- CARD 3: Batasan Member & Hadiah Akun Baru -->
        <div class="glass-card bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-5 sm:p-7 shadow-xl space-y-5">
            <div class="flex items-center space-x-3 pb-3 border-b border-slate-800">
                <div class="w-8 h-8 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center text-sm border border-sky-500/30">
                    <i class="fa-solid fa-user-lock"></i>
                </div>
                <div>
                    <h3 class="font-display font-bold text-sm text-white">Batasan Kuota & Hadiah Pengguna Baru</h3>
                    <p class="text-[11px] text-slate-400">Pengaturan proteksi kuota harian member biasa dan insentif pendaftaran akun baru.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label for="regular_daily_max_clicks" class="block text-xs font-bold text-slate-300 mb-1.5">Batas Klik/Hari (Biasa)</label>
                    <input type="number" id="regular_daily_max_clicks" name="regular_daily_max_clicks" required min="5" max="500"
                           value="<?= (int)($settings['regular_daily_max_clicks'] ?? 50) ?>"
                           class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-xs font-mono focus:outline-none focus:border-sky-400 font-bold">
                    <p class="text-[10px] text-slate-500 mt-1">VIP Sultan bebas tanpa batas.</p>
                </div>

                <div>
                    <label for="regular_max_active_tasks" class="block text-xs font-bold text-slate-300 mb-1.5">Batas Pasang Tugas Aktif</label>
                    <input type="number" id="regular_max_active_tasks" name="regular_max_active_tasks" required min="1" max="20"
                           value="<?= (int)($settings['regular_max_active_tasks'] ?? 2) ?>"
                           class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-xs font-mono focus:outline-none focus:border-sky-400 font-bold">
                    <p class="text-[10px] text-slate-500 mt-1">Slot kampanye simultan member biasa.</p>
                </div>

                <div>
                    <label for="new_user_bonus_coins" class="block text-xs font-bold text-slate-300 mb-1.5">Bonus Koin Pendaftaran</label>
                    <input type="number" id="new_user_bonus_coins" name="new_user_bonus_coins" required min="0" max="10000"
                           value="<?= (int)($settings['new_user_bonus_coins'] ?? 500) ?>"
                           class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-xs font-mono focus:outline-none focus:border-sky-400 font-bold">
                    <p class="text-[10px] text-slate-500 mt-1">Koin gratis saat daftar pertama kali.</p>
                </div>

                <div>
                    <label for="new_user_vip_days" class="block text-xs font-bold text-slate-300 mb-1.5">Masa Uji Coba VIP Baru</label>
                    <input type="number" id="new_user_vip_days" name="new_user_vip_days" required min="0" max="365"
                           value="<?= (int)($settings['new_user_vip_days'] ?? 3) ?>"
                           class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-xs font-mono focus:outline-none focus:border-sky-400 font-bold">
                    <p class="text-[10px] text-slate-500 mt-1">Hari VIP gratis saat pendaftaran (0 = nonaktif).</p>
                </div>
            </div>
        </div>

        <!-- CARD 4: Siaran Pengumuman Global (Broadcast Announcement) -->
        <div class="glass-card bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-5 sm:p-7 shadow-xl space-y-4">
            <div class="flex items-center space-x-3 pb-3 border-b border-slate-800">
                <div class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-sm border border-indigo-500/30">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
                <div>
                    <h3 class="font-display font-bold text-sm text-white">Siaran Pengumuman Berjalan (Banner Global)</h3>
                    <p class="text-[11px] text-slate-400">Pesan ini akan tampil di bagian atas dashboard seluruh member (kosongkan jika tidak ada pengumuman).</p>
                </div>
            </div>

            <div>
                <textarea id="broadcast_announcement" name="broadcast_announcement" rows="3"
                          placeholder="Contoh: Promo Spesial Weekend! Dapatkan bonus 20% koin untuk setiap top up via WhatsApp..."
                          class="w-full px-4 py-3 bg-slate-950 border border-slate-700/80 rounded-2xl text-white text-xs focus:outline-none focus:border-indigo-400"><?= htmlspecialchars($settings['broadcast_announcement'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
        </div>

        <!-- Submit Button Bar -->
        <div class="flex items-center justify-end space-x-3 pt-2">
            <a href="index.php?page=admin" class="px-5 py-3 rounded-2xl bg-slate-800 text-slate-300 text-xs font-bold hover:bg-slate-700 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-purple-600 via-indigo-600 to-purple-600 hover:from-purple-500 hover:to-indigo-500 text-white text-xs font-black shadow-lg shadow-purple-600/30 transition-all active:scale-95 flex items-center space-x-2">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span>Simpan Semua Pengaturan</span>
            </button>
        </div>
    </form>

</div>

<script>
    function updateCalcPreview() {
        const val = parseInt(document.getElementById('coin_rate_idr').value) || 25;
        const total1000 = (1000 * val).toLocaleString('id-ID');
        document.getElementById('rateHelper').innerText = `Nilai ${val} = 1.000 Koin seharga Rp ${total1000}`;
    }
</script>
