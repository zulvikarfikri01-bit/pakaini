<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Admin Control Room', ENT_QUOTES, 'UTF-8') ?> — PAKAINI PRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">
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
                            500: '#8b5cf6',
                            600: '#6366f1',
                            700: '#4f46e5',
                        },
                        dark: {
                            950: '#030712',
                            900: '#0b0f19',
                        }
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
        }
        body { 
            background-color: #030712; 
            color: #f1f5f9; 
            font-family: 'Inter', sans-serif;
            background-image: 
                radial-gradient(ellipse 70% 40% at 20% 0%, rgba(245, 158, 11, 0.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 10%, rgba(99, 102, 241, 0.15) 0%, transparent 60%),
                radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 28px 28px;
            background-attachment: fixed;
        }
        .glass-card {
            background: linear-gradient(135deg, rgba(17, 24, 39, 0.85) 0%, rgba(11, 15, 25, 0.92) 100%);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.65);
        }
        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        ::-webkit-scrollbar-track {
            background: #030712;
        }
        ::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 9999px;
            border: 2px solid #030712;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #f59e0b;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row antialiased">

    <!-- Admin Scroll Progress Bar -->
    <div class="fixed top-0 left-0 right-0 z-50 h-[3px] bg-transparent pointer-events-none">
        <div id="adminProgressBar" class="h-full w-0 bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-500 shadow-md shadow-amber-500/50 transition-all duration-100"></div>
    </div>

    <!-- Admin Sidebar -->
    <aside class="w-full md:w-64 bg-dark-950/90 backdrop-blur-xl border-r border-white/[0.08] p-5 flex flex-col justify-between flex-shrink-0">
        <div>
            <!-- Admin Brand -->
            <a href="index.php?page=admin" class="flex items-center space-x-3 px-2 py-3 mb-6 border-b border-white/[0.08] group">
                <img src="assets/img/logo.png" onerror="this.onerror=null; this.src='logo.png';" alt="Logo PAKAINI" class="h-10 sm:h-12 w-auto object-contain rounded-xl transition-transform group-hover:scale-105 drop-shadow-md">
                <div>
                    <h1 class="font-display font-black text-lg text-white tracking-tight group-hover:text-amber-300 transition-colors">PAKAINI</h1>
                    <span class="text-[9px] text-amber-400 font-black uppercase tracking-widest font-mono">Control Room</span>
                </div>
            </a>

            <!-- Admin Nav -->
            <nav class="space-x-1 md:space-x-0 md:space-y-1.5 flex md:flex-col overflow-x-auto pb-2 md:pb-0">
                <?php $currAction = $_GET['action'] ?? 'index'; ?>
                <a href="index.php?page=admin" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap <?= ($currAction === 'index' || empty($currAction)) ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-white/[0.04]' ?>">
                    <i class="fa-solid fa-chart-line w-6 text-sm text-amber-400"></i>Ringkasan Sistem
                </a>
                <a href="index.php?page=admin&action=users" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap <?= ($currAction === 'users') ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-white/[0.04]' ?>">
                    <i class="fa-solid fa-users-gear w-6 text-sm text-sky-400"></i>Kelola User & Akun
                </a>
                <a href="index.php?page=admin&action=tasks" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap <?= ($currAction === 'tasks') ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-white/[0.04]' ?>">
                    <i class="fa-solid fa-list-check w-6 text-sm text-indigo-400"></i>Kelola Kampanye
                </a>
                <a href="index.php?page=admin&action=reports" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap <?= ($currAction === 'reports') ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-white/[0.04]' ?>">
                    <i class="fa-solid fa-shield-virus w-6 text-sm text-rose-400"></i>Validasi Kecurangan
                </a>
                <a href="index.php?page=admin&action=transactions" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap <?= ($currAction === 'transactions') ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-white/[0.04]' ?>">
                    <i class="fa-solid fa-receipt w-6 text-sm text-emerald-400"></i>Buku Kas & Mutasi
                </a>
                <a href="index.php?page=admin&action=settings" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap <?= ($currAction === 'settings') ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30 shadow-sm' : 'text-slate-400 hover:text-white hover:bg-white/[0.04]' ?>">
                    <i class="fa-solid fa-sliders w-6 text-sm text-purple-400"></i>Pengaturan Web
                </a>
            </nav>
        </div>

        <div class="mt-6 pt-4 border-t border-white/[0.08] space-y-2">
            <div class="px-3.5 py-2 rounded-xl bg-white/[0.02] border border-white/[0.05] flex items-center justify-between">
                <div class="truncate">
                    <p class="text-[11px] font-bold text-white truncate"><?= htmlspecialchars((Auth::user()['username'] ?? 'Admin'), ENT_QUOTES, 'UTF-8') ?></p>
                    <span class="text-[9px] text-amber-400 font-semibold uppercase">Super Administrator</span>
                </div>
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            </div>
            <a href="index.php?page=dashboard" class="flex items-center px-3.5 py-2 text-xs font-semibold text-slate-400 hover:text-white hover:bg-white/[0.04] rounded-xl transition-colors">
                <i class="fa-solid fa-arrow-left w-5"></i>Area Member
            </a>
            <a href="index.php?page=logout" class="flex items-center px-3.5 py-2 text-xs font-semibold text-rose-400 hover:bg-rose-500/10 rounded-xl transition-colors">
                <i class="fa-solid fa-power-off w-5"></i>Keluar
            </a>
        </div>
    </aside>

    <!-- Main Admin Content Area -->
    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        
        <!-- Flash notifications in admin -->
        <div class="p-4 sm:p-6 pb-0">
            <?php if (!empty($flash['success'])): ?>
                <?php foreach ($flash['success'] as $msg): ?>
                    <div class="p-4 mb-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs sm:text-sm flex items-center justify-between shadow-lg">
                        <div class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-circle-check text-emerald-400"></i>
                            <span><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-400/60 hover:text-emerald-400"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($flash['error'])): ?>
                <?php foreach ($flash['error'] as $msg): ?>
                    <div class="p-4 mb-3 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs sm:text-sm flex items-center justify-between shadow-lg">
                        <div class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-circle-exclamation text-rose-400"></i>
                            <span><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-rose-400/60 hover:text-rose-400"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="p-4 sm:p-6 lg:p-8">
            <?= $content ?>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mainArea = document.querySelector('main');
            const progressBar = document.getElementById('adminProgressBar');

            if (mainArea && progressBar) {
                mainArea.addEventListener('scroll', () => {
                    const max = mainArea.scrollHeight - mainArea.clientHeight;
                    const pct = max > 0 ? (mainArea.scrollTop / max) * 100 : 0;
                    progressBar.style.width = `${pct}%`;
                }, { passive: true });
            }
        });
    </script>
</body>
</html>
