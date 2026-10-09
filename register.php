<?php
/**
 * PAKAINI - Social Media Exchange Platform
 * Halaman Pendaftaran Akun Baru (Register)
 * Logika: Bonus 500 Koin + Trial VIP Sultan 3 Hari Otomatis
 */

require_once __DIR__ . '/config.php';

// Jika pengguna sudah login, langsung alihkan ke dashboard
if (is_logged_in()) {
    redirect('dashboard.php');
}

$refParam = clean($_GET['ref'] ?? '');
$errors = [];
$old = [
    'username'    => '',
    'email'       => '',
    'referred_by' => $refParam
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username    = strtolower(clean($_POST['username'] ?? ''));
    $email       = strtolower(clean($_POST['email'] ?? ''));
    $password    = $_POST['password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';
    $referredBy  = clean($_POST['referred_by'] ?? '');

    $old['username']    = $username;
    $old['email']       = $email;
    $old['referred_by'] = $referredBy;

    // Validasi Form
    if (empty($username)) {
        $errors[] = 'Nama pengguna (username) wajib diisi.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
        $errors[] = 'Username hanya boleh huruf, angka, underscore, dan panjang 3-30 karakter.';
    }

    if (empty($email)) {
        $errors[] = 'Alamat email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format alamat email tidak valid.';
    }

    if (empty($password)) {
        $errors[] = 'Kata sandi wajib diisi.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Kata sandi minimal harus 6 karakter.';
    }

    if ($password !== $confirmPass) {
        $errors[] = 'Konfirmasi kata sandi tidak cocok.';
    }

    // Cek duplikasi di database
    if (empty($errors)) {
        $stmtCheck = $pdo->prepare("SELECT id, username, email FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmtCheck->execute([$username, $email]);
        $existing = $stmtCheck->fetch();

        if ($existing) {
            if ($existing['username'] === $username) {
                $errors[] = "Username '{$username}' sudah terdaftar. Silakan pilih username lain.";
            }
            if ($existing['email'] === $email) {
                $errors[] = "Email '{$email}' sudah terdaftar. Silakan gunakan email lain atau login.";
            }
        }
    }

    // Tangkap IP Address Pengguna & Blokir Pembuatan Akun Ganda (Anti-Tuyul) Sesuai Aturan 1
    // (Bypass IP localhost agar developer/admin lokal dapat menguji pendaftaran)
    $userIp = trim($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
    $isLocalhost = in_array($userIp, ['127.0.0.1', '::1', 'localhost']) || empty($userIp);
    if (empty($errors) && !$isLocalhost) {
        $stmtIp = $pdo->prepare("SELECT id FROM users WHERE ip_address = ? LIMIT 1");
        $stmtIp->execute([$userIp]);
        if ($stmtIp->fetch()) {
            $errors[] = "Sistem mendeteksi IP ini sudah terdaftar. 1 Perangkat/Jaringan hanya diizinkan memiliki 1 akun.";
        }
    }

    // Cek kode referral jika diisi
    $validReferrer = null;
    if (empty($errors) && !empty($referredBy)) {
        $stmtRef = $pdo->prepare("SELECT username FROM users WHERE username = ? LIMIT 1");
        $stmtRef->execute([$referredBy]);
        $referrer = $stmtRef->fetch();
        if ($referrer && $referrer['username'] !== $username) {
            $validReferrer = $referrer['username'];
        }
    }

    // Eksekusi Pendaftaran
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Cek jumlah pengguna terdaftar untuk penugasan peran (Aturan 1)
            // Cek jumlah pengguna terdaftar untuk penugasan peran (Aturan 1)
            // Akun pertama atau username bernada admin otomatis menjadi Super Admin
            $countUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
            $isFirst = ($countUsers === 0);
            $isSpecialAdmin = in_array(strtolower($username), ['admin', 'adminzul', 'superadmin', 'administrator', 'muawansonhaji02']);
            $assignedRole = ($isFirst || $isSpecialAdmin) ? 'admin' : 'user';

            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $today        = date('Y-m-d');
            $initialCoins = ($assignedRole === 'admin') ? 50000 : 0; // Admin mendapat saldo awal

            $isVip = ($assignedRole === 'admin') ? 1 : 0;

            // Deteksi kolom-kolom tabel users secara dinamis agar 100% tahan error
            $colCheck = [];
            try {
                $driver = Database::getDriver();
                if ($driver === 'pgsql') {
                    $rawCols = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'users'")->fetchAll(PDO::FETCH_COLUMN);
                    $colCheck = array_map('strtolower', $rawCols);
                } elseif ($driver === 'sqlite') {
                    $rawPragma = $pdo->query("PRAGMA table_info(users)")->fetchAll();
                    foreach ($rawPragma as $p) {
                        $colCheck[] = strtolower($p['name'] ?? '');
                    }
                } else {
                    $rawCols = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
                    $colCheck = array_map('strtolower', $rawCols);
                }
            } catch (Exception $eCols) {
                try {
                    $rawPragma = $pdo->query("PRAGMA table_info(users)")->fetchAll();
                    foreach ($rawPragma as $p) {
                        $colCheck[] = strtolower($p['name'] ?? '');
                    }
                } catch (Exception $ePragma) {}
            }

            $hasPassword     = in_array('password', $colCheck);
            $hasPasswordHash = in_array('password_hash', $colCheck);
            $hasIpAddress    = in_array('ip_address', $colCheck);

            $inserted = false;

            if ($hasPassword && $hasPasswordHash) {
                // Ada kedua kolom: isi keduanya
                $stmtInsert = $pdo->prepare("
                    INSERT INTO users (
                        username, email, password, password_hash, role, coins, is_vip, vip_expired_at, has_claimed_wa_vip,
                        referred_by, daily_clicks, active_days_streak, last_active_date, ip_address, created_at
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, ?, NULL, 0,
                        ?, 0, 1, ?, ?, NOW()
                    )
                ");
                $stmtInsert->execute([
                    $username, $email, $passwordHash, $passwordHash, $assignedRole,
                    $initialCoins, $isVip, $validReferrer, $today, $userIp
                ]);
                $inserted = true;
            } elseif ($hasPasswordHash && !$hasPassword) {
                // HANYA ada password_hash di database pengguna
                $stmtInsert = $pdo->prepare("
                    INSERT INTO users (
                        username, email, password_hash, role, coins, is_vip, vip_expired_at, has_claimed_wa_vip,
                        referred_by, daily_clicks, active_days_streak, last_active_date, ip_address, created_at
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, NULL, 0,
                        ?, 0, 1, ?, ?, NOW()
                    )
                ");
                $stmtInsert->execute([
                    $username, $email, $passwordHash, $assignedRole,
                    $initialCoins, $isVip, $validReferrer, $today, $userIp
                ]);
                $inserted = true;
            } elseif ($hasPassword && !$hasPasswordHash) {
                // HANYA ada password di database pengguna
                $stmtInsert = $pdo->prepare("
                    INSERT INTO users (
                        username, email, password, role, coins, is_vip, vip_expired_at, has_claimed_wa_vip,
                        referred_by, daily_clicks, active_days_streak, last_active_date, ip_address, created_at
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, NULL, 0,
                        ?, 0, 1, ?, ?, NOW()
                    )
                ");
                $stmtInsert->execute([
                    $username, $email, $passwordHash, $assignedRole,
                    $initialCoins, $isVip, $validReferrer, $today, $userIp
                ]);
                $inserted = true;
            }

            // Fallback keamanan jika SHOW COLUMNS gagal: coba satu per satu
            if (!$inserted) {
                try {
                    // Coba 1: password_hash saja (sesuai kondisi database live)
                    $stmtInsert = $pdo->prepare("
                        INSERT INTO users (
                            username, email, password_hash, role, coins, is_vip, vip_expired_at, has_claimed_wa_vip,
                            referred_by, daily_clicks, active_days_streak, last_active_date, ip_address, created_at
                        ) VALUES (
                            ?, ?, ?, ?, ?, ?, NULL, 0,
                            ?, 0, 1, ?, ?, NOW()
                        )
                    ");
                    $stmtInsert->execute([
                        $username, $email, $passwordHash, $assignedRole,
                        $initialCoins, $isVip, $validReferrer, $today, $userIp
                    ]);
                    $inserted = true;
                } catch (Exception $eTryHash) {
                    try {
                        // Coba 2: password saja
                        $stmtInsert = $pdo->prepare("
                            INSERT INTO users (
                                username, email, password, role, coins, is_vip, vip_expired_at, has_claimed_wa_vip,
                                referred_by, daily_clicks, active_days_streak, last_active_date, ip_address, created_at
                            ) VALUES (
                                ?, ?, ?, ?, ?, ?, NULL, 0,
                                ?, 0, 1, ?, ?, NOW()
                            )
                        ");
                        $stmtInsert->execute([
                            $username, $email, $passwordHash, $assignedRole,
                            $initialCoins, $isVip, $validReferrer, $today, $userIp
                        ]);
                        $inserted = true;
                    } catch (Exception $eTryPass) {
                        // Coba 3: keduanya
                        $stmtInsert = $pdo->prepare("
                            INSERT INTO users (
                                username, email, password, password_hash, role, coins, is_vip, vip_expired_at, has_claimed_wa_vip,
                                referred_by, daily_clicks, active_days_streak, last_active_date, ip_address, created_at
                            ) VALUES (
                                ?, ?, ?, ?, ?, ?, ?, NULL, 0,
                                ?, 0, 1, ?, ?, NOW()
                            )
                        ");
                        $stmtInsert->execute([
                            $username, $email, $passwordHash, $passwordHash, $assignedRole,
                            $initialCoins, $isVip, $validReferrer, $today, $userIp
                        ]);
                    }
                }
            }

            $newUserId = (int)($pdo->lastInsertId('users_id_seq') ?: $pdo->lastInsertId());

            $pdo->commit();

            // Otomatis login ke sesi baru (Anti Session Fixation)
            session_regenerate_id(true);
            $_SESSION['user_id'] = $newUserId;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $assignedRole;
            $_SESSION['user_role'] = $assignedRole;

            if ($assignedRole === 'admin') {
                set_flash('success', "🎉 Selamat datang Super Admin {$username}! Akun Anda berhasil dibuat dengan hak akses penuh.");
                redirect('panel_pusat_pakaini_99x.php');
            } else {
                set_flash('success', "🎉 Selamat bergabung, {$username}! Akun Anda berhasil dibuat. Silakan mulai kumpulkan koin.");
                redirect('dashboard.php');
            }

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errors[] = 'Gagal mendaftar: ' . $e->getMessage();
        }
    }
}

$pageTitle = 'Daftar Akun Baru — Bonus 500 Koin & Trial VIP 3 Hari';
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> — PAKAINI</title>

    <!-- Open Graph Meta Tags (Untuk Berbagi WhatsApp/FB) -->
    <!-- CATATAN: Silakan sesuaikan URL domain pada tag og:image (https://(domain-anda)/logo.png) dengan domain aktif website Anda nanti -->
    <meta property="og:title" content="Pakaini - Social Media Exchange & Growth Platform">
    <meta property="og:description" content="Tingkatkan Followers, Subscribers, dan interaksi media sosial Anda dengan komunitas pengguna asli Indonesia. Zero Risk, Cepat & Aman!">
    <meta property="og:image" content="https://(domain-anda)/logo.png">
    <meta property="og:type" content="website">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #030712;
            background-image: 
                radial-gradient(ellipse 80% 60% at 50% -15%, rgba(99, 102, 241, 0.22) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 90% 90%, rgba(245, 158, 11, 0.12) 0%, transparent 50%),
                radial-gradient(rgba(255, 255, 255, 0.04) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 32px 32px;
            color: #f3f4f6;
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 py-8">

    <div class="w-full max-w-md">

        <!-- Logo & Brand Header -->
        <div class="text-center mb-6">
            <a href="index.php" class="inline-flex items-center space-x-2.5 mb-2 group">
                <img src="assets/img/logo.png" onerror="this.onerror=null; this.src='logo.png';" alt="Logo PAKAINI" class="h-11 sm:h-12 w-auto object-contain rounded-xl transition-transform group-hover:scale-105 drop-shadow-md">
                <div class="text-left">
                    <h1 class="font-display font-black text-2xl tracking-tight text-white group-hover:text-amber-300 transition-colors">
                        PAKAINI
                    </h1>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-semibold">Social Media Exchange</p>
                </div>
            </a>
            <p class="text-xs text-slate-400">Tukar Saling Follow & Subscribe Secara Cepat, Aman & Organik</p>
        </div>

        <!-- Bonus Banner (Klaim via WA) -->
        <div class="mb-5 p-4 rounded-2xl bg-gradient-to-r from-emerald-500/15 via-indigo-600/15 to-purple-600/15 border border-emerald-500/30 shadow-lg backdrop-blur-xl relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-emerald-500/10 rounded-full blur-xl pointer-events-none"></div>
            <div class="flex items-center space-x-3 relative z-10">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-brands fa-whatsapp animate-bounce"></i>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-black text-emerald-300 tracking-wide uppercase">Bonus Komunitas</span>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30">Grup WhatsApp</span>
                    </div>
                    <p class="text-xs text-slate-200 font-semibold mt-0.5">
                        Klaim <span class="text-amber-400 font-bold">500 Koin</span> + <span class="text-amber-300 font-bold">VIP Sultan 3 Hari</span> setelah bergabung di grup WA!
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-slate-900/80 border border-white/[0.08] backdrop-blur-2xl rounded-3xl p-6 sm:p-8 shadow-2xl shadow-black/80">
            <h2 class="font-display font-bold text-lg text-white mb-1">Buat Akun Baru</h2>
            <p class="text-xs text-slate-400 mb-6">Mulai bangun jangkauan sosial media Anda dalam 1 menit.</p>

            <!-- Error Alerts -->
            <?php if (!empty($errors)): ?>
                <div class="mb-5 p-3.5 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs space-y-1">
                    <div class="font-bold flex items-center space-x-1.5">
                        <i class="fa-solid fa-circle-exclamation text-rose-400"></i>
                        <span>Mohon periksa kembali form:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 pl-1">
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST" class="space-y-4">
                
                <!-- Username -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="username">
                        Username <span class="text-rose-400">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-sm">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" name="username" id="username" required
                            value="<?= htmlspecialchars($old['username'], ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="contoh: budi_creator"
                            class="w-full bg-slate-950/70 border border-slate-700/80 rounded-xl pl-10 pr-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1">Hanya huruf, angka, underscore (3-30 karakter).</p>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="email">
                        Alamat Email <span class="text-rose-400">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-sm">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" name="email" id="email" required
                            value="<?= htmlspecialchars($old['email'], ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="nama@email.com"
                            class="w-full bg-slate-950/70 border border-slate-700/80 rounded-xl pl-10 pr-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="password">
                        Kata Sandi <span class="text-rose-400">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-sm">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" required minlength="6"
                            placeholder="Minimal 6 karakter"
                            class="w-full bg-slate-950/70 border border-slate-700/80 rounded-xl pl-10 pr-10 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all font-mono">
                        <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition-colors">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="confirm_password">
                        Ulangi Kata Sandi <span class="text-rose-400">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-sm">
                            <i class="fa-solid fa-shield-halved"></i>
                        </span>
                        <input type="password" name="confirm_password" id="confirm_password" required minlength="6"
                            placeholder="Ketik ulang kata sandi"
                            class="w-full bg-slate-950/70 border border-slate-700/80 rounded-xl pl-10 pr-10 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all font-mono">
                        <button type="button" onclick="togglePasswordVisibility('confirm_password', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition-colors">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Referral Code (Opsional) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="referred_by">
                        Kode Referral (Opsional)
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-sm">
                            <i class="fa-solid fa-gift"></i>
                        </span>
                        <input type="text" name="referred_by" id="referred_by"
                            value="<?= htmlspecialchars($old['referred_by'], ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Username pengajak jika ada"
                            class="w-full bg-slate-950/70 border border-slate-700/80 rounded-xl pl-10 pr-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit"
                        class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold text-xs sm:text-sm tracking-wide shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/50 hover:scale-[1.01] transition-all flex items-center justify-center space-x-2">
                        <span>Daftar & Klaim 500 Koin + VIP 3 Hari</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>

            </form>

            <div class="mt-6 pt-5 border-t border-slate-800 text-center">
                <p class="text-xs text-slate-400">
                    Sudah memiliki akun?
                    <a href="login.php" class="text-indigo-400 hover:text-indigo-300 font-bold transition-colors ml-1">
                        Masuk di Sini
                    </a>
                </p>
            </div>
        </div>

        <!-- Platform Vision Footer -->
        <div class="text-center mt-6 text-xs text-slate-500 font-medium">
            Pakaini © 2026 • Social Media Exchange & Growth Platform
        </div>

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
