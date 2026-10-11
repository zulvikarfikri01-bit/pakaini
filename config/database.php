<?php
/**
 * PAKAINI - Social Media Exchange Platform
 * Halaman Depan / Landing Page Promosi Utama (index.php)
 * Menggunakan Tailwind CSS Modern, Responsif & Estetik
 */

defined('ROOT_PATH') or define('ROOT_PATH', __DIR__);
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config.php';

// Jika terdapat parameter ?page=, alihkan ke Router MVC jika tersedia
$pageParam = $_GET['page'] ?? '';
if (!empty($pageParam) && file_exists(ROOT_PATH . '/app/Core/Router.php')) {
    require_once ROOT_PATH . '/app/Core/Router.php';
    $router = new Router();
    $router->register('dashboard', 'DashboardController');
    $router->register('tasks', 'TaskController');
    $router->register('referral', 'ReferralController');
    $router->register('shop', 'ShopController');
    $router->register('community', 'CommunityController');
    $router->register('reports', 'ReportController');
    $router->register('admin', 'AdminController');
    $router->register('claim_vip', 'DashboardController', 'claimVip');
    $router->register('login', 'AuthController', 'login');
    $router->register('register', 'AuthController', 'register');
    $router->register('logout', 'AuthController', 'logout');

    $router->dispatch();
    exit;
}

