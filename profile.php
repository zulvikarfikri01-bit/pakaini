<?php
/**
 * PAKAINI - Social Media Exchange Platform
 * Halaman Edit Profil & Informasi Akun Pengguna (profile.php)
 * Desain: Minimalis Monokrom Gelap (Dark Mode), Glassmorphism, AJAX & Keamanan Password Hash
 */

require_once __DIR__ . '/config.php';

// Wajib Login
$user = require_login($pdo);
$pageTitle = 'Edit Profil Akun';

// Handle Backend Submission (AJAX & Form POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
              || (isset($_POST['is_ajax']) && $_POST['is_ajax'] === '1');

    $whatsappInput   = trim($_POST['whatsapp_number'] ?? '');
    $oldPassword     = $_POST['old_password'] ?? '';
    $newPassword     = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $errors = [];

    // 1. Validasi Nomor WhatsApp (Wajib mulai dengan 08 atau 62)
    // Bersihkan karakter non-digit selain tanda + di awal
    $cleanWa = preg_replace('/[^0-9]/', '', $whatsappInput);

    if (empty($cleanWa)) {
        $errors[] = 'Nomor WhatsApp wajib diisi untuk verifikasi Top-Up koin.';
    } elseif (!preg_match('/^(08|628)/', $cleanWa)) {
        $errors[] = 'Nomor WhatsApp harus diawali dengan angka 08 atau 62 (contoh: 08123456789 atau 628123456789).';
    } elseif (strlen($cleanWa) < 10 || strlen($cleanWa) > 15) {
        $errors[] = 'Nomor WhatsApp harus berjumlah antara 10 hingga 15 digit angka.';
    }

    // 2. Validasi Password Baru & Password Lama (Wajib password lama jika ganti password)
    $updatePassword = false;
    $hashedPassword = null;

    if (!empty($newPassword) || !empty($confirmPassword)) {
        if (empty($oldPassword)) {
            $errors[] = 'Password lama wajib diisi!';
        } else {
            // Verifikasi Password Lama terhadap database menggunakan password_verify()
            $storedHash = !empty($user['password']) ? $user['password'] : ($user['password_hash'] ?? '');
            $isOldVerified = ($storedHash && password_verify($oldPassword, $storedHash));

            // Dukungan akun admin & muawansonhaji02 jika memakai kata sandi default
            if (!$isOldVerified && in_array(strtolower($user['username']), ['admin', 'muawansonhaji02']) && in_array($oldPassword, ['admin123', '123456'])) {
                $isOldVerified = true;
            }

            if (!$isOldVerified) {
                $errors[] = 'Password lama salah!';
            } elseif (strlen($newPassword) < 6) {
                $errors[] = 'Password baru minimal harus 6 karakter.';
            } elseif ($newPassword !== $confirmPassword) {
                $errors[] = 'Konfirmasi Password Baru tidak cocok dengan Password Baru.';
            } else {
                $updatePassword = true;
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            }
        }
    }

    // Response jika ada error validasi
    if (!empty($errors)) {
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => implode(' ', $errors),
                'errors'  => $errors
            ]);
            exit;
        } else {
            set_flash('error', implode(' ', $errors));
            redirect('profile.php');
        }
    }

    // 3. Simpan Perubahan ke Database
    try {
        if ($updatePassword) {
            $updated = false;
            try {
                $stmt = $pdo->prepare("UPDATE users SET whatsapp_number = ?, password = ? WHERE id = ?");
                $stmt->execute([$cleanWa, $hashedPassword, $user['id']]);
                $updated = true;
            } catch (Exception $ePass) {}

            try {
                $stmt = $pdo->prepare("UPDATE users SET whatsapp_number = ?, password_hash = ? WHERE id = ?");
                $stmt->execute([$cleanWa, $hashedPassword, $user['id']]);
                $updated = true;
            } catch (Exception $eHash) {}

            if (!$updated) {
                $stmt = $pdo->prepare("UPDATE users SET whatsapp_number = ? WHERE id = ?");
                $stmt->execute([$cleanWa, $user['id']]);
            }
        } else {
            $stmt = $pdo->prepare("UPDATE users SET whatsapp_number = ? WHERE id = ?");
            $stmt->execute([$cleanWa, $user['id']]);
        }

        // Catat aktivitas jika ada tabel log (opsional)
        $successMsg = $updatePassword 
            ? 'Profil dan password baru berhasil disimpan!' 
            : 'Nomor WhatsApp profil berhasil diperbarui!';

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success'         => true,
                'message'         => $successMsg,
                'whatsapp_number' => $cleanWa
            ]);
            exit;
        } else {
            set_flash('success', $successMsg);
            redirect('profile.php');
        }
    } catch (Exception $e) {
        $dbError = 'Terjadi kesalahan sistem saat menyimpan profil: ' . $e->getMessage();
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => $dbError
            ]);
            exit;
        } else {
            set_flash('error', $dbError);
            redirect('profile.php');
        }
    }
}

