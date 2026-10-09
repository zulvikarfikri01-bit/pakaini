<?php
/**
 * PAKAINI - Social Media Exchange Platform
 * Manajemen Kampanye Tugas (add_task.php)
 * Logika:
 * - Pengguna reguler maksimal pasang 2 tugas aktif, VIP tidak terbatas.
 * - Batas Minimal BPK:
 *   - Subs YouTube = 150 koin
 *   - Ikuti TikTok/IG = 50 koin
 *   - Suka/Tayangan = 20 koin
 */

require_once __DIR__ . '/config.php';

// Wajib Login
$user = require_login($pdo);
$pageTitle = 'Pasang Kampanye & Manajemen Tugas';

$isVip = ((int)$user['is_vip'] === 1);
$isAdmin = (($user['role'] ?? '') === 'admin' || (int)$user['id'] === 1);

// Hitung tugas aktif milik pengguna
$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ? AND status = 'active'");
$stmtCount->execute([$user['id']]);
$activeCount = (int)$stmtCount->fetchColumn();

// ==============================================================================
// Handle Aksi Laporkan Pekerja (Curang) - Aturan 2: Sistem Strike 3x Banned
// ==============================================================================
$reportAction = clean($_POST['action'] ?? $_GET['action'] ?? '');
if ($reportAction === 'report_worker') {
    $historyId = (int)($_POST['history_id'] ?? $_GET['history_id'] ?? 0);

    if ($historyId > 0) {
        $stmtRep = $pdo->prepare("
            SELECT th.*, t.user_id AS owner_id, u.id AS worker_id, u.username AS worker_username, 
                   u.strike_count, u.is_banned, u.coins AS worker_coins
            FROM task_history th
            JOIN tasks t ON th.task_id = t.id
            JOIN users u ON th.user_id = u.id
            WHERE th.id = ? AND t.user_id = ?
            LIMIT 1
        ");
        $stmtRep->execute([$historyId, $user['id']]);
        $reportTarget = $stmtRep->fetch();

        if (!$reportTarget) {
            set_flash('error', 'Data riwayat tugas tidak ditemukan atau bukan milik kampanye Anda.');
            redirect('add_task.php');
        }

        if ((int)($reportTarget['is_reported'] ?? 0) === 1) {
            set_flash('error', "Pekerja @{$reportTarget['worker_username']} sudah pernah dilaporkan untuk tugas ini.");
            redirect('add_task.php');
        }

        $workerId = (int)$reportTarget['worker_id'];
        $currentStrikes = (int)($reportTarget['strike_count'] ?? 0);
        $newStrikes = $currentStrikes + 1;

        try {
            $pdo->beginTransaction();

            // Tandai riwayat task_history sebagai sudah dilaporkan
            $pdo->prepare("UPDATE task_history SET is_reported = 1 WHERE id = ?")->execute([$historyId]);

            if ($newStrikes >= 3) {
                // Aturan 2: Jika strike_count mencapai angka 3, otomatis ubah is_banned = 1 dan hanguskan semua saldo koin mereka (coins = 0)
                $stmtBan = $pdo->prepare("UPDATE users SET strike_count = ?, is_banned = 1, coins = 0 WHERE id = ?");
                $stmtBan->execute([$newStrikes, $workerId]);

                // Catat transaksi hukuman hangus koin
                $stmtTx = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, date, description) VALUES (?, 'strike_ban', ?, NOW(), ?)");
                $stmtTx->execute([
                    $workerId,
                    -(int)$reportTarget['worker_coins'],
                    "Hukuman Tilang Strike 3/3: Akun diblokir permanen dan seluruh saldo koin dihanguskan karena kecurangan tugas."
                ]);

                $pdo->commit();
                set_flash('success', "🚨 LAPORAN SUKSES! Pekerja @{$reportTarget['worker_username']} mencapai Strike ke-3. Akun pekerja resmi DIBLOKIR PERMANEN dan seluruh saldo koinnya DIHANGUSKAN!");
            } else {
                // Tambah strike pekerja +1
                $stmtStrike = $pdo->prepare("UPDATE users SET strike_count = ? WHERE id = ?");
                $stmtStrike->execute([$newStrikes, $workerId]);

                // Catat log transaksi tilang strike
                $stmtTx = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, date, description) VALUES (?, 'strike_warning', 0, NOW(), ?)");
                $stmtTx->execute([
                    $workerId,
                    "Peringatan Tilang Strike {$newStrikes}/3: Dilaporkan curang oleh pemasang iklan pada tugas ID #{$reportTarget['task_id']}."
                ]);

                $pdo->commit();
                set_flash('success', "Laporan berhasil! Pekerja @{$reportTarget['worker_username']} mendapat +1 Strike (Total: {$newStrikes}/3). Akun akan otomatis diblokir permanen dan koin dihanguskan jika mencapai 3 Strike.");
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            set_flash('error', 'Gagal memproses laporan kecurangan: ' . $e->getMessage());
        }

        redirect('add_task.php');
    }
}

// Handle Aksi Toggle Status / Hentikan Iklan / Hapus Tugas
$action = clean($_GET['action'] ?? $_POST['action'] ?? '');
$taskId = (int)($_GET['id'] ?? $_POST['task_id'] ?? 0);

