<?php
/**
 * PAKAINI - Social Media Exchange Platform
 * Halaman Beli Koin (Top-Up) & VIP Sultan (topup.php)
 * Versi Promo Diskon 50% & Taktik Harga Coret (Aturan 3)
 * - Paket Pemula: Asli Rp 50.000 -> Promo Rp 25.000 (5.000 Koin)
 * - Paket Menengah: Asli Rp 100.000 -> Promo Rp 50.000 (10.000 Koin)
 * - Paket Sultan: Asli Rp 200.000 -> Promo Rp 100.000 (22.000 Koin) + Badge "Terlaris & Bonus Promo"
 * - Tombol "Beli Sekarang" mengarahkan langsung ke WhatsApp Admin dengan format pesanan lengkap.
 */

require_once __DIR__ . '/config.php';

// Wajib Login
$user = require_login($pdo);
$pageTitle = 'Beli Koin Promosi & Upgrade VIP';

$adminWa = ADMIN_WHATSAPP;
$username = $user['username'];

// Format Pesan WhatsApp Prefilled Otomatis (Aturan 3)
$waMsgPemula = urlencode("Halo Admin, saya ingin membeli Paket Pemula 5.000 Koin (Harga Promo Rp 25.000) dengan username {$username}");
$waLinkPemula = "https://wa.me/{$adminWa}?text={$waMsgPemula}";

$waMsgMenengah = urlencode("Halo Admin, saya ingin membeli Paket Menengah 10.000 Koin (Harga Promo Rp 50.000) dengan username {$username}");
$waLinkMenengah = "https://wa.me/{$adminWa}?text={$waMsgMenengah}";

$waMsgSultan = urlencode("Halo Admin, saya ingin membeli Paket Sultan 22.000 Koin (Harga Promo Rp 100.000) dengan username {$username}");
$waLinkSultan = "https://wa.me/{$adminWa}?text={$waMsgSultan}";

$waMsgVip = urlencode("Halo Admin, saya ingin berlangganan VIP Sultan 1 Bulan (Rp 30.000) dengan username {$username}");
$waLinkVip = "https://wa.me/{$adminWa}?text={$waMsgVip}";

require_once __DIR__ . '/navbar.php';
?>

