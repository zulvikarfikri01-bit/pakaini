<?php
session_start();
require_once __DIR__ . '/config.php'; // Koneksi database & helper terpusat

// Sinkronisasi sesi Super Admin secara real-time
$adminUser = current_user($pdo);
if ($adminUser && in_array(strtolower($adminUser['username'] ?? ''), ['adminzul', 'admin', 'superadmin', 'administrator'])) {
    $_SESSION['role'] = 'admin';
    $_SESSION['user_role'] = 'admin';
}

// 1. KUNCI BAJA: Validasi Super Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Hancurkan sesi jika mencoba menyusup dan lempar ke halaman login
    header("Location: login.php");
    exit(); // SANGAT PENTING: Menghentikan eksekusi kode di bawahnya
}

// Internal routing helper: redirect('panel_pusat_pakaini_99x.php');
$pesan = null;
$tipePesan = 'success';

// 2. LOGIKA KENDALI PENUH (Action Handlers)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // A. Fitur Mesin Koin (Top-Up)
    if ($action === 'tambah_koin') {
        $username = htmlspecialchars(trim($_POST['username'] ?? ''), ENT_QUOTES, 'UTF-8');
        $jumlah_koin = (int)($_POST['jumlah_koin'] ?? 0);
        
        if (empty($username)) {
            $pesan = "Nama pengguna (username) wajib diisi!";
            $tipePesan = 'error';
        } elseif ($jumlah_koin <= 0) {
            $pesan = "Jumlah koin harus berupa angka positif!";
            $tipePesan = 'error';
        } else {
            $stmt = $pdo->prepare("UPDATE users SET coins = coins + ? WHERE username = ?");
            $stmt->execute([$jumlah_koin, $username]);

            // Catat log transaksi (opsional)
            try {
                $stmtId = $pdo->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
                $stmtId->execute([$username]);
                $targetId = (int)$stmtId->fetchColumn();
                if ($targetId > 0) {
                    $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description) VALUES (?, 'credit', ?, ?)")
                        ->execute([$targetId, $jumlah_koin, "Top-Up koin manual oleh Super Admin"]);
                }
            } catch (Exception $e) {}

            $pesan = "Berhasil menambah " . number_format($jumlah_koin) . " koin ke @$username!";
            $tipePesan = 'success';
        }
    }

    // B. Fitur Eksekusi Akun (Banned / Unbanned)
    elseif ($action === 'ubah_status_akun') {
        $user_id_target = (int)($_POST['user_id'] ?? 0);
        $status_baru = (int)($_POST['status'] ?? 1); // 1 = aktif, 0 = banned
        $isBanned = ($status_baru === 1) ? 0 : 1;
        
        if ($user_id_target <= 0) {
            $pesan = "ID Pengguna target tidak valid!";
            $tipePesan = 'error';
        } else {
            $stmt = $pdo->prepare("UPDATE users SET is_active = ?, is_banned = ? WHERE id = ? AND role != 'admin'");
            $stmt->execute([$status_baru, $isBanned, $user_id_target]);
            $statusTeks = ($status_baru === 1) ? "Diaktifkan (Buka Blokir)" : "Diblokir (Banned)";
            $pesan = "Status akun #$user_id_target berhasil diubah menjadi: $statusTeks!";
            $tipePesan = 'success';
        }
    }

    // C. Fitur Reset Password
    elseif ($action === 'reset_password') {
        $user_id_target = (int)($_POST['user_id'] ?? 0);
        $password_baru = password_hash('123456', PASSWORD_DEFAULT);
        
        if ($user_id_target <= 0) {
            $pesan = "ID Pengguna target tidak valid!";
            $tipePesan = 'error';
        } else {
            try {
                $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$password_baru, $user_id_target]);
            } catch (Exception $ePass) {}
            try {
                $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?")->execute([$password_baru, $user_id_target]);
            } catch (Exception $eHash) {}
            $pesan = "Password akun #$user_id_target berhasil direset menjadi: 123456";
            $tipePesan = 'success';
        }
    }
}