// Cek status login pengguna
$currentUser = current_user($pdo);
$pageTitle = 'PAKAINI — Tingkatkan Followers, Subscribers, dan Likes Anda secara Gratis!';
?>
<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>

    <!-- Open Graph Meta Tags (Untuk Berbagi WhatsApp/FB) -->
    <!-- CATATAN: Silakan sesuaikan URL domain pada tag og:image (https://(domain-anda)/logo.png) dengan domain aktif website Anda nanti -->
    <meta property="og:title" content="Pakaini - Social Media Exchange & Growth Platform">
    <meta property="og:description" content="Tingkatkan Followers, Subscribers, dan interaksi media sosial Anda dengan komunitas pengguna asli Indonesia. Zero Risk, Cepat & Aman!">
    <meta property="og:image" content="https://(domain-anda)/logo.png">
    <meta property="og:type" content="website">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
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
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
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
                            850: '#111827'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #0B0F19;
            color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            background-image: 
                radial-gradient(ellipse 90% 60% at 50% -20%, rgba(99, 102, 241, 0.22) 0%, transparent 65%),
                radial-gradient(ellipse 70% 45% at 85% 95%, rgba(139, 92, 246, 0.15) 0%, transparent 55%),
                radial-gradient(rgba(255, 255, 255, 0.035) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 32px 32px;
            background-attachment: fixed;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.09);
            box-shadow: 0 12px 35px -10px rgba(0, 0, 0, 0.65);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            border-color: rgba(139, 92, 246, 0.35);
            box-shadow: 0 0 25px rgba(139, 92, 246, 0.15);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between antialiased selection:bg-purple-600 selection:text-white pb-20 md:pb-0">

    <!-- Top Sticky Header / Navigation Bar -->
    <header class="sticky top-0 z-50 bg-slate-950/80 backdrop-blur-xl border-b border-white/10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 sm:h-20 flex items-center justify-between">
            
            <!-- Brand Logo -->
            <a href="<?= $currentUser ? 'dashboard.php' : 'index.php' ?>" class="flex items-center space-x-2.5 group">
                <img src="assets/img/logo.png" onerror="this.onerror=null; this.src='logo.png';" alt="Logo PAKAINI" class="h-10 sm:h-12 w-auto object-contain rounded-xl transition-transform group-hover:scale-105 drop-shadow-md">
                <div>
                    <span class="font-display font-black text-lg sm:text-xl text-white tracking-tight group-hover:text-purple-300 transition-colors">
                        PAKAINI
                    </span>
                    <span class="text-[9px] text-slate-400 uppercase tracking-widest font-semibold block -mt-1 hidden sm:block">
                        Social Media Exchange #1
                    </span>
                </div>
            </a>

            <!-- Navigation Anchors -->
            <nav class="hidden md:flex items-center space-x-6 text-xs font-semibold text-slate-300">
                <a href="#cara-kerja" class="hover:text-purple-300 transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-compass text-slate-400"></i> Cara Kerja
                </a>
                <a href="#keunggulan" class="hover:text-purple-300 transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-halved text-slate-400"></i> Keunggulan
                </a>
                <a href="#platform" class="hover:text-purple-300 transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-share-nodes text-slate-400"></i> Platform
                </a>
                <a href="index.php?page=community" class="hover:text-emerald-400 transition-colors flex items-center gap-1.5 text-emerald-300">
                    <i class="fa-brands fa-whatsapp text-emerald-400"></i> Komunitas WA
                </a>
            </nav>

            <!-- Action Buttons (Right) -->
            <div class="flex items-center space-x-2 sm:space-x-3">
                <?php if ($currentUser): ?>
                    <a href="dashboard.php" class="px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-display font-bold text-xs sm:text-sm shadow-lg hover:shadow-[0_0_20px_rgba(139,92,246,0.4)] transition-all duration-300 flex items-center gap-2">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>Ke Dashboard</span>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl text-xs sm:text-sm font-bold text-slate-300 hover:text-white hover:bg-white/[0.05] transition-all">
                        <i class="fa-solid fa-right-to-bracket mr-1 text-slate-400"></i> Masuk
                    </a>
                    <a href="register.php" class="px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-display font-black text-xs sm:text-sm shadow-lg hover:shadow-[0_0_20px_rgba(139,92,246,0.4)] hover:scale-[1.03] transition-all duration-300 flex items-center gap-1.5">
                        <i class="fa-solid fa-bolt text-xs"></i>
                        <span>Mulai Sekarang</span>
                        <span class="hidden sm:inline-block text-[10px] px-1.5 py-0.5 rounded-full bg-slate-950/70 text-purple-300 font-bold ml-1 border border-white/10">+500 Koin</span>
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </header>

    <!-- Main Content Container -->
    <main>

        <!-- 1. HERO SECTION -->
        <section class="max-w-5xl mx-auto px-4 sm:px-6 pt-12 sm:pt-20 pb-16 text-center relative">
            
            <!-- Soft Ambient Glow Backing -->
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[300px] sm:w-[550px] h-[300px] sm:h-[400px] bg-gradient-to-tr from-blue-600/20 via-purple-600/20 to-indigo-500/20 rounded-full blur-3xl pointer-events-none -z-10"></div>

            <!-- Value Badge -->
            <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-gradient-to-r from-blue-500/15 via-purple-600/15 to-indigo-500/15 border border-purple-500/30 text-purple-200 text-xs sm:text-sm font-semibold mb-6 shadow-sm">
                <i class="fa-solid fa-crown text-purple-400 animate-pulse"></i>
                <span>Platform Pertukaran Media Sosial Organik #1 Indonesia</span>
                <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                <span class="font-bold text-white">100% Gratis</span>
            </div>

            <!-- Big Main Headline (Direct & To-The-Point) -->
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-display font-black text-white tracking-tight max-w-4xl mx-auto leading-[1.18]">
                Tingkatkan Followers, Subscribers, dan Likes Anda secara
                <span class="bg-gradient-to-r from-blue-400 via-indigo-300 to-purple-400 bg-clip-text text-transparent underline decoration-purple-400/30 decoration-wavy decoration-2">
                    Gratis & Organik!
                </span>
            </h1>

            <!-- Subtitle Description -->
            <p class="text-sm sm:text-base lg:text-lg text-slate-300 max-w-2xl mx-auto mt-5 sm:mt-6 leading-relaxed">
                Platform gotong-royong sesama kreator Indonesia. Saling bantu follow, like, dan subscribe tanpa biaya sepeserpun. Kumpulkan koin dan viralkan akun media sosial Anda sekarang juga!
            </p>

            <!-- Call To Action Buttons -->
            <div class="mt-8 sm:mt-10 flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 max-w-md mx-auto">
                <a href="register.php" class="w-full sm:w-auto px-7 py-4 rounded-2xl bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-display font-black text-sm sm:text-base shadow-xl hover:shadow-[0_0_25px_rgba(139,92,246,0.5)] hover:scale-[1.03] active:scale-95 transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rocket text-sm"></i>
                    <span>Mulai Sekarang (Gratis Koin)</span>
                </a>
                <a href="login.php" class="w-full sm:w-auto px-6 py-4 rounded-2xl bg-slate-900/80 border border-white/10 hover:border-purple-500/40 text-white font-bold text-sm sm:text-base hover:bg-slate-800 transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket text-purple-400"></i>
                    <span>Masuk Akun Saya</span>
                </a>
            </div>

            <!-- Social Proof Points Bar -->
            <div class="mt-12 sm:mt-16 pt-8 border-t border-white/10 grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div class="p-3">
                    <div class="text-xl sm:text-2xl font-display font-black text-amber-300 flex items-center justify-center gap-1">
                        <i class="fa-solid fa-user-check text-amber-400 text-base"></i>
                        <span>100% Nyata</span>
                    </div>
                    <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5">Pengguna Asli, Tanpa Bot</p>
                </div>
                <div class="p-3">
                    <div class="text-xl sm:text-2xl font-display font-black text-sky-300 flex items-center justify-center gap-1">
                        <i class="fa-solid fa-stopwatch text-sky-400 text-base"></i>
                        <span>15 Detik</span>
                    </div>
                    <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5">Timer Verifikasi Berkualitas</p>
                </div>
                <div class="p-3">
                    <div class="text-xl sm:text-2xl font-display font-black text-emerald-300 flex items-center justify-center gap-1">
                        <i class="fa-solid fa-shield-halved text-emerald-400 text-base"></i>
                        <span>Anti-Unfollow</span>
                    </div>
                    <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5">Proteksi Tugas 1x Permanen</p>
                </div>
                <div class="p-3">
                    <div class="text-xl sm:text-2xl font-display font-black text-purple-300 flex items-center justify-center gap-1">
                        <i class="fa-solid fa-gift text-purple-400 text-base"></i>
                        <span>+500 Koin</span>
                    </div>
                    <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5">Bonus Pendaftaran Baru</p>
                </div>
            </div>

        </section>

        <!-- 2. SECTION CARA KERJA (3 LANGKAH MUDAH) -->
        <section id="cara-kerja" class="py-16 sm:py-24 border-t border-white/[0.06] bg-slate-950/40">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                
                <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16">
                    <span class="px-3 py-1 rounded-full bg-indigo-500/15 border border-indigo-500/30 text-indigo-300 text-xs font-bold uppercase tracking-wider">
                        🚀 Alur Sederhana
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-display font-black text-white mt-3 tracking-tight">
                        Cara Kerja PAKAINI (3 Langkah Mudah)
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-400 mt-2">
                        Semua orang bisa mulai dalam hitungan detik. Cukup ikuti 3 langkah berikut untuk menaikkan engagement akun Anda:
                    </p>
                </div>

                <!-- 3 Columns Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
                    
                    <!-- Langkah 1: Kumpulkan Koin -->
                    <div class="glass-card p-6 sm:p-8 rounded-3xl border border-white/[0.08] relative group hover:border-amber-500/40 transition-all flex flex-col justify-between">
                        <div>
                            <!-- Step Badge & Icon -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-14 h-14 rounded-2xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shadow-md shadow-amber-500/10">
                                    <i class="fa-solid fa-coins"></i>
                                </div>
                                <span class="font-display font-black text-3xl sm:text-4xl text-amber-500/20 group-hover:text-amber-500/40 transition-colors">
                                    01
                                </span>
                            </div>

                            <h3 class="font-display font-extrabold text-white text-lg sm:text-xl">
                                Kumpulkan Koin
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-300 mt-2.5 leading-relaxed">
                                Kerjakan tugas dari orang lain untuk mendapat koin. Cukup tonton video, follow, atau subscribe akun member lain selama 15 detik, dan saldo koin langsung otomatis masuk ke akun Anda.
                            </p>
                        </div>

                        <div class="mt-6 pt-4 border-t border-white/[0.06] flex items-center justify-between text-xs font-bold text-amber-300">
                            <span>✓ 100% Gratis Tanpa Modal</span>
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </div>

                    <!-- Langkah 2: Pasang Kampanye -->
                    <div class="glass-card p-6 sm:p-8 rounded-3xl border border-white/[0.08] relative group hover:border-sky-500/40 transition-all flex flex-col justify-between">
                        <div>
                            <!-- Step Badge & Icon -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-14 h-14 rounded-2xl bg-sky-500/15 border border-sky-500/30 text-sky-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shadow-md shadow-sky-500/10">
                                    <i class="fa-solid fa-bullhorn"></i>
                                </div>
                                <span class="font-display font-black text-3xl sm:text-4xl text-sky-500/20 group-hover:text-sky-500/40 transition-colors">
                                    02
                                </span>
                            </div>

                            <h3 class="font-display font-extrabold text-white text-lg sm:text-xl">
                                Pasang Kampanye
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-300 mt-2.5 leading-relaxed">
                                Gunakan koin untuk mempromosikan link sosmed Anda. Masukkan tautan YouTube, TikTok, atau Instagram Anda, tentukan jumlah follower yang Anda inginkan, dan biarkan sistem berjalan otomatis.
                            </p>
                        </div>

                        <div class="mt-6 pt-4 border-t border-white/[0.06] flex items-center justify-between text-xs font-bold text-sky-300">
                            <span>✓ Atur Target Sesuka Hati</span>
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </div>

                    <!-- Langkah 3: Nikmati Hasilnya -->
                    <div class="glass-card p-6 sm:p-8 rounded-3xl border border-white/[0.08] relative group hover:border-emerald-500/40 transition-all flex flex-col justify-between">
                        <div>
                            <!-- Step Badge & Icon -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-14 h-14 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shadow-md shadow-emerald-500/10">
                                    <i class="fa-solid fa-chart-line"></i>
                                </div>
                                <span class="font-display font-black text-3xl sm:text-4xl text-emerald-500/20 group-hover:text-emerald-500/40 transition-colors">
                                    03
                                </span>
                            </div>

                            <h3 class="font-display font-extrabold text-white text-lg sm:text-xl">
                                Nikmati Hasilnya
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-300 mt-2.5 leading-relaxed">
                                Dapatkan interaksi nyata dari pengguna lain. Subscribers, followers, dan likes Anda bertambah secara alami, aman, dan tanpa risiko bot karena dikerjakan langsung oleh akun manusia asli.
                            </p>
                        </div>

                        <div class="mt-6 pt-4 border-t border-white/[0.06] flex items-center justify-between text-xs font-bold text-emerald-300">
                            <span>✓ Interaksi Organik & Asli</span>
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- 3. PLATFORM MEDIA SOSIAL YANG DIDUKUNG -->
        <section id="platform" class="py-16 sm:py-20 border-t border-white/[0.06]">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                
                <div class="text-center max-w-xl mx-auto mb-12">
                    <span class="px-3 py-1 rounded-full bg-sky-500/15 border border-sky-400/30 text-sky-300 text-xs font-bold uppercase tracking-wider">
                        📱 Multi-Platform Support
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-display font-black text-white mt-3">
                        Dukung Semua Platform Favorit Anda
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-400 mt-1.5">
                        Promosikan berbagai platform media sosial dalam satu akun PAKAINI:
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                    
                    <!-- YouTube -->
                    <div class="glass-card p-5 rounded-2xl border border-white/[0.08] text-center hover:border-red-500/40 transition-all group">
                        <div class="w-12 h-12 rounded-xl bg-red-600/20 text-red-500 flex items-center justify-center text-2xl mx-auto mb-3 group-hover:scale-110 transition-transform">
                            <i class="fa-brands fa-youtube"></i>
                        </div>
                        <h4 class="font-display font-bold text-white text-base">YouTube</h4>
                        <p class="text-[11px] text-slate-400 mt-1">Subscribers, Views, & Likes Video</p>
                    </div>

                    <!-- TikTok -->
                    <div class="glass-card p-5 rounded-2xl border border-white/[0.08] text-center hover:border-cyan-400/40 transition-all group">
                        <div class="w-12 h-12 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-2xl mx-auto mb-3 group-hover:scale-110 transition-transform">
                            <i class="fa-brands fa-tiktok"></i>
                        </div>
                        <h4 class="font-display font-bold text-white text-base">TikTok</h4>
                        <p class="text-[11px] text-slate-400 mt-1">Followers Akun & Likes Video</p>
                    </div>

                    <!-- Instagram -->
                    <div class="glass-card p-5 rounded-2xl border border-white/[0.08] text-center hover:border-pink-500/40 transition-all group">
                        <div class="w-12 h-12 rounded-xl bg-pink-600/20 text-pink-400 flex items-center justify-center text-2xl mx-auto mb-3 group-hover:scale-110 transition-transform">
                            <i class="fa-brands fa-instagram"></i>
                        </div>
                        <h4 class="font-display font-bold text-white text-base">Instagram</h4>
                        <p class="text-[11px] text-slate-400 mt-1">Followers Profil & Likes Postingan</p>
                    </div>

                    <!-- Facebook -->
                    <div class="glass-card p-5 rounded-2xl border border-white/[0.08] text-center hover:border-blue-500/40 transition-all group">
                        <div class="w-12 h-12 rounded-xl bg-blue-600/20 text-blue-400 flex items-center justify-center text-2xl mx-auto mb-3 group-hover:scale-110 transition-transform">
                            <i class="fa-brands fa-facebook"></i>
                        </div>
                        <h4 class="font-display font-bold text-white text-base">Facebook</h4>
                        <p class="text-[11px] text-slate-400 mt-1">Followers Profil & Suka Halaman</p>
                    </div>

                </div>

            </div>
        </section>

        <!-- 4. SECTION KEUNGGULAN & SISTEM PENGAMANAN -->
        <section id="keunggulan" class="py-16 sm:py-24 border-t border-white/[0.06] bg-slate-950/60">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                
                <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16">
                    <span class="px-3 py-1 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs font-bold uppercase tracking-wider">
                        🛡️ Aman & Terpercaya
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-display font-black text-white mt-3 tracking-tight">
                        Mengapa Harus Memilih PAKAINI?
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-400 mt-2">
                        Dibangun dengan sistem perlindungan ganda agar akun Anda aman dan tidak terkena sanksi spam platform.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div class="glass-card p-6 sm:p-7 rounded-3xl border border-white/[0.08] flex items-start space-x-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-500/15 text-amber-400 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-white text-base sm:text-lg">Sistem Anti-Toggle / Anti-Unfollow</h3>
                            <p class="text-xs sm:text-sm text-slate-300 mt-1.5 leading-relaxed">
                                Satu pengguna hanya bisa mengerjakan 1 link tugas satu kali selamanya. Sistem ini mengunci riwayat sehingga tidak ada member yang bisa melakukan unfollow/unsubscribe berulang.
                            </p>
                        </div>
                    </div>

                    <div class="glass-card p-6 sm:p-7 rounded-3xl border border-white/[0.08] flex items-start space-x-4">
                        <div class="w-12 h-12 rounded-2xl bg-sky-500/15 text-sky-400 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-white text-base sm:text-lg">Timer 15 Detik Anti-Cheat</h3>
                            <p class="text-xs sm:text-sm text-slate-300 mt-1.5 leading-relaxed">
                                Setiap member wajib mengunjungi link selama minimal 15 detik sebelum koin dapat diklaim. Hal ini menjamin algoritma YouTube & TikTok merekam interaksi sebagai penonton aktif asli.
                            </p>
                        </div>
                    </div>

                    <div class="glass-card p-6 sm:p-7 rounded-3xl border border-white/[0.08] flex items-start space-x-4">
                        <div class="w-12 h-12 rounded-2xl bg-purple-500/15 text-purple-400 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-crown text-amber-300"></i>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-white text-base sm:text-lg">Fitur VIP Sultan Prioritas Teratas</h3>
                            <p class="text-xs sm:text-sm text-slate-300 mt-1.5 leading-relaxed">
                                Member VIP Sultan menikmati slot kampanye tanpa batas (unlimited), klik tugas unlimited, dan seluruh tugas diprioritaskan di posisi paling atas layar.
                            </p>
                        </div>
                    </div>

                    <div class="glass-card p-6 sm:p-7 rounded-3xl border border-white/[0.08] flex items-start space-x-4">
                        <div class="w-12 h-12 rounded-2xl bg-purple-500/15 text-purple-400 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-gift"></i>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-white text-base sm:text-lg">Program Referral Ribuan Koin Gratis</h3>
                            <p class="text-xs sm:text-sm text-slate-300 mt-1.5 leading-relaxed">
                                Bagikan link referral Anda ke sesama kreator. Dapatkan bonus ribuan Koin Gratis untuk setiap teman aktif yang bergabung guna melipatgandakan kampanye promosi Anda!
                            </p>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- 5. KOTAK KOMUNITAS WHATSAPP & TELEGRAM -->
        <section class="py-14 sm:py-20 border-t border-white/[0.06]">
            <div class="max-w-4xl mx-auto px-4 sm:px-6">
                <div class="glass-card p-6 sm:p-10 rounded-3xl border border-emerald-500/30 text-center relative overflow-hidden">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-2xl mx-auto mb-4 shadow-lg shadow-emerald-500/20">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-display font-black text-white">
                        💬 Komunitas Saling Sapa & Diskusi Kreator
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-300 max-w-xl mx-auto mt-2 leading-relaxed">
                        Gabung ke grup resmi WhatsApp dan Telegram PAKAINI. Berkenalan dengan sesama pejuang monetisasi, saling tukar tips konten, dan dapatkan info bonus koin harian!
                    </p>
                    
                    <div class="flex flex-wrap justify-center gap-3 sm:gap-4 mt-6">
                        <a href="https://chat.whatsapp.com/J6M0TwrjuvoGm4Mq1eJz2G" target="_blank" 
                           class="flex items-center px-5 py-3 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-400 hover:to-green-500 text-white font-bold text-xs sm:text-sm rounded-xl shadow-lg shadow-emerald-600/25 transition-all hover:scale-[1.02]">
                            <i class="fa-brands fa-whatsapp text-lg mr-2"></i>
                            <span>Gabung Grup WhatsApp</span>
                        </a>
                        <a href="https://t.me/pakaini_community" target="_blank" 
                           class="flex items-center px-5 py-3 bg-slate-900 border border-slate-700 hover:border-sky-400 text-sky-400 hover:text-white font-bold text-xs sm:text-sm rounded-xl transition-all">
                            <i class="fa-brands fa-telegram text-lg mr-2"></i>
                            <span>Kanal Telegram</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. FINAL CALL TO ACTION BANNER -->
        <section class="py-16 sm:py-24 border-t border-white/[0.06] bg-gradient-to-b from-transparent via-indigo-950/20 to-slate-950 text-center">
            <div class="max-w-4xl mx-auto px-4 sm:px-6">
                <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold uppercase tracking-wider">
                    🎉 Ambil Kesempatan Anda
                </span>
                <h2 class="text-3xl sm:text-5xl font-display font-black text-white mt-4 tracking-tight">
                    Mulai Kembangkan Akun Anda Hari Ini!
                </h2>
                <p class="text-sm sm:text-base text-slate-300 max-w-xl mx-auto mt-3">
                    Daftar gratis sekarang dan langsung dapatkan saldo 500 Koin pertama untuk kampanye media sosial Anda.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="register.php" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-display font-black text-sm sm:text-base shadow-xl hover:shadow-[0_0_20px_rgba(139,92,246,0.4)] hover:scale-[1.03] active:scale-95 transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-rocket text-sm"></i>
                        <span>Daftar Akun Baru Sekarang (Gratis)</span>
                    </a>
                    <a href="login.php" class="w-full sm:w-auto px-7 py-4 rounded-2xl bg-slate-900 border border-slate-700 hover:border-slate-500 text-white font-bold text-sm sm:text-base transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-right-to-bracket text-purple-400"></i>
                        <span>Masuk Akun Saya</span>
                    </a>
                </div>
            </div>
        </section>

    </main>

    <!-- Professional Footer -->
    <footer class="border-t border-white/[0.08] bg-slate-950 py-10 text-xs text-slate-500">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6 pb-6 border-b border-white/[0.06]">
                <div class="flex items-center space-x-2.5">
                    <img src="assets/img/logo.png" onerror="this.onerror=null; this.src='logo.png';" alt="Logo PAKAINI" class="h-7 w-auto object-contain rounded-lg">
                    <span class="font-display font-black text-white text-base">PAKAINI</span>
                    <span class="text-slate-500">— Platform Social Media Exchange Terpercaya</span>
                </div>
                <div class="flex items-center space-x-6 text-slate-400 font-medium">
                    <a href="login.php" class="hover:text-white transition-colors">Masuk</a>
                    <a href="register.php" class="hover:text-purple-300 transition-colors">Daftar Akun</a>
                    <a href="topup.php" class="hover:text-purple-300 transition-colors">Toko VIP</a>
                    <a href="index.php?page=community" class="hover:text-emerald-400 transition-colors">Komunitas WA</a>
                    <a href="https://wa.me/<?= ADMIN_WHATSAPP ?>" target="_blank" class="hover:text-emerald-400 transition-colors">Bantuan Admin</a>
                </div>
            </div>
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-slate-500 text-[11px]">
                <p>&copy; <?= date('Y') ?> PAKAINI (Social Media Exchange Platform). Hak Cipta Dilindungi.</p>
                <p>100% Organik • Tanpa Bot • Gotong Royong Sesama Kreator Indonesia</p>
            </div>
        </div>
    </footer>

    <!-- Mobile-First App Bottom Navigation Bar (Glassmorphism 21st.dev style) -->
    <nav class="md:hidden fixed bottom-0 w-full backdrop-blur-md bg-slate-950/80 border-t border-white/10 z-50 px-3 py-2 shadow-[0_-10px_30px_rgba(0,0,0,0.8)]">
        <?php if ($currentUser): ?>
            <div class="grid grid-cols-5 gap-1 max-w-md mx-auto text-center items-center">
                <a href="dashboard.php" class="flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-slate-400 hover:text-white transition-all">
                    <i class="fa-solid fa-house text-sm mb-0.5 text-purple-400"></i>
                    <span class="text-[9px] sm:text-[10px] font-bold">Home</span>
                </a>
                <a href="earn_coins.php" class="flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-slate-400 hover:text-white transition-all">
                    <i class="fa-solid fa-coins text-sm mb-0.5 text-amber-400"></i>
                    <span class="text-[9px] sm:text-[10px] font-bold">Cari Koin</span>
                </a>
                <a href="add_task.php" title="Pasang Iklan" class="flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-slate-400 hover:text-white transition-all">
                    <i class="fa-solid fa-bullhorn text-sm mb-0.5 text-sky-400"></i>
                    <span class="text-[9px] sm:text-[10px] font-bold">Tambah Iklan</span>
                </a>
                <a href="community.php" class="flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-slate-400 hover:text-white transition-all">
                    <i class="fa-solid fa-comments text-sm mb-0.5 text-purple-400"></i>
                    <span class="text-[9px] sm:text-[10px] font-bold">Komunitas</span>
                </a>
                <a href="referral.php" class="flex flex-col items-center justify-center py-1.5 px-0.5 rounded-xl text-slate-400 hover:text-white transition-all">
                    <i class="fa-solid fa-circle-user text-sm mb-0.5 text-slate-400"></i>
                    <span class="text-[9px] sm:text-[10px] font-bold">Profil</span>
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-4 gap-1.5 max-w-md mx-auto text-center items-center">
                <a href="index.php" class="flex flex-col items-center justify-center py-2 px-1 rounded-2xl text-white bg-gradient-to-r from-blue-500/20 to-purple-600/20 border border-purple-500/40 shadow-[0_0_15px_rgba(139,92,246,0.3)] font-bold transition-all">
                    <i class="fa-solid fa-house text-base mb-1 text-purple-400 drop-shadow-[0_0_8px_rgba(168,85,247,0.8)]"></i>
                    <span class="text-[10px]">Home</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-400 shadow-[0_0_6px_rgba(168,85,247,0.9)] mt-0.5"></span>
                </a>
                <a href="#cara-kerja" class="flex flex-col items-center justify-center py-2 px-1 rounded-2xl text-slate-400 hover:text-slate-200 hover:bg-white/[0.04] transition-all">
                    <i class="fa-solid fa-compass text-base mb-1 text-slate-400"></i>
                    <span class="text-[10px] font-medium">Panduan</span>
                </a>
                <a href="login.php" class="flex flex-col items-center justify-center py-2 px-1 rounded-2xl text-slate-400 hover:text-slate-200 hover:bg-white/[0.04] transition-all">
                    <i class="fa-solid fa-right-to-bracket text-base mb-1 text-slate-400"></i>
                    <span class="text-[10px] font-medium">Masuk</span>
                </a>
                <a href="register.php" class="flex flex-col items-center justify-center py-2 px-1 rounded-2xl text-white bg-gradient-to-r from-blue-500 to-purple-600 font-bold shadow-md shadow-purple-600/30 hover:shadow-[0_0_20px_rgba(139,92,246,0.4)] transition-all">
                    <i class="fa-solid fa-rocket text-base mb-1 text-white"></i>
                    <span class="text-[10px]">Daftar</span>
                </a>
            <?php endif; ?>
        </div>
    </nav>

</body>
</html>