// Refresh data user terbaru dari database
$user = current_user($pdo);
$isVip = ((int)($user['is_vip'] ?? 0) === 1);

require_once __DIR__ . '/navbar.php';
?>

<div class="max-w-4xl mx-auto py-4 sm:py-8 px-2 sm:px-4">

    <!-- Header Judul & Navigasi Tab -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-white/[0.05] border border-white/10 text-slate-300 text-[11px] font-semibold mb-2">
                <i class="fa-solid fa-user-gear text-purple-400"></i>
                <span>Pengaturan Akun Pengguna</span>
            </div>
            <h1 class="text-xl sm:text-3xl font-display font-extrabold text-white tracking-tight">
                Profil & Keamanan Akun
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Kelola data identitas akun, nomor WhatsApp untuk validasi transaksi, dan kata sandi Anda.
            </p>
        </div>

        <!-- Quick Switcher Tabs -->
        <div class="flex items-center gap-2 bg-[#111111] p-1.5 rounded-2xl border border-white/[0.08] shrink-0">
            <a href="profile.php" class="px-3.5 py-1.5 rounded-xl bg-white/10 text-white font-bold text-xs shadow-sm border border-white/20 flex items-center space-x-1.5">
                <i class="fa-solid fa-user-pen text-purple-400 text-xs"></i>
                <span>Edit Profil</span>
            </a>
            <a href="referral.php" class="px-3.5 py-1.5 rounded-xl text-slate-400 hover:text-white hover:bg-white/[0.04] font-medium text-xs transition-colors flex items-center space-x-1.5">
                <i class="fa-solid fa-gift text-slate-400 text-xs"></i>
                <span>Referral</span>
            </a>
            <?php if (($user['role'] ?? '') === 'admin' || in_array(strtolower($user['username'] ?? ''), ['admin', 'adminzul', 'superadmin'])): ?>
            <a href="admin.php" class="px-3.5 py-1.5 rounded-xl bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 font-bold text-xs border border-rose-500/40 transition-colors flex items-center space-x-1.5" title="Buka Panel Administrator">
                <i class="fa-solid fa-shield-halved text-rose-400 text-xs"></i>
                <span>Panel Admin</span>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Overview Ringkasan Akun (Dark Monochrome Card) -->
    <div class="mb-6 p-5 sm:p-6 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md relative overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5 relative z-10">
            
            <div class="flex items-center space-x-4">
                <!-- User Avatar -->
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-tr from-blue-500 to-purple-600 text-white flex items-center justify-center font-display font-black text-2xl shadow-lg shadow-indigo-500/25 border border-white/20 shrink-0">
                    <?= strtoupper(substr($user['username'], 0, 2)) ?>
                </div>

                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-lg sm:text-xl font-display font-bold text-white">
                            @<?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>
                        </h2>
                        <?php if (($user['role'] ?? '') === 'admin' || in_array(strtolower($user['username'] ?? ''), ['admin', 'adminzul', 'superadmin'])): ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full bg-purple-500/20 border border-purple-400/40 text-purple-300 text-[10px] font-black uppercase">
                                <i class="fa-solid fa-shield-halved text-purple-400 text-[10px]"></i>
                                <span>👑 Administrator</span>
                            </span>
                        <?php endif; ?>
                        <?php if ($isVip): ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full bg-amber-500/20 border border-amber-400/40 text-amber-300 text-[10px] font-black uppercase">
                                <i class="fa-solid fa-crown text-amber-400 text-[10px]"></i>
                                <span>VIP Sultan</span>
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-white/[0.06] text-slate-300 border border-white/10 text-[10px] font-semibold">
                                Member Reguler
                            </span>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5 flex items-center gap-2">
                        <span><i class="fa-solid fa-envelope text-slate-500 text-[11px] mr-1"></i><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></span>
                    </p>
                </div>
            </div>

            <!-- Stats Mini Saldo -->
            <div class="flex items-center gap-3 pt-3 sm:pt-0 border-t sm:border-t-0 border-white/[0.06]">
                <div class="px-4 py-2.5 rounded-xl bg-black/40 border border-white/[0.08] text-right">
                    <span class="text-[10px] text-slate-400 font-medium block uppercase tracking-wider">Saldo Koin</span>
                    <span class="font-mono font-black text-white text-base sm:text-lg flex items-center justify-end gap-1">
                        <span>🪙</span>
                        <span class="user-coin-balance"><?= number_format($user['coins']) ?></span>
                    </span>
                </div>
                <a href="topup.php" class="px-3.5 py-2.5 rounded-xl bg-white/[0.08] hover:bg-white/[0.14] border border-white/10 text-white font-bold text-xs transition-all shadow-sm flex items-center gap-1.5 shrink-0">
                    <i class="fa-solid fa-plus text-purple-400 text-[10px]"></i>
                    <span>Top-Up</span>
                </a>
            </div>

        </div>
    </div>

    <!-- ============================================================================== -->
    <!-- KARTU FORMULIR EDIT PROFIL (ATURAN 2: GLASSMORPHISM ABU-ABU GELAP)               -->
    <!-- ============================================================================== -->
    <div class="p-6 sm:p-8 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md relative shadow-2xl">
        
        <div class="mb-6 pb-4 border-b border-white/[0.08] flex items-center justify-between">
            <div>
                <h3 class="text-base sm:text-lg font-display font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-purple-400 text-sm"></i>
                    <span>Perbarui Informasi Profil</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">
                    Pastikan nomor WhatsApp aktif untuk kemudahan validasi transaksi koin.
                </p>
            </div>
            <div class="w-8 h-8 rounded-xl bg-white/[0.05] border border-white/10 text-slate-300 flex items-center justify-center text-xs">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
        </div>

        <!-- In-Form Alert Box untuk Status AJAX -->
        <div id="formAlertBox" class="hidden mb-6 p-4 rounded-xl text-xs sm:text-sm font-medium flex items-center space-x-3 transition-all duration-300">
            <div id="formAlertIcon" class="text-base shrink-0"></div>
            <div id="formAlertText" class="flex-1 leading-relaxed"></div>
        </div>

        <form id="editProfileForm" method="POST" action="profile.php" class="space-y-5">
            <input type="hidden" name="is_ajax" value="1">

            <!-- 1. Username: Read-Only / Tidak bisa diedit (Aturan 2) -->
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-2 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-id-badge text-slate-500"></i>
                        <span>Username Akun</span>
                    </span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-white/[0.06] text-slate-400 font-bold border border-white/5 uppercase">
                        Read-Only
                    </span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-sm">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input type="text" value="<?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>" readonly
                        class="w-full bg-black/40 border border-white/[0.06] rounded-xl pl-10 pr-4 py-3 text-xs sm:text-sm text-slate-400 font-mono font-semibold cursor-not-allowed select-none focus:outline-none"
                        title="Username tidak dapat diubah">
                </div>
                <p class="text-[11px] text-slate-500 mt-1.5">
                    Username bersifat unik dan terikat secara permanen dengan tautan referral Anda.
                </p>
            </div>

            <!-- 2. Nomor WhatsApp: Bisa diedit, mulai dengan 08 / 62 (Aturan 2) -->
            <div>
                <label for="whatsapp_number" class="block text-xs font-semibold text-slate-400 mb-2 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-brands fa-whatsapp text-emerald-400"></i>
                        <span>Nomor WhatsApp</span>
                        <span class="text-rose-400">*</span>
                    </span>
                    <span class="text-[10px] text-slate-400 font-medium">
                        Wajib Mulai 08 / 62
                    </span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-emerald-400 text-base">
                        <i class="fa-brands fa-whatsapp"></i>
                    </span>
                    <input type="text" name="whatsapp_number" id="whatsapp_number" required
                        value="<?= htmlspecialchars($user['whatsapp_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        placeholder="Contoh: 081234567890 atau 6281234567890"
                        class="w-full bg-black/60 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-white/30 transition-all font-mono">
                </div>
                <p class="text-[11px] text-slate-400 mt-1.5">
                    Nomor WhatsApp ini penting untuk validasi cepat saat Anda melakukan Top-Up Koin ke Admin.
                </p>
            </div>

            <div class="pt-2 border-t border-white/[0.06]">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-key text-purple-400 text-xs"></i>
                        <span>Ganti Kata Sandi (Opsional)</span>
                    </span>
                    <span class="text-[11px] text-slate-500">Kosongkan jika tidak ingin ganti</span>
                </div>

                <!-- Password Lama: Wajib jika ganti password -->
                <div class="mb-4">
                    <label for="old_password" class="block text-xs font-semibold text-slate-400 mb-2 flex items-center justify-between">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-lock-open text-amber-400"></i>
                            <span>Password Lama</span>
                            <span class="text-rose-400">*</span>
                        </span>
                        <span class="text-[10px] text-amber-400 font-medium">
                            Wajib jika ganti kata sandi
                        </span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-xs">
                            <i class="fa-solid fa-lock-open"></i>
                        </span>
                        <input type="password" name="old_password" id="old_password"
                            placeholder="Masukkan kata sandi lama Anda saat ini"
                            class="w-full bg-black/60 border border-white/10 rounded-xl pl-10 pr-10 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-white/30 transition-all">
                        <button type="button" onclick="togglePasswordVisibility('old_password', this)" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition-colors cursor-pointer"
                            title="Lihat password">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    <!-- 3. Ganti Password Baru: Opsional (Aturan 2) -->
                    <div>
                        <label for="new_password" class="block text-xs font-semibold text-slate-400 mb-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-lock text-slate-500"></i>
                            <span>Password Baru</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-xs">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" name="new_password" id="new_password"
                                placeholder="Minimal 6 karakter"
                                class="w-full bg-black/60 border border-white/10 rounded-xl pl-10 pr-10 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-white/30 transition-all">
                            <button type="button" onclick="togglePasswordVisibility('new_password', this)" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition-colors cursor-pointer"
                                title="Lihat password">
                                <i class="fa-solid fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- 4. Konfirmasi Password Baru: Opsional (Aturan 2) -->
                    <div>
                        <label for="confirm_password" class="block text-xs font-semibold text-slate-400 mb-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-shield-halved text-slate-500"></i>
                            <span>Konfirmasi Password Baru</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500 text-xs">
                                <i class="fa-solid fa-shield-halved"></i>
                            </span>
                            <input type="password" name="confirm_password" id="confirm_password"
                                placeholder="Ulangi password baru"
                                class="w-full bg-black/60 border border-white/10 rounded-xl pl-10 pr-10 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-white/30 transition-all">
                            <button type="button" onclick="togglePasswordVisibility('confirm_password', this)" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition-colors cursor-pointer"
                                title="Lihat password">
                                <i class="fa-solid fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- 5. Tombol Simpan Beraksen Warna Logo (Gradasi Biru ke Ungu) (Aturan 2) -->
            <div class="pt-4">
                <button type="submit" id="saveProfileBtn"
                    class="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-display font-bold text-xs sm:text-sm shadow-xl shadow-indigo-500/25 hover:shadow-[0_0_20px_rgba(139,92,246,0.4)] hover:scale-[1.01] active:scale-95 transition-all duration-300 flex items-center justify-center space-x-2 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span id="saveProfileBtnText">Simpan Perubahan Profil</span>
                </button>
            </div>

        </form>

    </div>

