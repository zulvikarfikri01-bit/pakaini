<div class="max-w-4xl mx-auto space-y-4 sm:space-y-6">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
        <div>
            <div class="flex items-center space-x-2 text-[10px] sm:text-xs text-slate-400 mb-1">
                <a href="index.php?page=dashboard" class="hover:text-brand-400 transition-colors">Dashboard</a>
                <span>/</span>
                <a href="index.php?page=tasks" class="hover:text-brand-400 transition-colors">Tugas</a>
                <span>/</span>
                <span class="text-white font-medium">Pasang Kampanye</span>
            </div>
            <h2 class="font-display font-black text-xl sm:text-3xl text-white flex items-center space-x-2 sm:space-x-2.5">
                <span class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-gradient-to-tr from-brand-600 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-brand-500/30 text-xs sm:text-sm">
                    <i class="fa-solid fa-bullhorn"></i>
                </span>
                <span>Pasang Kampanye Media Sosial</span>
            </h2>
            <p class="text-[11px] sm:text-sm text-slate-400 mt-0.5 sm:mt-1">
                Dapatkan followers, subscriber, views, dan likes organik dari sesama pengguna nyata PAKAINI.
            </p>
        </div>

        <a href="index.php?page=tasks&action=my_tasks" class="inline-flex items-center space-x-2 px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl sm:rounded-2xl bg-slate-900/90 border border-slate-800 text-[11px] sm:text-xs font-bold text-slate-300 hover:text-white hover:border-slate-700 transition-all shadow-sm">
            <i class="fa-solid fa-layer-group text-indigo-400"></i>
            <span>Kelola Kampanye Saya</span>
            <span class="px-2 py-0.5 rounded-full bg-slate-800 text-[9px] sm:text-[10px] text-slate-300 font-mono"><?= $active_count ?></span>
        </a>
    </div>

    <!-- Stepper Visual Walkthrough (3 Langkah Mudah) -->
    <div class="glass-card rounded-2xl sm:rounded-3xl p-3 sm:p-5 border border-slate-800 shadow-xl">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 sm:gap-3">
            <div class="flex items-center space-x-2.5 sm:space-x-3 p-2.5 sm:p-3 rounded-xl sm:rounded-2xl bg-brand-500/10 border border-brand-500/30 text-[11px] sm:text-xs">
                <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg sm:rounded-xl bg-brand-600 text-white font-black flex items-center justify-center text-[10px] sm:text-xs shadow-md">
                    1
                </div>
                <div>
                    <span class="font-bold text-white block">Pilih Platform</span>
                    <span class="text-[10px] sm:text-[11px] text-brand-300">YouTube, TikTok, IG, FB</span>
                </div>
            </div>
            <div class="flex items-center space-x-2.5 sm:space-x-3 p-2.5 sm:p-3 rounded-xl sm:rounded-2xl bg-slate-950/60 border border-slate-800 text-[11px] sm:text-xs">
                <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg sm:rounded-xl bg-slate-800 text-slate-300 font-black flex items-center justify-center text-[10px] sm:text-xs">
                    2
                </div>
                <div>
                    <span class="font-bold text-slate-300 block">Tentukan Tindakan & URL</span>
                    <span class="text-[10px] sm:text-[11px] text-slate-500">Follow, Sub, Like, View</span>
                </div>
            </div>
            <div class="flex items-center space-x-2.5 sm:space-x-3 p-2.5 sm:p-3 rounded-xl sm:rounded-2xl bg-slate-950/60 border border-slate-800 text-[11px] sm:text-xs">
                <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg sm:rounded-xl bg-slate-800 text-slate-300 font-black flex items-center justify-center text-[10px] sm:text-xs">
                    3
                </div>
                <div>
                    <span class="font-bold text-slate-300 block">Atur Hadiah & Terbitkan</span>
                    <span class="text-[10px] sm:text-[11px] text-slate-500">Pay-as-you-go otomatis</span>
                </div>
            </div>
        </div>
    </div>

    <!-- VIP vs Reguler Notice -->
    <?php if (!$is_vip): ?>
        <div class="p-3.5 sm:p-5 rounded-2xl sm:rounded-3xl bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border border-amber-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 text-[11px] sm:text-xs">
            <div class="flex items-start space-x-2.5 sm:space-x-3.5">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-sm sm:text-lg flex-shrink-0 border border-amber-500/30">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <strong class="text-amber-300 text-xs sm:text-sm font-bold">Kuota Kampanye Member Reguler</strong>
                        <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-mono text-[9px] sm:text-[10px] font-bold">
                            <?= $active_count ?> / 2 Tugas Aktif
                        </span>
                    </div>
                    <p class="text-slate-300 text-[11px] sm:text-xs mt-0.5">
                        User biasa dibatasi maksimal 2 kampanye aktif secara bersamaan. Upgrade ke <strong class="text-amber-400">VIP Sultan</strong> untuk pasang kampanye <strong>UNLIMITED</strong> & dapat sorotan prioritas teratas dengan border emas!
                    </p>
                </div>
            </div>
            <a href="index.php?page=shop" class="inline-flex items-center justify-center space-x-1.5 px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl sm:rounded-2xl bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 font-extrabold text-[11px] sm:text-xs shadow-lg shadow-amber-500/20 hover:opacity-95 transition-all whitespace-nowrap active:scale-95">
                <i class="fa-solid fa-crown text-[10px] sm:text-xs"></i>
                <span>Upgrade VIP Sultan</span>
            </a>
        </div>
    <?php else: ?>
        <div class="p-3.5 sm:p-5 rounded-2xl sm:rounded-3xl bg-gradient-to-r from-amber-500/15 via-yellow-500/10 to-transparent border border-amber-500/40 flex items-center justify-between text-[11px] sm:text-xs text-amber-300 vip-border-glow">
            <div class="flex items-center space-x-2.5 sm:space-x-3.5">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-base sm:text-xl flex-shrink-0 border border-amber-500/30">
                    <i class="fa-solid fa-crown"></i>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <strong class="text-white text-xs sm:text-sm font-bold">Status VIP Sultan Aktif</strong>
                        <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-bold text-[9px] sm:text-[10px] uppercase tracking-wider">
                            Prioritas Teratas #1
                        </span>
                    </div>
                    <p class="text-amber-400/90 text-[11px] sm:text-xs mt-0.5">
                        Kampanye Anda berhak dipasang tanpa batas (Unlimited) dan otomatis mendapatkan <strong>Golden Glow Border</strong> sehingga dikerjakan paling cepat oleh member lain!
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Creation Form -->
    <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-8 border border-slate-800 shadow-2xl relative overflow-hidden">
        
        <form id="campaignForm" action="index.php?page=tasks&action=store" method="POST" class="space-y-4 sm:space-y-6">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            <!-- Hidden real inputs synced with visual selectors -->
            <input type="hidden" id="selectedPlatform" name="platform" value="youtube">
            <input type="hidden" id="selectedTaskType" name="task_type" value="subscribers">

            <!-- 1. STEP 1: PILIH PLATFORM (VISUAL INTERACTIVE CARDS) -->
            <div>
                <label class="block text-[11px] sm:text-xs font-bold text-slate-300 uppercase tracking-wider mb-2.5 sm:mb-3">
                    Langkah 1: Pilih Platform Media Sosial
                </label>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3">
                    <!-- YouTube Card -->
                    <button type="button" onclick="selectPlatform('youtube')" id="platCard_youtube"
                            class="plat-card relative text-left p-2.5 sm:p-4 rounded-xl sm:rounded-2xl border-2 transition-all duration-200 border-indigo-500/80 bg-indigo-500/10 shadow-md shadow-indigo-500/10 active:scale-95">
                        <span id="platCheck_youtube" class="plat-check absolute top-2 right-2 sm:top-2.5 sm:right-2.5 w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[9px] sm:text-[10px] font-black shadow-sm">
                            <i class="fa-solid fa-check"></i>
                        </span>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-slate-900 border border-slate-800 text-red-500 flex items-center justify-center text-base sm:text-lg mb-1.5 sm:mb-2 shadow-sm">
                            <i class="fa-brands fa-youtube"></i>
                        </div>
                        <h4 class="font-bold text-white text-xs sm:text-sm">YouTube</h4>
                        <span class="text-[10px] sm:text-[11px] text-slate-400 block mt-0.5">Sub & Views</span>
                    </button>

                    <!-- TikTok Card -->
                    <button type="button" onclick="selectPlatform('tiktok')" id="platCard_tiktok"
                            class="plat-card relative text-left p-2.5 sm:p-4 rounded-xl sm:rounded-2xl border-2 transition-all duration-200 border-slate-800 bg-slate-900/60 hover:border-slate-700 active:scale-95">
                        <span id="platCheck_tiktok" class="plat-check hidden absolute top-2 right-2 sm:top-2.5 sm:right-2.5 w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[9px] sm:text-[10px] font-black shadow-sm">
                            <i class="fa-solid fa-check"></i>
                        </span>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-slate-900 border border-slate-800 text-cyan-400 flex items-center justify-center text-base sm:text-lg mb-1.5 sm:mb-2 shadow-sm">
                            <i class="fa-brands fa-tiktok"></i>
                        </div>
                        <h4 class="font-bold text-white text-xs sm:text-sm">TikTok</h4>
                        <span class="text-[10px] sm:text-[11px] text-slate-400 block mt-0.5">Follow & Likes</span>
                    </button>

                    <!-- Instagram Card -->
                    <button type="button" onclick="selectPlatform('instagram')" id="platCard_instagram"
                            class="plat-card relative text-left p-2.5 sm:p-4 rounded-xl sm:rounded-2xl border-2 transition-all duration-200 border-slate-800 bg-slate-900/60 hover:border-slate-700 active:scale-95">
                        <span id="platCheck_instagram" class="plat-check hidden absolute top-2 right-2 sm:top-2.5 sm:right-2.5 w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[9px] sm:text-[10px] font-black shadow-sm">
                            <i class="fa-solid fa-check"></i>
                        </span>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-slate-900 border border-slate-800 text-pink-400 flex items-center justify-center text-base sm:text-lg mb-1.5 sm:mb-2 shadow-sm">
                            <i class="fa-brands fa-instagram"></i>
                        </div>
                        <h4 class="font-bold text-white text-xs sm:text-sm">Instagram</h4>
                        <span class="text-[10px] sm:text-[11px] text-slate-400 block mt-0.5">Follow & Likes</span>
                    </button>

                    <!-- Facebook Card -->
                    <button type="button" onclick="selectPlatform('facebook')" id="platCard_facebook"
                            class="plat-card relative text-left p-2.5 sm:p-4 rounded-xl sm:rounded-2xl border-2 transition-all duration-200 border-slate-800 bg-slate-900/60 hover:border-slate-700 active:scale-95">
                        <span id="platCheck_facebook" class="plat-check hidden absolute top-2 right-2 sm:top-2.5 sm:right-2.5 w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[9px] sm:text-[10px] font-black shadow-sm">
                            <i class="fa-solid fa-check"></i>
                        </span>
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-slate-900 border border-slate-800 text-blue-400 flex items-center justify-center text-base sm:text-lg mb-1.5 sm:mb-2 shadow-sm">
                            <i class="fa-brands fa-facebook"></i>
                        </div>
                        <h4 class="font-bold text-white text-xs sm:text-sm">Facebook</h4>
                        <span class="text-[10px] sm:text-[11px] text-slate-400 block mt-0.5">Page & Likes</span>
                    </button>
                </div>
            </div>

            <!-- 2. STEP 2: JENIS TINDAKAN (PILL BUTTONS) -->
            <div>
                <label class="block text-[11px] sm:text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                    Langkah 2: Pilih Jenis Tindakan yang Diinginkan
                </label>
                <div id="actionPillsContainer" class="flex flex-wrap gap-2">
                    <!-- Populated dynamically by JS -->
                </div>
            </div>

            <!-- 3. TARGET URL FIELD -->
            <div>
                <div class="flex items-center justify-between mb-1.5 sm:mb-2">
                    <label for="task_url" class="block text-[11px] sm:text-xs font-bold text-slate-300 uppercase tracking-wider">
                        Tautan Link Target (Wajib URL Valid)
                    </label>
                    <span id="urlValidationBadge" class="text-[10px] sm:text-[11px] text-slate-500">
                        Harus diawali https://
                    </span>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-link text-xs"></i>
                    </div>
                    <input type="text" id="task_url" name="task_url" required
                        class="w-full pl-9 sm:pl-10 pr-3 sm:pr-4 py-2.5 sm:py-3.5 bg-slate-950/80 border border-slate-700/80 rounded-xl sm:rounded-2xl text-white placeholder-slate-500 text-xs sm:text-sm focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all font-mono"
                        placeholder="https://www.youtube.com/@ChannelAnda"
                        oninput="validateUrlPreview(this.value)">
                </div>
                <p id="urlTip" class="text-[10px] sm:text-[11px] text-slate-500 mt-1 sm:mt-1.5 flex items-center space-x-1">
                    <i class="fa-solid fa-circle-info text-brand-400"></i>
                    <span>Masukkan URL profil, channel, atau postingan spesifik Anda. Pastikan postingan/akun bersifat publik.</span>
                </p>
            </div>

            <!-- 4. STEP 3: ANGGARAN & TARGET KLIK (CPC & MAX CLICKS DENGAN QUICK PRESET HP) -->
            <div class="pt-2 border-t border-slate-800/80">
                <label class="block text-[11px] sm:text-xs font-bold text-slate-300 uppercase tracking-wider mb-2.5 sm:mb-3">
                    Langkah 3: Tentukan Hadiah Koin & Target Eksekusi
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <!-- CPC Input -->
                    <div class="bg-slate-950/60 p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-800">
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="cpc" class="text-[11px] sm:text-xs font-semibold text-slate-300">
                                Hadiah Koin per Klik (CPC)
                            </label>
                            <span id="cpcMinBadge" class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/30 text-[9px] sm:text-[10px] font-bold">
                                Min: 150 Koin
                            </span>
                        </div>
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 pl-3 sm:pl-3.5 flex items-center pointer-events-none text-amber-400 font-bold text-xs">
                                <i class="fa-solid fa-coins"></i>
                            </div>
                            <input type="number" id="cpc" name="cpc" value="150" min="20" required oninput="calculateSimulator()"
                                class="w-full pl-9 sm:pl-10 pr-3 sm:pr-4 py-2 sm:py-3 bg-slate-900 border border-slate-700/80 rounded-lg sm:rounded-xl text-white text-sm sm:text-base focus:outline-none focus:border-amber-400 font-black">
                        </div>
                        <!-- Quick CPC Presets for Mobile Touch -->
                        <div class="flex items-center space-x-1.5 mt-2 sm:mt-2.5 overflow-x-auto no-scrollbar py-0.5">
                            <span class="text-[9px] sm:text-[10px] text-slate-500 font-bold uppercase tracking-wider mr-1">Preset:</span>
                            <button type="button" onclick="setPresetCpc(50)" class="px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg bg-slate-900 border border-slate-800 text-[10px] sm:text-[11px] text-slate-300 font-mono font-bold hover:text-amber-400 active:scale-95 transition-all">50</button>
                            <button type="button" onclick="setPresetCpc(100)" class="px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg bg-slate-900 border border-slate-800 text-[10px] sm:text-[11px] text-slate-300 font-mono font-bold hover:text-amber-400 active:scale-95 transition-all">100</button>
                            <button type="button" onclick="setPresetCpc(150)" class="px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg bg-slate-900 border border-slate-800 text-[10px] sm:text-[11px] text-slate-300 font-mono font-bold hover:text-amber-400 active:scale-95 transition-all">150</button>
                            <button type="button" onclick="setPresetCpc(200)" class="px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg bg-slate-900 border border-slate-800 text-[10px] sm:text-[11px] text-slate-300 font-mono font-bold hover:text-amber-400 active:scale-95 transition-all">200</button>
                        </div>
                    </div>

                    <!-- Max Clicks Input -->
                    <div class="bg-slate-950/60 p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-800">
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="max_clicks" class="text-[11px] sm:text-xs font-semibold text-slate-300">
                                Target Total Eksekusi
                            </label>
                            <span class="text-[9px] sm:text-[10px] text-slate-500">Min 5 Eksekusi</span>
                        </div>
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 pl-3 sm:pl-3.5 flex items-center pointer-events-none text-indigo-400 font-bold text-xs">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <input type="number" id="max_clicks" name="max_clicks" value="50" min="5" required oninput="calculateSimulator()"
                                class="w-full pl-9 sm:pl-10 pr-3 sm:pr-4 py-2 sm:py-3 bg-slate-900 border border-slate-700/80 rounded-lg sm:rounded-xl text-white text-sm sm:text-base focus:outline-none focus:border-indigo-400 font-black">
                        </div>
                        <!-- Quick Click Presets for Mobile Touch -->
                        <div class="flex items-center space-x-1.5 mt-2 sm:mt-2.5 overflow-x-auto no-scrollbar py-0.5">
                            <span class="text-[9px] sm:text-[10px] text-slate-500 font-bold uppercase tracking-wider mr-1">Preset:</span>
                            <button type="button" onclick="setPresetClicks(10)" class="px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg bg-slate-900 border border-slate-800 text-[10px] sm:text-[11px] text-slate-300 font-mono font-bold hover:text-indigo-400 active:scale-95 transition-all">10x</button>
                            <button type="button" onclick="setPresetClicks(25)" class="px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg bg-slate-900 border border-slate-800 text-[10px] sm:text-[11px] text-slate-300 font-mono font-bold hover:text-indigo-400 active:scale-95 transition-all">25x</button>
                            <button type="button" onclick="setPresetClicks(50)" class="px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg bg-slate-900 border border-slate-800 text-[10px] sm:text-[11px] text-slate-300 font-mono font-bold hover:text-indigo-400 active:scale-95 transition-all">50x</button>
                            <button type="button" onclick="setPresetClicks(100)" class="px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg bg-slate-900 border border-slate-800 text-[10px] sm:text-[11px] text-slate-300 font-mono font-bold hover:text-indigo-400 active:scale-95 transition-all">100x</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- OPSI PRIORITAS (SEMATKAN KE ATAS DENGAN BIAYA 2X LIPAT) -->
            <div class="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-amber-500/10 border-2 border-amber-500/40 text-amber-200 shadow-md">
                <label class="flex items-start space-x-3 cursor-pointer select-none">
                    <input type="checkbox" name="is_priority" id="is_priority" value="1" onchange="calculateSimulator()"
                        class="w-5 h-5 rounded mt-0.5 text-amber-500 focus:ring-amber-400 bg-slate-900 border-slate-700">
                    <div>
                        <span class="text-xs sm:text-sm font-black text-amber-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-fire text-amber-400"></i>
                            Jadikan Prioritas (Biaya 2x Lipat)
                        </span>
                        <span class="text-[11px] text-amber-200/90 block mt-0.5 leading-relaxed">
                            Tugas Anda akan disematkan di <strong>urutan paling atas #1</strong> di halaman Cari Koin sehingga langsung dikerjakan pertama kali oleh member lain. Biaya koin yang dipotong adalah 2x lipat BPK normal.
                        </span>
                    </div>
                </label>
            </div>

            <!-- LIVE BUDGET SIMULATOR & WALLET IMPACT -->
            <div class="p-3.5 sm:p-5 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-slate-950 to-slate-900 border border-slate-800 shadow-xl space-y-3 sm:space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-2.5 sm:pb-3 border-b border-slate-800 gap-2 sm:gap-3">
                    <div class="flex items-center space-x-2 text-[11px] sm:text-xs font-bold text-slate-300">
                        <i class="fa-solid fa-calculator text-brand-400"></i>
                        <span>Simulasi Anggaran & Kebutuhan Koin</span>
                    </div>
                    <div class="text-[11px] sm:text-xs">
                        <span class="text-slate-400">Saldo Koin Anda:</span>
                        <strong class="text-amber-300 ml-1 font-mono text-xs sm:text-sm"><?= number_format($user['coins']) ?> Koin</strong>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3 text-xs">
                    <div class="bg-slate-900/90 p-2.5 sm:p-3 rounded-xl sm:rounded-2xl border border-slate-800">
                        <span class="text-[10px] sm:text-[11px] text-slate-400 block mb-0.5">Total Estimasi Koin:</span>
                        <span id="simTotalCoins" class="text-sm sm:text-lg font-black text-white font-mono">7.500</span>
                        <span class="text-[9px] sm:text-[10px] text-slate-500 block">Koin Maksimal</span>
                    </div>

                    <div class="bg-slate-900/90 p-2.5 sm:p-3 rounded-xl sm:rounded-2xl border border-slate-800">
                        <span class="text-[10px] sm:text-[11px] text-slate-400 block mb-0.5">Sistem Pembayaran:</span>
                        <span id="simTotalIdr" class="text-sm sm:text-lg font-black text-amber-300 font-mono">🪙 Koin Murni</span>
                        <span class="text-[9px] sm:text-[10px] text-slate-500 block">Ekosistem Tanpa Konversi Tunai</span>
                    </div>

                    <div class="col-span-2 sm:col-span-1 bg-slate-900/90 p-2.5 sm:p-3 rounded-xl sm:rounded-2xl border border-slate-800 flex flex-col justify-center">
                        <span class="text-[10px] sm:text-[11px] text-slate-400 block mb-0.5">Status Saldo:</span>
                        <span id="simBalanceStatus" class="inline-flex items-center text-[11px] sm:text-xs font-bold text-emerald-400">
                            <i class="fa-solid fa-circle-check mr-1.5"></i> Saldo Cukup
                        </span>
                    </div>
                </div>

                <div class="p-2.5 sm:p-3 rounded-lg sm:rounded-xl bg-brand-500/10 border border-brand-500/20 text-[10px] sm:text-[11px] text-brand-200 flex items-start space-x-2">
                    <i class="fa-solid fa-shield-halved text-brand-400 mt-0.5 flex-shrink-0"></i>
                    <span>
                        <strong>Sistem Perlindungan Pay-As-You-Go:</strong> Saldo koin Anda <em>tidak</em> ditarik di muka sekaligus. Saldo hanya terpotong satu per satu setiap kali ada member yang benar-benar menyelesaikan tugas. Jika target belum tercapai, sisa koin tetap aman di dompet Anda.
                    </span>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                <?= (!$is_vip && $active_count >= 2) ? 'disabled' : '' ?>
                class="w-full py-3 sm:py-4 px-4 sm:px-6 rounded-xl sm:rounded-2xl font-black text-xs sm:text-sm flex items-center justify-center space-x-2 sm:space-x-2.5 transition-all <?= (!$is_vip && $active_count >= 2) ? 'bg-slate-800 text-slate-500 cursor-not-allowed border border-slate-700' : 'bg-gradient-to-r from-brand-600 via-indigo-600 to-brand-700 hover:opacity-95 text-white shadow-xl shadow-brand-500/30 active:scale-98' ?>">
                <i class="fa-solid fa-rocket text-xs sm:text-sm"></i>
                <span>Terbitkan Kampanye Sekarang</span>
            </button>
            
            <?php if (!$is_vip && $active_count >= 2): ?>
                <p class="text-center text-[11px] sm:text-xs text-rose-400 font-semibold">
                    <i class="fa-solid fa-circle-exclamation mr-1"></i>
                    Anda telah mencapai batas 2 kampanye aktif. Jeda salah satu tugas di <a href="index.php?page=tasks&action=my_tasks" class="underline">Kampanye Saya</a> atau beli <a href="index.php?page=shop" class="underline">VIP Sultan</a>.
                </p>
            <?php endif; ?>
        </form>

    </div>