if ($taskId > 0 && !empty($action)) {
    // Pastikan tugas ini benar milik pengguna yang sedang login
    $stmtT = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ? LIMIT 1");
    $stmtT->execute([$taskId, $user['id']]);
    $targetTask = $stmtT->fetch();

    if ($targetTask) {
        if ($action === 'stop' || $action === 'stop_task') {
            // Fitur Hentikan Kampanye & Pengembalian Dana Otomatis (Proses 2)
            if ((int)($targetTask['is_active'] ?? 1) === 0 || $targetTask['status'] === 'paused' || $targetTask['status'] === 'completed') {
                set_flash('error', 'Kampanye iklan ini sudah dalam status tidak aktif atau selesai.');
                redirect('add_task.php');
            }

            $targetClicks = (int)($targetTask['target_clicks'] ?? $targetTask['max_clicks'] ?? 0);
            $currentClicks = (int)($targetTask['current_clicks'] ?? 0);
            $remainingClicks = max(0, $targetClicks - $currentClicks);

            $isPriority = ((int)($targetTask['is_priority'] ?? 0) === 1);
            $cpc = (int)$targetTask['cpc'];
            $pricePerClick = $isPriority ? ($cpc * 2) : $cpc;
            $refundCoins = $remainingClicks * $pricePerClick;
            $isAdminTask = ((int)($targetTask['is_admin_task'] ?? 0) === 1);

            try {
                $pdo->beginTransaction();

                // 1. Ubah status tugas menjadi is_active = 0
                $stmtStop = $pdo->prepare("UPDATE tasks SET is_active = 0, status = 'paused' WHERE id = ?");
                $stmtStop->execute([$taskId]);

                // 2. Logika Refund: Hitung sisa klik yang belum terpenuhi: (target_clicks - current_clicks) * harga_per_klik
                // Kembalikan jumlah koin tersebut ke saldo coins pengguna pembuat tugas
                if (!$isAdminTask && $refundCoins > 0) {
                    $stmtRefund = $pdo->prepare("UPDATE users SET coins = coins + ? WHERE id = ?");
                    $stmtRefund->execute([$refundCoins, $user['id']]);

                    $descRefund = "Pengembalian dana (Refund) iklan #{$taskId}: Sisa {$remainingClicks} klik x {$pricePerClick} koin";
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
            redirect('add_task.php');
        } elseif ($action === 'pause') {
            $pdo->prepare("UPDATE tasks SET status = 'paused', is_active = 0 WHERE id = ?")->execute([$taskId]);
            set_flash('success', 'Kampanye tugas berhasil dijeda.');
            redirect('add_task.php');
        } elseif ($action === 'resume') {
            // Jika member biasa, cek apakah slot aktif sudah 2
            if (!$isVip && $activeCount >= REGULAR_MAX_ACTIVE_TASKS) {
                set_flash('error', 'Gagal mengaktifkan: Kuota tugas aktif Anda sudah mencapai batas maksimal 2 tugas untuk member biasa.');
                redirect('add_task.php');
            }
            $targetClicks = (int)($targetTask['target_clicks'] ?? $targetTask['max_clicks'] ?? 0);
            $currentClicks = (int)($targetTask['current_clicks'] ?? 0);
            if ($currentClicks >= $targetClicks) {
                set_flash('error', 'Gagal mengaktifkan: Kuota target klik kampanye ini sudah tercapai.');
                redirect('add_task.php');
            }
            $pdo->prepare("UPDATE tasks SET status = 'active', is_active = 1 WHERE id = ?")->execute([$taskId]);
            set_flash('success', 'Kampanye tugas berhasil diaktifkan kembali.');
            redirect('add_task.php');
        } elseif ($action === 'delete') {
            $targetClicks = (int)($targetTask['target_clicks'] ?? $targetTask['max_clicks'] ?? 0);
            $currentClicks = (int)($targetTask['current_clicks'] ?? 0);
            $remainingClicks = max(0, $targetClicks - $currentClicks);
            $isPriority = ((int)($targetTask['is_priority'] ?? 0) === 1);
            $cpc = (int)$targetTask['cpc'];
            $pricePerClick = $isPriority ? ($cpc * 2) : $cpc;
            $refundCoins = $remainingClicks * $pricePerClick;
            $isAdminTask = ((int)($targetTask['is_admin_task'] ?? 0) === 1);
            $isActive = ((int)($targetTask['is_active'] ?? 1) === 1 && $targetTask['status'] === 'active');

            try {
                $pdo->beginTransaction();
                if ($isActive && !$isAdminTask && $refundCoins > 0) {
                    $pdo->prepare("UPDATE users SET coins = coins + ? WHERE id = ?")->execute([$refundCoins, $user['id']]);
                    $descRefund = "Refund hapus iklan #{$taskId}: Sisa {$remainingClicks} klik x {$pricePerClick} koin";
                    $pdo->prepare("INSERT INTO transactions (user_id, type, amount, date, description) VALUES (?, 'refund_tugas', ?, NOW(), ?)")->execute([$user['id'], $refundCoins, $descRefund]);
                }
                $pdo->prepare("DELETE FROM tasks WHERE id = ?")->execute([$taskId]);
                $pdo->commit();
                $msg = 'Kampanye tugas telah dihapus secara permanen.';
                if ($isActive && !$isAdminTask && $refundCoins > 0) {
                    $msg .= ' Sisa dana sebesar ' . number_format($refundCoins) . ' koin telah dikembalikan ke saldo dompet Anda.';
                }
                set_flash('success', $msg);
            } catch (Exception $e) {
                if ($pdo->inTransaction()) { $pdo->rollBack(); }
                set_flash('error', 'Gagal menghapus tugas: ' . $e->getMessage());
            }
            redirect('add_task.php');
        }
    }
}

// Handle Form Tambah Tugas Baru (POST)
$errors = [];
$old = [
    'platform'      => 'youtube',
    'task_type'     => 'subscribers',
    'task_url'      => '',
    'cpc'           => 100,
    'target_clicks' => 100,
    'max_clicks'    => 100
];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $platform     = strtolower(clean($_POST['platform'] ?? ''));
    $taskType     = strtolower(clean($_POST['task_type'] ?? ''));
    $taskUrl      = trim($_POST['task_url'] ?? '');

    // 1. Blokir Eksploitasi Angka Minus & Validasi Super Ketat (Anti Inspect Element)
    $target_clicks = $_POST['target_clicks'] ?? $_POST['max_clicks'] ?? null;
    if (!is_numeric($target_clicks) || $target_clicks < 10) {
        die("Target klik minimal adalah 10!");
    }
    $targetClicks = (int)$target_clicks;

    $cpc = $_POST['cpc'] ?? null;
    if (!is_numeric($cpc) || $cpc < 50) {
        die("Biaya per klik (Cost Per Click) minimal adalah 50 koin!");
    }
    $cpc = (int)$cpc;

    // Auto-Format: Jika pengguna lupa memasukkan http:// atau https:// di awal link, otomatis tambahkan https://
    if (!empty($taskUrl) && !preg_match('#^https?://#i', $taskUrl)) {
        $taskUrl = 'https://' . $taskUrl;
    }

    $old['platform']      = $platform;
    $old['task_type']     = $taskType;
    $old['task_url']      = $taskUrl;
    $old['cpc']           = $cpc;
    $old['target_clicks'] = $targetClicks;
    $old['max_clicks']    = $targetClicks;

    // 1. Logika Wajib: Pengguna reguler maksimal pasang 2 tugas aktif, VIP tidak terbatas
    if (!$isVip && $activeCount >= REGULAR_MAX_ACTIVE_TASKS) {
        $errors[] = 'Batas Kuota Tercapai: Member reguler maksimal memiliki ' . REGULAR_MAX_ACTIVE_TASKS . ' tugas aktif sekaligus. Silakan jeda/hapus tugas lama atau upgrade ke VIP Sultan untuk pasang tugas tanpa batas.';
    }

    if ($targetClicks < 10) {
        $errors[] = 'Target Jumlah Interaksi/Klik minimal 10.';
    }

    // 2. Validasi Platform & Jenis Tugas
    $allowedPlatforms = ['youtube', 'tiktok', 'instagram', 'facebook'];
    $allowedTypes     = ['subscribers', 'followers', 'likes', 'views'];

    if (!in_array($platform, $allowedPlatforms)) {
        $errors[] = 'Platform yang dipilih tidak valid.';
    }
    if (!in_array($taskType, $allowedTypes)) {
        $errors[] = 'Jenis tindakan tugas tidak valid.';
    }

    // 3. Validasi URL Berlapis (Platform Rules & Format)
    if (empty($taskUrl)) {
        $errors[] = 'URL / Link target tugas wajib diisi.';
    } elseif (!filter_var($taskUrl, FILTER_VALIDATE_URL)) {
        $errors[] = 'Format link URL tidak valid. Pastikan alamat tautan benar.';
    } else {
        $urlLower = strtolower($taskUrl);
        if ($platform === 'youtube') {
            // Perluasan Validasi YouTube: Harus menerima youtube.com/watch, youtu.be, dan youtube.com/shorts (serta format channel)
            $isYtValid = (
                strpos($urlLower, 'youtube.com/watch') !== false ||
                strpos($urlLower, 'youtu.be') !== false ||
                strpos($urlLower, 'youtube.com/shorts') !== false ||
                strpos($urlLower, 'youtube.com/@') !== false ||
                strpos($urlLower, 'youtube.com/channel') !== false ||
                strpos($urlLower, 'youtube.com/c/') !== false ||
                strpos($urlLower, 'youtube.com') !== false
            );
            if (!$isYtValid) {
                $errors[] = 'URL YouTube tidak cocok! Format yang diterima: youtube.com/watch, youtu.be, youtube.com/shorts, atau profil channel (@).';
            }
        } elseif ($platform === 'instagram') {
            // Perluasan Validasi Instagram: Harus menerima instagram.com/p/ (Post), instagram.com/reel/, dan link profil
            $isIgValid = (
                strpos($urlLower, 'instagram.com/p/') !== false ||
                strpos($urlLower, 'instagram.com/reel/') !== false ||
                strpos($urlLower, 'instagram.com/reels/') !== false ||
                strpos($urlLower, 'instagram.com/') !== false ||
                strpos($urlLower, 'instagram.com') !== false
            );
            if (!$isIgValid) {
                $errors[] = 'URL Instagram tidak cocok! Format yang diterima: instagram.com/p/ (Post), instagram.com/reel/, atau link profil.';
            }
        } elseif ($platform === 'tiktok') {
            // Perluasan Validasi TikTok: Harus menerima tiktok.com, vt.tiktok.com, dan vm.tiktok.com
            $isTtValid = (
                strpos($urlLower, 'tiktok.com') !== false ||
                strpos($urlLower, 'vt.tiktok.com') !== false ||
                strpos($urlLower, 'vm.tiktok.com') !== false
            );
            if (!$isTtValid) {
                $errors[] = 'URL TikTok tidak cocok! Format yang diterima: tiktok.com, vt.tiktok.com, atau vm.tiktok.com.';
            }
        }
    }

    // 4. Logika Timer & Harga Dinamis (Berdasarkan Platform - Aturan 1)
    // Jika YouTube: harga tugas = 100 Koin/klik.
    // Jika Instagram / TikTok / Facebook: harga tugas = 50 Koin/klik.
    $minCpc = ($platform === 'youtube') ? 100 : 50;

    if ($cpc < $minCpc) {
        die("Biaya per klik (BPK) untuk platform " . ucfirst($platform) . " minimal adalah {$minCpc} koin!");
    }

    // Opsi Prioritas (Biaya Koin 2x Lipat, Sematkan ke Atas)
    $isPriority = isset($_POST['is_priority']) && (int)$_POST['is_priority'] === 1;
    $costPerClick = $isPriority ? ($cpc * 2) : $cpc;

    // Opsi Tugas Wajib Admin (Khusus Akun Admin)
    $isAdmin = (($user['role'] ?? '') === 'admin' || (int)$user['id'] === 1);
    $isAdminTask = ($isAdmin && isset($_POST['is_admin_task']) && (int)$_POST['is_admin_task'] === 1) ? 1 : 0;

    // 5. Potong Saldo Koin Pengguna di Awal (Target Klik * Harga per Klik) - Proses 1
    // Jika koin tidak cukup, tolak!
    $totalUpfrontCost = $targetClicks * $costPerClick;
    $userCoins = (int)($user['coins'] ?? 0);

    if (!$isAdminTask && $userCoins < $totalUpfrontCost) {
        $priorityNote = $isPriority ? " (Prioritas Biaya 2x Lipat = {$costPerClick} Koin/klik)" : "";
        $errors[] = "Saldo Koin Anda (" . number_format($userCoins) . " Koin) tidak mencukupi untuk total biaya kampanye sebesar " . number_format($totalUpfrontCost) . " Koin ({$targetClicks} target klik x " . number_format($costPerClick) . " Koin{$priorityNote}). Silakan kurangi target klik atau lakukan Top-Up koin terlebih dahulu.";
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Potong saldo koin di awal jika bukan admin task
            if (!$isAdminTask) {
                $stmtDeduct = $pdo->prepare("UPDATE users SET coins = coins - ? WHERE id = ? AND coins >= ?");
                $stmtDeduct->execute([$totalUpfrontCost, $user['id'], $totalUpfrontCost]);
                if ($stmtDeduct->rowCount() === 0) {
                    throw new Exception("Saldo koin tidak mencukupi saat proses pemotongan saldo kampanye.");
                }

                $txDesc = "Biaya pasang iklan {$platform} ({$targetClicks} target klik @ {$costPerClick} koin)" . ($isPriority ? " [Prioritas 2x]" : "");
                $stmtTx = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, date, description) VALUES (?, 'pasang_iklan', ?, NOW(), ?)");
                $stmtTx->execute([$user['id'], -$totalUpfrontCost, $txDesc]);
            }

            $stmtInsert = $pdo->prepare("
                INSERT INTO tasks (user_id, platform, task_type, task_url, cpc, is_priority, is_admin_task, status, target_clicks, max_clicks, current_clicks, is_active, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'active', ?, ?, 0, 1, NOW())
            ");
            $stmtInsert->execute([
                $user['id'],
                $platform,
                $taskType,
                $taskUrl,
                $cpc,
                $isPriority ? 1 : 0,
                $isAdminTask,
                $targetClicks,
                $targetClicks
            ]);

            $pdo->commit();

            $successMsg = 'Kampanye iklan berhasil dipasang dan langsung aktif!';
            if (!$isAdminTask) {
                $successMsg .= ' Saldo koin telah dipotong di awal sebesar ' . number_format($totalUpfrontCost) . ' Koin.';
            }
            if ($isAdminTask) {
                $successMsg .= ' ⭐ (Status: Tugas Wajib Admin - Seluruh user baru wajib mengerjakan ini dulu)';
            } elseif ($isPriority) {
                $successMsg .= ' 🔥 (Status: Prioritas - Selalu tampil paling atas di daftar Cari Koin)';
            }

            set_flash('success', $successMsg);
            redirect('add_task.php');
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errors[] = 'Gagal menyimpan tugas: ' . $e->getMessage();
        }
    }
}

