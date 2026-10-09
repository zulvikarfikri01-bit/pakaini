<div class="space-y-4 sm:space-y-6">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
        <div>
            <div class="flex items-center space-x-2 text-[10px] sm:text-xs text-slate-400 mb-1">
                <a href="index.php?page=dashboard" class="hover:text-brand-400 transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-white font-medium">Komunitas</span>
            </div>
            <h2 class="font-display font-black text-xl sm:text-3xl text-white flex items-center space-x-2 sm:space-x-2.5">
                <span class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-slate-900 border border-slate-700/80 flex items-center justify-center text-indigo-400 shadow-sm text-xs sm:text-sm">
                    <i class="fa-solid fa-comments"></i>
                </span>
                <span>Ruang Obrolan & Komunitas Kreator</span>
            </h2>
            <p class="text-[11px] sm:text-sm text-slate-400 mt-0.5 sm:mt-1">
                Diskusikan tips algoritma, jalin relasi saling support, dan pantau kampanye terbaru di HP & Desktop.
            </p>
        </div>

        <div class="flex items-center space-x-2">
            <span class="px-2.5 py-1 sm:px-3.5 sm:py-1.5 rounded-full bg-slate-900 border border-slate-800 text-slate-300 text-[10px] sm:text-xs font-bold flex items-center space-x-1.5 sm:space-x-2 shadow-sm">
                <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>296 Kreator Online</span>
            </span>
        </div>
    </div>

    <!-- Community Multi-Tab Buttons (Global, YouTube, TikTok, IG, FB) -->
    <div class="flex items-center space-x-1.5 sm:space-x-2 overflow-x-auto no-scrollbar pb-1.5 sm:pb-2 border-b border-slate-800/80">
        <button type="button" onclick="switchCommunityTab('global')" id="tabBtn_global"
                class="comm-tab-btn px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl sm:rounded-2xl text-[11px] sm:text-xs font-bold flex items-center space-x-1.5 sm:space-x-2 transition-all bg-indigo-600 text-white shadow-md shadow-indigo-600/20 whitespace-nowrap active:scale-95">
            <i class="fa-solid fa-earth-americas text-xs sm:text-sm"></i>
            <span>Global Chat</span>
            <span class="px-1.5 py-0.2 rounded-full bg-white/20 text-[9px] sm:text-[10px] font-mono">128</span>
        </button>

        <button type="button" onclick="switchCommunityTab('youtube')" id="tabBtn_youtube"
                class="comm-tab-btn px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl sm:rounded-2xl text-[11px] sm:text-xs font-bold flex items-center space-x-1.5 sm:space-x-2 transition-all bg-slate-900 border border-slate-800 text-slate-400 hover:text-white hover:bg-slate-800 whitespace-nowrap active:scale-95">
            <i class="fa-brands fa-youtube text-red-500 text-xs sm:text-sm"></i>
            <span>YouTube Creators</span>
            <span class="px-1.5 py-0.2 rounded-full bg-slate-800 text-[9px] sm:text-[10px] font-mono">54</span>
        </button>

        <button type="button" onclick="switchCommunityTab('tiktok')" id="tabBtn_tiktok"
                class="comm-tab-btn px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl sm:rounded-2xl text-[11px] sm:text-xs font-bold flex items-center space-x-1.5 sm:space-x-2 transition-all bg-slate-900 border border-slate-800 text-slate-400 hover:text-white hover:bg-slate-800 whitespace-nowrap active:scale-95">
            <i class="fa-brands fa-tiktok text-cyan-400 text-xs sm:text-sm"></i>
            <span>TikTok Community</span>
            <span class="px-1.5 py-0.2 rounded-full bg-slate-800 text-[9px] sm:text-[10px] font-mono">68</span>
        </button>

        <button type="button" onclick="switchCommunityTab('instagram')" id="tabBtn_instagram"
                class="comm-tab-btn px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl sm:rounded-2xl text-[11px] sm:text-xs font-bold flex items-center space-x-1.5 sm:space-x-2 transition-all bg-slate-900 border border-slate-800 text-slate-400 hover:text-white hover:bg-slate-800 whitespace-nowrap active:scale-95">
            <i class="fa-brands fa-instagram text-pink-400 text-xs sm:text-sm"></i>
            <span>Instagram Growth</span>
            <span class="px-1.5 py-0.2 rounded-full bg-slate-800 text-[9px] sm:text-[10px] font-mono">32</span>
        </button>

        <button type="button" onclick="switchCommunityTab('facebook')" id="tabBtn_facebook"
                class="comm-tab-btn px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl sm:rounded-2xl text-[11px] sm:text-xs font-bold flex items-center space-x-1.5 sm:space-x-2 transition-all bg-slate-900 border border-slate-800 text-slate-400 hover:text-white hover:bg-slate-800 whitespace-nowrap active:scale-95">
            <i class="fa-brands fa-facebook text-blue-400 text-xs sm:text-sm"></i>
            <span>Facebook Network</span>
            <span class="px-1.5 py-0.2 rounded-full bg-slate-800 text-[9px] sm:text-[10px] font-mono">14</span>
        </button>
    </div>

    <!-- MAIN CHAT CONTAINER & EMBED CONTROLS -->
    <div class="glass-card rounded-2xl sm:rounded-3xl p-3.5 sm:p-6 border border-slate-800 shadow-2xl space-y-3 sm:space-y-4">
        
        <!-- Toggle Mode Bar: Preview vs Iframe Embed Guide -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-2.5 sm:pb-3 border-b border-slate-800 gap-2 sm:gap-3">
            <div class="flex items-center space-x-2">
                <span id="currentRoomTitle" class="font-display font-black text-white text-sm sm:text-base flex items-center space-x-2">
                    <i class="fa-solid fa-earth-americas text-brand-400"></i>
                    <span>Ruang Obrolan: Global Chat PAKAINI</span>
                </span>
            </div>

            <div class="flex items-center space-x-2 text-[10px] sm:text-xs">
                <button type="button" onclick="toggleEmbedGuide()" id="btnToggleEmbed"
                        class="px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-lg sm:rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 border border-slate-800 font-bold transition-all flex items-center space-x-1.5">
                    <i class="fa-solid fa-code text-indigo-400"></i>
                    <span>Panduan Pasang Minnit / Chatango</span>
                </button>
            </div>
        </div>

        <!-- EMBED GUIDE COLLAPSIBLE (Bisa dibuka/tutup) -->
        <div id="embedGuideSection" class="hidden p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-slate-950 border border-slate-800 text-[11px] sm:text-xs space-y-2 sm:space-y-3">
            <div class="flex items-center justify-between text-white font-bold">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-circle-info text-amber-400"></i>
                    <span>Cara Memasang Iframe Chat Minnit / Chatango di PAKAINI</span>
                </div>
                <button type="button" onclick="toggleEmbedGuide()" class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <p class="text-slate-400 text-xs leading-relaxed">
                Anda dapat menghubungkan room chat nyata seperti <strong>Minnit Chat</strong> (<a href="https://minnit.chat" target="_blank" class="text-brand-400 underline">minnit.chat</a>) atau <strong>Chatango</strong> ke wadah ini. Buka file <code class="text-amber-300 bg-slate-900 px-1 py-0.5 rounded">views/community/index.php</code> dan ganti elemen mockup dengan tag iframe resmi room Anda:
            </p>
            <div class="p-3 bg-slate-900 rounded-xl border border-slate-800 font-mono text-[11px] text-slate-300 overflow-x-auto">
                <span class="text-slate-500">// Contoh Embed Iframe Minnit Chat Responsif:</span><br>
                <span class="text-indigo-400">&lt;iframe</span> <span class="text-amber-400">src=</span><span class="text-emerald-400">"https://minnit.chat/PakainiGlobal?embed&web"</span> <span class="text-amber-400">class=</span><span class="text-emerald-400">"w-full h-[520px] rounded-2xl border-0"</span> <span class="text-amber-400">allowtransparency=</span><span class="text-emerald-400">"true"</span><span class="text-indigo-400">&gt;&lt;/iframe&gt;</span>
            </div>
        </div>

        <!-- SIMULATED INTERACTIVE CHAT ENGINE -->
        <div class="bg-slate-950/80 rounded-2xl border border-slate-800/80 p-4 sm:p-5 flex flex-col justify-between min-h-[480px]">
            
            <!-- Chat Feed Messages -->
            <div id="chatFeed" class="space-y-3.5 overflow-y-auto max-h-[380px] pr-2 no-scrollbar">
                
                <!-- System Welcome Announcement -->
                <div class="p-3 rounded-xl bg-brand-500/10 border border-brand-500/20 text-center text-xs text-brand-200">
                    <i class="fa-solid fa-bullhorn text-brand-400 mr-1.5"></i>
                    Selamat datang di Komunitas PAKAINI! Saling follow & like secara sportif. Pelanggar unfollow akan otomatis didenda 3x lipat.
                </div>

                <!-- Message 1 -->
                <div class="flex items-start space-x-3 text-xs">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-300 flex items-center justify-center font-black flex-shrink-0 border border-amber-500/30">
                        SV
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="font-bold text-white">sultan_vip</span>
                            <span class="px-1.5 py-0.2 rounded-full bg-amber-500/20 text-amber-300 text-[9px] font-black uppercase">👑 VIP</span>
                            <span class="text-[10px] text-slate-500">12:30</span>
                        </div>
                        <div class="bg-slate-900 border border-slate-800/80 p-3 rounded-2xl rounded-tl-none text-slate-300 inline-block max-w-lg leading-relaxed">
                            Halo all! Saya baru pasang kampanye YouTube Subscriber 150 koin. Siapa cepat dia dapat yaa, gas kerjakan!
                        </div>
                    </div>
                </div>

                <!-- Message 2 -->
                <div class="flex items-start space-x-3 text-xs">
                    <div class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-black flex-shrink-0 border border-indigo-500/30">
                        BP
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="font-bold text-white">budi_pemula</span>
                            <span class="text-[10px] text-slate-500">12:33</span>
                        </div>
                        <div class="bg-slate-900 border border-slate-800/80 p-3 rounded-2xl rounded-tl-none text-slate-300 inline-block max-w-lg leading-relaxed">
                            Siap gan! Sudah saya tonton 30 detik dan klik subscribe. Koin langsung masuk ke saldo saya, mantap cepat banget! 👍
                        </div>
                    </div>
                </div>

                <!-- Message 3 -->
                <div class="flex items-start space-x-3 text-xs">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-black flex-shrink-0 border border-emerald-500/30">
                        CK
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="font-bold text-white">cantika_tiktok</span>
                            <span class="text-[10px] text-slate-500">12:41</span>
                        </div>
                        <div class="bg-slate-900 border border-slate-800/80 p-3 rounded-2xl rounded-tl-none text-slate-300 inline-block max-w-lg leading-relaxed">
                            Ada yang fokus TikTok? Saya pasang follow profil 50 koin. Jangan lupa stay follow yaa biar gak kena sanksi denda 3x min.
                        </div>
                    </div>
                </div>

                <!-- User custom dynamic message insert here -->
                <div id="dynamicMessages" class="space-y-3.5"></div>

            </div>

            <!-- Quick Chat Reaction Pills for Mobile Thumb Convenience -->
            <div class="flex items-center space-x-1.5 overflow-x-auto no-scrollbar pt-2 pb-1 text-slate-300">
                <button type="button" onclick="insertQuickChat('👋 Halo all! Ayo saling follow & like sportif.')"
                        class="px-2.5 py-1 rounded-xl bg-slate-900 border border-slate-800 text-[11px] text-slate-300 hover:text-white hover:border-brand-400 whitespace-nowrap active:scale-95 transition-all">
                    👋 Ayo Saling Follow
                </button>
                <button type="button" onclick="insertQuickChat('✅ Sudah saya kerjakan tugasnya, koin mantap!')"
                        class="px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg sm:rounded-xl bg-slate-900 border border-slate-800 text-[10px] sm:text-[11px] text-slate-300 hover:text-white hover:border-emerald-400 whitespace-nowrap active:scale-95 transition-all">
                    ✅ Sudah Saya Kerjakan
                </button>
                <button type="button" onclick="insertQuickChat('🔥 Ada tugas baru bernilai tinggi, gas eksekusi!')"
                        class="px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg sm:rounded-xl bg-slate-900 border border-slate-800 text-[10px] sm:text-[11px] text-slate-300 hover:text-white hover:border-amber-400 whitespace-nowrap active:scale-95 transition-all">
                    🔥 Tugas Koin Tinggi
                </button>
                <button type="button" onclick="insertQuickChat('👑 Member VIP dapat klik unlimited & prioritas #1, recommended banget.')"
                        class="px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-lg sm:rounded-xl bg-slate-900 border border-slate-800 text-[10px] sm:text-[11px] text-slate-300 hover:text-white hover:border-yellow-400 whitespace-nowrap active:scale-95 transition-all">
                    👑 VIP Prioritas #1
                </button>
            </div>

            <!-- Chat Interactive Input Box -->
            <form id="chatForm" onsubmit="sendChatMessage(event)" class="mt-2 pt-2 sm:pt-2.5 border-t border-slate-800/80 flex items-center space-x-1.5 sm:space-x-2">
                <input type="text" id="chatInput" placeholder="Tulis pesan atau ajak kreator saling tukar tugas..."
                    class="flex-1 px-3 sm:px-4 py-2 sm:py-3 bg-slate-900 border border-slate-700/80 rounded-xl sm:rounded-2xl text-white placeholder-slate-500 text-[11px] sm:text-xs focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-all">
                
                <button type="submit" class="px-3.5 sm:px-5 py-2 sm:py-3 rounded-xl sm:rounded-2xl bg-gradient-to-r from-brand-600 to-indigo-600 hover:opacity-95 text-white font-bold text-[11px] sm:text-xs flex items-center space-x-1 sm:space-x-1.5 shadow-lg shadow-brand-500/20 active:scale-95 transition-all flex-shrink-0">
                    <span>Kirim</span>
                    <i class="fa-solid fa-paper-plane text-[10px] sm:text-xs"></i>
                </button>
            </form>

        </div>

    </div>

    <!-- KOTAK KOMUNITAS WHATSAPP & TELEGRAM -->
    <div class="glass-card rounded-xl sm:rounded-2xl p-4 sm:p-6 border border-slate-800 shadow-xl relative overflow-hidden text-center">
        <!-- Ambient glow effect -->
        <div class="absolute -top-14 left-1/2 -translate-x-1/2 w-64 h-24 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 max-w-lg mx-auto">
            <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-[10px] font-bold uppercase tracking-wider mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Grup Resmi Pejuang Koin</span>
            </div>

            <h2 class="font-display font-bold text-lg sm:text-xl text-white mb-1.5">
                💬 Komunitas Saling Follow
            </h2>
            <p class="text-xs text-slate-400 mb-4 leading-relaxed">
                Gabung dengan para pejuang koin lainnya untuk saling sapa, diskusi, dan bagi-bagi tips!
            </p>
            
            <div class="flex flex-wrap items-center justify-center gap-2.5 sm:gap-3">
                <!-- Tombol WhatsApp (Aktif) -->
                <a href="https://chat.whatsapp.com/J6M0TwrjuvoGm4Mq1eJz2G" target="_blank" rel="noopener noreferrer" 
                   class="flex items-center px-4 sm:px-5 py-2 sm:py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-400 hover:to-green-500 text-white font-bold text-xs rounded-xl transition-all shadow-md shadow-emerald-500/20 active:scale-95">
                    <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.015c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                    </svg>
                    <span>Gabung Grup WhatsApp</span>
                </a>
                
                <!-- Tombol Telegram (Nonaktif / Coming Soon) -->
                <span class="flex items-center px-3.5 sm:px-4 py-2 sm:py-2.5 bg-slate-900 border border-slate-800 text-slate-500 font-semibold text-xs rounded-xl cursor-not-allowed select-none opacity-80">
                    <svg class="w-4 h-4 mr-1.5 flex-shrink-0 text-slate-500" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                    </svg>
                    <span>Telegram (Coming Soon)</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Community Rules Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4 text-xs">
        <div class="glass-card bg-slate-950/60 p-3.5 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-800">
            <div class="flex items-center space-x-2 text-brand-400 font-bold mb-1 sm:mb-1.5 text-xs sm:text-sm">
                <i class="fa-solid fa-handshake"></i>
                <span>Saling Support Sportif</span>
            </div>
            <p class="text-slate-400 text-[10px] sm:text-[11px] leading-relaxed">
                Jalankan tugas dengan sungguh-sungguh. Akun yang melakukan unsub atau unfollow akan dikenakan penalti saldo denda 3x lipat.
            </p>
        </div>

        <div class="glass-card bg-slate-950/60 p-3.5 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-800">
            <div class="flex items-center space-x-2 text-amber-400 font-bold mb-1 sm:mb-1.5 text-xs sm:text-sm">
                <i class="fa-solid fa-shield-virus"></i>
                <span>Dilarang Spam Link</span>
            </div>
            <p class="text-slate-400 text-[10px] sm:text-[11px] leading-relaxed">
                Gunakan menu <strong>Pasang Kampanye</strong> untuk mempromosikan tautan Anda. Hindari membanjiri ruang obrolan dengan link referral bertubi-tubi.
            </p>
        </div>

        <div class="glass-card bg-slate-950/60 p-3.5 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-800">
            <div class="flex items-center space-x-2 text-cyan-400 font-bold mb-1 sm:mb-1.5 text-xs sm:text-sm">
                <i class="fa-solid fa-comments"></i>
                <span>Komunikasi Santun</span>
            </div>
            <p class="text-slate-400 text-[10px] sm:text-[11px] leading-relaxed">
                Gunakan bahasa yang ramah dan saling menghargai. Laporkan perilaku kasar atau penipuan ke tim admin PAKAINI via WhatsApp.
            </p>
        </div>
    </div>

