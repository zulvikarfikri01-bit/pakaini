<?php
/**
 * PAKAINI - Social Media Exchange Platform
 * Halaman Utama (Dashboard) Pengguna
 * Menampilkan: Saldo Koin, Status VIP (Icon Mahkota), Kuota Harian, Streak, Navigasi, & Mutasi Koin
 */

require_once __DIR__ . '/config.php';

// Wajib Login
$user = require_login($pdo);
$pageTitle = 'Dashboard Utama';

$isVip = ((int)$user['is_vip'] === 1);
$dailyLimit = $isVip ? 'Tanpa Batas' : REGULAR_DAILY_MAX_CLICKS;
$dailyClicks = (int)$user['daily_clicks'];
$dailyPercent = $isVip ? 100 : min(100, round(($dailyClicks / REGULAR_DAILY_MAX_CLICKS) * 100));

// ==============================================================================
// Handle Aksi Hentikan Kampanye & Pengembalian Dana Otomatis (Proses 2)
// ==============================================================================
$dashAction = clean($_POST['action'] ?? $_GET['action'] ?? '');
$stopTaskId = (int)($_POST['task_id'] ?? $_GET['task_id'] ?? 0);

if ($dashAction === 'stop_task' && $stopTaskId > 0) {
    $stmtT = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ? LIMIT 1");
    $stmtT->execute([$stopTaskId, $user['id']]);
    $taskToStop = $stmtT->fetch();

    if (!$taskToStop) {
        set_flash('error', 'Kampanye tugas tidak ditemukan atau bukan milik Anda.');
        redirect('dashboard.php');
    }

    if ((int)($taskToStop['is_active'] ?? 1) === 0 || $taskToStop['status'] === 'paused' || $taskToStop['status'] === 'completed') {
        set_flash('error', 'Kampanye iklan ini sudah dalam status tidak aktif atau selesai.');
        redirect('dashboard.php');
    }

    $targetClicks = (int)($taskToStop['target_clicks'] ?? $taskToStop['max_clicks'] ?? 0);
    $currentClicks = (int)($taskToStop['current_clicks'] ?? 0);
    $remainingClicks = max(0, $targetClicks - $currentClicks);

    $isPriority = ((int)($taskToStop['is_priority'] ?? 0) === 1);
    $cpc = (int)$taskToStop['cpc'];
    $pricePerClick = $isPriority ? ($cpc * 2) : $cpc;
    $refundCoins = $remainingClicks * $pricePerClick;
    $isAdminTask = ((int)($taskToStop['is_admin_task'] ?? 0) === 1);

    try {
        $pdo->beginTransaction();

        // 1. Ubah status tugas menjadi is_active = 0
        $stmtStop = $pdo->prepare("UPDATE tasks SET is_active = 0, status = 'paused' WHERE id = ?");
        $stmtStop->execute([$stopTaskId]);

        // 2. Logika Refund: Kembalikan jumlah koin tersebut ke saldo coins pengguna pembuat tugas
        if (!$isAdminTask && $refundCoins > 0) {
            $stmtRefund = $pdo->prepare("UPDATE users SET coins = coins + ? WHERE id = ?");
            $stmtRefund->execute([$refundCoins, $user['id']]);

            $descRefund = "Pengembalian dana (Refund) iklan #{$stopTaskId}: Sisa {$remainingClicks} klik x {$pricePerClick} koin";
            $stmtTx = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, date, description) VALUES (?, 'refund_tugas', ?, NOW(), ?)");
            $stmtTx->execute([$user['id'], $refundCoins, $descRefund]);
        }

        $pdo->commit();

        $msg = 'Kampanye iklan berhasil dihentikan (Status Nonaktif).';
        if ($refundCoins > 0) {
            $msg .= ' Sisa dana sebesar ' . number_format($refundCoins) . ' Koin (' . $remainingClicks . ' klik x ' . number_format($pricePerClick) . ' koin) telah berhasil dikembalikan ke saldo dompet Anda!';
        }
        set_flash('success', $msg);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        set_flash('error', 'Gagal menghentikan kampanye: ' . $e->getMessage());
    }
    redirect('dashboard.php');
}