</div>

<script>
    // Konfigurasi Standar Minimal Harga PAKAINI
    const platformConfig = {
        youtube: {
            name: 'YouTube',
            icon: 'fa-brands fa-youtube',
            color: 'red',
            urlPlaceholder: 'https://www.youtube.com/@ChannelAnda atau https://youtu.be/...',
            types: [
                { id: 'subscribers', name: 'Subscribers', min: 150, hint: 'Tingkatkan jumlah subscriber channel', icon: 'fa-solid fa-user-plus' },
                { id: 'views',       name: 'Views (30 Detik)', min: 30,  hint: 'Penayangan video durasi minimal 30 detik', icon: 'fa-solid fa-play' },
                { id: 'likes',       name: 'Video Likes', min: 20,  hint: 'Tambahkan likes organik ke video pilihan Anda', icon: 'fa-solid fa-thumbs-up' }
            ]
        },
        tiktok: {
            name: 'TikTok',
            icon: 'fa-brands fa-tiktok',
            color: 'cyan',
            urlPlaceholder: 'https://www.tiktok.com/@UsernameAnda atau https://vt.tiktok.com/...',
            types: [
                { id: 'followers',   name: 'Followers Akun', min: 50, hint: 'Tingkatkan follower profil TikTok Anda', icon: 'fa-solid fa-user-plus' },
                { id: 'likes',       name: 'Video Likes', min: 20, hint: 'Likes video untuk mendorong potensi FYP', icon: 'fa-solid fa-heart' }
            ]
        },
        instagram: {
            name: 'Instagram',
            icon: 'fa-brands fa-instagram',
            color: 'pink',
            urlPlaceholder: 'https://www.instagram.com/UsernameAnda atau https://www.instagram.com/p/...',
            types: [
                { id: 'followers',   name: 'Followers Akun', min: 50, hint: 'Tambah pengikut akun Instagram bisnis/personal', icon: 'fa-solid fa-user-plus' },
                { id: 'likes',       name: 'Post Likes', min: 20, hint: 'Tambahkan like ke feed postingan foto/reels', icon: 'fa-solid fa-heart' }
            ]
        },
        facebook: {
            name: 'Facebook',
            icon: 'fa-brands fa-facebook',
            color: 'blue',
            urlPlaceholder: 'https://www.facebook.com/HalamanAnda atau https://facebook.com/post/...',
            types: [
                { id: 'followers',   name: 'Page Followers', min: 120, hint: 'Tingkatkan pengikut fanspage Facebook', icon: 'fa-solid fa-thumbs-up' },
                { id: 'likes',       name: 'Post Likes', min: 20,  hint: 'Like postingan Facebook untuk meningkatkan reach', icon: 'fa-solid fa-heart' }
            ]
        }
    };

    const userBalance = <?= (int)($user['coins']) ?>;

    function selectPlatform(platformKey) {
        document.getElementById('selectedPlatform').value = platformKey;

        // Reset all card styles and checkmarks
        const cards = ['youtube', 'tiktok', 'instagram', 'facebook'];
        cards.forEach(k => {
            const cardEl = document.getElementById(`platCard_${k}`);
            if (cardEl) {
                cardEl.className = "plat-card relative text-left p-3.5 sm:p-4 rounded-2xl border-2 transition-all duration-200 border-slate-800 bg-slate-900/60 hover:border-slate-700 active:scale-95";
            }
            const checkEl = document.getElementById(`platCheck_${k}`);
            if (checkEl) checkEl.classList.add('hidden');
        });

        // Set active card style & show checkmark
        const activeCard = document.getElementById(`platCard_${platformKey}`);
        if (activeCard) {
            activeCard.className = "plat-card relative text-left p-3.5 sm:p-4 rounded-2xl border-2 transition-all duration-200 border-indigo-500/80 bg-indigo-500/10 shadow-md shadow-indigo-500/10 active:scale-95";
        }

        const activeCheck = document.getElementById(`platCheck_${platformKey}`);
        if (activeCheck) activeCheck.classList.remove('hidden');

        // Update placeholder URL
        const pData = platformConfig[platformKey];
        const urlInput = document.getElementById('task_url');
        if (pData && urlInput) {
            urlInput.placeholder = pData.urlPlaceholder;
        }

        // Render Action Pills
        renderActionPills(platformKey);
    }

    function setPresetCpc(val) {
        const input = document.getElementById('cpc');
        const min = parseInt(input.min) || 20;
        input.value = Math.max(min, val);
        calculateSimulator();
    }

    function setPresetClicks(val) {
        const input = document.getElementById('max_clicks');
        input.value = val;
        calculateSimulator();
    }

    function renderActionPills(platformKey) {
        const container = document.getElementById('actionPillsContainer');
        const types = platformConfig[platformKey].types;
        container.innerHTML = '';

        types.forEach((t, idx) => {
            const isFirst = (idx === 0);
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.id = `actionPill_${t.id}`;
            btn.onclick = () => selectActionType(t.id, t.min);
            btn.className = isFirst 
                ? "action-pill px-3 py-2 sm:px-4 sm:py-3 rounded-xl sm:rounded-2xl font-bold text-[11px] sm:text-xs flex items-center space-x-1.5 sm:space-x-2 transition-all border-2 border-indigo-500 bg-indigo-600/20 text-white shadow-sm"
                : "action-pill px-3 py-2 sm:px-4 sm:py-3 rounded-xl sm:rounded-2xl font-bold text-[11px] sm:text-xs flex items-center space-x-1.5 sm:space-x-2 transition-all border-2 border-slate-800 bg-slate-900/60 text-slate-300 hover:border-slate-700";

            btn.innerHTML = `
                <i class="${t.icon} text-[11px] sm:text-xs"></i>
                <span>${t.name}</span>
                <span class="px-1.5 sm:px-2 py-0.5 rounded-full bg-slate-900 border border-slate-800 text-[9px] sm:text-[10px] text-amber-400 font-mono font-bold">Min ${t.min} 🪙</span>
            `;
            container.appendChild(btn);

            if (isFirst) {
                selectActionType(t.id, t.min);
            }
        });
    }

    function selectActionType(typeId, minCpc) {
        document.getElementById('selectedTaskType').value = typeId;

        // Reset pills
        document.querySelectorAll('.action-pill').forEach(pill => {
            pill.className = "action-pill px-3 py-2 sm:px-4 sm:py-3 rounded-xl sm:rounded-2xl font-bold text-[11px] sm:text-xs flex items-center space-x-1.5 sm:space-x-2 transition-all border-2 border-slate-800 bg-slate-900/60 text-slate-300 hover:border-slate-700";
        });

        // Set active pill
        const activePill = document.getElementById(`actionPill_${typeId}`);
        if (activePill) {
            activePill.className = "action-pill px-3 py-2 sm:px-4 sm:py-3 rounded-xl sm:rounded-2xl font-bold text-[11px] sm:text-xs flex items-center space-x-1.5 sm:space-x-2 transition-all border-2 border-indigo-500 bg-indigo-600/20 text-white shadow-sm";
        }

        // Adjust CPC
        const cpcInput = document.getElementById('cpc');
        cpcInput.min = minCpc;
        if (parseInt(cpcInput.value || 0) < minCpc) {
            cpcInput.value = minCpc;
        }

        // Update badge
        document.getElementById('cpcMinBadge').innerText = `Min: ${minCpc} Koin`;

        calculateSimulator();
    }

    function calculateSimulator() {
        const cpc = parseInt(document.getElementById('cpc').value) || 0;
        const maxClicks = parseInt(document.getElementById('max_clicks').value) || 0;
        const isPriority = document.getElementById('is_priority')?.checked ? 2 : 1;
        const effectiveCpc = cpc * isPriority;
        const total = effectiveCpc * maxClicks;
        document.getElementById('simTotalCoins').innerText = total.toLocaleString('id-ID') + (isPriority === 2 ? ' (Prioritas 2x)' : '');

        const statusEl = document.getElementById('simBalanceStatus');
        if (userBalance >= total) {
            statusEl.className = "inline-flex items-center text-xs font-bold text-emerald-400";
            statusEl.innerHTML = `<i class="fa-solid fa-circle-check mr-1.5"></i> Saldo Cukup (${userBalance.toLocaleString()} Koin Iklan)`;
        } else {
            statusEl.className = "inline-flex items-center text-xs font-bold text-amber-400";
            statusEl.innerHTML = `<i class="fa-solid fa-triangle-exclamation mr-1.5"></i> Saldo Parsial (${userBalance.toLocaleString()} Koin Iklan)`;
        }
    }

    function validateUrlPreview(val) {
        const badge = document.getElementById('urlValidationBadge');
        if (!badge) return;
        let url = (val || '').trim();
        if (!url) {
            badge.innerText = 'Harus diawali https://';
            badge.className = 'text-[11px] text-slate-500';
            return;
        }
        if (!/^https?:\/\//i.test(url)) {
            url = 'https://' + url;
        }
        const platform = selectedPlatform || 'youtube';
        const urlLower = url.toLowerCase();
        let validPlatform = true;
        let hint = '';

        if (platform === 'youtube') {
            validPlatform = urlLower.includes('youtube.com') || urlLower.includes('youtu.be');
            hint = 'Wajib mengandung youtube.com atau youtu.be';
        } else if (platform === 'instagram') {
            validPlatform = urlLower.includes('instagram.com');
            hint = 'Wajib mengandung instagram.com';
        } else if (platform === 'tiktok') {
            validPlatform = urlLower.includes('tiktok.com') || urlLower.includes('vt.tiktok.com');
            hint = 'Wajib mengandung tiktok.com atau vt.tiktok.com';
        }

        if (!validPlatform) {
            badge.innerText = `${hint} ✗`;
            badge.className = 'text-[11px] text-rose-400 font-bold';
        } else {
            badge.innerText = 'Format Valid ✓';
            badge.className = 'text-[11px] text-emerald-400 font-bold';
        }
    }

    // Inisialisasi awal saat halaman dimuat
    document.addEventListener('DOMContentLoaded', () => {
        selectPlatform('youtube');

        const campaignForm = document.getElementById('campaignForm');
        if (campaignForm) {
            campaignForm.addEventListener('submit', function(e) {
                const urlInput = document.getElementById('task_url');
                let val = urlInput.value.trim();
                if (!val) {
                    e.preventDefault();
                    alert('URL target tugas wajib diisi!');
                    return false;
                }
                if (!/^https?:\/\//i.test(val)) {
                    val = 'https://' + val;
                    urlInput.value = val;
                }
                const platform = selectedPlatform || 'youtube';
                const urlLower = val.toLowerCase();
                let isValid = true;
                let errorMsg = '';

                if (platform === 'youtube') {
                    if (!urlLower.includes('youtube.com') && !urlLower.includes('youtu.be')) {
                        isValid = false;
                        errorMsg = 'URL YouTube tidak cocok! Tautan wajib mengandung domain "youtube.com" atau "youtu.be".';
                    }
                } else if (platform === 'instagram') {
                    if (!urlLower.includes('instagram.com')) {
                        isValid = false;
                        errorMsg = 'URL Instagram tidak cocok! Tautan wajib mengandung domain "instagram.com".';
                    }
                } else if (platform === 'tiktok') {
                    if (!urlLower.includes('tiktok.com') && !urlLower.includes('vt.tiktok.com')) {
                        isValid = false;
                        errorMsg = 'URL TikTok tidak cocok! Tautan wajib mengandung domain "tiktok.com" atau "vt.tiktok.com".';
                    }
                }

                if (!isValid) {
                    e.preventDefault();
                    alert(errorMsg);
                    urlInput.focus();
                    return false;
                }
            });
        }
    });
</script>