</div>

<script>
    const userCurrentUsername = '<?= htmlspecialchars($user['username'] ?? 'Anda', ENT_QUOTES, 'UTF-8') ?>';
    const isUserVip = <?= !empty($user['is_vip']) ? 'true' : 'false' ?>;

    const roomDetails = {
        global:    { name: 'Global Chat PAKAINI', icon: 'fa-solid fa-earth-americas text-brand-400' },
        youtube:   { name: 'YouTube Creators Community', icon: 'fa-brands fa-youtube text-red-500' },
        tiktok:    { name: 'TikTok Growth Club', icon: 'fa-brands fa-tiktok text-cyan-400' },
        instagram: { name: 'Instagram Engagement Hub', icon: 'fa-brands fa-instagram text-pink-500' },
        facebook:  { name: 'Facebook Page & Group Network', icon: 'fa-brands fa-facebook text-blue-500' }
    };

    function switchCommunityTab(platform) {
        // Update styling semua button
        document.querySelectorAll('.comm-tab-btn').forEach(btn => {
            btn.className = "comm-tab-btn px-4 py-2.5 rounded-2xl text-xs font-bold flex items-center space-x-2 transition-all bg-slate-900 border border-slate-800 text-slate-400 hover:text-white hover:bg-slate-800 whitespace-nowrap active:scale-95";
        });

        // Set active styling pada button yang dipilih
        const activeBtn = document.getElementById(`tabBtn_${platform}`);
        if (activeBtn) {
            activeBtn.className = "comm-tab-btn px-4 py-2.5 rounded-2xl text-xs font-bold flex items-center space-x-2 transition-all bg-indigo-600 text-white shadow-md shadow-indigo-600/20 whitespace-nowrap active:scale-95";
        }

        // Update Room Title
        const details = roomDetails[platform] || roomDetails.global;
        const titleEl = document.getElementById('currentRoomTitle');
        if (titleEl) {
            titleEl.innerHTML = `<i class="${details.icon}"></i><span>Ruang Obrolan: ${details.name}</span>`;
        }
    }

    function toggleEmbedGuide() {
        const sec = document.getElementById('embedGuideSection');
        if (sec.classList.contains('hidden')) {
            sec.classList.remove('hidden');
        } else {
            sec.classList.add('hidden');
        }
    }

    function sendChatMessage(e) {
        e.preventDefault();
        const input = document.getElementById('chatInput');
        const text = input.value.trim();
        if (!text) return;

        const container = document.getElementById('dynamicMessages');
        const now = new Date();
        const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

        const msgDiv = document.createElement('div');
        msgDiv.className = "flex items-start space-x-3 text-xs animate-fade-in";
        msgDiv.innerHTML = `
            <div class="w-8 h-8 rounded-xl bg-brand-500/20 text-brand-300 flex items-center justify-center font-black flex-shrink-0 border border-brand-500/30">
                ${userCurrentUsername.substring(0, 2).toUpperCase()}
            </div>
            <div class="flex-1">
                <div class="flex items-center space-x-2 mb-1">
                    <span class="font-bold text-white">${userCurrentUsername}</span>
                    ${isUserVip ? '<span class="px-1.5 py-0.2 rounded-full bg-amber-500/20 text-amber-300 text-[9px] font-black uppercase">👑 VIP</span>' : ''}
                    <span class="text-[10px] text-slate-500">${timeStr}</span>
                </div>
                <div class="bg-brand-600/20 border border-brand-500/30 p-3 rounded-2xl rounded-tl-none text-slate-200 inline-block max-w-lg leading-relaxed">
                    ${escapeHtml(text)}
                </div>
            </div>
        `;

        container.appendChild(msgDiv);
        input.value = '';

        // Auto scroll to bottom
        const feed = document.getElementById('chatFeed');
        feed.scrollTop = feed.scrollHeight;
    }

    function insertQuickChat(text) {
        const input = document.getElementById('chatInput');
        if (input) {
            input.value = text;
            input.focus();
        }
    }

    function escapeHtml(str) {
        return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }
</script>
