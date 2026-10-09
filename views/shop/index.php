<div class="space-y-4 sm:space-y-8">

    <!-- Header -->
    <div class="text-center max-w-2xl mx-auto space-y-1.5 sm:space-y-3">
        <div class="inline-flex items-center space-x-1.5 sm:space-x-2 px-2.5 py-0.5 sm:px-3.5 sm:py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-300 text-[10px] sm:text-xs font-bold shadow-sm">
            <i class="fa-solid fa-crown text-amber-400 text-[9px] sm:text-xs"></i>
            <span>Toko Koin & Kasta VIP Sultan</span>
        </div>
        <h2 class="font-display font-black text-lg sm:text-4xl text-white tracking-tight">
            Investasi Cerdas untuk Pertumbuhan Media Sosial
        </h2>
        <p class="text-[10px] sm:text-sm text-slate-400 leading-relaxed">
            Dapatkan koin instan untuk pasang ratusan kampanye atau upgrade ke kasta <strong class="text-amber-400">VIP Sultan</strong> untuk menikmati prioritas kampanye tertinggi dan klik tugas tanpa batas.
        </p>
    </div>

    <!-- Mobile Swipe Hint -->
    <div class="md:hidden flex items-center justify-between text-[10px] text-slate-400 px-1 pt-0.5">
        <span class="font-bold text-white">Daftar Paket Pilihan:</span>
        <span class="text-[9px] text-amber-400 font-bold flex items-center space-x-1">
            <i class="fa-solid fa-arrows-left-right text-[8px]"></i>
            <span>Geser &rarr;</span>
        </span>
    </div>

    <!-- Pricing Cards Grid (Swipeable on mobile, 4-col on desktop) -->
    <div class="flex md:grid md:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-6 pt-1 sm:pt-2 overflow-x-auto md:overflow-visible no-scrollbar pb-2.5 sm:pb-4 snap-x snap-mandatory px-0.5 sm:px-0">
        <?php foreach ($packages as $key => $pkg): 
            $isVipPkg = ($key === 'vip_monthly');
            $isPopular = !empty($pkg['popular']);
        ?>
            <div class="glass-card rounded-xl sm:rounded-3xl p-3 sm:p-7 border relative flex flex-col justify-between transition-all duration-300 min-w-[76vw] max-w-[285px] md:min-w-0 md:max-w-none snap-center hover:translate-y-[-4px] <?= $isVipPkg ? 'border-amber-400/80 bg-amber-500/[0.04] shadow-2xl shadow-amber-500/10 vip-border-glow' : ($isPopular ? 'border-indigo-500/80 bg-indigo-500/[0.04] shadow-xl shadow-indigo-500/10' : 'border-slate-800 hover:border-slate-700 bg-slate-900/60') ?>">
                
                <!-- Floating Badge -->
                <?php if ($isPopular): ?>
                    <div class="absolute -top-2.5 sm:-top-3 left-1/2 transform -translate-x-1/2 px-2.5 py-0.5 sm:px-3.5 sm:py-1 rounded-full bg-indigo-600 text-white text-[8px] sm:text-[10px] font-black uppercase tracking-wider shadow-md shadow-indigo-600/30">
                        <?= $pkg['badge'] ?>
                    </div>
                <?php elseif ($isVipPkg): ?>
                    <div class="absolute -top-2.5 sm:-top-3 left-1/2 transform -translate-x-1/2 px-2.5 py-0.5 sm:px-3.5 sm:py-1 rounded-full bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 text-[8px] sm:text-[10px] font-black uppercase tracking-wider shadow-md shadow-amber-500/30 flex items-center space-x-1">
                        <i class="fa-solid fa-crown text-[7px] sm:text-[9px]"></i>
                        <span><?= $pkg['badge'] ?></span>
                    </div>
                <?php elseif (!empty($pkg['badge'])): ?>
                    <div class="absolute -top-2.5 sm:-top-3 left-1/2 transform -translate-x-1/2 px-2 py-0.2 sm:px-3 sm:py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700 text-[8px] sm:text-[10px] font-bold uppercase tracking-wider">
                        <?= $pkg['badge'] ?>
                    </div>
                <?php endif; ?>

                <div>
                    <!-- Package Name & Bonus -->
                    <div class="text-center mb-2 sm:mb-4 pt-1">
                        <h3 class="font-display font-black text-sm sm:text-xl text-white"><?= htmlspecialchars($pkg['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="text-[10px] sm:text-xs font-semibold mt-0.5 sm:mt-1 <?= $isVipPkg ? 'text-amber-300 font-bold' : ($isPopular ? 'text-indigo-300' : 'text-slate-400') ?>">
                            <?= htmlspecialchars($pkg['bonus'], ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    </div>

                    <!-- Price Block -->
                    <div class="text-center py-2 sm:py-4 my-1.5 sm:my-2 border-y border-slate-800/80 bg-slate-950/40 rounded-lg sm:rounded-2xl">
                        <div class="flex items-baseline justify-center gap-1">
                            <span class="text-slate-400 text-[10px] sm:text-xs font-bold">Rp</span>
                            <span class="text-xl sm:text-4xl font-black text-white font-display tabular-nums tracking-tight">
                                <?= number_format($pkg['price'], 0, ',', '.') ?>
                            </span>
                        </div>
                        <span class="text-[9px] sm:text-[11px] text-slate-400 block mt-0.5 sm:mt-1">
                            <?= $isVipPkg ? 'Langganan 30 Hari Penuh' : 'Top Up Sekali Bayar' ?>
                        </span>
                    </div>

                    <!-- Features Checklist -->
                    <ul class="space-y-1.5 sm:space-y-3 my-2.5 sm:my-6 text-[10px] sm:text-xs text-slate-300">
                        <?php if ($isVipPkg): ?>
                            <li class="flex items-start space-x-1.5 sm:space-x-2.5">
                                <i class="fa-solid fa-circle-check text-amber-400 text-[10px] sm:text-sm mt-0.5 flex-shrink-0"></i>
                                <span><strong class="text-white">Alokasi Koin Penuh</strong> (Masuk langsung ke Saldo Koin Anda)</span>
                            </li>
                            <li class="flex items-start space-x-1.5 sm:space-x-2.5">
                                <i class="fa-solid fa-circle-check text-amber-400 text-[10px] sm:text-sm mt-0.5 flex-shrink-0"></i>
                                <span><strong class="text-white">Klik Harian Unlimited</strong> (Bebas dari limit 50 klik)</span>
                            </li>
                            <li class="flex items-start space-x-1.5 sm:space-x-2.5">
                                <i class="fa-solid fa-circle-check text-amber-400 text-[10px] sm:text-sm mt-0.5 flex-shrink-0"></i>
                                <span><strong class="text-white">Kampanye Unlimited</strong> (Bebas dari batas 2 kampanye)</span>
                            </li>
                            <li class="flex items-start space-x-1.5 sm:space-x-2.5">
                                <i class="fa-solid fa-circle-check text-amber-400 text-[10px] sm:text-sm mt-0.5 flex-shrink-0"></i>
                                <span><strong class="text-white">Golden Glow Border</strong> & Prioritas Teratas</span>
                            </li>
                        <?php else: ?>
                            <li class="flex items-start space-x-1.5 sm:space-x-2.5">
                                <i class="fa-solid fa-circle-check text-indigo-400 text-[10px] sm:text-sm mt-0.5 flex-shrink-0"></i>
                                <span>Saldo Langsung <strong class="text-white">+<?= number_format($pkg['coins']) ?> Koin</strong></span>
                            </li>
                            <li class="flex items-start space-x-1.5 sm:space-x-2.5">
                                <i class="fa-solid fa-circle-check text-indigo-400 text-[10px] sm:text-sm mt-0.5 flex-shrink-0"></i>
                                <span>Bisa pasang hingga <strong><?= round($pkg['coins'] / 30) ?>+ interaksi</strong></span>
                            </li>
                            <li class="flex items-start space-x-1.5 sm:space-x-2.5">
                                <i class="fa-solid fa-circle-check text-indigo-400 text-[10px] sm:text-sm mt-0.5 flex-shrink-0"></i>
                                <span>Koin selamanya aktif (Tanpa kedaluwarsa)</span>
                            </li>
                            <?php if ($pkg['coins'] >= 5500): ?>
                                <li class="flex items-start space-x-1.5 sm:space-x-2.5 text-amber-300">
                                    <i class="fa-solid fa-gift text-amber-400 text-[10px] sm:text-sm mt-0.5 flex-shrink-0"></i>
                                    <span>Termasuk <strong>Bonus 500 Koin Gratis</strong>!</span>
                                </li>
                            <?php else: ?>
                                <li class="flex items-start space-x-1.5 sm:space-x-2.5">
                                    <i class="fa-solid fa-circle-check text-indigo-400 text-[10px] sm:text-sm mt-0.5 flex-shrink-0"></i>
                                    <span>Akses semua platform (YT, TikTok, IG, FB)</span>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Direct WhatsApp Order CTA -->
                <div class="pt-2 sm:pt-4 border-t border-slate-800/80">
                    <a href="<?= htmlspecialchars($pkg['wa_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank"
                       class="w-full py-2 sm:py-3.5 px-3 sm:px-4 rounded-lg sm:rounded-2xl font-black text-[10px] sm:text-xs flex items-center justify-center space-x-1.5 sm:space-x-2 transition-all shadow-md active:scale-95 <?= $isVipPkg ? 'bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-500 hover:opacity-95 text-slate-950 shadow-amber-500/20' : ($isPopular ? 'bg-indigo-600 hover:bg-indigo-500 text-white shadow-indigo-600/30' : 'bg-slate-900 hover:bg-slate-800 border border-slate-700/80 hover:border-slate-600 text-slate-200 hover:text-white') ?>">
                        <i class="fa-brands fa-whatsapp text-emerald-400 text-xs sm:text-base"></i>
                        <span>Order via WhatsApp</span>
                    </a>
                </div>

            </div>
        <?php endforeach; ?>
    </div>

    <!-- Perbandingan Kasta: Member Reguler vs VIP Sultan -->
    <div class="glass-card rounded-xl sm:rounded-3xl p-3 sm:p-8 border border-slate-800 shadow-2xl">
        <div class="text-center max-w-xl mx-auto mb-3 sm:mb-6">
            <h3 class="font-display font-black text-sm sm:text-2xl text-white">
                Perbandingan Kasta: Member Biasa vs VIP Sultan
            </h3>
            <p class="text-[10px] sm:text-xs text-slate-400 mt-0.5 sm:mt-1">
                Pahami perbedaan hak istimewa untuk memaksimalkan perolehan koin dan kecepatan promosi Anda.
            </p>
        </div>

        <!-- MOBILE CARD VIEW FOR COMPARISON (Cepat dipahami di layar smartphone) -->
        <div class="block sm:hidden space-y-1.5">
            <div class="p-2.5 rounded-lg bg-dark-950/80 border border-white/[0.06] space-y-1 text-[10px]">
                <span class="font-bold text-white block text-[10px]">Alokasi Saldo Koin</span>
                <div class="grid grid-cols-2 gap-1.5 text-[9px]">
                    <div class="p-1.5 rounded-md bg-slate-900 border border-slate-800 text-slate-300">
                        <span class="text-[7px] uppercase font-bold text-slate-400 block">Reguler:</span>
                        <span>100% Masuk Saldo Koin</span>
                    </div>
                    <div class="p-1.5 rounded-md bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 font-bold">
                        <span class="text-[7px] uppercase font-black text-amber-400 block">👑 VIP Sultan:</span>
                        <span>100% Utuh Masuk Saldo Koin</span>
                    </div>
                </div>
            </div>

            <div class="p-2.5 rounded-lg bg-dark-950/80 border border-white/[0.06] space-y-1 text-[10px]">
                <span class="font-bold text-white block text-[10px]">Batas Klik Tugas Harian</span>
                <div class="grid grid-cols-2 gap-1.5 text-[9px]">
                    <div class="p-1.5 rounded-md bg-slate-900 border border-slate-800 text-slate-400">
                        <span class="text-[7px] uppercase font-bold text-slate-400 block">Reguler:</span>
                        <span>Maks 50 Klik/hari</span>
                    </div>
                    <div class="p-1.5 rounded-md bg-amber-500/15 border border-amber-500/30 text-amber-300 font-black">
                        <span class="text-[7px] uppercase font-black text-amber-400 block">👑 VIP Sultan:</span>
                        <span>UNLIMITED (Bebas Batas)</span>
                    </div>
                </div>
            </div>

            <div class="p-2.5 rounded-lg bg-dark-950/80 border border-white/[0.06] space-y-1 text-[10px]">
                <span class="font-bold text-white block text-[10px]">Batas Kampanye Aktif</span>
                <div class="grid grid-cols-2 gap-1.5 text-[9px]">
                    <div class="p-1.5 rounded-md bg-slate-900 border border-slate-800 text-slate-400">
                        <span class="text-[7px] uppercase font-bold text-slate-400 block">Reguler:</span>
                        <span>Maks 2 Kampanye</span>
                    </div>
                    <div class="p-1.5 rounded-md bg-amber-500/15 border border-amber-500/30 text-amber-300 font-black">
                        <span class="text-[7px] uppercase font-black text-amber-400 block">👑 VIP Sultan:</span>
                        <span>UNLIMITED (Bebas Pasang)</span>
                    </div>
                </div>
            </div>

            <div class="p-2.5 rounded-lg bg-dark-950/80 border border-white/[0.06] space-y-1 text-[10px]">
                <span class="font-bold text-white block text-[10px]">Prioritas & Bingkai Visual</span>
                <div class="grid grid-cols-2 gap-1.5 text-[9px]">
                    <div class="p-1.5 rounded-md bg-slate-900 border border-slate-800 text-slate-400">
                        <span class="text-[7px] uppercase font-bold text-slate-400 block">Reguler:</span>
                        <span>Urutan Standar CPC</span>
                    </div>
                    <div class="p-1.5 rounded-md bg-amber-500/15 border border-amber-500/30 text-amber-300 font-black">
                        <span class="text-[7px] uppercase font-black text-amber-400 block">👑 VIP Sultan:</span>
                        <span>Prioritas #1 + Border Emas</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- DESKTOP TABLE VIEW (Tampil rapi di layar lebar) -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 uppercase text-[10px] tracking-wider">
                        <th class="py-3 px-4">Fitur & Batasan</th>
                        <th class="py-3 px-4">Member Reguler (Gratis)</th>
                        <th class="py-3 px-4 text-amber-300">👑 VIP Sultan (Rp 30.000/bln)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <tr class="hover:bg-slate-800/20">
                        <td class="py-3 px-4 font-semibold text-white">Alokasi Saldo Koin</td>
                        <td class="py-3 px-4 text-slate-300 font-semibold">
                            <i class="fa-solid fa-check mr-1.5 text-emerald-400"></i>100% ke Saldo Koin
                        </td>
                        <td class="py-3 px-4 text-emerald-400 font-black">
                            <i class="fa-solid fa-check mr-1.5"></i>100% Utuh ke Saldo Koin
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-800/20">
                        <td class="py-3 px-4 font-semibold text-white">Batas Klik Tugas Harian</td>
                        <td class="py-3 px-4 text-slate-400">
                            Maksimal 50 Klik per hari
                        </td>
                        <td class="py-3 px-4 text-amber-300 font-black">
                            <i class="fa-solid fa-infinity mr-1.5 text-xs"></i>UNLIMITED (Bebas Tanpa Batas)
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-800/20">
                        <td class="py-3 px-4 font-semibold text-white">Batas Kampanye Aktif</td>
                        <td class="py-3 px-4 text-slate-400">
                            Maksimal 2 Kampanye bersamaan
                        </td>
                        <td class="py-3 px-4 text-amber-300 font-black">
                            <i class="fa-solid fa-infinity mr-1.5 text-xs"></i>UNLIMITED (Pasang Sebanyaknya)
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-800/20">
                        <td class="py-3 px-4 font-semibold text-white">Posisi Urutan Kampanye</td>
                        <td class="py-3 px-4 text-slate-400">
                            Sesuai besaran CPC
                        </td>
                        <td class="py-3 px-4 text-amber-300 font-black">
                            <i class="fa-solid fa-crown mr-1.5"></i>Prioritas Teratas #1 di Semua Kategori
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-800/20">
                        <td class="py-3 px-4 font-semibold text-white">Tampilan Visual Kampanye</td>
                        <td class="py-3 px-4 text-slate-400">
                            Border Standar
                        </td>
                        <td class="py-3 px-4 text-amber-300 font-black">
                            <i class="fa-solid fa-star mr-1.5"></i>Golden Glow Border (Menarik Banyak Klik)
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Supported Payment Methods & 3-Step Manual Flow -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6">
        
        <!-- 3-Step Guide (7 Cols) -->
        <div class="lg:col-span-7 glass-card rounded-xl sm:rounded-3xl p-3.5 sm:p-7 border border-slate-800 shadow-xl space-y-2.5 sm:space-y-4">
            <div class="flex items-center space-x-2 sm:space-x-3">
                <div class="w-7 h-7 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-brand-500/20 text-brand-400 flex items-center justify-center text-xs sm:text-lg">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
                <div>
                    <h3 class="font-display font-bold text-xs sm:text-base text-white">Cara Pembelian & Konfirmasi Manual Cepat</h3>
                    <p class="text-[10px] sm:text-xs text-slate-400">Verifikasi instan langsung oleh admin dalam 1 - 5 menit.</p>
                </div>
            </div>

            <div class="space-y-2 sm:space-y-3 pt-0.5 sm:pt-2">
                <div class="p-2 sm:p-3.5 rounded-lg sm:rounded-2xl bg-slate-950/70 border border-slate-800 flex items-start space-x-2 sm:space-x-3 text-[10px] sm:text-xs">
                    <span class="w-4 h-4 sm:w-6 sm:h-6 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-[9px] sm:text-xs flex-shrink-0 mt-0.5">1</span>
                    <div>
                        <strong class="text-white block mb-0.5">Pilih Paket & Klik Order via WhatsApp</strong>
                        <p class="text-slate-400 text-[9px] sm:text-[11px]">Format pesan otomatis terisi rincian paket, nominal transfer, dan username akun Anda (<code class="text-amber-300 bg-slate-900 px-1 py-0.5 rounded"><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></code>) ke WhatsApp Admin (<strong><?= htmlspecialchars($formatted_wa_number ?? '0851-4325-3217', ENT_QUOTES, 'UTF-8') ?></strong>).</p>
                    </div>
                </div>

                <div class="p-2 sm:p-3.5 rounded-lg sm:rounded-2xl bg-slate-950/70 border border-slate-800 flex items-start space-x-2 sm:space-x-3 text-[10px] sm:text-xs">
                    <span class="w-4 h-4 sm:w-6 sm:h-6 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-[9px] sm:text-xs flex-shrink-0 mt-0.5">2</span>
                    <div>
                        <strong class="text-white block mb-0.5">Transfer Sesuai Tagihan</strong>
                        <p class="text-slate-400 text-[9px] sm:text-[11px]">Admin mengirimkan rekening transfer (BCA, Mandiri, BRI, BNI, DANA, GoPay, atau QRIS).</p>
                    </div>
                </div>

                <div class="p-2 sm:p-3.5 rounded-lg sm:rounded-2xl bg-slate-950/70 border border-slate-800 flex items-start space-x-2 sm:space-x-3 text-[10px] sm:text-xs">
                    <span class="w-4 h-4 sm:w-6 sm:h-6 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-[9px] sm:text-xs flex-shrink-0 mt-0.5">3</span>
                    <div>
                        <strong class="text-white block mb-0.5">Koin & VIP Aktif Seketika</strong>
                        <p class="text-slate-400 text-[9px] sm:text-[11px]">Setelah bukti transfer diverifikasi, saldo koin atau status VIP Anda langsung aktif!</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods Trust Showcase (5 Cols) -->
        <div class="lg:col-span-5 glass-card rounded-xl sm:rounded-3xl p-3.5 sm:p-7 border border-slate-800 shadow-xl flex flex-col justify-between">
            <div>
                <div class="flex items-center space-x-1.5 sm:space-x-2 text-[10px] sm:text-xs font-bold text-slate-300 uppercase tracking-wider mb-1 sm:mb-2">
                    <i class="fa-solid fa-credit-card text-emerald-400"></i>
                    <span>Metode Pembayaran Tersedia</span>
                </div>
                <h4 class="font-display font-bold text-xs sm:text-base text-white mb-1 sm:mb-2">Dukungan Bank & E-Wallet Lengkap</h4>
                <p class="text-[10px] sm:text-xs text-slate-400 leading-relaxed mb-2.5 sm:mb-4">
                    Kami melayani pembayaran melalui transfer bank nasional, dompet digital, dan QRIS gratis biaya admin.
                </p>

                <!-- Payment Badges Grid -->
                <div class="grid grid-cols-3 gap-1.5 sm:gap-2 text-center text-[10px] sm:text-xs">
                    <div class="p-1.5 sm:p-2.5 rounded-lg sm:rounded-xl bg-slate-950 border border-slate-800 font-bold text-white">
                        <span class="text-blue-400 block text-[8px] sm:text-[10px] font-mono">BANK</span> BCA
                    </div>
                    <div class="p-1.5 sm:p-2.5 rounded-lg sm:rounded-xl bg-slate-950 border border-slate-800 font-bold text-white">
                        <span class="text-yellow-400 block text-[8px] sm:text-[10px] font-mono">BANK</span> Mandiri
                    </div>
                    <div class="p-1.5 sm:p-2.5 rounded-lg sm:rounded-xl bg-slate-950 border border-slate-800 font-bold text-white">
                        <span class="text-blue-500 block text-[8px] sm:text-[10px] font-mono">BANK</span> BRI
                    </div>
                    <div class="p-1.5 sm:p-2.5 rounded-lg sm:rounded-xl bg-slate-950 border border-slate-800 font-bold text-white">
                        <span class="text-cyan-400 block text-[8px] sm:text-[10px] font-mono">E-WALLET</span> DANA
                    </div>
                    <div class="p-1.5 sm:p-2.5 rounded-lg sm:rounded-xl bg-slate-950 border border-slate-800 font-bold text-white">
                        <span class="text-emerald-400 block text-[8px] sm:text-[10px] font-mono">E-WALLET</span> GoPay
                    </div>
                    <div class="p-1.5 sm:p-2.5 rounded-lg sm:rounded-xl bg-slate-950 border border-slate-800 font-bold text-white">
                        <span class="text-rose-400 block text-[8px] sm:text-[10px] font-mono">INSTANT</span> QRIS
                    </div>
                </div>
            </div>

            <div class="pt-2.5 sm:pt-4 mt-2.5 sm:mt-4 border-t border-slate-800/80 flex items-center justify-between text-[10px] sm:text-xs">
                <span class="text-slate-400">Ada kendala atau pertanyaan?</span>
                <a href="https://wa.me/<?= $wa_number ?>?text=<?= urlencode("Halo Admin PAKAINI, saya [" . ($user['username'] ?? 'User') . "] butuh bantuan terkait top up koin atau langganan VIP.") ?>" target="_blank" class="text-emerald-400 hover:underline font-bold flex items-center space-x-1">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>Tanya CS (<?= htmlspecialchars($formatted_wa_number ?? '0851-4325-3217', ENT_QUOTES, 'UTF-8') ?>)</span>
                </a>
            </div>
        </div>

    </div>

</div>