// Ambil statistik tugas aktif milik pengguna
$stmtActiveTasks = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ? AND is_active = 1 AND status = 'active'");
$stmtActiveTasks->execute([$user['id']]);
$totalActiveTasks = (int)$stmtActiveTasks->fetchColumn();

// Ambil riwayat transaksi koin terbaru milik pengguna
$stmtTx = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY id DESC LIMIT 8");
$stmtTx->execute([$user['id']]);
$recentTransactions = $stmtTx->fetchAll();

// Ambil beberapa tugas aktif & terbaru milik pengguna untuk ringkasan cepat
$stmtMyTasks = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY is_active DESC, id DESC LIMIT 5");
$stmtMyTasks->execute([$user['id']]);
$myRecentTasks = $stmtMyTasks->fetchAll();

require_once __DIR__ . '/navbar.php';
?>

<?php if (($user['role'] ?? '') === 'admin' || in_array(strtolower($user['username'] ?? ''), ['admin', 'adminzul', 'superadmin'])): ?>
<!-- SUPER ADMIN CONTROL HERO BANNER -->
<div class="mb-5 p-4 sm:p-5 rounded-3xl bg-gradient-to-r from-rose-950/80 via-slate-900 to-red-950/70 border border-rose-500/40 text-white shadow-xl relative overflow-hidden backdrop-blur-md">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-start space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-400 flex items-center justify-center text-xl shrink-0 shadow-lg shadow-rose-500/20 mt-0.5">
                <i class="fa-solid fa-shield-halved animate-pulse"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-rose-500/30 text-rose-200 border border-rose-500/40 text-[10px] font-black uppercase tracking-wider">
                        👑 Super Administrator
                    </span>
                    <span class="text-xs text-rose-300/80 font-semibold hidden sm:inline">Hak Akses Penuh Sistem Terverifikasi</span>
                </div>
                <h3 class="font-display font-extrabold text-white text-base sm:text-lg">
                    Panel Kendali Utama Tersedia — Halo, <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>!
                </h3>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 leading-relaxed">
                    Anda login sebagai Administrator. Anda memiliki hak kendali penuh: top-up koin tanpa batas, manajemen tugas, kontrol member, dan reset sistem.
                </p>
            </div>
        </div>
        <div class="shrink-0 flex items-center gap-2.5">
            <a href="admin.php" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white font-display font-bold text-xs sm:text-sm flex items-center justify-center space-x-2 shadow-lg shadow-rose-600/30 hover:scale-[1.02] active:scale-95 transition-all">
                <i class="fa-solid fa-gauge-high"></i>
                <span>Buka Panel Administrator</span>
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- BANNER ONBOARDING PENGGUNA BARU (ELEGAN MONOKROM GELAP) -->
<div class="mb-5 p-4 sm:p-5 rounded-3xl bg-[#111111] border border-white/[0.08] text-white shadow-xl relative overflow-hidden backdrop-blur-md">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-start space-x-3.5">
            <div class="w-12 h-12 rounded-2xl bg-white/[0.05] border border-white/10 text-white flex items-center justify-center text-xl shrink-0 shadow-sm mt-0.5">
                <i class="fa-solid fa-rocket text-purple-400"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-white/[0.08] text-slate-300 border border-white/10 text-[10px] font-bold uppercase tracking-wider">
                        💡 Panduan Cepat
                    </span>
                    <span class="text-xs text-slate-300 font-semibold hidden sm:inline">Selamat Datang di PAKAINI</span>
                </div>
                <h3 class="font-display font-extrabold text-white text-base sm:text-lg">
                    Bingung harus mulai dari mana?
                </h3>
                <p class="text-xs sm:text-sm text-slate-300 mt-1.5 leading-relaxed max-w-2xl">
                    <strong class="text-white font-bold">1. Masuk ke Cari Koin</strong> untuk mengumpulkan saldo gratis. <br class="hidden sm:inline">
                    <strong class="text-white font-bold">2. Pasang Iklan</strong> untuk menambah subscriber/follower akun Anda.
                </p>
            </div>
        </div>

        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2.5 shrink-0 pt-1 md:pt-0">
            <a href="earn_coins.php" 
               class="flex-1 sm:flex-none px-4 py-3 rounded-2xl bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-display font-extrabold text-xs flex items-center justify-center space-x-2 shadow-lg shadow-indigo-500/25 hover:shadow-[0_0_20px_rgba(139,92,246,0.4)] hover:scale-[1.02] active:scale-95 transition-all duration-300">
                <i class="fa-solid fa-coins text-xs text-amber-300"></i>
                <span>1. Mulai Cari Koin (Gratis)</span>
            </a>
            <a href="add_task.php" 
               class="flex-1 sm:flex-none px-4 py-3 rounded-2xl bg-white/[0.06] hover:bg-white/[0.1] text-slate-200 border border-white/10 font-display font-bold text-xs flex items-center justify-center space-x-2 shadow-md hover:scale-[1.02] active:scale-95 transition-all">
                <i class="fa-solid fa-bullhorn text-xs text-slate-300"></i>
                <span>2. Pasang Iklan Akun</span>
            </a>
        </div>
    </div>
