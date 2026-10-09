<?php
/**
 * PAKAINI - Global Navigation & Header Layout Component
 * Modern, Responsive Tailwind CSS via CDN, Mobile-Friendly
 */
$currentUser = current_user($pdo);
$currPage = basename($_SERVER['PHP_SELF']);
$flashSuccess = get_flash('success');
$flashError = get_flash('error');
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Social Media Exchange', ENT_QUOTES, 'UTF-8') ?> — PAKAINI</title>

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
                            850: '#111827'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        html { scroll-behavior: smooth; }
        body { 
            background-color: #0B0F19; 
            color: #f1f5f9; 
            font-family: 'Inter', sans-serif;
            background-image: 
                radial-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 28px 28px;
            background-attachment: fixed;
        }
        .glass-card {
            background: #111111;
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.7);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            border-color: rgba(255, 255, 255, 0.16);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col pb-24 md:pb-8 antialiased selection:bg-purple-600 selection:text-white bg-[#0B0F19]">

    <!-- Top Sticky Header -->
    <header class="sticky top-0 z-40 bg-[#0B0F19]/90 backdrop-blur-xl border-b border-white/[0.08]">
        <div class="max-w-6xl mx-auto px-3.5 sm:px-6 h-14 sm:h-16 flex items-center justify-between">
            
            <!-- Brand Logo -->
            <a href="<?= $currentUser ? 'dashboard.php' : 'index.php' ?>" class="flex items-center space-x-2.5 group">
                <img src="assets/img/logo.png" onerror="this.onerror=null; this.src='logo.png';" alt="Logo PAKAINI" class="h-10 sm:h-12 w-auto object-contain rounded-xl transition-transform group-hover:scale-105 drop-shadow-md">
                <div class="flex flex-col">
                    <span class="font-display font-black text-sm sm:text-lg tracking-tight text-white group-hover:text-slate-300 transition-colors">
                        PAKAINI
                    </span>
                    <span class="text-[8px] sm:text-[9px] text-slate-400 tracking-wider font-semibold uppercase -mt-1 hidden sm:block">
                        Social Media Exchange
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <?php if ($currentUser): ?>
            <nav class="hidden md:flex items-center space-x-1">
                <a href="dashboard.php" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all <?= ($currPage === 'dashboard.php') ? 'bg-white/10 text-white border border-white/20' : 'text-slate-400 hover:text-white hover:bg-white/[0.04]' ?>">
                    <i class="fa-solid fa-gauge-high mr-1.5 text-xs <?= ($currPage === 'dashboard.php') ? 'text-purple-400' : 'text-slate-400' ?>"></i>Dashboard
                </a>
                <a href="earn_coins.php" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all <?= ($currPage === 'earn_coins.php') ? 'bg-white/10 text-white border border-white/20' : 'text-slate-400 hover:text-white hover:bg-white/[0.04]' ?>" title="Kumpulkan koin dengan follow/subscribe akun orang lain">
                    <i class="fa-solid fa-coins mr-1.5 text-xs <?= ($currPage === 'earn_coins.php') ? 'text-purple-400' : 'text-slate-400' ?>"></i>Cari Koin
                </a>
                <a href="add_task.php" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all <?= ($currPage === 'add_task.php') ? 'bg-white/10 text-white border border-white/20' : 'text-slate-400 hover:text-white hover:bg-white/[0.04]' ?>" title="Promosikan akun YouTube, TikTok, atau Instagram Anda">
                    <i class="fa-solid fa-bullhorn mr-1.5 text-xs <?= ($currPage === 'add_task.php') ? 'text-purple-400' : 'text-slate-400' ?>"></i>Tambah Iklan
                </a>
                <a href="community.php" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all <?= ($currPage === 'community.php' || ($_GET['page'] ?? '') === 'community') ? 'bg-white/10 text-white border border-white/20' : 'text-slate-400 hover:text-white hover:bg-white/[0.04]' ?>">
                    <i class="fa-solid fa-comments mr-1.5 text-xs <?= ($currPage === 'community.php' || ($_GET['page'] ?? '') === 'community') ? 'text-purple-400' : 'text-slate-400' ?>"></i>Komunitas
                </a>
                <a href="profile.php" data-ref="referral.php" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all <?= ($currPage === 'profile.php' || $currPage === 'edit_profile.php' || $currPage === 'referral.php') ? 'bg-white/10 text-white border border-white/20' : 'text-slate-400 hover:text-white hover:bg-white/[0.04]' ?>">
                    <i class="fa-solid fa-circle-user mr-1.5 text-xs <?= ($currPage === 'profile.php' || $currPage === 'edit_profile.php' || $currPage === 'referral.php') ? 'text-purple-400' : 'text-slate-400' ?>"></i>Profil
                </a>
                <a href="topup.php" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all <?= ($currPage === 'topup.php') ? 'bg-gradient-to-r from-blue-500/20 to-purple-600/20 text-white border border-purple-500/30' : 'text-amber-300 hover:text-white hover:bg-white/[0.04]' ?>">
                    <i class="fa-solid fa-crown mr-1.5 text-xs text-amber-400"></i>Beli Koin
                </a>
                <?php if (($currentUser['role'] ?? '') === 'admin' || in_array(strtolower($currentUser['username'] ?? ''), ['admin', 'adminzul', 'superadmin'])): ?>
                <a href="admin.php" class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all bg-rose-500/20 text-rose-300 hover:bg-rose-500/30 border border-rose-500/40 shadow-sm" title="Ruang Kendali Super Admin">
                    <i class="fa-solid fa-shield-halved mr-1.5 text-xs text-rose-400"></i>Panel Admin
                </a>
                <?php endif; ?>

            </nav>
            <?php endif; ?>

            <!-- Right Controls: Coins Pill & User Status -->
            <div class="flex items-center space-x-2 sm:space-x-3">
                <?php if ($currentUser): ?>
                    <?php if (($currentUser['role'] ?? '') === 'admin' || in_array(strtolower($currentUser['username'] ?? ''), ['admin', 'adminzul', 'superadmin'])): ?>
                    <!-- Tombol Panel Admin Khusus (Terlihat Jelas di HP & Komputer) -->
                    <a href="admin.php" class="flex items-center space-x-1.5 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-full bg-rose-600/25 border border-rose-500/50 text-rose-200 text-xs font-black hover:bg-rose-600/40 hover:border-rose-400 transition-all shadow-[0_0_14px_rgba(244,63,94,0.35)]" title="Ruang Kendali Super Admin">
                        <i class="fa-solid fa-shield-halved text-rose-400 text-xs animate-pulse"></i>
                        <span>Admin</span>
                    </a>
                    <?php endif; ?>

                    <!-- Koin Murni Pill (Real-Time Counter dengan Neon Glow Ringan) -->
                    <a href="topup.php" id="navbarCoinsPill" class="flex items-center space-x-1.5 px-3 sm:px-3.5 py-1 sm:py-1.5 rounded-full bg-[#111111] border border-amber-500/30 text-xs font-bold hover:border-amber-400/60 transition-all shadow-[0_0_12px_rgba(245,158,11,0.18)] hover:shadow-[0_0_18px_rgba(245,158,11,0.3)] group" title="Saldo Koin Murni - Klik untuk Tambah Koin">
                        <span class="text-xs sm:text-sm drop-shadow-[0_0_6px_rgba(245,158,11,0.5)]">🪙</span>
                        <span id="navbarCoinsAmount" class="font-mono font-black text-amber-300 text-xs sm:text-sm user-coin-balance drop-shadow-[0_0_8px_rgba(245,158,11,0.35)]">
                            <?= number_format($currentUser['coins']) ?>
                        </span>
                        <span class="text-[10px] sm:text-[11px] font-semibold text-slate-300">Koin</span>
                        <span class="w-4 h-4 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 text-white flex items-center justify-center text-[9px] font-black ml-0.5 group-hover:scale-110 transition-transform">
                            +
                        </span>
                    </a>

                    <!-- VIP Crown or Upgrade Button -->
                    <?php if ((int)$currentUser['is_vip'] === 1): ?>
                        <span class="px-2 sm:px-2.5 py-1 rounded-full bg-gradient-to-r from-amber-500/20 to-yellow-500/10 border border-amber-500/40 text-amber-300 text-[10px] font-black uppercase flex items-center space-x-1 shadow-sm" title="Member VIP Sultan">
                            <i class="fa-solid fa-crown text-amber-400 text-[10px] animate-pulse"></i>
                            <span class="hidden sm:inline">VIP</span>
                        </span>
                    <?php else: ?>
                        <a href="topup.php" class="hidden sm:inline-flex items-center space-x-1 px-2.5 py-1 rounded-full bg-indigo-600/20 border border-indigo-500/30 text-indigo-300 text-[10px] font-bold hover:bg-indigo-600/30 transition-all">
                            <span>Upgrade</span>
                        </a>
                    <?php endif; ?>



                    <!-- Mobile App Profile Pill -->
                    <a href="profile.php" class="md:hidden w-8 h-8 rounded-xl bg-gradient-to-tr from-blue-500 to-purple-600 text-white flex items-center justify-center font-bold text-xs shadow-md border border-white/20 active:scale-95 transition-transform" title="Profil Akun">
                        <?= strtoupper(substr($currentUser['username'], 0, 1)) ?>
                    </a>

                    <!-- Desktop Logout Button -->
                    <a href="logout.php" title="Keluar Akun" class="hidden sm:flex w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-slate-800/80 hover:bg-rose-500/20 hover:text-rose-400 text-slate-400 items-center justify-center text-xs transition-colors">
                        <i class="fa-solid fa-power-off"></i>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="px-3 py-1.5 rounded-xl text-xs font-bold text-slate-300 hover:text-white transition-colors">
                        Masuk
                    </a>
                    <a href="register.php" class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white text-xs font-black shadow-md shadow-indigo-600/25 hover:shadow-[0_0_20px_rgba(139,92,246,0.4)] transition-all duration-300">
                        Daftar Akun
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Global Flash Alerts Container -->
    <div class="max-w-6xl mx-auto px-3.5 sm:px-6 pt-4 w-full">
        <?php if (!empty($flashSuccess)): ?>
            <?php foreach ($flashSuccess as $msg): ?>
                <div class="mb-3 p-3.5 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-xs sm:text-sm flex items-center justify-between shadow-lg">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-circle-check text-emerald-400"></i>
                        <span><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-400/60 hover:text-emerald-400"><i class="fa-solid fa-xmark"></i></button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($flashError)): ?>
            <?php foreach ($flashError as $msg): ?>
                <div class="mb-3 p-3.5 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs sm:text-sm flex items-center justify-between shadow-lg">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-400"></i>
                        <span><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-400/60 hover:text-rose-400"><i class="fa-solid fa-xmark"></i></button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Main Page Content Starts Here -->
    <main class="max-w-6xl mx-auto px-3.5 sm:px-6 py-4 sm:py-6 flex-1 w-full">