<!-- Header Toko Beli Koin & Banner Diskon 50% -->
<div class="mb-8 text-center max-w-3xl mx-auto">
    <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-white/[0.06] border border-white/10 text-white text-xs font-bold uppercase tracking-wider mb-3 shadow-md">
        <span>🔥 FLASH SALE HARI INI • SEMUA PAKET DISKON 50%</span>
    </div>
    
    <h1 class="text-2xl sm:text-4xl font-display font-black text-white tracking-tight">
        Beli Koin & Percepat Kampanye Anda
    </h1>
    <p class="text-xs sm:text-sm text-slate-400 mt-2 leading-relaxed">
        Pilih paket koin promo dengan harga coret terbaik untuk menambah subscriber YouTube, follower TikTok, dan follower Instagram secara instan.
    </p>

    <!-- Info Saldo Koin Pengguna -->
    <div class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-2xl bg-[#111111] border border-white/[0.08] shadow-md">
        <span class="text-xs text-slate-400">Saldo Koin Anda Saat Ini:</span>
        <span class="text-base font-black text-white font-mono">🪙 <?= number_format($user['coins']) ?> Koin</span>
    </div>
</div>

<!-- ============================================================================== -->
<!-- GRID 3 KARTU PAKET KOIN UTAMA DENGAN TAKTIK HARGA CORET (Aturan 3)              -->
<!-- ============================================================================== -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

    <!-- 1. PAKET PEMULA (Rp 50.000 -> Promo Rp 25.000 / 5.000 Koin) -->
    <div class="glass-card rounded-3xl p-6 border border-white/[0.08] hover:border-white/20 bg-[#111111] transition-all hover:scale-[1.02] flex flex-col justify-between relative group shadow-xl">
        
        <!-- Badge Diskon 50% -->
        <div class="absolute -top-3.5 right-6 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-white font-display font-black text-[10px] uppercase tracking-wider shadow-md">
            DISKON 50%
        </div>

        <div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Level Pemula</span>
                <div class="w-10 h-10 rounded-2xl bg-white/[0.05] border border-white/10 text-slate-300 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-seedling"></i>
                </div>
            </div>

            <h3 class="font-display font-extrabold text-xl text-white">Paket Pemula</h3>
            <p class="text-xs text-slate-400 mt-0.5">Sangat pas untuk akun yang baru mulai promosi.</p>

            <!-- Koin yang didapat -->
            <div class="mt-4 p-3 rounded-2xl bg-black/50 border border-white/[0.06] flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-semibold">Total Perolehan:</span>
                    <div class="text-xl font-mono font-black text-white flex items-center gap-1.5">
                        <span>🪙</span>
                        <span>5.000 Koin</span>
                    </div>
                </div>
                <span class="px-2 py-1 rounded-lg bg-white/[0.08] text-slate-300 text-[10px] font-extrabold border border-white/10">
                    5.000 Koin
                </span>
            </div>

            <!-- Taktik Harga Coret -->
            <div class="my-5">
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-slate-400">Harga Asli:</span>
                    <span class="line-through text-gray-400 text-sm sm:text-base font-bold">Rp 50.000</span>
                    <span class="px-2 py-0.5 rounded-full bg-white/[0.08] text-slate-300 text-[10px] font-black uppercase">Hemat Rp 25rb</span>
                </div>
                <div class="mt-1 flex items-baseline space-x-1">
                    <span class="text-3xl sm:text-4xl font-display font-black text-white tracking-tight">Rp 25.000</span>
                    <span class="text-xs text-slate-400 font-semibold">/ paket promo</span>
                </div>
            </div>

            <!-- Manfaat & Estimasi Tugas -->
            <div class="space-y-2.5 text-xs text-slate-300 pt-4 border-t border-white/[0.08]">
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-circle-check text-slate-400 text-xs shrink-0"></i>
                    <span><strong>5.000 Koin</strong> Saldo Promosi Murni</span>
                </div>
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-circle-check text-slate-400 text-xs shrink-0"></i>
                    <span>Hingga <strong>50 Subs YouTube</strong> (100 Koin/klik)</span>
                </div>
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-circle-check text-slate-400 text-xs shrink-0"></i>
                    <span>Hingga <strong>100 Followers TikTok/IG</strong> (50 Koin/klik)</span>
                </div>
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-circle-check text-slate-400 text-xs shrink-0"></i>
                    <span>Aktivasi kilat WhatsApp 1-3 menit</span>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <a href="<?= $waLinkPemula ?>" target="_blank"
                class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-display font-bold text-xs transition-all flex items-center justify-center space-x-2 shadow-lg shadow-indigo-500/25 hover:shadow-[0_0_20px_rgba(139,92,246,0.4)] hover:scale-[1.02]">
                <i class="fa-brands fa-whatsapp text-base"></i>
                <span>Beli Sekarang (Rp 25.000)</span>
            </a>
        </div>
    </div>

    <!-- 2. PAKET MENENGAH (Rp 100.000 -> Promo Rp 50.000 / 10.000 Koin) -->
    <div class="glass-card rounded-3xl p-6 border border-white/[0.08] hover:border-white/20 bg-[#111111] transition-all hover:scale-[1.02] flex flex-col justify-between relative group shadow-xl">
        
        <!-- Badge Diskon 50% -->
        <div class="absolute -top-3.5 right-6 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-white font-display font-black text-[10px] uppercase tracking-wider shadow-md">
            DISKON 50%
        </div>

        <div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kreator Aktif</span>
                <div class="w-10 h-10 rounded-2xl bg-white/[0.05] border border-white/10 text-slate-300 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-bolt"></i>
                </div>
            </div>

            <h3 class="font-display font-extrabold text-xl text-white">Paket Menengah</h3>
            <p class="text-xs text-slate-400 mt-0.5">Pilihan favorit untuk peningkatan followers cepat.</p>

            <!-- Koin yang didapat -->
            <div class="mt-4 p-3 rounded-2xl bg-black/50 border border-white/[0.06] flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-semibold">Total Perolehan:</span>
                    <div class="text-xl font-mono font-black text-white flex items-center gap-1.5">
                        <span>🪙</span>
                        <span>10.000 Koin</span>
                    </div>
                </div>
                <span class="px-2 py-1 rounded-lg bg-white/[0.08] text-slate-300 text-[10px] font-extrabold border border-white/10">
                    10.000 Koin
                </span>
            </div>

            <!-- Taktik Harga Coret -->
            <div class="my-5">
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-slate-400">Harga Asli:</span>
                    <span class="line-through text-gray-400 text-sm sm:text-base font-bold">Rp 100.000</span>
                    <span class="px-2 py-0.5 rounded-full bg-white/[0.08] text-slate-300 text-[10px] font-black uppercase">Hemat 50%</span>
                </div>
                <div class="mt-1 flex items-baseline space-x-1">
                    <span class="text-3xl sm:text-4xl font-display font-black text-white tracking-tight">Rp 50.000</span>
                    <span class="text-xs text-slate-400 font-semibold">/ paket promo</span>
                </div>
            </div>

            <!-- Manfaat & Estimasi Tugas -->
            <div class="space-y-2.5 text-xs text-slate-300 pt-4 border-t border-white/[0.08]">
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-circle-check text-slate-400 text-xs shrink-0"></i>
                    <span><strong>10.000 Koin</strong> Saldo Promosi Murni</span>
                </div>
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-circle-check text-slate-400 text-xs shrink-0"></i>
                    <span>Hingga <strong>100 Subs YouTube</strong> (100 Koin/klik)</span>
                </div>
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-circle-check text-slate-400 text-xs shrink-0"></i>
                    <span>Hingga <strong>200 Followers TikTok/IG</strong> (50 Koin/klik)</span>
                </div>
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-circle-check text-slate-400 text-xs shrink-0"></i>
                    <span>Prioritas pengerjaan oleh ribuan member</span>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <a href="<?= $waLinkMenengah ?>" target="_blank"
                class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-display font-bold text-xs transition-all flex items-center justify-center space-x-2 shadow-lg shadow-indigo-500/25 hover:shadow-[0_0_20px_rgba(139,92,246,0.4)] hover:scale-[1.02]">
                <i class="fa-brands fa-whatsapp text-emerald-300 text-base"></i>
                <span>Beli Sekarang (Rp 50.000)</span>
            </a>
        </div>
    </div>

    <!-- 3. PAKET SULTAN (Rp 200.000 -> Promo Rp 100.000 / 22.000 Koin) - TERLARIS & BONUS PROMO -->
    <div class="glass-card rounded-3xl p-6 border border-white/[0.15] hover:border-white/30 bg-[#111111] transition-all hover:scale-[1.03] flex flex-col justify-between relative group shadow-2xl">
        
        <!-- Label / Badge: Terlaris & Bonus Promo (Aturan 3) -->
        <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 text-white font-display font-bold text-[10px] uppercase tracking-wider shadow-lg flex items-center space-x-1.5">
            <i class="fa-solid fa-fire text-amber-300"></i>
            <span>Terlaris & Bonus Promo</span>
        </div>

        <div>
            <div class="flex items-center justify-between mb-3 mt-1">
                <span class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">Paling Menguntungkan</span>
                <div class="w-10 h-10 rounded-2xl bg-white/[0.05] border border-white/10 text-amber-300 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-crown text-amber-400"></i>
                </div>
            </div>

            <h3 class="font-display font-black text-xl text-white flex items-center gap-2">
                <span>Paket Sultan</span>
                <span class="px-2 py-0.5 rounded-full bg-white/[0.08] text-amber-300 border border-white/10 text-[9px] font-bold">VIP</span>
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Solusi terlengkap kreator besar & online shop.</p>

            <!-- Koin yang didapat: 22.000 Koin -->
            <div class="mt-4 p-3 rounded-2xl bg-black/50 border border-white/[0.06] flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-bold">Total Koin (+Bonus 2.000):</span>
                    <div class="text-xl font-mono font-black text-white flex items-center gap-1.5">
                        <span>🪙</span>
                        <span>22.000 Koin</span>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-lg bg-white/[0.08] text-amber-300 text-[10px] font-bold tracking-wide border border-white/10">
                    +2.000 Bonus
                </span>
            </div>

            <!-- Taktik Harga Coret -->
            <div class="my-5">
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-slate-400">Harga Asli:</span>
                    <span class="line-through text-gray-400 text-sm sm:text-base font-bold">Rp 200.000</span>
                    <span class="px-2 py-0.5 rounded-full bg-white/[0.08] text-slate-300 text-[10px] font-black uppercase">Hemat Rp 100rb</span>
                </div>
                <div class="mt-1 flex items-baseline space-x-1">
                    <span class="text-3xl sm:text-4xl font-display font-black text-white tracking-tight">Rp 100.000</span>
                    <span class="text-xs text-slate-400 font-semibold">/ 22.000 Koin</span>
                </div>
            </div>

            <!-- Manfaat & Estimasi Tugas -->
            <div class="space-y-2.5 text-xs text-slate-300 pt-4 border-t border-white/[0.08]">
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-circle-check text-slate-400 text-xs shrink-0"></i>
                    <span><strong>22.000 Koin</strong> (Ekstra Bonus 2.000 Koin)</span>
                </div>
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-circle-check text-slate-400 text-xs shrink-0"></i>
                    <span>Hingga <strong>220 Subs YouTube</strong> (100 Koin/klik)</span>
                </div>
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-circle-check text-slate-400 text-xs shrink-0"></i>
                    <span>Hingga <strong>440 Followers TikTok/IG</strong> (50 Koin/klik)</span>
                </div>
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-circle-check text-slate-400 text-xs shrink-0"></i>
                    <span><strong>Prioritas Tertinggi</strong> di Antrean Pengerjaan</span>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <a href="<?= $waLinkSultan ?>" target="_blank"
                class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-display font-bold text-xs transition-all flex items-center justify-center space-x-2 shadow-lg shadow-indigo-500/25 hover:shadow-[0_0_20px_rgba(139,92,246,0.4)] hover:scale-[1.02]">
                <i class="fa-brands fa-whatsapp text-emerald-300 text-base"></i>
                <span>Beli Sekarang (Rp 100.000)</span>
            </a>
        </div>
    </div>

</div>

<!-- ============================================================================== -->
<!-- CARD MEMBERSHIP VIP SULTAN (BYPASS LIMIT HARIAN & AKSES UNLIMITED)            -->
<!-- ============================================================================== -->
<!-- ============================================================================== -->
<!-- CARD MEMBERSHIP VIP SULTAN (BYPASS LIMIT HARIAN & AKSES UNLIMITED)            -->
<!-- ============================================================================== -->
<div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/[0.12] bg-[#111111] shadow-2xl mb-10 relative overflow-hidden">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="flex items-start sm:items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl bg-white/[0.05] text-amber-300 border border-white/10 flex items-center justify-center text-2xl shrink-0">
                <i class="fa-solid fa-crown text-amber-400"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-white/[0.08] text-slate-300 font-bold text-[10px] uppercase tracking-wider border border-white/10">Membership Eksklusif</span>
                    <span class="text-xs text-slate-300 font-bold">Hanya Rp 1.000/hari</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-display font-black text-white">
                    Upgrade ke VIP Sultan 1 Bulan
                </h3>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-xl leading-relaxed">
                    Bebas batas klik harian (Cari Koin tanpa batas 50 klik/hari), slot kampanye tidak terbatas, dan selalu tampil prioritas paling atas.
                </p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 shrink-0">
            <div class="text-left sm:text-right">
                <span class="line-through text-gray-400 text-xs font-semibold block">Harga Normal: Rp 60.000</span>
                <span class="text-2xl sm:text-3xl font-display font-black text-white">Rp 30.000</span>
                <span class="text-[11px] text-slate-400 block">/ 30 Hari Penuh</span>
            </div>
            <a href="<?= $waLinkVip ?>" target="_blank"
                class="px-6 py-3.5 rounded-2xl bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-display font-bold text-xs transition-all shadow-xl shadow-indigo-500/25 flex items-center space-x-2 shrink-0 hover:scale-[1.02]">
                <i class="fa-brands fa-whatsapp text-emerald-300 text-base"></i>
                <span>Langganan VIP Sekarang</span>
            </a>
        </div>
    </div>
</div>

<!-- ============================================================================== -->
<!-- 3 LANGKAH MUDAH PEMBELIAN KOIN                                                 -->
<!-- ============================================================================== -->
<div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/[0.08] bg-[#111111]">
    <div class="text-center max-w-xl mx-auto mb-6">
        <h3 class="font-display font-bold text-lg text-white">Cara Membeli Koin (Sangat Mudah)</h3>
        <p class="text-xs text-slate-400 mt-1">3 langkah instan untuk mengisi koin akun Anda.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10 text-center">
            <div class="w-10 h-10 mx-auto rounded-xl bg-white/[0.06] border border-white/10 text-white flex items-center justify-center font-black text-sm mb-2.5">
                1
            </div>
            <h4 class="text-xs font-bold text-white mb-1">Pilih Paket Koin Promo</h4>
            <p class="text-[11px] text-slate-400">Pilih Paket Pemula, Menengah, atau Sultan dengan diskon 50% di atas.</p>
        </div>

        <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10 text-center">
            <div class="w-10 h-10 mx-auto rounded-xl bg-white/[0.06] border border-white/10 text-white flex items-center justify-center font-black text-sm mb-2.5">
                2
            </div>
            <h4 class="text-xs font-bold text-white mb-1">Klik "Beli Sekarang"</h4>
            <p class="text-[11px] text-slate-400">WhatsApp Admin langsung terbuka dengan format pesanan akun @<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?> otomatis.</p>
        </div>

        <div class="p-4 rounded-2xl bg-white/[0.03] border border-white/10 text-center">
            <div class="w-10 h-10 mx-auto rounded-xl bg-white/[0.06] border border-white/10 text-white flex items-center justify-center font-black text-sm mb-2.5">
                3
            </div>
            <h4 class="text-xs font-bold text-white mb-1">Transfer & Koin Masuk</h4>
            <p class="text-[11px] text-slate-400">Tersedia BCA, BRI, Mandiri, QRIS, DANA, GoPay, OVO. Koin langsung masuk dalam hitungan menit!</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