</div>

<?php if ((int)($user['has_claimed_wa_vip'] ?? 0) === 0): ?>
<!-- BANNER KHUSUS KLAIM VIP 3 HARI VIA GRUP WHATSAPP -->
<div class="mb-5 p-4 rounded-3xl bg-[#111111] border border-white/[0.08] shadow-lg relative overflow-hidden backdrop-blur-md">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 relative z-10">
        <div class="flex items-center space-x-3">
            <div class="w-11 h-11 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-xl shrink-0">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-0.5 rounded-full bg-white/[0.08] text-emerald-400 border border-white/10 text-[9px] font-bold uppercase tracking-wider">
                        🎁 Bonus Komunitas
                    </span>
                    <span class="text-[11px] text-slate-300 font-semibold hidden sm:inline">• Gratis Koin & VIP 3 Hari</span>
                </div>
                <h3 class="font-display font-bold text-white text-sm sm:text-base mt-0.5">
                    Gabung Grup WhatsApp untuk Klaim VIP 3 Hari!
                </h3>
                <p class="text-xs text-slate-300 mt-0.5 leading-snug">
                    Dapatkan instan <strong class="text-white font-semibold">+500 Koin Saldo</strong> dan status <strong class="text-amber-400 font-semibold">VIP Sultan 3 Hari</strong>.
                </p>
            </div>
        </div>

        <div class="shrink-0 flex items-center pt-1 sm:pt-0">
            <a href="claim_vip.php" 
               class="w-full sm:w-auto px-4 py-2.5 rounded-2xl bg-white/[0.08] hover:bg-white/[0.14] border border-white/10 text-white font-display font-extrabold text-xs flex items-center justify-center space-x-2 shadow-md hover:scale-[1.02] active:scale-95 transition-all">
                <i class="fa-brands fa-whatsapp text-sm text-emerald-400"></i>
                <span>Gabung Grup WA untuk Klaim VIP</span>
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Header Sambutan & Status Akun (Modern Mobile-First Hero Card) -->
<div class="mb-5">
    <div class="glass-card rounded-3xl p-5 sm:p-6 relative overflow-hidden border border-white/[0.08]">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-5 relative z-10">
            <!-- Welcome Info -->
            <div class="space-y-2">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-500 to-purple-600 text-white flex items-center justify-center font-display font-black text-lg shadow-lg shadow-indigo-500/30">
                        <?= strtoupper(substr($user['username'], 0, 2)) ?>
                    </div>
                    <div>
                        <h1 class="text-base sm:text-xl font-display font-bold text-white flex items-center gap-2">
                            Halo, <span class="text-white font-extrabold"><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php if ($isVip): ?>
                                <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full bg-amber-500/20 border border-amber-400/40 text-amber-300 text-[10px] font-black" title="Akun Sultan VIP Aktif">
                                    <i class="fa-solid fa-crown text-amber-400 text-[10px]"></i>
                                    <span>VIP</span>
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-white/[0.06] text-slate-300 border border-white/10 text-[10px] font-medium">
                                    Member Reguler
                                </span>
                            <?php endif; ?>
                        </h1>
                        <p class="text-xs text-slate-400">
                            Pusat kendali promosi & pertukaran media sosial Anda.
                        </p>
                    </div>
                </div>

                <!-- Super Admin Quick Access Banner -->
                <?php if (($user['role'] ?? '') === 'admin'): ?>
                    <div class="mt-3 p-3.5 rounded-2xl bg-gradient-to-r from-rose-950/60 to-purple-950/60 border border-rose-500/40 flex items-center justify-between max-w-lg shadow-lg">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-300 flex items-center justify-center text-xs shrink-0 border border-rose-500/30">
                                <i class="fa-solid fa-shield-halved text-rose-400"></i>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-rose-200 block">Hak Akses: Super Administrator 🛡️</span>
                                <span class="text-[10px] text-slate-300">Akses kendali saldo koin, data pengguna & tugas sistem.</span>
                            </div>
                        </div>
                        <a href="panel_pusat_pakaini_99x.php" class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-rose-600 to-purple-600 hover:from-rose-500 hover:to-purple-500 text-white text-xs font-bold transition-all shadow-md flex items-center gap-1.5 shrink-0">
                            <span>Buka Panel</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                <?php endif; ?>

                <!-- VIP Status Detail Card -->
                <?php if ($isVip): ?>
                    <div class="mt-3 p-3 rounded-2xl bg-white/[0.04] border border-white/10 flex items-center justify-between max-w-lg">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs shrink-0">
                                <i class="fa-solid fa-crown text-amber-300"></i>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-amber-300 block">Status: Sultan VIP Aktif 👑</span>
                                <span class="text-[10px] sm:text-[11px] text-slate-300">
                                    Berlaku s/d: <strong><?= !empty($user['vip_expired_at']) ? date('d M Y, H:i', strtotime($user['vip_expired_at'])) . ' WIB' : 'Selamanya' ?></strong>
                                </span>
                            </div>
                        </div>
                        <a href="topup.php" class="px-3 py-1.5 rounded-xl bg-white/[0.08] hover:bg-white/[0.15] border border-white/15 text-white text-[11px] font-bold transition-all shadow-sm">
                            Perpanjang VIP
                        </a>
                    </div>
                <?php else: ?>
                    <div class="mt-3 p-3 rounded-2xl bg-white/[0.04] border border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-2 max-w-lg">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-xl bg-white/[0.08] text-slate-300 flex items-center justify-center text-xs shrink-0">
                                <i class="fa-solid fa-gem text-purple-400"></i>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-white block">Tingkatkan ke Sultan VIP</span>
                                <span class="text-[10px] sm:text-[11px] text-slate-400">Klik tanpa batas, prioritas teratas, dan slot tugas unlimited.</span>
                            </div>
                        </div>
                        <a href="topup.php" class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white text-[11px] font-bold transition-all shadow-sm shrink-0 text-center">
                            <i class="fa-solid fa-crown mr-1 text-amber-300"></i>Upgrade VIP
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Action Primary Buttons (Gradient Brand Logo) -->
            <div class="flex flex-col sm:flex-row md:flex-col gap-2.5 shrink-0 w-full md:w-auto">
                <a href="earn_coins.php" class="px-4 py-3 rounded-2xl bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-display font-bold text-xs flex items-center justify-center space-x-2 shadow-lg shadow-indigo-500/25 hover:shadow-[0_0_20px_rgba(139,92,246,0.4)] transition-all duration-300 hover:scale-[1.02] active:scale-95">
                    <i class="fa-solid fa-coins text-amber-300 text-sm"></i>
                    <span>Cari Koin / Kerjakan Tugas</span>
                </a>
                <a href="add_task.php" class="px-4 py-2.5 rounded-2xl bg-white/[0.06] hover:bg-white/[0.1] text-slate-200 border border-white/10 font-display font-semibold text-xs flex items-center justify-center space-x-2 shadow-sm transition-all hover:scale-[1.02] active:scale-95">
                    <i class="fa-solid fa-bullhorn text-slate-300 text-xs"></i>
                    <span>Pasang Iklan / Kampanye</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- 4 Metrics Stats Grid: MVP Koin Murni -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-5">
    
    <!-- 1. Saldo Koin Murni (Real-Time Counter dengan Neon Glow Ringan) -->
    <div class="glass-card rounded-2xl sm:rounded-3xl p-4 border border-amber-500/20 hover:border-amber-500/40 relative overflow-hidden group transition-all bg-[#111111] shadow-[0_0_20px_rgba(245,158,11,0.06)] hover:shadow-[0_0_25px_rgba(245,158,11,0.12)]">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[11px] font-bold text-slate-300">Saldo Koin Anda</span>
            <div class="w-8 h-8 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-300 flex items-center justify-center text-xs group-hover:scale-105 transition-transform shadow-[0_0_10px_rgba(245,158,11,0.2)]">
                <i class="fa-solid fa-coins text-amber-400"></i>
            </div>
        </div>
        <div class="text-xl sm:text-2xl font-display font-black text-white tracking-tight font-mono flex items-baseline gap-1.5">
            <span class="drop-shadow-[0_0_8px_rgba(245,158,11,0.5)]">🪙</span>
            <span class="user-coin-balance text-amber-300 drop-shadow-[0_0_12px_rgba(245,158,11,0.4)]"><?= number_format($user['coins']) ?></span>
            <span class="text-xs font-bold text-slate-400 font-sans">Koin</span>
        </div>
        <div class="mt-1.5 flex items-center justify-between text-[10px]">
            <span class="text-slate-400">Pencairan Instan</span>
            <a href="topup.php" class="text-amber-400 hover:text-amber-300 font-bold transition-colors">
                + Beli Koin
            </a>
        </div>
    </div>

    <!-- 2. Keanggotaan Akun -->
    <div class="glass-card rounded-2xl sm:rounded-3xl p-4 border border-white/[0.08] relative overflow-hidden group hover:border-white/20 transition-all bg-[#111111]">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[11px] font-medium text-slate-400">Keanggotaan</span>
            <div class="w-8 h-8 rounded-xl bg-white/[0.05] border border-white/10 text-slate-300 flex items-center justify-center text-xs group-hover:scale-105 transition-transform">
                <?php if ($isVip): ?>
                    <i class="fa-solid fa-crown text-amber-400"></i>
                <?php else: ?>
                    <i class="fa-solid fa-user text-slate-400"></i>
                <?php endif; ?>
            </div>
        </div>
        <div class="text-sm sm:text-base font-display font-bold text-white flex items-center gap-1">
            <?php if ($isVip): ?>
                <i class="fa-solid fa-crown text-amber-400 text-xs"></i>
                <span>Sultan VIP</span>
            <?php else: ?>
                <span>Member Biasa</span>
            <?php endif; ?>
        </div>
        <div class="mt-1.5 text-[10px] text-slate-400 flex items-center justify-between">
            <span><?= $isVip ? 'Klik Unlimited' : 'Batas 50 Klik/hari' ?></span>
            <a href="topup.php" class="text-purple-400 hover:text-purple-300 font-bold"><?= $isVip ? 'Perpanjang' : 'Upgrade' ?></a>
        </div>
    </div>

    <!-- 3. Klik Hari Ini -->
    <div class="glass-card rounded-2xl sm:rounded-3xl p-4 border border-white/[0.08] relative overflow-hidden group hover:border-white/20 transition-all bg-[#111111]">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[11px] font-medium text-slate-400">Klik Hari Ini</span>
            <div class="w-8 h-8 rounded-xl bg-white/[0.05] border border-white/10 text-slate-300 flex items-center justify-center text-xs group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-mouse-pointer text-slate-400"></i>
            </div>
        </div>
        <div class="text-xl sm:text-2xl font-display font-black text-white tracking-tight font-mono">
            <?= $dailyClicks ?> <span class="text-xs font-normal text-slate-400 font-sans">/ <?= $dailyLimit ?></span>
        </div>
        <div class="mt-1.5 text-[10px] text-slate-400 flex items-center justify-between">
            <span class="<?= $isVip ? 'text-amber-300 font-semibold' : 'text-slate-400' ?>"><?= $isVip ? '👑 Bebas Batas' : 'Reset Setiap Hari' ?></span>
            <a href="earn_coins.php" class="text-purple-400 hover:text-purple-300 font-bold">Cari Koin</a>
        </div>
    </div>

    <!-- 4. Kampanye Aktif -->
    <div class="glass-card rounded-2xl sm:rounded-3xl p-4 border border-white/[0.08] relative overflow-hidden group hover:border-white/20 transition-all bg-[#111111]">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[11px] font-medium text-slate-400">Kampanye Aktif</span>
            <div class="w-8 h-8 rounded-xl bg-white/[0.05] border border-white/10 text-slate-300 flex items-center justify-center text-xs group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-bullhorn text-slate-400"></i>
            </div>
        </div>
        <div class="text-xl sm:text-2xl font-display font-black text-white tracking-tight flex items-center gap-1 font-mono">
            <span><?= $totalActiveTasks ?></span>
            <span class="text-xs font-semibold text-slate-400 font-sans">Tugas</span>
        </div>
        <div class="mt-1.5 text-[10px] text-slate-400 flex items-center justify-between">
            <span>Sedang Berjalan</span>
            <a href="add_task.php" class="text-purple-400 hover:text-purple-300 font-bold">+ Pasang</a>
        </div>
    </div>