// Ambil semua tugas milik pengguna
$stmtMyAllTasks = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY id DESC");
$stmtMyAllTasks->execute([$user['id']]);
$myTasks = $stmtMyAllTasks->fetchAll();

// Ambil riwayat pekerja yang mengambil koin dari tugas milik pemasang iklan (Aturan 2)
$myTaskIds = array_column($myTasks, 'id');
$taskWorkersMap = [];
if (!empty($myTaskIds)) {
    $placeholders = implode(',', array_fill(0, count($myTaskIds), '?'));
    $stmtWorkers = $pdo->prepare("
        SELECT th.id AS history_id, th.task_id, th.completed_at, th.is_reported,
               u.id AS worker_id, u.username AS worker_username, u.strike_count, u.is_banned
        FROM task_history th
        JOIN users u ON th.user_id = u.id
        WHERE th.task_id IN ($placeholders)
        ORDER BY th.id DESC
    ");
    $stmtWorkers->execute($myTaskIds);
    $allWorkers = $stmtWorkers->fetchAll();
    foreach ($allWorkers as $w) {
        $taskWorkersMap[$w['task_id']][] = $w;
    }
}

require_once __DIR__ . '/navbar.php';
?>

<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl sm:text-2xl font-display font-extrabold text-white flex items-center gap-2">
            <i class="fa-solid fa-bullhorn text-sky-400"></i>
            <span>Manajemen Kampanye Tugas</span>
        </h1>
        <p class="text-xs sm:text-sm text-slate-400 mt-1">
            Pasang link YouTube, TikTok, atau Instagram Anda agar di-subscribe dan di-follow oleh member lain.
        </p>
    </div>

    <!-- Status Slot Tugas -->
    <div class="glass-card px-4 py-2.5 rounded-2xl border border-white/[0.08] flex items-center space-x-3 shrink-0">
        <div class="w-8 h-8 rounded-xl <?= $isVip ? 'bg-amber-500/20 text-amber-400' : 'bg-sky-500/20 text-sky-400' ?> flex items-center justify-center text-sm">
            <i class="fa-solid <?= $isVip ? 'fa-crown text-amber-300' : 'fa-layer-group' ?>"></i>
        </div>
        <div>
            <div class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Slot Kampanye Aktif</div>
            <div class="text-xs font-bold text-white flex items-center gap-1.5">
                <?php if ($isVip): ?>
                    <span class="text-amber-300 font-extrabold">👑 Tanpa Batas (VIP Unlimited)</span>
                <?php else: ?>
                    <span class="<?= ($activeCount >= REGULAR_MAX_ACTIVE_TASKS) ? 'text-rose-400' : 'text-emerald-400' ?>">
                        <?= $activeCount ?> / <?= REGULAR_MAX_ACTIVE_TASKS ?> Tugas Terpakai
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Warning Banner jika Regular Member mencapai 2 slot -->
<?php if (!$isVip && $activeCount >= REGULAR_MAX_ACTIVE_TASKS): ?>
    <div class="mb-6 p-4 rounded-2xl bg-amber-500/15 border border-amber-500/30 text-amber-200 text-xs sm:text-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg">
        <div class="flex items-center space-x-3">
            <i class="fa-solid fa-triangle-exclamation text-amber-400 text-lg"></i>
            <div>
                <strong>Batas Slot Kampanye Reguler Penuh!</strong>
                <p class="text-xs text-amber-300/80 mt-0.5">Anda sudah memiliki 2 tugas aktif. Jeda tugas lama atau beralih ke VIP Sultan untuk menambah tugas sepuasnya.</p>
            </div>
        </div>
        <a href="topup.php" class="px-3.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs transition-all shrink-0 text-center">
            Upgrade VIP Sultan
        </a>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

    <!-- Form Tambah Tugas Baru (5 Kolom di Layar Lebar) -->
    <div class="lg:col-span-5">
        <div class="glass-card rounded-3xl p-6 border border-white/[0.08] bg-[#111111] relative">
            <h2 class="font-display font-bold text-base text-white mb-1 flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-slate-300"></i>
                <span>Formulir Kampanye Baru</span>
            </h2>
            <p class="text-xs text-slate-400 mb-5">Atur detail kampanye dan biaya koin per klik (BPK).</p>

            <!-- Error Alerts -->
            <?php if (!empty($errors)): ?>
                <div class="mb-4 p-3.5 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs space-y-1">
                    <div class="font-bold flex items-center space-x-1.5">
                        <i class="fa-solid fa-circle-exclamation text-rose-400"></i>
                        <span>Perhatian:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5">
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="add_task.php" method="POST" class="space-y-4" id="taskForm">

                <!-- Platform -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                        Platform Media Sosial <span class="text-rose-400">*</span>
                    </label>
                    <select name="platform" id="platformSelect" onchange="updateMinCpc()" required
                        class="w-full bg-black/60 border border-white/[0.1] rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-white/30 transition-all">
                        <option value="youtube" <?= ($old['platform'] === 'youtube') ? 'selected' : '' ?>>YouTube</option>
                        <option value="tiktok" <?= ($old['platform'] === 'tiktok') ? 'selected' : '' ?>>TikTok</option>
                        <option value="instagram" <?= ($old['platform'] === 'instagram') ? 'selected' : '' ?>>Instagram</option>
                        <option value="facebook" <?= ($old['platform'] === 'facebook') ? 'selected' : '' ?>>Facebook</option>
                    </select>
                </div>

                <!-- Jenis Tindakan (Task Type) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                        Jenis Tindakan <span class="text-rose-400">*</span>
                    </label>
                    <select name="task_type" id="typeSelect" onchange="updateMinCpc()" required
                        class="w-full bg-black/60 border border-white/[0.1] rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-white/30 transition-all">
                        <option value="subscribers" <?= ($old['task_type'] === 'subscribers') ? 'selected' : '' ?>>Subscribers Channel (Khusus YouTube)</option>
                        <option value="followers" <?= ($old['task_type'] === 'followers') ? 'selected' : '' ?>>Followers Akun (TikTok / Instagram / Facebook)</option>
                        <option value="likes" <?= ($old['task_type'] === 'likes') ? 'selected' : '' ?>>Likes / Suka Video & Foto</option>
                        <option value="views" <?= ($old['task_type'] === 'views') ? 'selected' : '' ?>>Views / Tayangan Video</option>
                    </select>
                </div>

                <!-- URL Link Target -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="task_url">
                        Link URL Target <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="task_url" id="task_url" required
                        value="<?= htmlspecialchars($old['task_url'], ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="https://www.youtube.com/@channelAnda"
                        class="w-full bg-black/60 border border-white/[0.1] rounded-2xl px-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-white/30 transition-all">
                    <p class="text-[10px] text-slate-400 mt-1" id="urlHelpText">
                        Contoh: <code>youtube.com/@channel</code> atau diawali <code>https://</code> (otomatis ditambahkan jika lupa).
                    </p>
                    <div id="urlErrorFeedback" class="hidden mt-1.5 p-2.5 rounded-2xl bg-rose-500/15 border border-rose-500/30 text-[11px] text-rose-300 font-medium flex items-center space-x-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-400 shrink-0"></i>
                        <span id="urlErrorText"></span>
                    </div>
                </div>

                <!-- CPC (Cost Per Click) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-slate-300" for="cpcInput">
                            Harga per Klik (BPK) <span class="text-rose-400">*</span>
                        </label>
                        <span id="minCpcBadge" class="text-[10px] font-bold text-amber-300 bg-white/[0.08] border border-amber-500/30 px-2.5 py-0.5 rounded-full">
                            Min: 100 Koin
                        </span>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-amber-400 text-xs font-bold">
                            🪙
                        </span>
                        <input type="number" name="cpc" id="cpcInput" required min="50"
                            value="<?= (int)$old['cpc'] ?>"
                            class="w-full bg-black/60 border border-white/[0.1] rounded-2xl pl-9 pr-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-white/30 transition-all font-mono font-bold">
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1" id="cpcHelpText">
                        YouTube = 100 Koin/klik (Timer 30s) • Instagram / TikTok / Facebook = 50 Koin/klik (Timer 15s).
                    </p>
                </div>

                <!-- Target Jumlah Interaksi/Klik (Proses 1) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5" for="target_clicks">
                        Target Jumlah Interaksi/Klik <span class="text-rose-400">*</span>
                    </label>
                    <input type="number" name="target_clicks" id="target_clicks" min="10" max="10000" required
                        value="<?= max(10, (int)($old['target_clicks'] ?? $old['max_clicks'] ?? 100)) ?>"
                        oninput="updateTotalCostPreview()"
                        class="w-full bg-black/60 border border-white/[0.1] rounded-2xl px-4 py-3 text-xs text-white focus:outline-none focus:border-white/30 transition-all font-mono font-bold">
                    <p class="text-[10px] text-slate-400 mt-1">Total biaya kampanye: <code>Target Klik × Harga per Klik</code>.</p>
                    
                    <!-- Box Perhitungan Biaya Di Awal -->
                    <div class="mt-2.5 p-3 rounded-2xl bg-white/[0.04] border border-white/10 flex items-center justify-between">
                        <span class="text-slate-400 text-[11px] font-medium">
                            <i class="fa-solid fa-calculator text-amber-400 mr-1"></i>Total Biaya:
                        </span>
                        <span class="font-extrabold text-amber-300 font-mono text-xs" id="totalCostPreview">
                            - Koin
                        </span>
                    </div>
                </div>

                <!-- Ketentuan Minimal BPK Card Helper -->
                <div class="p-3.5 rounded-2xl bg-white/[0.03] border border-white/10 text-[11px] text-slate-400 space-y-1">
                    <span class="font-bold text-slate-300 block mb-1">
                        <i class="fa-solid fa-circle-info text-slate-400 mr-1"></i>Harga Standar Platform:
                    </span>
                    <div class="flex justify-between">
                        <span>• Platform YouTube (Timer 30 Detik):</span>
                        <strong class="text-amber-300">100 Koin / klik</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>• Instagram / TikTok / Facebook (Timer 15 Detik):</span>
                        <strong class="text-amber-300">50 Koin / klik</strong>
                    </div>
                </div>

                <!-- Opsi Prioritas (Biaya Koin 2x Lipat) Sesuai Aturan 4 -->
                <div class="p-3.5 rounded-2xl bg-gradient-to-r from-amber-500/10 to-orange-500/10 border border-amber-500/30 flex items-start space-x-3">
                    <div class="flex items-center h-5 mt-0.5">
                        <input type="checkbox" id="is_priority" name="is_priority" value="1"
                               class="w-4 h-4 rounded text-amber-500 bg-slate-900 border-slate-700 focus:ring-amber-400 focus:ring-offset-slate-900 cursor-pointer">
                    </div>
                    <div class="flex-1">
                        <label for="is_priority" class="text-xs font-bold text-amber-300 flex items-center gap-1.5 cursor-pointer">
                            <span>🔥 Jadikan Prioritas (Biaya Koin 2x Lipat)</span>
                            <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-[9px] font-black uppercase text-amber-400 border border-amber-500/30">Paling Atas</span>
                        </label>
                        <p class="text-[10px] sm:text-[11px] text-slate-300 mt-0.5 leading-snug">
                            Tugas prioritas <strong class="text-white">selalu tampil paling atas</strong> di daftar "Cari Koin". Biaya koin yang dipotong saat dikerjakan adalah 2x lipat BPK.
                        </p>
                    </div>
                </div>

                <?php if ($isAdmin): ?>
                <!-- Fitur Tugas Wajib Admin (Monopoli Admin) Sesuai Aturan 4 -->
                <div class="p-3.5 rounded-2xl bg-gradient-to-r from-rose-500/10 to-red-500/10 border border-rose-500/30 flex items-start space-x-3">
                    <div class="flex items-center h-5 mt-0.5">
                        <input type="checkbox" id="is_admin_task" name="is_admin_task" value="1"
                               class="w-4 h-4 rounded text-rose-500 bg-slate-900 border-slate-700 focus:ring-rose-400 focus:ring-offset-slate-900 cursor-pointer">
                    </div>
                    <div class="flex-1">
                        <label for="is_admin_task" class="text-xs font-bold text-rose-300 flex items-center gap-1.5 cursor-pointer">
                            <span>⭐ Jadikan Tugas Wajib Admin</span>
                            <span class="px-2 py-0.5 rounded-full bg-rose-500/20 text-[9px] font-black uppercase text-rose-400 border border-rose-500/30">Wajib New User</span>
                        </label>
                        <p class="text-[10px] sm:text-[11px] text-slate-300 mt-0.5 leading-snug">
                            User baru <strong class="text-white">WAJIB mengerjakan tugas ini terlebih dahulu</strong> sebelum bisa melihat atau mengerjakan tugas dari user lain.
                        </p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" title="Terbitkan Kampanye"
                        <?= (!$isVip && $activeCount >= REGULAR_MAX_ACTIVE_TASKS) ? 'disabled' : '' ?>
                        class="w-full py-3.5 px-4 rounded-2xl <?= (!$isVip && $activeCount >= REGULAR_MAX_ACTIVE_TASKS) ? 'bg-slate-800 text-slate-500 cursor-not-allowed' : 'bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white shadow-xl shadow-indigo-500/25 hover:shadow-[0_0_20px_rgba(139,92,246,0.4)] hover:scale-[1.01] active:scale-95 cursor-pointer' ?> font-display font-bold text-xs tracking-wide transition-all duration-300 flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                        <span>Bayar Koin & Pasang Iklan</span>
                    </button>

                    <!-- Disclaimer Lepas Tanggung Jawab (Fenomena Drop) Sesuai Aturan 3 -->
                    <p class="text-xs text-slate-400 mt-3 leading-relaxed">
                        Penting: Penurunan jumlah (Drop) bisa saja terjadi akibat pembersihan rutin dari sistem algoritma YouTube/Instagram. Pakaini adalah platform perantara dan tidak memberikan garansi isi ulang (refill) untuk interaksi yang di-drop oleh platform pihak ketiga.
                    </p>
                </div>

            </form>
        </div>
    </div>

    <!-- Tabel Daftar Kampanye Saya (7 Kolom di Layar Lebar) -->
    <div class="lg:col-span-7">
        <div class="glass-card rounded-3xl p-6 border border-white/[0.08] bg-[#111111]">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-list-check text-slate-300"></i>
                    <h3 class="font-display font-bold text-white text-base">Daftar Kampanye Saya</h3>
                </div>
                <span class="text-xs text-slate-400">Total: <?= count($myTasks) ?> Tugas</span>
            </div>

            <?php if (empty($myTasks)): ?>
                <div class="py-16 text-center text-slate-500">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-white/[0.04] border border-white/10 flex items-center justify-center text-2xl mb-3 text-slate-500">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-400">Belum ada kampanye</p>
                    <p class="text-xs text-slate-500 mt-1">Gunakan formulir di sebelah kiri untuk memasang tugas pertama Anda.</p>
                </div>
            <?php else: ?>
                <div class="space-y-3.5">
                    <?php foreach ($myTasks as $task): ?>
                        <?php 
                        $targetClicks = (int)($task['target_clicks'] ?? $task['max_clicks'] ?? 100);
                        $currentClicks = (int)($task['current_clicks'] ?? 0);
                        $isTaskActive = ((int)($task['is_active'] ?? 1) === 1 && $task['status'] === 'active');
                        $isCompleted = (!$isTaskActive && $currentClicks >= $targetClicks);
                        $progress = min(100, round(($currentClicks / max(1, $targetClicks)) * 100));
                        ?>
                        <div class="p-4 rounded-2xl bg-white/[0.03] border <?= $isTaskActive ? 'border-white/10' : 'border-white/[0.05] opacity-75' ?> transition-all">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-2.5">
                                
                                <div class="flex items-center space-x-3 overflow-hidden">
                                    <?php if ($task['platform'] === 'youtube'): ?>
                                        <div class="w-9 h-9 rounded-xl bg-red-600/20 text-red-500 flex items-center justify-center text-lg shrink-0">
                                            <i class="fa-brands fa-youtube"></i>
                                        </div>
                                    <?php elseif ($task['platform'] === 'tiktok'): ?>
                                        <div class="w-9 h-9 rounded-xl bg-pink-600/20 text-pink-400 flex items-center justify-center text-lg shrink-0">
                                            <i class="fa-brands fa-tiktok"></i>
                                        </div>
                                    <?php elseif ($task['platform'] === 'instagram'): ?>
                                        <div class="w-9 h-9 rounded-xl bg-purple-600/20 text-purple-400 flex items-center justify-center text-lg shrink-0">
                                            <i class="fa-brands fa-instagram"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-9 h-9 rounded-xl bg-blue-600/20 text-blue-400 flex items-center justify-center text-lg shrink-0">
                                            <i class="fa-brands fa-facebook"></i>
                                        </div>
                                    <?php endif; ?>

                                    <div class="overflow-hidden">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-bold text-white text-xs capitalize"><?= htmlspecialchars($task['platform'], ENT_QUOTES, 'UTF-8') ?></span>
                                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 font-semibold uppercase">
                                                <?= htmlspecialchars($task['task_type'], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                            <?php if ($isCompleted): ?>
                                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-500/15 text-blue-400 border border-blue-500/30 font-bold">
                                                    Selesai
                                                </span>
                                            <?php elseif ($isTaskActive): ?>
                                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 font-bold">
                                                    Aktif
                                                </span>
                                            <?php else: ?>
                                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-400 border border-white/5 font-bold">
                                                    Dihentikan
                                                </span>
                                            <?php endif; ?>
                                            <?php if ((int)($task['is_priority'] ?? 0) === 1): ?>
                                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-gradient-to-r from-amber-500/20 to-orange-500/20 text-amber-300 border border-amber-500/40 font-black tracking-wide">
                                                    🔥 Prioritas
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <a href="<?= htmlspecialchars($task['task_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="text-[11px] text-sky-400 hover:underline truncate block mt-0.5 max-w-sm">
                                            <?= htmlspecialchars($task['task_url'], ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2 shrink-0 self-end sm:self-center">
                                    <span class="px-2.5 py-1 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-300 font-mono font-bold text-xs">
                                        🪙 <?= $task['cpc'] ?> BPK
                                    </span>

                                    <!-- Action Buttons & Tombol Hentikan Iklan (Proses 2) -->
                                    <?php if ($isTaskActive): ?>
                                        <a href="add_task.php?action=stop&id=<?= $task['id'] ?>" 
                                            onclick="return confirm('Hentikan kampanye iklan ini? Sisa kuota klik akan otomatis dikembalikan (refund) ke saldo dompet koin Anda.');" 
                                            title="Hentikan Iklan & Refund Koin"
                                            class="px-2.5 py-1 rounded-xl bg-rose-500/15 hover:bg-rose-500/30 text-rose-300 border border-rose-500/30 flex items-center space-x-1 text-xs transition-colors font-bold cursor-pointer">
                                            <i class="fa-solid fa-stop text-[10px]"></i>
                                            <span>Hentikan Iklan</span>
                                        </a>
                                        <a href="add_task.php?action=pause&id=<?= $task['id'] ?>" title="Jeda Sementara"
                                            class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-amber-500/20 hover:text-amber-300 text-slate-400 flex items-center justify-center text-xs transition-colors">
                                            <i class="fa-solid fa-pause"></i>
                                        </a>
                                    <?php elseif (!$isCompleted): ?>
                                        <a href="add_task.php?action=resume&id=<?= $task['id'] ?>" title="Aktifkan Kembali"
                                            class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-emerald-500/20 hover:text-emerald-300 text-slate-400 flex items-center justify-center text-xs transition-colors">
                                            <i class="fa-solid fa-play"></i>
                                        </a>
                                    <?php endif; ?>

                                    <a href="add_task.php?action=delete&id=<?= $task['id'] ?>" onclick="return confirm('Hapus kampanye tugas ini?');" title="Hapus Permanen"
                                        class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-rose-500/20 hover:text-rose-400 text-slate-400 flex items-center justify-center text-xs transition-colors">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>

                            </div>

                            <!-- Progress Bar Klik -->
                            <div class="mt-2.5 pt-2 border-t border-slate-900 flex items-center justify-between text-[11px] text-slate-400">
                                <span>Pencapaian: <strong class="text-white"><?= $currentClicks ?></strong> / <?= $targetClicks ?> Klik</span>
                                <span class="font-bold text-slate-300"><?= $progress ?>%</span>
                            </div>
                            <div class="mt-1 w-full bg-slate-900 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-sky-500 h-full rounded-full transition-all" style="width: <?= $progress ?>%;"></div>
                            </div>

                            <!-- ============================================================================== -->
                            <!-- DAFTAR PEKERJA & TOMBOL LAPOR CURANG (Aturan 2: Sistem Strike 3x Banned)         -->
                            <!-- ============================================================================== -->
                            <?php 
                            $workers = $taskWorkersMap[$task['id']] ?? []; 
                            ?>
                            <div class="mt-3 pt-2.5 border-t border-slate-900">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-1.5 text-[11px] font-bold text-slate-300">
                                        <i class="fa-solid fa-users text-sky-400 text-[10px]"></i>
                                        <span>Pekerja yang Mengambil Koin (<?= count($workers) ?>)</span>
                                    </div>
                                    <?php if (!empty($workers)): ?>
                                        <button type="button" onclick="toggleWorkersList(<?= $task['id'] ?>)" class="text-[11px] text-sky-400 hover:text-sky-300 font-semibold focus:outline-none flex items-center gap-1 cursor-pointer">
                                            <span id="btnWorkerText-<?= $task['id'] ?>">Lihat Pekerja</span>
                                            <i class="fa-solid fa-chevron-down text-[9px]" id="iconWorkerArrow-<?= $task['id'] ?>"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <?php if (empty($workers)): ?>
                                    <p class="text-[10px] text-slate-500 italic mt-1">Belum ada pekerja yang mengambil koin dari tugas ini.</p>
                                <?php else: ?>
                                    <div id="workersBox-<?= $task['id'] ?>" class="hidden mt-2 space-y-1.5">
                                        <?php foreach ($workers as $w): ?>
                                            <?php 
                                            $isWorkerBanned = ((int)$w['is_banned'] === 1);
                                            $isReported = ((int)$w['is_reported'] === 1);
                                            $strikeCount = (int)$w['strike_count'];
                                            ?>
                                            <div class="p-2 rounded-xl bg-slate-900/90 border border-slate-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-6 h-6 rounded-lg <?= $isWorkerBanned ? 'bg-rose-500/20 text-rose-400' : 'bg-slate-800 text-slate-300' ?> flex items-center justify-center text-[10px] font-bold shrink-0">
                                                        <i class="fa-solid <?= $isWorkerBanned ? 'fa-user-slash' : 'fa-user' ?>"></i>
                                                    </div>
                                                    <div>
                                                        <div class="text-[11px] font-bold text-white flex items-center gap-1.5">
                                                            <span>@<?= htmlspecialchars($w['worker_username'], ENT_QUOTES, 'UTF-8') ?></span>
                                                            <?php if ($isWorkerBanned): ?>
                                                                <span class="px-1.5 py-0.2 rounded bg-rose-500/20 text-rose-400 text-[9px] font-black border border-rose-500/30">⛔ BANNED</span>
                                                            <?php elseif ($strikeCount > 0): ?>
                                                                <span class="px-1.5 py-0.2 rounded bg-amber-500/20 text-amber-300 text-[9px] font-bold border border-amber-500/30">Strike <?= $strikeCount ?>/3</span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <span class="text-[9px] text-slate-500 block">
                                                            <?= date('d M Y, H:i', strtotime($w['completed_at'])) ?>
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="shrink-0 flex items-center">
                                                    <?php if ($isWorkerBanned): ?>
                                                        <span class="text-[10px] text-rose-400/90 bg-rose-500/10 border border-rose-500/30 px-2 py-0.5 rounded-lg font-bold">
                                                            <i class="fa-solid fa-ban mr-1"></i>Akun Diblokir
                                                        </span>
                                                    <?php elseif ($isReported): ?>
                                                        <span class="text-[10px] text-amber-300 bg-amber-500/15 border border-amber-500/30 px-2 py-0.5 rounded-lg font-bold">
                                                            <i class="fa-solid fa-check mr-1"></i>Sudah Dilaporkan
                                                        </span>
                                                    <?php else: ?>
                                                        <form method="POST" action="add_task.php" onsubmit="return confirm('⚠️ KONFIRMASI LAPORAN KECURANGAN:\n\nApakah Anda yakin pekerja @<?= htmlspecialchars(addslashes($w['worker_username']), ENT_QUOTES, 'UTF-8') ?> tidak melakukan tugas (curang)?\n\n• Pekerja akan dikenakan +1 Strike.\n• Jika mencapai 3 Strike, akun akan otomatis DIBLOKIR PERMANEN dan seluruh saldo koinnya DIHANGUSKAN!');" class="m-0">
                                                            <input type="hidden" name="action" value="report_worker">
                                                            <input type="hidden" name="history_id" value="<?= (int)$w['history_id'] ?>">
                                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-500 text-white font-extrabold text-[10px] shadow-sm hover:shadow transition-all flex items-center justify-center space-x-1 cursor-pointer">
                                                                <i class="fa-solid fa-triangle-exclamation text-[9px]"></i>
                                                                <span>Laporkan Pekerja (Curang)</span>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

</div>

<script>
// Logika Dinamis Batas Minimal BPK Berdasarkan Platform (Aturan 1)
function updateMinCpc() {
    const platform = document.getElementById('platformSelect').value;
    const cpcInput = document.getElementById('cpcInput');
    const badge    = document.getElementById('minCpcBadge');

    // YouTube = 100 Koin/klik, Platform Lain (IG, TikTok, FB) = 50 Koin/klik
    let min = (platform === 'youtube') ? 100 : 50;

    if (cpcInput) {
        cpcInput.min = min;
        if (parseInt(cpcInput.value) < min) {
            cpcInput.value = min;
        }
    }
    if (badge) {
        badge.innerText = `Min: ${min} Koin`;
    }
    updateTotalCostPreview();
}

// Perhitungan Total Biaya (Target Klik * Harga per Klik)
function updateTotalCostPreview() {
    const cpcInput = document.getElementById('cpcInput');
    const targetInput = document.getElementById('target_clicks');
    const priorityCheckbox = document.getElementById('is_priority');
    const previewEl = document.getElementById('totalCostPreview');
    if (!cpcInput || !targetInput || !previewEl) return;

    const cpc = parseInt(cpcInput.value) || 0;
    const clicks = parseInt(targetInput.value) || 0;
    const isPriority = priorityCheckbox && priorityCheckbox.checked;
    const costPerClick = isPriority ? (cpc * 2) : cpc;
    const total = clicks * costPerClick;

    previewEl.innerText = `${total.toLocaleString('id-ID')} Koin (${clicks.toLocaleString('id-ID')} klik @ ${costPerClick.toLocaleString('id-ID')} koin)`;
}

// Validasi URL Berlapis & Auto-Format di Frontend Sebelum Form Di-submit
const taskForm         = document.getElementById('taskForm');
const platformSelect   = document.getElementById('platformSelect');
const urlInput         = document.getElementById('task_url');
const urlErrorFeedback = document.getElementById('urlErrorFeedback');
const urlErrorText     = document.getElementById('urlErrorText');

function updatePlaceholder() {
    if (!platformSelect || !urlInput) return;
    const platform = platformSelect.value;
    if (platform === 'youtube') {
        urlInput.placeholder = 'https://youtube.com/watch?v=..., youtu.be/..., youtube.com/shorts/...';
    } else if (platform === 'instagram') {
        urlInput.placeholder = 'https://instagram.com/p/... (Post), /reel/..., atau profil akun';
    } else if (platform === 'tiktok') {
        urlInput.placeholder = 'https://tiktok.com/@user, vt.tiktok.com/..., atau vm.tiktok.com/...';
    } else if (platform === 'facebook') {
        urlInput.placeholder = 'https://www.facebook.com/halamanAnda';
    }
}

function clearUrlError() {
    if (urlErrorFeedback) {
        urlErrorFeedback.classList.add('hidden');
    }
    if (urlErrorText) {
        urlErrorText.innerText = '';
    }
    if (urlInput) {
        urlInput.classList.remove('border-rose-500', 'focus:border-rose-500');
        urlInput.classList.add('border-slate-700/80', 'focus:border-sky-500');
    }
}

function showUrlError(msg) {
    if (urlErrorText) {
        urlErrorText.innerText = msg;
    }
    if (urlErrorFeedback) {
        urlErrorFeedback.classList.remove('hidden');
    }
    if (urlInput) {
        urlInput.classList.remove('border-slate-700/80', 'focus:border-sky-500');
        urlInput.classList.add('border-rose-500', 'focus:border-rose-500');
        urlInput.focus();
    }
}

if (urlInput) {
    urlInput.addEventListener('input', clearUrlError);
}

if (platformSelect) {
    platformSelect.addEventListener('change', () => {
        updateMinCpc();
        updatePlaceholder();
        clearUrlError();
    });
}

if (taskForm) {
    taskForm.addEventListener('submit', function(e) {
        clearUrlError();
        const platform = platformSelect.value;
        let url = urlInput.value.trim();

        if (!url) {
            e.preventDefault();
            showUrlError('Link URL target tugas wajib diisi!');
            return false;
        }

        // Auto-Format: Tambahkan https:// jika pengguna lupa memasukkannya di awal link
        if (!/^https?:\/\//i.test(url)) {
            url = 'https://' + url;
            urlInput.value = url;
        }

        const urlLower = url.toLowerCase();
        let isValid = true;
        let errorMsg = '';

        // Aturan Pencocokan String URL Platform (Sesuai Aturan 2: Fleksibel Mendukung Format Baru)
        if (platform === 'youtube') {
            // YouTube: Harus menerima youtube.com/watch, youtu.be, dan youtube.com/shorts
            const isYt = urlLower.includes('youtube.com/watch') || 
                         urlLower.includes('youtu.be') || 
                         urlLower.includes('youtube.com/shorts') || 
                         urlLower.includes('youtube.com/@') || 
                         urlLower.includes('youtube.com/channel') || 
                         urlLower.includes('youtube.com');
            if (!isYt) {
                isValid = false;
                errorMsg = 'URL YouTube tidak cocok! Format yang diterima: youtube.com/watch, youtu.be, atau youtube.com/shorts.';
            }
        } else if (platform === 'instagram') {
            // Instagram: Harus menerima instagram.com/p/ (Post), instagram.com/reel/, dan link profil
            const isIg = urlLower.includes('instagram.com/p/') || 
                         urlLower.includes('instagram.com/reel/') || 
                         urlLower.includes('instagram.com/reels/') || 
                         urlLower.includes('instagram.com');
            if (!isIg) {
                isValid = false;
                errorMsg = 'URL Instagram tidak cocok! Format yang diterima: instagram.com/p/ (Post), instagram.com/reel/, atau link profil.';
            }
        } else if (platform === 'tiktok') {
            // TikTok: Harus menerima tiktok.com, vt.tiktok.com, dan vm.tiktok.com
            const isTt = urlLower.includes('tiktok.com') || 
                         urlLower.includes('vt.tiktok.com') || 
                         urlLower.includes('vm.tiktok.com');
            if (!isTt) {
                isValid = false;
                errorMsg = 'URL TikTok tidak cocok! Format yang diterima: tiktok.com, vt.tiktok.com, atau vm.tiktok.com.';
            }
        }

        if (!isValid) {
            e.preventDefault();
            showUrlError(errorMsg);
            return false;
        }
    });
}

// Toggle Tampilan Daftar Pekerja Kampanye (Aturan 2)
function toggleWorkersList(taskId) {
    const box = document.getElementById(`workersBox-${taskId}`);
    const text = document.getElementById(`btnWorkerText-${taskId}`);
    const arrow = document.getElementById(`iconWorkerArrow-${taskId}`);
    if (box) {
        if (box.classList.contains('hidden')) {
            box.classList.remove('hidden');
            if (text) text.innerText = 'Sembunyikan';
            if (arrow) arrow.className = 'fa-solid fa-chevron-up text-[9px]';
        } else {
            box.classList.add('hidden');
            if (text) text.innerText = 'Lihat Pekerja';
            if (arrow) arrow.className = 'fa-solid fa-chevron-down text-[9px]';
        }
    }
}

// Inisialisasi saat pertama kali dimuat
document.addEventListener('DOMContentLoaded', () => {
    updateMinCpc();
    updatePlaceholder();
    updateTotalCostPreview();

    const cpcInput = document.getElementById('cpcInput');
    const targetInput = document.getElementById('target_clicks');
    const priorityCheckbox = document.getElementById('is_priority');

    if (cpcInput) {
        cpcInput.addEventListener('input', updateTotalCostPreview);
    }
    if (targetInput) {
        targetInput.addEventListener('input', updateTotalCostPreview);
    }
    if (priorityCheckbox) {
        priorityCheckbox.addEventListener('change', updateTotalCostPreview);
    }
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
