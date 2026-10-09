<div class="max-w-4xl mx-auto py-4 sm:py-8">

    <!-- Coming Soon Hero Card -->
    <div class="glass-card rounded-3xl p-6 sm:p-12 border border-indigo-500/30 bg-gradient-to-b from-indigo-950/40 via-slate-900/90 to-slate-950 text-center relative overflow-hidden shadow-2xl">
        
        <!-- Background Ambient Glow -->
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 left-1/2 -translate-x-1/2 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-2xl mx-auto">
            
            <!-- Badge Status -->
            <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-indigo-500/20 border border-indigo-500/40 text-indigo-300 text-xs font-bold uppercase tracking-widest mb-6 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-indigo-400 animate-ping"></span>
                <i class="fa-solid fa-rocket text-indigo-400"></i>
                <span>Tahap Pengembangan MVP</span>
            </div>

            <!-- Ilustrasi / Ikon Menarik Sesuai Aturan 3 -->
            <div class="relative w-28 h-28 sm:w-36 sm:h-36 mx-auto mb-6 flex items-center justify-center">
                <div class="absolute inset-0 bg-gradient-to-tr from-amber-500 to-indigo-600 rounded-3xl rotate-6 opacity-30 blur-lg animate-pulse"></div>
                <div class="w-full h-full rounded-3xl bg-slate-900/90 border border-indigo-500/40 flex items-center justify-center shadow-2xl relative">
                    <div class="relative text-5xl sm:text-6xl text-amber-400 drop-shadow-[0_10px_20px_rgba(245,158,11,0.3)]">
                        🎁
                    </div>
                    <span class="absolute -top-2 -right-2 px-2.5 py-0.5 rounded-full bg-amber-500 text-slate-950 font-black text-[10px] uppercase shadow-lg border border-amber-300">
                        New
                    </span>
                </div>
            </div>

            <!-- Judul Besar Sesuai Aturan 3 -->
            <h1 class="text-2xl sm:text-4xl lg:text-5xl font-display font-black text-white tracking-tight leading-tight mb-4">
                Sistem Referral (Bawa Teman) - COMING SOON!
            </h1>

            <!-- Keterangan Kecil Sesuai Aturan 3 -->
            <p class="text-xs sm:text-base text-slate-300 leading-relaxed mb-8 max-w-xl mx-auto">
                Fitur mengundang teman berhadiah ribuan Koin Gratis sedang dalam tahap pengembangan. Nantikan pembaruannya!
            </p>

            <!-- Preview Referral Link Box -->
            <div class="glass-card rounded-2xl p-4 sm:p-5 border border-white/[0.08] bg-slate-950/70 mb-8 text-left shadow-inner">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-link text-indigo-400"></i>
                        <span>Tautan Afiliasi Unik Anda (Sneak Peek)</span>
                    </span>
                    <span class="text-[11px] text-amber-400 font-mono font-bold">@<?= htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                
                <div class="flex flex-col sm:flex-row items-stretch gap-2">
                    <input type="text" id="refLinkPreview" readonly value="<?= htmlspecialchars($referral_link ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-amber-300 font-mono focus:outline-none select-all shadow-inner">
                    <button onclick="copyRefLink()" id="copyBtn"
                        class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition-all flex items-center justify-center space-x-1.5 shrink-0 shadow-md">
                        <i class="fa-solid fa-copy text-xs"></i>
                        <span id="copyBtnText">Salin Tautan</span>
                    </button>
                </div>
                <p class="text-[10px] text-slate-500 mt-2">
                    *Tautan ini sudah dapat dibagikan. Sistem pencatatan bonus akan otomatis aktif begitu fitur resmi diluncurkan.
                </p>
            </div>

            <!-- Sneak Peek Fitur Mendatang -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 text-left mb-8">
                <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-sm mb-2">
                        🪙
                    </div>
                    <h3 class="text-xs font-bold text-white mb-0.5">Ribuan Koin Gratis</h3>
                    <p class="text-[11px] text-slate-400">Dapatkan bonus koin untuk setiap teman baru yang mendaftar melalui tautan Anda.</p>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm mb-2">
                        ⚡
                    </div>
                    <h3 class="text-xs font-bold text-white mb-0.5">Otomatis Masuk Saldo</h3>
                    <p class="text-[11px] text-slate-400">Koin bonus langsung ditambahkan ke saldo dompet koin Anda tanpa perlu menunggu verifikasi rumit.</p>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800">
                    <div class="w-8 h-8 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-sm mb-2">
                        🏆
                    </div>
                    <h3 class="text-xs font-bold text-white mb-0.5">Leaderboard Kreator</h3>
                    <p class="text-[11px] text-slate-400">Top pengundang dengan referral terbanyak akan mendapatkan hadiah eksklusif setiap bulan.</p>
                </div>
            </div>

            <!-- Tombol Aksi Alternatif -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="index.php?page=earn"
                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-400 hover:to-yellow-400 text-slate-950 font-display font-black text-xs transition-all shadow-lg shadow-amber-500/25 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-coins text-slate-950"></i>
                    <span>Kumpulkan Koin di Zona Cari Koin</span>
                </a>
                <a href="index.php?page=tasks&action=create"
                    class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition-all flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-bullhorn text-sky-400"></i>
                    <span>Pasang Kampanye Tugas</span>
                </a>
            </div>

        </div>

    </div>

</div>

<script>
function copyRefLink() {
    const input = document.getElementById('refLinkPreview');
    if (!input) return;
    input.select();
    input.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(input.value).then(() => {
        const btnText = document.getElementById('copyBtnText');
        if (btnText) {
            const originalText = btnText.innerText;
            btnText.innerText = "Tersalin! ✅";
            setTimeout(() => {
                btnText.innerText = originalText;
            }, 2000);
        }
    }).catch(err => {
        alert('Gagal menyalin link: ' + err);
    });
}
</script>