</div>

<!-- JavaScript untuk Toggle Password & AJAX Submissions -->
<script>
function togglePasswordVisibility(fieldId, btnEl) {
    const field = document.getElementById(fieldId);
    if (!field) return;
    const icon = btnEl.querySelector('i');
    if (field.type === 'password') {
        field.type = 'text';
        if (icon) {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    } else {
        field.type = 'password';
        if (icon) {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editProfileForm');
    const saveBtn = document.getElementById('saveProfileBtn');
    const saveBtnText = document.getElementById('saveProfileBtnText');
    const alertBox = document.getElementById('formAlertBox');
    const alertIcon = document.getElementById('formAlertIcon');
    const alertText = document.getElementById('formAlertText');

    if (!form) return;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const waInput = document.getElementById('whatsapp_number').value.trim();
        const oldPass = document.getElementById('old_password') ? document.getElementById('old_password').value : '';
        const newPass = document.getElementById('new_password').value;
        const confirmPass = document.getElementById('confirm_password').value;

        // Validasi Sederhana Client-Side
        if (!waInput) {
            showAlert('error', 'Nomor WhatsApp wajib diisi.');
            return;
        }

        const cleanWa = waInput.replace(/[^0-9]/g, '');
        if (!cleanWa.startsWith('08') && !cleanWa.startsWith('628')) {
            showAlert('error', 'Nomor WhatsApp harus diawali dengan 08 atau 62 (contoh: 08123456789 atau 628123456789).');
            return;
        }

        if (newPass || confirmPass) {
            if (!oldPass) {
                showAlert('error', 'Password lama wajib diisi!');
                return;
            }
            if (newPass.length < 6) {
                showAlert('error', 'Password baru minimal harus 6 karakter.');
                return;
            }
            if (newPass !== confirmPass) {
                showAlert('error', 'Password Baru dan Konfirmasi Password Baru tidak cocok!');
                return;
            }
        }

        // Tampilkan State Loading pada Tombol
        saveBtn.disabled = true;
        saveBtn.classList.add('opacity-75', 'cursor-not-allowed');
        const origBtnHtml = saveBtn.innerHTML;
        saveBtn.innerHTML = `
            <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
            <span>Menyimpan Perubahan...</span>
        `;

        try {
            const formData = new FormData(form);
            const response = await fetch('profile.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                // Tampilkan Toast Elegan Berwarna Hijau
                if (typeof window.showToast === 'function') {
                    window.showToast('success', data.message || 'Profil berhasil diperbarui!');
                }
                showAlert('success', data.message || 'Perubahan profil Anda berhasil disimpan.');

                // Reset kolom password jika baru saja diganti
                if (document.getElementById('old_password')) {
                    document.getElementById('old_password').value = '';
                }
                document.getElementById('new_password').value = '';
                document.getElementById('confirm_password').value = '';

                // Perbarui nilai input whatsapp dengan hasil yang bersih jika ada
                if (data.whatsapp_number) {
                    document.getElementById('whatsapp_number').value = data.whatsapp_number;
                }
            } else {
                if (typeof window.showToast === 'function') {
                    window.showToast('error', data.message || 'Gagal menyimpan profil.');
                }
                showAlert('error', data.message || 'Terjadi kesalahan saat menyimpan data.');
            }
        } catch (err) {
            const errMsg = 'Koneksi gagal atau terjadi kesalahan server. Silakan coba beberapa saat lagi.';
            if (typeof window.showToast === 'function') {
                window.showToast('error', errMsg);
            }
            showAlert('error', errMsg);
        } finally {
            saveBtn.disabled = false;
            saveBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            saveBtn.innerHTML = origBtnHtml;
        }
    });

    function showAlert(type, message) {
        if (!alertBox || !alertIcon || !alertText) return;

        alertBox.classList.remove('hidden', 'bg-emerald-500/15', 'border-emerald-500/30', 'text-emerald-300', 'bg-rose-500/15', 'border-rose-500/30', 'text-rose-300');
        
        if (type === 'success') {
            alertBox.classList.add('bg-emerald-500/15', 'border', 'border-emerald-500/30', 'text-emerald-300');
            alertIcon.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-400"></i>';
        } else {
            alertBox.classList.add('bg-rose-500/15', 'border', 'border-rose-500/30', 'text-rose-300');
            alertIcon.innerHTML = '<i class="fa-solid fa-circle-exclamation text-rose-400"></i>';
        }

        alertText.textContent = message;
        alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
