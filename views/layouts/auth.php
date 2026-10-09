<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($page_title ?? 'Masuk / Daftar', ENT_QUOTES, 'UTF-8') ?> — PAKAINI PRO</title>
    <!-- Tailwind CSS CDN -->
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
        }
        body {
            background-color: #030712;
            color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            background-image: 
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(99, 102, 241, 0.25) 0%, transparent 70%),
                radial-gradient(ellipse 50% 30% at 80% 80%, rgba(245, 158, 11, 0.12) 0%, transparent 60%),
                radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 28px 28px;
            background-attachment: fixed;
        }
        .glass-card {
            background: linear-gradient(135deg, rgba(17, 24, 39, 0.88) 0%, rgba(11, 15, 25, 0.94) 100%);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.7), 0 0 0 1px rgba(255, 255, 255, 0.03) inset;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-center items-center p-4 antialiased selection:bg-brand-600 selection:text-white">

    <!-- Flash Messages -->
    <div class="w-full max-w-md mb-4">
        <?php if (!empty($flash['error'])): ?>
            <?php foreach ($flash['error'] as $msg): ?>
                <div class="flex items-center space-x-3 p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs sm:text-sm mb-2 shadow-lg shadow-rose-500/10">
                    <i class="fa-solid fa-circle-exclamation text-rose-400"></i>
                    <span><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($flash['success'])): ?>
            <?php foreach ($flash['success'] as $msg): ?>
                <div class="flex items-center space-x-3 p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs sm:text-sm mb-2 shadow-lg shadow-emerald-500/10">
                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                    <span><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Auth Glass Card -->
    <div class="w-full max-w-md glass-card rounded-2xl sm:rounded-3xl p-5 sm:p-8 relative overflow-hidden">
        <div class="absolute -top-16 -right-16 w-32 h-32 bg-indigo-500/15 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-32 h-32 bg-amber-500/15 rounded-full blur-2xl pointer-events-none"></div>

        <!-- Logo -->
        <div class="text-center mb-5 sm:mb-6">
            <a href="index.php" class="inline-flex items-center space-x-2.5 mb-2 group">
                <img src="assets/img/logo.png" onerror="this.onerror=null; this.src='logo.png';" alt="Logo PAKAINI" class="h-10 sm:h-12 w-auto object-contain rounded-xl transition-transform group-hover:scale-105 drop-shadow-md">
                <div class="text-left">
                    <div class="flex items-center space-x-1.5">
                        <span class="font-display font-black text-xl sm:text-2xl tracking-tight text-white">PAKAINI</span>
                        <span class="px-1.5 py-0.5 rounded text-[8px] sm:text-[9px] font-black uppercase tracking-wider bg-brand-600/20 text-brand-400 border border-brand-500/30">PRO</span>
                    </div>
                </div>
            </a>
            <p class="text-[11px] sm:text-xs text-slate-400">Social Media Exchange & Growth Platform</p>
        </div>

        <?= $content ?>

    </div>

    <!-- Footer Note -->
    <div class="text-center mt-6 text-xs text-slate-500 font-medium">
        Pakaini © 2026 • Social Media Exchange & Growth Platform
    </div>

    <!-- Password Visibility Toggle Script -->
    <script>
    function togglePasswordVisibility(fieldId, btn) {
        const input = document.getElementById(fieldId);
        if (!input) return;
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        } else {
            input.type = 'password';
            if (icon) {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    }
    </script>
</body>
</html>
