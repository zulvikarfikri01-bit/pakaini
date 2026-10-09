<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?= htmlspecialchars($page_title ?? 'PAKAINI', ENT_QUOTES, 'UTF-8') ?> — Social Media Exchange Platform</title>

    <!-- Open Graph Meta Tags (Untuk Berbagi WhatsApp/FB) -->
    <!-- CATATAN: Silakan sesuaikan URL domain pada tag og:image (https://(domain-anda)/logo.png) dengan domain aktif website Anda nanti -->
    <meta property="og:title" content="Pakaini - Social Media Exchange & Growth Platform">
    <meta property="og:description" content="Tingkatkan Followers, Subscribers, dan interaksi media sosial Anda dengan komunitas pengguna asli Indonesia. Zero Risk, Cepat & Aman!">
    <meta property="og:image" content="https://(domain-anda)/logo.png">
    <meta property="og:type" content="website">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                        display: ['Outfit', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#6366f1',
                            700: '#4f46e5',
                            800: '#4338ca',
                            900: '#312e81',
                        },
                        dark: {
                            950: '#030712',
                            900: '#0b0f19',
                            850: '#111827',
                            800: '#1f2937'
                        }
                    },
                    boxShadow: {
                        'glow-brand': '0 0 25px -3px rgba(99, 102, 241, 0.45)',
                        'glow-gold': '0 0 25px -3px rgba(245, 158, 11, 0.45)',
                        'mobile-fab': '0 8px 25px -4px rgba(99, 102, 241, 0.65)'
                    }
                }
            }
        }
    </script>
    
    <style>
        html {
            scroll-behavior: smooth;
            scrollbar-width: thin;
            scrollbar-color: #334155 #030712;
            -webkit-text-size-adjust: 100%;
        }
        body {
            background-color: #030712;
            color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
            background-image: 
                radial-gradient(ellipse 90% 45% at 50% -15%, rgba(99, 102, 241, 0.22) 0%, transparent 70%),
                radial-gradient(ellipse 50% 30% at 85% 15%, rgba(245, 158, 11, 0.10) 0%, transparent 60%),
                radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 28px 28px;
            background-attachment: fixed;
            padding-bottom: env(safe-area-inset-bottom, 24px);
        }
        
        /* Glassmorphism Surface Tokens */
        .glass-card {
            background: linear-gradient(135deg, rgba(17, 24, 39, 0.85) 0%, rgba(11, 15, 25, 0.92) 100%);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.03) inset;
        }
        
        .glass-card-subtle {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .glass-input {
            background: rgba(3, 7, 18, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #f8fafc;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .glass-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
            outline: none;
        }

        /* VIP Champagne Glow */
        .vip-border-glow {
            border: 1px solid rgba(245, 158, 11, 0.6) !important;
            box-shadow: 0 0 25px -4px rgba(245, 158, 11, 0.25), inset 0 0 15px rgba(245, 158, 11, 0.08);
        }

        /* Tactile Mobile Luxury Buttons */
        .btn-luxury {
            position: relative;
            background: linear-gradient(180deg, #6366f1 0%, #4f46e5 100%);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.25), 0 4px 16px -2px rgba(99, 102, 241, 0.5);
            transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-luxury:active {
            transform: scale(0.97);
        }

        .btn-luxury-gold {
            background: linear-gradient(180deg, #fbbf24 0%, #d97706 100%);
            color: #0f172a;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.4), 0 4px 16px -2px rgba(245, 158, 11, 0.45);
            transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-luxury-gold:active {
            transform: scale(0.97);
        }

        /* Mobile Bottom Sheet Modal Animation */
        @keyframes slideUpBottom {
            from { transform: translateY(100%); opacity: 0.5; }
            to { transform: translateY(0); opacity: 1; }
        }
        .mobile-sheet-enter {
            animation: slideUpBottom 0.28s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #030712;
        }
        ::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 9999px;
            border: 2px solid #030712;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
        }

        /* Top Progress Bar Indicator */
        #scrollProgressBar {
            background: linear-gradient(90deg, #4f46e5 0%, #6366f1 100%);
            box-shadow: 0 0 10px rgba(99, 102, 241, 0.5);
            transition: width 0.1s ease-out;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col pb-28 md:pb-10 antialiased selection:bg-indigo-600 selection:text-white relative">

    <!-- Top Gradient Scroll Progress Indicator -->
    <div class="fixed top-0 left-0 right-0 z-50 h-[2.5px] bg-transparent pointer-events-none">
        <div id="scrollProgressBar" class="h-full w-0 rounded-r-full"></div>
    </div>

    <!-- PREMIER MOBILE & DESKTOP HEADER -->
    <header class="sticky top-0 z-40 bg-dark-950/90 backdrop-blur-2xl border-b border-white/[0.08] transition-all">
        <div class="max-w-7xl mx-auto px-3.5 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14 sm:h-18">
                
                <!-- Brand & Logo -->
                <div class="flex items-center space-x-2.5 sm:space-x-8">
                    <a href="<?= Auth::check() ? 'index.php?page=dashboard' : 'index.php' ?>" class="flex items-center space-x-2.5 group">
                        <img src="assets/img/logo.png" onerror="this.onerror=null; this.src='logo.png';" alt="Logo PAKAINI" class="h-10 sm:h-12 w-auto object-contain rounded-xl transition-transform group-hover:scale-105 drop-shadow-md">
                        <div class="flex flex-col">
                            <div class="flex items-center space-x-1.5">
                                <span class="font-display font-black text-lg sm:text-xl tracking-tight text-white">PAKAINI</span>
                                <span class="px-1.5 py-0.2 rounded text-[8px] sm:text-[9px] font-bold uppercase tracking-wider bg-slate-800 text-slate-300 border border-slate-700">PRO</span>
                            </div>
                            <span class="text-[9px] text-slate-400 font-mono tracking-wider hidden sm:block">Exchange Engine</span>
                        </div>
                    </a>

                    <!-- Desktop Nav Links (Hidden on Mobile) -->
                    <nav class="hidden md:flex items-center space-x-1">
                        <?php 
                            $currPage = $_GET['page'] ?? 'dashboard';
                            $currAction = $_GET['action'] ?? '';
                        ?>
                        <a href="index.php?page=dashboard" class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all <?= ($currPage === 'dashboard') ? 'bg-white/[0.08] text-white shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-white/[0.04]' ?>">
                            <i class="fa-solid fa-gauge mr-1.5 text-xs text-indigo-400"></i>Dashboard
                        </a>
                        <a href="index.php?page=tasks" class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all <?= ($currPage === 'tasks' && empty($currAction)) ? 'bg-white/[0.08] text-white shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-white/[0.04]' ?>">
                            <i class="fa-solid fa-coins mr-1.5 text-xs text-indigo-400"></i>Cari Koin
                        </a>
                        <a href="index.php?page=tasks&action=my_tasks" class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all <?= ($currAction === 'my_tasks') ? 'bg-white/[0.08] text-white shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-white/[0.04]' ?>">
                            <i class="fa-solid fa-layer-group mr-1.5 text-xs text-indigo-400"></i>Kampanye
                        </a>
                        <a href="index.php?page=referral" class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all <?= ($currPage === 'referral') ? 'bg-white/[0.08] text-white shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-white/[0.04]' ?>">
                            <i class="fa-solid fa-gift mr-1.5 text-xs text-indigo-400"></i>Misi Afiliasi
                        </a>
                        <a href="index.php?page=shop" class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all <?= ($currPage === 'shop') ? 'bg-white/[0.08] text-white shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-white/[0.04]' ?>">
                            <i class="fa-solid fa-crown mr-1.5 text-xs text-amber-400"></i>Toko VIP
                        </a>
                        <a href="index.php?page=community" class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-all <?= ($currPage === 'community') ? 'bg-white/[0.08] text-white shadow-sm' : 'text-slate-400 hover:text-slate-200 hover:bg-white/[0.04]' ?>">
                            <i class="fa-solid fa-comments mr-1.5 text-xs text-indigo-400"></i>Komunitas
                        </a>

                    </nav>
                </div>

                <!-- Right Header: Saldo Widget & User Profile (Ultra-Clean on Mobile) -->
                <div class="flex items-center space-x-2 sm:space-x-3.5">
                    
                    <!-- Streak Harian Counter (Compact pill on mobile) -->
                    <div class="flex items-center space-x-1.5 bg-slate-900 border border-white/[0.08] px-2.5 py-1 rounded-xl text-xs font-bold text-slate-300 shadow-sm" title="Streak Keaktifan Harian">
                        <i class="fa-solid fa-fire text-amber-400 text-xs"></i>
                        <span class="font-mono text-xs text-white"><?= (int)($user['active_days_streak'] ?? 1) ?></span>
                        <span class="hidden sm:inline text-[11px] font-sans text-slate-400">Hari</span>
                    </div>

                    <!-- Saldo Koin Live Pill Widget (Neon Glow Ringan) -->
                    <a href="index.php?page=shop" class="flex items-center space-x-1.5 sm:space-x-2 bg-[#111111] border border-amber-500/30 hover:border-amber-400/60 px-2.5 sm:px-3.5 py-1 sm:py-1.5 rounded-xl transition-all shadow-[0_0_12px_rgba(245,158,11,0.18)] group active:scale-95" title="Saldo Koin Murni">
                        <span class="text-xs sm:text-sm drop-shadow-[0_0_6px_rgba(245,158,11,0.5)]">🪙</span>
                        <div class="text-left font-mono font-black text-xs sm:text-sm <?= ((int)($user['coins'] ?? 0) < 0) ? 'text-rose-400' : 'text-amber-300 drop-shadow-[0_0_8px_rgba(245,158,11,0.35)]' ?>">
                            <?= number_format($user['coins'] ?? 0) ?> <span class="text-[10px] sm:text-xs font-sans font-bold text-slate-300">Koin</span>
                        </div>
                        <span class="w-3.5 h-3.5 rounded bg-white/[0.08] text-slate-300 hover:text-white flex items-center justify-center text-[9px]">
                            <i class="fa-solid fa-plus text-[8px]"></i>
                        </span>
                    </a>

                    <!-- VIP Sultan Status Pill (Desktop only) -->
                    <?php if ((int)($user['is_vip'] ?? 0) === 1): ?>
                        <div class="hidden sm:flex items-center space-x-1.5 bg-gradient-to-r from-amber-500/20 to-yellow-500/10 border border-amber-500/40 px-3 py-1.5 rounded-xl text-xs font-black text-amber-300 shadow-sm" title="Member VIP Sultan: Prioritas #1, Klik Unlimited!">
                            <i class="fa-solid fa-crown text-amber-400 text-xs"></i>
                            <span class="tracking-wide">VIP</span>
                        </div>
                    <?php endif; ?>

                    <!-- User Profile Avatar (Tap to open menu on mobile & desktop) -->
                    <div class="relative">
                        <button type="button" id="btnUserMenu" onclick="toggleUserMenu()" class="flex items-center space-x-1 p-0.5 rounded-xl hover:bg-white/[0.08] active:scale-95 transition-all focus:outline-none">
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-gradient-to-br from-indigo-500 via-brand-600 to-indigo-700 flex items-center justify-center font-black text-white text-[11px] sm:text-xs shadow-md uppercase border border-white/20">
                                <?= substr($user['username'] ?? 'U', 0, 2) ?>
                            </div>
                        </button>

                        <!-- Desktop Dropdown Menu (Hidden on mobile, opens below button) -->
                        <div id="desktopProfileDropdown" class="hidden absolute right-0 mt-2 w-60 glass-card rounded-2xl shadow-2xl py-2 transition-all z-50 border border-white/[0.12]">
                            <div class="px-4 py-3 border-b border-white/[0.08]">
                                <p class="text-xs font-bold text-white truncate"><?= htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="text-[11px] text-slate-400 truncate mt-0.5"><?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                                <div class="mt-2">
                                    <?php if ((int)($user['is_vip'] ?? 0) === 1): ?>
                                        <span class="inline-flex items-center text-[10px] font-black text-amber-300 bg-amber-500/15 px-2.5 py-0.5 rounded-full border border-amber-500/30">
                                            <i class="fa-solid fa-crown mr-1.5 text-[9px] text-amber-400"></i>VIP Sultan Member
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center text-[10px] font-semibold text-slate-400 bg-white/[0.06] px-2.5 py-0.5 rounded-full">
                                            Member Biasa (Reguler)
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="py-1 text-xs">
                                <a href="index.php?page=tasks&action=create" class="flex items-center px-4 py-2 font-medium text-slate-300 hover:text-white hover:bg-white/[0.06] transition-colors">
                                    <i class="fa-solid fa-plus-circle w-5 text-indigo-400"></i>Pasang Kampanye Baru
                                </a>
                                <a href="index.php?page=tasks&action=my_tasks" class="flex items-center px-4 py-2 font-medium text-slate-300 hover:text-white hover:bg-white/[0.06] transition-colors">
                                    <i class="fa-solid fa-layer-group w-5 text-indigo-400"></i>Kampanye Saya
                                </a>
                                <a href="index.php?page=referral" class="flex items-center px-4 py-2 font-medium text-slate-300 hover:text-white hover:bg-white/[0.06] transition-colors">
                                    <i class="fa-solid fa-gift w-5 text-rose-400"></i>Misi Bonus Afiliasi
                                </a>
                                <a href="index.php?page=shop" class="flex items-center px-4 py-2 font-medium text-slate-300 hover:text-white hover:bg-white/[0.06] transition-colors">
                                    <i class="fa-solid fa-crown w-5 text-amber-400"></i>Toko Koin & VIP
                                </a>
                                <?php if (Auth::isAdmin()): ?>
                                <a href="index.php?page=admin" class="flex items-center px-4 py-2 font-bold text-amber-300 hover:bg-amber-500/10 transition-colors">
                                    <i class="fa-solid fa-shield-halved w-5"></i>Admin Control Room
                                </a>
                                <?php endif; ?>
                            </div>
                            <div class="border-t border-white/[0.08] pt-1">
                                <a href="index.php?page=logout" class="flex items-center px-4 py-2 text-xs font-semibold text-rose-400 hover:bg-rose-500/10 transition-colors">
                                    <i class="fa-solid fa-arrow-right-from-bracket w-5"></i>Keluar (Logout)
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </header>

    <!-- MOBILE PROFILE BOTTOM SHEET DRAWER (Sliding up on mobile screens) -->
    <div id="mobileProfileDrawer" class="fixed inset-0 z-50 bg-dark-950/85 backdrop-blur-md hidden flex items-end justify-center" onclick="closeUserMenu(event)">
        <div class="glass-card border-t border-white/[0.12] rounded-t-[32px] p-5 w-full max-w-lg shadow-2xl relative mobile-sheet-enter pb-[calc(env(safe-area-inset-bottom,0px)+24px)]" onclick="event.stopPropagation()">
            <!-- Drag Handle -->
            <div class="w-12 h-1.5 rounded-full bg-slate-700/80 mx-auto mb-4"></div>

            <div class="flex items-center justify-between pb-4 border-b border-white/[0.08]">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 via-brand-600 to-indigo-700 flex items-center justify-center font-black text-white text-base shadow-md uppercase border border-white/20">
                        <?= substr($user['username'] ?? 'U', 0, 2) ?>
                    </div>
                    <div>
                        <div class="text-sm font-black text-white flex items-center space-x-1.5">
                            <span><?= htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                            <?php if ((int)($user['is_vip'] ?? 0) === 1): ?>
                                <i class="fa-solid fa-crown text-amber-400 text-xs"></i>
                            <?php endif; ?>
                        </div>
                        <div class="text-xs text-slate-400 truncate"><?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                </div>

                <button type="button" onclick="closeMobileDrawer()" class="w-8 h-8 rounded-full bg-white/[0.06] text-slate-400 flex items-center justify-center active:scale-95">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Quick Coin Summary inside drawer -->
            <div class="p-3.5 my-3.5 rounded-2xl bg-dark-950/80 border border-white/[0.06] flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Saldo Koin Aktif</span>
                    <span class="font-mono font-black text-lg text-amber-300"><?= number_format($user['coins'] ?? 0) ?> 🪙</span>
                </div>
                <div>
                    <?php if ((int)($user['is_vip'] ?? 0) === 1): ?>
                        <span class="px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 text-[10px] font-black uppercase">
                            VIP Sultan 👑
                        </span>
                    <?php else: ?>
                        <a href="index.php?page=shop" class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-300 border border-amber-500/30 text-[10px] font-bold">
                            Upgrade VIP &rarr;
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Drawer Links -->
            <div class="space-y-1 text-xs">
                <a href="index.php?page=tasks&action=create" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-slate-200 hover:bg-white/[0.06] active:bg-white/[0.1] font-semibold transition-all">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center"><i class="fa-solid fa-plus text-xs"></i></div>
                    <span>Pasang Kampanye Baru</span>
                </a>
                <a href="index.php?page=tasks&action=my_tasks" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-slate-200 hover:bg-white/[0.06] active:bg-white/[0.1] font-semibold transition-all">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center"><i class="fa-solid fa-layer-group text-xs"></i></div>
                    <span>Kelola Kampanye Saya</span>
                </a>
                <a href="index.php?page=referral" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-slate-200 hover:bg-white/[0.06] active:bg-white/[0.1] font-semibold transition-all">
                    <div class="w-8 h-8 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center"><i class="fa-solid fa-gift text-xs"></i></div>
                    <span>Misi Bonus Afiliasi (Ajak Teman)</span>
                </a>
                <a href="index.php?page=shop" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-slate-200 hover:bg-white/[0.06] active:bg-white/[0.1] font-semibold transition-all">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center"><i class="fa-solid fa-crown text-xs"></i></div>
                    <span>Toko Koin & Upgrade VIP Sultan</span>
                </a>
                <a href="index.php?page=community" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-indigo-300 hover:bg-white/[0.06] active:bg-white/[0.1] font-semibold transition-all">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-brand-400 flex items-center justify-center"><i class="fa-solid fa-comments text-xs"></i></div>
                    <span>💬 Komunitas & Grup WA</span>
                </a>
                <?php if (Auth::isAdmin()): ?>
                <a href="index.php?page=admin" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-amber-300 hover:bg-amber-500/10 active:bg-amber-500/20 font-bold transition-all">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center"><i class="fa-solid fa-shield-halved text-xs"></i></div>
                    <span>Admin Control Room</span>
                </a>
                <?php endif; ?>
                <div class="pt-2 border-t border-white/[0.08]">
                    <a href="index.php?page=logout" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl text-rose-400 hover:bg-rose-500/10 active:bg-rose-500/20 font-bold transition-all">
                        <div class="w-8 h-8 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center"><i class="fa-solid fa-arrow-right-from-bracket text-xs"></i></div>
                        <span>Keluar Akun (Logout)</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- FLASH NOTIFICATIONS -->
    <div class="max-w-7xl mx-auto px-3.5 sm:px-6 lg:px-8 mt-3 w-full">
        <?php if (!empty($flash['success'])): ?>
            <?php foreach ($flash['success'] as $msg): ?>
                <div class="flex items-center justify-between p-3.5 mb-2 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs sm:text-sm shadow-xl shadow-emerald-500/5 animate-fade-in">
                    <div class="flex items-center space-x-2.5">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
                        <span><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-400/60 hover:text-emerald-400">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($flash['error'])): ?>
            <?php foreach ($flash['error'] as $msg): ?>
                <div class="flex items-center justify-between p-3.5 mb-2 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs sm:text-sm shadow-xl shadow-rose-500/5 animate-fade-in">
                    <div class="flex items-center space-x-2.5">
                        <i class="fa-solid fa-circle-exclamation text-rose-400 text-base"></i>
                        <span><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-400/60 hover:text-rose-400">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- MAIN BODY CONTENT CONTAINER (Full Width Padding Optimized for HP) -->
    <main class="flex-grow max-w-7xl mx-auto px-3.5 sm:px-6 lg:px-8 py-3 sm:py-6 w-full">
        <?= $content ?>
    </main>

    <!-- PREMIER DESKTOP FOOTER -->
    <footer class="hidden md:block border-t border-white/[0.06] bg-dark-950/80 mt-16 py-8 text-xs text-slate-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-2.5">
                <span class="font-display font-black text-white text-base tracking-tight">PAKAINI</span>
                <span class="text-slate-400 font-mono text-[11px] flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Ekosistem Koin Murni</span>
            </div>
            <div class="flex items-center space-x-6 text-[11px] text-slate-400">
                <span class="flex items-center space-x-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span>Sistem Nilai Tukar Transparan</span>
                </span>
                <span class="flex items-center space-x-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                    <span>Validasi Streak Anti-Tuyul</span>
                </span>
                <span class="flex items-center space-x-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                    <span>Anti-Unfollow Denda 3x</span>
                </span>
            </div>
            <div class="text-slate-400 font-mono text-[11px]">
                &copy; <?= date('Y') ?> PAKAINI Platform. Shared Hosting Ready.
            </div>
        </div>
    </footer>

    <!-- NATIVE-LIKE MOBILE APP BOTTOM DOCK BAR (Ergonomis Jempol HP & Dukungan Safe Area iOS/Android) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-dark-950/92 backdrop-blur-2xl border-t border-white/[0.12] px-2 pt-1.5 pb-[calc(env(safe-area-inset-bottom,0px)+8px)] shadow-2xl">
        <div class="grid grid-cols-5 text-center items-center">
            
            <?php 
                $isHome = ($currPage === 'dashboard' || empty($currPage));
                $isTasks = ($currPage === 'tasks' && empty($currAction));
                $isCreate = ($currAction === 'create');
                $isComm = ($currPage === 'community');
                $isShop = ($currPage === 'shop');
            ?>

            <!-- 1. Home / Dashboard -->
            <a href="index.php?page=dashboard" class="flex flex-col items-center justify-center py-1 transition-all active:scale-90 <?= $isHome ? 'text-white font-bold' : 'text-slate-400 hover:text-slate-200' ?>">
                <div class="relative w-8 h-8 rounded-xl flex items-center justify-center <?= $isHome ? 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/30' : '' ?>">
                    <i class="fa-solid fa-gauge text-base"></i>
                    <?php if ($isHome): ?>
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 absolute -bottom-0.5 left-1/2 -translate-x-1/2 shadow-[0_0_8px_#818cf8]"></span>
                    <?php endif; ?>
                </div>
                <span class="text-[10px] mt-0.5 tracking-tight">Home</span>
            </a>

            <!-- 2. Cari Koin -->
            <a href="index.php?page=tasks" class="flex flex-col items-center justify-center py-1 transition-all active:scale-90 <?= $isTasks ? 'text-white font-bold' : 'text-slate-400 hover:text-slate-200' ?>">
                <div class="relative w-8 h-8 rounded-xl flex items-center justify-center <?= $isTasks ? 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/30' : '' ?>">
                    <i class="fa-solid fa-coins text-base"></i>
                    <?php if ($isTasks): ?>
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 absolute -bottom-0.5 left-1/2 -translate-x-1/2 shadow-[0_0_8px_#818cf8]"></span>
                    <?php endif; ?>
                </div>
                <span class="text-[10px] mt-0.5 tracking-tight">Cari Koin</span>
            </a>

            <!-- 3. Pasang Kampanye (Center Luxury Floating Action Button) -->
            <a href="index.php?page=tasks&action=create" class="flex flex-col items-center justify-center -mt-6 group active:scale-90 transition-transform">
                <div class="w-13 h-13 p-1 rounded-2xl bg-slate-900 border border-white/[0.12] shadow-2xl flex items-center justify-center">
                    <div class="w-11 h-11 rounded-xl bg-indigo-600 hover:bg-indigo-500 flex items-center justify-center text-white shadow-lg shadow-indigo-600/30">
                        <i class="fa-solid fa-plus text-base group-hover:rotate-90 transition-transform duration-200"></i>
                    </div>
                </div>
                <span class="text-[10px] text-white font-black mt-1 tracking-tight">Pasang</span>
            </a>

            <!-- 4. Komunitas Chat -->
            <a href="index.php?page=community" class="flex flex-col items-center justify-center py-1 transition-all active:scale-90 <?= $isComm ? 'text-white font-bold' : 'text-slate-400 hover:text-slate-200' ?>">
                <div class="relative w-8 h-8 rounded-xl flex items-center justify-center <?= $isComm ? 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/30' : '' ?>">
                    <i class="fa-solid fa-comments text-base"></i>
                    <?php if ($isComm): ?>
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 absolute -bottom-0.5 left-1/2 -translate-x-1/2 shadow-[0_0_8px_#818cf8]"></span>
                    <?php endif; ?>
                </div>
                <span class="text-[10px] mt-0.5 tracking-tight">Komunitas</span>
            </a>

            <!-- 5. Toko VIP -->
            <a href="index.php?page=shop" class="flex flex-col items-center justify-center py-1 transition-all active:scale-90 <?= $isShop ? 'text-amber-300 font-bold' : 'text-slate-400 hover:text-slate-200' ?>">
                <div class="relative w-8 h-8 rounded-xl flex items-center justify-center <?= $isShop ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : '' ?>">
                    <i class="fa-solid fa-crown text-base"></i>
                    <?php if ($isShop): ?>
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 absolute -bottom-0.5 left-1/2 -translate-x-1/2 shadow-[0_0_8px_#fbbf24]"></span>
                    <?php endif; ?>
                </div>
                <span class="text-[10px] mt-0.5 tracking-tight">Toko VIP</span>
            </a>

        </div>
    </nav>

    <!-- Floating Back to Top Button -->
    <button id="btnBackToTop" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
            class="fixed bottom-22 md:bottom-8 right-3.5 sm:right-4 z-40 w-10 h-10 rounded-2xl bg-dark-900/90 hover:bg-brand-600 text-slate-300 hover:text-white border border-white/[0.1] hover:border-brand-500 shadow-2xl backdrop-blur-md transition-all duration-300 transform translate-y-16 opacity-0 pointer-events-none flex items-center justify-center group"
            title="Kembali ke atas">
        <i class="fa-solid fa-arrow-up text-xs group-hover:-translate-y-0.5 transition-transform"></i>
    </button>

    <!-- Global Toast / Alert Helper -->
    <script>
        function showNotification(type, message) {
            const container = document.createElement('div');
            container.className = `fixed top-16 sm:top-20 right-3.5 sm:right-4 left-3.5 sm:left-auto z-50 max-w-sm p-3.5 rounded-2xl shadow-2xl border text-xs sm:text-sm transition-all duration-300 flex items-start space-x-3 ${
                type === 'success' ? 'bg-emerald-950/95 border-emerald-500/50 text-emerald-200' :
                type === 'error'   ? 'bg-rose-950/95 border-rose-500/50 text-rose-200' :
                                     'bg-slate-900/95 border-slate-700 text-slate-200'
            }`;
            container.innerHTML = `
                <i class="fa-solid ${type === 'success' ? 'fa-circle-check text-emerald-400' : 'fa-circle-exclamation text-rose-400'} text-base mt-0.5 flex-shrink-0"></i>
                <span class="flex-1">${message}</span>
            `;
            document.body.appendChild(container);
            setTimeout(() => {
                container.style.opacity = '0';
                container.style.transform = 'translateY(-10px)';
                setTimeout(() => container.remove(), 300);
            }, 3500);
        }

        // Top Scroll Progress Bar Script
        window.addEventListener('scroll', () => {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = height > 0 ? (winScroll / height) * 100 : 0;
            const bar = document.getElementById('scrollProgressBar');
            if (bar) bar.style.width = scrolled + '%';

            const btnTop = document.getElementById('btnBackToTop');
            if (btnTop) {
                if (winScroll > 320) {
                    btnTop.classList.remove('translate-y-16', 'opacity-0', 'pointer-events-none');
                    btnTop.classList.add('translate-y-0', 'opacity-100');
                } else {
                    btnTop.classList.add('translate-y-16', 'opacity-0', 'pointer-events-none');
                    btnTop.classList.remove('translate-y-0', 'opacity-100');
                }
            }
        });

        // User Profile Menu Handlers (Desktop Dropdown vs Mobile Bottom Sheet Drawer)
        function toggleUserMenu() {
            if (window.innerWidth < 768) {
                const drawer = document.getElementById('mobileProfileDrawer');
                if (drawer) {
                    drawer.classList.remove('hidden');
                    drawer.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                }
            } else {
                const dropdown = document.getElementById('desktopProfileDropdown');
                if (dropdown) dropdown.classList.toggle('hidden');
            }
        }

        function closeMobileDrawer() {
            const drawer = document.getElementById('mobileProfileDrawer');
            if (drawer) {
                drawer.classList.add('hidden');
                drawer.classList.remove('flex');
                document.body.style.overflow = '';
            }
        }

        function closeUserMenu(e) {
            closeMobileDrawer();
        }

        // Close desktop dropdown on click outside
        document.addEventListener('click', (e) => {
            const btn = document.getElementById('btnUserMenu');
            const dropdown = document.getElementById('desktopProfileDropdown');
            if (dropdown && !dropdown.classList.contains('hidden')) {
                if (btn && !btn.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            }
        });
    </script>

</body>
</html>