</div>

<!-- Navigation Quick Links (4 Features - Modern Mobile-App Style Cards) -->
<div class="mb-5">
    <h2 class="font-display font-bold text-sm text-white mb-3 flex items-center gap-2">
        <i class="fa-solid fa-compass text-indigo-400 text-xs"></i>
        <span>Menu & Fitur Unggulan</span>
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        
        <!-- Fitur 1: Cari Koin -->
        <a href="earn_coins.php" class="glass-card p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-white/[0.08] hover:border-white/20 bg-[#111111] transition-all group flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-2xl bg-white/[0.05] border border-white/10 text-white flex items-center justify-center text-base mb-3 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-coins text-amber-300"></i>
                </div>
                <h3 class="font-display font-bold text-white text-sm transition-colors">
                    Cari Koin / Kerjakan Tugas
                </h3>
                <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                    Kunjungi tugas member lain, subscribe/follow, & klaim koin gratis timer 15-30 detik.
                </p>
            </div>
            <div class="mt-3.5 pt-2.5 border-t border-white/[0.05] flex items-center justify-between text-[11px] font-semibold text-slate-300 group-hover:text-white">
                <span>Mulai Mengerjakan</span>
                <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
            </div>
        </a>

        <!-- Fitur 2: Pasang Tugas -->
        <a href="add_task.php" class="glass-card p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-white/[0.08] hover:border-white/20 bg-[#111111] transition-all group flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-2xl bg-white/[0.05] border border-white/10 text-white flex items-center justify-center text-base mb-3 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-bullhorn text-slate-300"></i>
                </div>
                <h3 class="font-display font-bold text-white text-sm transition-colors">
                    Pasang Iklan / Kampanye
                </h3>
                <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                    Buat kampanye YouTube Subs, TikTok, atau Instagram. Atur BPK sesuai kebutuhan.
                </p>
            </div>
            <div class="mt-3.5 pt-2.5 border-t border-white/[0.05] flex items-center justify-between text-[11px] font-semibold text-slate-300 group-hover:text-white">
                <span>Kelola Kampanye</span>
                <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
            </div>
        </a>

        <!-- Fitur 3: Referral Bounty -->
        <a href="referral.php" class="glass-card p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-white/[0.08] hover:border-white/20 bg-[#111111] transition-all group flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-2xl bg-white/[0.05] border border-white/10 text-white flex items-center justify-center text-base mb-3 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-gift text-slate-300"></i>
                </div>
                <h3 class="font-display font-bold text-white text-sm transition-colors">
                    Bounty Referral
                </h3>
                <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                    Ajak teman dan dapatkan bonus referral koin & komisi dari pendaftaran.
                </p>
            </div>
            <div class="mt-3.5 pt-2.5 border-t border-white/[0.05] flex items-center justify-between text-[11px] font-semibold text-slate-300 group-hover:text-white">
                <span>Lihat Tracker</span>
                <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
            </div>
        </a>

        <!-- Fitur 4: Toko VIP & Koin -->
        <a href="topup.php" class="glass-card p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-white/[0.08] hover:border-white/20 bg-[#111111] transition-all group flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-2xl bg-white/[0.05] border border-white/10 text-amber-300 flex items-center justify-center text-base mb-3 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-crown text-amber-400"></i>
                </div>
                <h3 class="font-display font-bold text-white text-sm transition-colors">
                    Toko VIP & Koin
                </h3>
                <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                    Paket Koin diskon 50% & VIP Sultan 1 Bulan via WhatsApp.
                </p>
            </div>
            <div class="mt-3.5 pt-2.5 border-t border-white/[0.05] flex items-center justify-between text-[11px] font-semibold text-slate-300 group-hover:text-white">
                <span>Pesan Sekarang</span>
                <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
            </div>
        </a>

    </div>