// 3. AMBIL DATA STATISTIK DAN DAFTAR PENGGUNA
$totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalCoins = (int)$pdo->query("SELECT SUM(coins) FROM users")->fetchColumn();
$activeUsers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE (is_active = 1 OR is_active IS NULL) AND (is_banned = 0 OR is_banned IS NULL)")->fetchColumn();
$bannedUsers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 0 OR is_banned = 1")->fetchColumn();

// Ambil seluruh pengguna untuk ditampilkan di tabel
$stmtUsers = $pdo->query("
    SELECT id, username, email, role, coins, is_active, is_banned, is_vip, strike_count, created_at 
    FROM users 
    ORDER BY id DESC
");
$allUsers = $stmtUsers->fetchAll();
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Kendali Pusat — PAKAINI</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                        mono: ['Courier New', 'monospace']
                    }
                }
            }
        }
    </script>
    <style>
        .glass-card {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-glow {
            box-shadow: 0 0 30px rgba(139, 92, 246, 0.15);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-300 min-h-screen font-sans antialiased selection:bg-purple-600 selection:text-white">

    <!-- Top Navigation Bar (Super Admin Stealth) -->
    <header class="border-b border-white/[0.08] bg-slate-950/80 backdrop-blur-xl sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-purple-600/30">
                    <i class="fa-solid fa-terminal text-lg"></i>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="font-display font-black text-white text-base tracking-wide">PAKAINI</span>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30">
                            🔒 Rahasia & Terisolasi
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400">Ruang Kendali Pusat Super Administrator</p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <div class="hidden sm:flex items-center space-x-2 px-3 py-1.5 rounded-full bg-slate-900 border border-white/10 text-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span class="text-slate-400">Admin Aktif:</span>
                    <strong class="text-white">@<?= htmlspecialchars($_SESSION['username'] ?? 'admin', ENT_QUOTES, 'UTF-8') ?></strong>
                </div>
                <a href="dashboard.php" class="px-3.5 py-1.5 rounded-xl bg-white/[0.05] hover:bg-white/[0.1] text-slate-300 hover:text-white text-xs font-semibold border border-white/10 transition-colors flex items-center space-x-1.5">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    <span class="hidden sm:inline">Ke Dashboard</span>
                </a>
                <a href="logout.php" class="px-3.5 py-1.5 rounded-xl bg-rose-500/15 hover:bg-rose-500/25 text-rose-300 border border-rose-500/30 text-xs font-bold transition-colors flex items-center space-x-1.5">
                    <i class="fa-solid fa-right-from-bracket text-[10px]"></i>
                    <span>Keluar</span>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-10 space-y-8">

        <!-- Notifikasi Pesan Aksi -->
        <?php if (!empty($pesan)): ?>
        <div class="p-4 rounded-3xl <?= ($tipePesan === 'success') ? 'bg-emerald-950/80 border border-emerald-500/50 text-emerald-200' : 'bg-rose-950/80 border border-rose-500/50 text-rose-200' ?> flex items-center justify-between shadow-2xl backdrop-blur-xl animate-fade-in">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl <?= ($tipePesan === 'success') ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30' ?> flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid <?= ($tipePesan === 'success') ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
                </div>
                <div>
                    <h4 class="font-display font-bold text-sm text-white"><?= ($tipePesan === 'success') ? 'Aksi Berhasil Dieksekusi!' : 'Terjadi Kesalahan!' ?></h4>
                    <p class="text-xs mt-0.5"><?= htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
            <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white p-2 text-sm transition-colors cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <?php endif; ?>

        <!-- Stat Overview Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="glass-card rounded-3xl p-5 border border-white/[0.08]">
                <div class="flex items-center justify-between text-slate-400 text-xs font-medium mb-2">
                    <span>Total Pengguna</span>
                    <i class="fa-solid fa-users text-purple-400"></i>
                </div>
                <div class="text-2xl sm:text-3xl font-display font-black text-white"><?= number_format($totalUsers) ?></div>
                <span class="text-[11px] text-slate-500 mt-1 block">Akun terdaftar di database</span>
            </div>

            <div class="glass-card rounded-3xl p-5 border border-white/[0.08]">
                <div class="flex items-center justify-between text-slate-400 text-xs font-medium mb-2">
                    <span>Total Koin Beredar</span>
                    <i class="fa-solid fa-coins text-amber-400"></i>
                </div>
                <div class="text-2xl sm:text-3xl font-display font-black text-amber-300 font-mono">🪙 <?= number_format($totalCoins) ?></div>
                <span class="text-[11px] text-slate-500 mt-1 block">Akumulasi seluruh saldo</span>
            </div>

            <div class="glass-card rounded-3xl p-5 border border-white/[0.08]">
                <div class="flex items-center justify-between text-slate-400 text-xs font-medium mb-2">
                    <span>Pengguna Aktif</span>
                    <i class="fa-solid fa-user-check text-emerald-400"></i>
                </div>
                <div class="text-2xl sm:text-3xl font-display font-black text-emerald-400"><?= number_format($activeUsers) ?></div>
                <span class="text-[11px] text-slate-500 mt-1 block">Bebas dari sanksi</span>
            </div>

            <div class="glass-card rounded-3xl p-5 border border-white/[0.08]">
                <div class="flex items-center justify-between text-slate-400 text-xs font-medium mb-2">
                    <span>Pengguna Diblokir</span>
                    <i class="fa-solid fa-user-slash text-rose-400"></i>
                </div>
                <div class="text-2xl sm:text-3xl font-display font-black text-rose-400"><?= number_format($bannedUsers) ?></div>
                <span class="text-[11px] text-slate-500 mt-1 block">Status Banned / Strike 3</span>
            </div>
        </div>

        <!-- 3 KARTU KENDALI UTAMA (FITUR A, B, C) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- ============================================================================== -->
            <!-- FITUR A: MESIN KOIN (TOP-UP)                                                    -->
            <!-- ============================================================================== -->
            <div class="glass-card rounded-3xl p-6 flex flex-col justify-between relative overflow-hidden group">
                <div class="absolute -right-8 -top-8 w-28 h-28 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all pointer-events-none"></div>

                <div>
                    <div class="flex items-center space-x-3 mb-4 pb-3 border-b border-white/[0.08]">
                        <div class="w-10 h-10 rounded-2xl bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center text-lg shrink-0">
                            🪙
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-white text-base">A. Mesin Koin (Top-Up)</h3>
                            <p class="text-[11px] text-slate-400">Suntik koin langsung ke saldo pengguna</p>
                        </div>
                    </div>

                    <form method="POST" action="panel_pusat_pakaini_99x.php" class="space-y-4" id="formTopup">
                        <input type="hidden" name="action" value="tambah_koin">

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="topup_username">
                                Username Target <span class="text-rose-400">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-xs">@</span>
                                <input type="text" name="username" id="topup_username" required
                                    placeholder="Contoh: muawansonhaji02"
                                    class="w-full bg-slate-950/80 border border-white/10 rounded-xl pl-8 pr-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-400/50 transition-all font-mono">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="topup_coins">
                                Jumlah Koin Tambahan <span class="text-rose-400">*</span>
                            </label>
                            <input type="number" name="jumlah_koin" id="topup_coins" required min="1" step="1"
                                placeholder="Contoh: 5000"
                                class="w-full bg-slate-950/80 border border-white/10 rounded-xl px-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-400/50 transition-all font-mono font-bold">
                            
                            <!-- Quick Buttons -->
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                <button type="button" onclick="setCoinAmount(1000)" class="px-2.5 py-1 rounded-lg bg-white/[0.05] hover:bg-white/[0.1] text-[10px] text-amber-300 font-mono transition-colors">+1.000</button>
                                <button type="button" onclick="setCoinAmount(5000)" class="px-2.5 py-1 rounded-lg bg-white/[0.05] hover:bg-white/[0.1] text-[10px] text-amber-300 font-mono transition-colors">+5.000</button>
                                <button type="button" onclick="setCoinAmount(10000)" class="px-2.5 py-1 rounded-lg bg-white/[0.05] hover:bg-white/[0.1] text-[10px] text-amber-300 font-mono transition-colors">+10.000</button>
                                <button type="button" onclick="setCoinAmount(50000)" class="px-2.5 py-1 rounded-lg bg-white/[0.05] hover:bg-white/[0.1] text-[10px] text-amber-300 font-mono transition-colors">+50.000</button>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-display font-extrabold text-xs shadow-lg shadow-amber-500/20 hover:scale-[1.01] active:scale-95 transition-all flex items-center justify-center space-x-2 cursor-pointer mt-2">
                            <i class="fa-solid fa-plus-circle text-xs"></i>
                            <span>Suntik Koin Sekarang</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- ============================================================================== -->
            <!-- FITUR B: EKSEKUSI STATUS AKUN (BANNED / UNBANNED)                               -->
            <!-- ============================================================================== -->
            <div class="glass-card rounded-3xl p-6 flex flex-col justify-between relative overflow-hidden group">
                <div class="absolute -right-8 -top-8 w-28 h-28 bg-rose-500/10 rounded-full blur-2xl group-hover:bg-rose-500/20 transition-all pointer-events-none"></div>

                <div>
                    <div class="flex items-center space-x-3 mb-4 pb-3 border-b border-white/[0.08]">
                        <div class="w-10 h-10 rounded-2xl bg-rose-500/20 text-rose-400 border border-rose-500/30 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-user-slash"></i>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-white text-base">B. Eksekusi Akun</h3>
                            <p class="text-[11px] text-slate-400">Blokir atau buka akses akun pengguna</p>
                        </div>
                    </div>

                    <form method="POST" action="panel_pusat_pakaini_99x.php" class="space-y-4" id="formStatusAkun">
                        <input type="hidden" name="action" value="ubah_status_akun">

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="status_user_id">
                                ID Pengguna (User ID) <span class="text-rose-400">*</span>
                            </label>
                            <input type="number" name="user_id" id="status_user_id" required min="1"
                                placeholder="Contoh: 2"
                                class="w-full bg-slate-950/80 border border-white/10 rounded-xl px-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-400/50 transition-all font-mono">
                            <p class="text-[11px] text-slate-500 mt-1">Anda juga dapat mengklik tombol di tabel pengguna di bawah.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                                Tindakan Status Baru <span class="text-rose-400">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="flex items-center space-x-2 p-3 rounded-xl bg-slate-950 border border-white/10 cursor-pointer hover:border-emerald-500/50 transition-colors">
                                    <input type="radio" name="status" value="1" checked class="text-emerald-500 focus:ring-0">
                                    <span class="text-xs font-bold text-emerald-400">✓ Aktif (Unban)</span>
                                </label>
                                <label class="flex items-center space-x-2 p-3 rounded-xl bg-slate-950 border border-white/10 cursor-pointer hover:border-rose-500/50 transition-colors">
                                    <input type="radio" name="status" value="0" class="text-rose-500 focus:ring-0">
                                    <span class="text-xs font-bold text-rose-400">⛔ Banned</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin memperbarui status akun ini?');"
                            class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white font-display font-extrabold text-xs shadow-lg shadow-rose-600/20 hover:scale-[1.01] active:scale-95 transition-all flex items-center justify-center space-x-2 cursor-pointer mt-2">
                            <i class="fa-solid fa-gavel text-xs"></i>
                            <span>Terapkan Status Akun</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- ============================================================================== -->
            <!-- FITUR C: RESET PASSWORD KE DEFAULT (123456)                                     -->
            <!-- ============================================================================== -->
            <div class="glass-card rounded-3xl p-6 flex flex-col justify-between relative overflow-hidden group">
                <div class="absolute -right-8 -top-8 w-28 h-28 bg-indigo-500/10 rounded-full blur-2xl group-hover:bg-indigo-500/20 transition-all pointer-events-none"></div>

                <div>
                    <div class="flex items-center space-x-3 mb-4 pb-3 border-b border-white/[0.08]">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-white text-base">C. Reset Password</h3>
                            <p class="text-[11px] text-slate-400">Kembalikan password akun ke default: 123456</p>
                        </div>
                    </div>

                    <form method="POST" action="panel_pusat_pakaini_99x.php" class="space-y-4" id="formResetPass">
                        <input type="hidden" name="action" value="reset_password">

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="reset_user_id">
                                ID Pengguna (User ID) <span class="text-rose-400">*</span>
                            </label>
                            <input type="number" name="user_id" id="reset_user_id" required min="1"
                                placeholder="Contoh: 3"
                                class="w-full bg-slate-950/80 border border-white/10 rounded-xl px-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-400/50 transition-all font-mono">
                            <p class="text-[11px] text-slate-500 mt-1">Kata sandi baru otomatis dienkripsi dengan <code>password_hash()</code>.</p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-indigo-500/20 text-[11px] text-indigo-300 space-y-1">
                            <div class="font-bold flex items-center space-x-1.5">
                                <i class="fa-solid fa-info-circle text-xs"></i>
                                <span>Informasi Password Baru:</span>
                            </div>
                            <p class="text-slate-400">Pengguna dapat login dengan sandi: <strong class="text-white font-mono bg-white/[0.08] px-1.5 py-0.5 rounded">123456</strong> lalu menggantinya di Edit Profil.</p>
                        </div>

                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin mereset password akun ini menjadi 123456?');"
                            class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-display font-extrabold text-xs shadow-lg shadow-indigo-600/20 hover:scale-[1.01] active:scale-95 transition-all flex items-center justify-center space-x-2 cursor-pointer mt-2">
                            <i class="fa-solid fa-rotate-left text-xs"></i>
                            <span>Reset Password ke 123456</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <!-- ============================================================================== -->
        <!-- TABEL KENDALI & MANAJEMEN PENGGUNA TERLENGKAP                                   -->
        <!-- ============================================================================== -->
        <div class="glass-card rounded-3xl p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-white/[0.08]">
                <div>
                    <h2 class="font-display font-black text-white text-lg sm:text-xl flex items-center space-x-2">
                        <i class="fa-solid fa-users-gear text-purple-400"></i>
                        <span>Daftar Seluruh Pengguna & Eksekusi Cepat</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Kelola dan eksekusi langsung akun pengguna dalam satu tampilan komprehensif.</p>
                </div>

                <!-- Live Client Search Filter -->
                <div class="relative w-full sm:w-72">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-xs">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" id="searchInput" oninput="filterUserTable()"
                        placeholder="Cari username atau email..."
                        class="w-full bg-slate-950 border border-white/10 rounded-xl pl-9 pr-4 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-purple-400/50 transition-all">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300" id="userTable">
                    <thead class="text-[11px] text-slate-400 uppercase tracking-wider bg-black/40 border-b border-white/[0.08]">
                        <tr>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Pengguna</th>
                            <th class="py-3 px-4">Role</th>
                            <th class="py-3 px-4">Saldo Koin</th>
                            <th class="py-3 px-4">Status Akun</th>
                            <th class="py-3 px-4 text-center">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/[0.04]">
                        <?php if (empty($allUsers)): ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-500 font-medium">Belum ada akun pengguna terdaftar.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($allUsers as $u): ?>
                                <?php 
                                $isBanned = ((int)($u['is_banned'] ?? 0) === 1 || (isset($u['is_active']) && (int)$u['is_active'] === 0));
                                $isAdmin = (($u['role'] ?? '') === 'admin');
                                $isVip = ((int)($u['is_vip'] ?? 0) === 1);
                                ?>
                                <tr class="hover:bg-white/[0.02] transition-colors user-row">
                                    <td class="py-3.5 px-4 font-mono text-slate-500 text-[11px]">#<?= $u['id'] ?></td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-white flex items-center space-x-1.5 font-mono">
                                            <span class="user-name">@<?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?></span>
                                            <?php if ($isVip): ?>
                                                <i class="fa-solid fa-crown text-amber-400 text-[10px]" title="VIP Sultan"></i>
                                            <?php endif; ?>
                                            <?php if ((int)$u['id'] === (int)$_SESSION['user_id']): ?>
                                                <span class="text-[9px] px-1.5 py-0.5 bg-purple-500/20 text-purple-300 rounded border border-purple-500/30 font-semibold font-sans">Anda</span>
                                            <?php endif; ?>
                                        </div>
                                        <span class="block text-[10px] text-slate-500 font-sans user-email"><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase <?= $isAdmin ? 'bg-purple-500/20 text-purple-300 border border-purple-500/40' : 'bg-slate-800 text-slate-400' ?>">
                                            <?= htmlspecialchars($u['role'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono font-bold text-amber-300">
                                        🪙 <?= number_format($u['coins']) ?>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <?php if ($isBanned): ?>
                                            <span class="px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/40 font-black text-[9px] uppercase inline-flex items-center space-x-1">
                                                <span>⛔ Banned</span>
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 font-bold text-[9px] uppercase inline-flex items-center space-x-1">
                                                <span>✓ Aktif</span>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <!-- Tombol Pilih untuk Top-Up -->
                                            <button type="button" onclick="selectUserForTopup('<?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?>')"
                                                class="px-2.5 py-1 rounded-xl bg-amber-500/15 hover:bg-amber-500/30 text-amber-300 border border-amber-500/30 text-[11px] font-bold transition-all cursor-pointer" title="Isi form Top-Up">
                                                🪙 +Koin
                                            </button>

                                            <!-- Tombol Eksekusi Status Akun -->
                                            <?php if (!$isAdmin): ?>
                                                <?php if ($isBanned): ?>
                                                    <form method="POST" action="panel_pusat_pakaini_99x.php" onsubmit="return confirm('Buka blokir akun @<?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?>?');" class="inline">
                                                        <input type="hidden" name="action" value="ubah_status_akun">
                                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                        <input type="hidden" name="status" value="1">
                                                        <button type="submit" class="px-2.5 py-1 rounded-xl bg-emerald-500/20 hover:bg-emerald-500/40 text-emerald-300 border border-emerald-500/40 text-[11px] font-bold transition-all cursor-pointer">
                                                            <i class="fa-solid fa-lock-open mr-1 text-[9px]"></i>Unban
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="POST" action="panel_pusat_pakaini_99x.php" onsubmit="return confirm('Apakah Anda yakin ingin memblokir akun @<?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?>?');" class="inline">
                                                        <input type="hidden" name="action" value="ubah_status_akun">
                                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                        <input type="hidden" name="status" value="0">
                                                        <button type="submit" class="px-2.5 py-1 rounded-xl bg-rose-500/20 hover:bg-rose-500/40 text-rose-300 border border-rose-500/40 text-[11px] font-bold transition-all cursor-pointer">
                                                            <i class="fa-solid fa-ban mr-1 text-[9px]"></i>Ban
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <!-- Tombol Reset Password -->
                                            <form method="POST" action="panel_pusat_pakaini_99x.php" onsubmit="return confirm('Reset password akun @<?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?> menjadi 123456?');" class="inline">
                                                <input type="hidden" name="action" value="reset_password">
                                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                <button type="submit" class="px-2.5 py-1 rounded-xl bg-indigo-500/20 hover:bg-indigo-500/40 text-indigo-300 border border-indigo-500/40 text-[11px] font-bold transition-all cursor-pointer" title="Reset Sandi ke 123456">
                                                    <i class="fa-solid fa-key text-[9px]"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Footer Stealth -->
    <footer class="border-t border-white/[0.06] py-6 text-center text-xs text-slate-500">
        <p>&copy; <?= date('Y') ?> PAKAINI — Ruang Kendali Super Admin. Akses terbatas untuk pemilik platform.</p>
    </footer>

    <!-- JavaScript Helper -->
    <script>
    function setCoinAmount(val) {
        const input = document.getElementById('topup_coins');
        if (input) {
            input.value = val;
            input.focus();
        }
    }

    function selectUserForTopup(username) {
        const input = document.getElementById('topup_username');
        if (input) {
            input.value = username;
            input.focus();
            window.scrollTo({ top: document.getElementById('formTopup').offsetTop - 120, behavior: 'smooth' });
        }
    }

    function filterUserTable() {
        const query = document.getElementById('searchInput').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.user-row');
        rows.forEach(row => {
            const name = row.querySelector('.user-name')?.innerText.toLowerCase() || '';
            const email = row.querySelector('.user-email')?.innerText.toLowerCase() || '';
            if (name.includes(query) || email.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    </script>
</body>
</html>