</div>

<!-- Grid: Kampanye Saya & Riwayat Mutasi Koin -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-5">

    <!-- Kolom Kiri (2 Kolom): Riwayat Mutasi Koin Terbaru -->
    <div class="lg:col-span-2 glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-white/[0.08] bg-[#111111]">
        <div class="flex items-center justify-between mb-3.5">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-receipt text-slate-300 text-xs"></i>
                <h3 class="font-display font-bold text-white text-sm">Riwayat Mutasi Koin Terbaru</h3>
            </div>
            <span class="text-[11px] text-slate-400">Catatan Otomatis</span>
        </div>

        <?php if (empty($recentTransactions)): ?>
            <div class="py-8 text-center text-slate-500">
                <i class="fa-solid fa-clock-rotate-left text-2xl mb-2 opacity-40"></i>
                <p class="text-xs">Belum ada riwayat transaksi koin. Mulai cari koin atau pasang tugas!</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-white/[0.06] text-slate-400">
                            <th class="pb-2 font-semibold">Waktu</th>
                            <th class="pb-2 font-semibold">Aktivitas</th>
                            <th class="pb-2 font-semibold text-right">Perubahan Koin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/[0.04]">
                        <?php foreach ($recentTransactions as $tx): ?>
                            <?php $isCredit = ((int)$tx['amount'] >= 0); ?>
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="py-2.5 text-slate-400 whitespace-nowrap text-[11px]">
                                    <?= date('d M, H:i', strtotime($tx['date'])) ?>
                                </td>
                                <td class="py-2.5 pr-2">
                                    <span class="font-semibold text-slate-200 block text-xs">
                                        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $tx['type'])), ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <span class="text-[10px] text-slate-400 truncate max-w-xs block">
                                        <?= htmlspecialchars($tx['description'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="py-2.5 text-right whitespace-nowrap font-mono font-bold text-xs">
                                    <?php if ($isCredit): ?>
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 text-[10px]">
                                            +<?= number_format($tx['amount']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-0.5 rounded-full bg-rose-500/15 text-rose-400 border border-rose-500/30 text-[10px]">
                                            <?= number_format($tx['amount']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Kolom Kanan (1 Kolom): Status Kampanye Aktif Saya -->
    <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-5 border border-white/[0.08] bg-[#111111] flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-3.5">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-chart-pie text-slate-300 text-xs"></i>
                    <h3 class="font-display font-bold text-white text-sm">Kampanye Saya</h3>
                </div>
                <span class="text-[11px] font-semibold <?= $isVip ? 'text-amber-400' : 'text-slate-400' ?>">
                    <?= $totalActiveTasks ?> / <?= $isVip ? '∞' : '2 Slot' ?>
                </span>
            </div>

            <?php if (empty($myRecentTasks)): ?>
                <div class="py-8 text-center text-slate-500">
                    <i class="fa-solid fa-bullhorn text-2xl mb-2 opacity-30"></i>
                    <p class="text-xs">Anda belum memasang kampanye link aktif.</p>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($myRecentTasks as $t): ?>
                        <?php 
                        $targetClicks = (int)($t['target_clicks'] ?? $t['max_clicks'] ?? 100);
                        $currentClicks = (int)($t['current_clicks'] ?? 0);
                        $isTaskActive = ((int)($t['is_active'] ?? 1) === 1 && $t['status'] === 'active');
                        $isCompleted = (!$isTaskActive && $currentClicks >= $targetClicks);
                        ?>
                        <div class="p-3.5 rounded-2xl bg-white/[0.03] border <?= $isTaskActive ? 'border-white/10' : 'border-white/[0.05] opacity-75' ?> flex flex-col gap-2.5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2.5 overflow-hidden">
                                    <?php if ($t['platform'] === 'youtube'): ?>
                                        <div class="w-8 h-8 rounded-xl bg-red-600/20 text-red-500 flex items-center justify-center shrink-0 text-sm">
                                            <i class="fa-brands fa-youtube"></i>
                                        </div>
                                    <?php elseif ($t['platform'] === 'tiktok'): ?>
                                        <div class="w-8 h-8 rounded-xl bg-pink-600/20 text-pink-400 flex items-center justify-center shrink-0 text-sm">
                                            <i class="fa-brands fa-tiktok"></i>
                                        </div>
                                    <?php elseif ($t['platform'] === 'instagram'): ?>
                                        <div class="w-8 h-8 rounded-xl bg-purple-600/20 text-purple-400 flex items-center justify-center shrink-0 text-sm">
                                            <i class="fa-brands fa-instagram"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-8 h-8 rounded-xl bg-blue-600/20 text-blue-400 flex items-center justify-center shrink-0 text-sm">
                                            <i class="fa-brands fa-facebook"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="truncate">
                                        <div class="text-[11px] font-bold text-slate-200 capitalize flex items-center gap-1.5">
                                            <span><?= htmlspecialchars($t['platform'], ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($t['task_type'], ENT_QUOTES, 'UTF-8') ?></span>
                                            <?php if ($isCompleted): ?>
                                                <span class="text-[9px] px-1.5 py-0.2 rounded-full bg-blue-500/15 text-blue-400 border border-blue-500/30 font-bold">Selesai</span>
                                            <?php elseif ($isTaskActive): ?>
                                                <span class="text-[9px] px-1.5 py-0.2 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 font-bold">Aktif</span>
                                            <?php else: ?>
                                                <span class="text-[9px] px-1.5 py-0.2 rounded-full bg-slate-800 text-slate-400 border border-white/5 font-semibold">Berhenti</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">
                                            <?= $currentClicks ?> / <?= $targetClicks ?> Klik diterima
                                        </div>
                                    </div>
                                </div>
                                <span class="text-[11px] font-bold font-mono text-amber-300 shrink-0 ml-2">
                                    🪙 <?= $t['cpc'] ?> BPK
                                </span>
                            </div>

                            <!-- Tombol Aksi Hentikan Iklan (Proses 2) -->
                            <?php if ($isTaskActive): ?>
                                <div class="pt-2 border-t border-white/[0.04] flex items-center justify-between">
                                    <span class="text-[10px] text-slate-400">Sisa kuota: <?= max(0, $targetClicks - $currentClicks) ?> klik</span>
                                    <form method="POST" action="dashboard.php" onsubmit="return confirm('Hentikan kampanye iklan ini? Sisa kuota klik akan otomatis dikembalikan (refund) ke saldo dompet koin Anda.');" class="inline">
                                        <input type="hidden" name="action" value="stop_task">
                                        <input type="hidden" name="task_id" value="<?= $t['id'] ?>">
                                        <button type="submit" class="px-2.5 py-1 rounded-xl bg-rose-500/15 hover:bg-rose-500/30 border border-rose-500/30 text-rose-300 font-bold text-[10px] flex items-center space-x-1 transition-all cursor-pointer">
                                            <i class="fa-solid fa-stop text-[9px]"></i>
                                            <span>Hentikan Iklan</span>
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-4 pt-3 border-t border-white/[0.06]">
            <a href="add_task.php" class="w-full py-2.5 px-3 rounded-2xl bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white text-xs font-bold transition-all flex items-center justify-center space-x-2 shadow-md shadow-indigo-500/20 active:scale-95">
                <i class="fa-solid fa-plus text-[10px]"></i>
                <span>Kelola & Pasang Tugas</span>
            </a>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/footer.php'; ?>
