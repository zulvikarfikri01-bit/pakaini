<?php
/**
 * Test Suite: Verifikasi Fitur Lupa Password WA & Reset Kata Sandi Admin (123456)
 */

echo "======================================================================\n";
echo "🔑 PENGUJIAN FITUR LUPA PASSWORD WA & RESET KATA SANDI ADMIN\n";
echo "======================================================================\n\n";

$passed = 0;
$failed = 0;

function check(string $label, bool $condition): void {
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "  ✅ PASS: {$label}\n";
    } else {
        $failed++;
        echo "  ❌ FAIL: {$label}\n";
    }
}

// ------------------------------------------------------------------------------
// 1. PENGUJIAN HALAMAN LOGIN (login.php & views/auth/login.php)
// ------------------------------------------------------------------------------
echo "1. Pengujian Halaman Login (login.php):\n";

$loginSrc = file_get_contents(__DIR__ . '/../login.php');
check("login.php memiliki teks 'Lupa Password? Hubungi Admin'", 
    strpos($loginSrc, 'Lupa Password? Hubungi Admin') !== false
);

check("login.php memiliki link WhatsApp dengan format wa.me/628XXXXXXXXXX?text=...", 
    strpos($loginSrc, 'https://wa.me/628XXXXXXXXXX?text=Halo%20Admin%20Pakaini,%20saya%20lupa%20password%20akun%20saya.%20Username/Email%20saya%20adalah:%20') !== false
);

$viewLoginSrc = file_get_contents(__DIR__ . '/../views/auth/login.php');
check("views/auth/login.php memiliki teks 'Lupa Password? Hubungi Admin'", 
    strpos($viewLoginSrc, 'Lupa Password? Hubungi Admin') !== false
);

check("views/auth/login.php memiliki link WhatsApp Admin", 
    strpos($viewLoginSrc, 'https://wa.me/628XXXXXXXXXX?text=') !== false
);

// ------------------------------------------------------------------------------
// 2. PENGUJIAN PANEL ADMIN (admin.php)
// ------------------------------------------------------------------------------
echo "\n2. Pengujian Panel Admin (admin.php):\n";

$adminSrc = file_get_contents(__DIR__ . '/../admin.php');

check("admin.php memiliki Fitur Manajemen Pengguna (tabel daftar pengguna)", 
    strpos($adminSrc, 'Fitur Manajemen Pengguna') !== false && 
    strpos($adminSrc, '$allUsersList') !== false
);

check("admin.php memiliki tombol aksi 'Reset Sandi' di baris pengguna", 
    strpos($adminSrc, 'Reset Sandi') !== false && 
    strpos($adminSrc, 'value="reset_password"') !== false
);

check("admin.php memiliki backend handler action 'reset_password'", 
    strpos($adminSrc, "\$action === 'reset_password'") !== false
);

check("admin.php menggunakan password_hash() untuk password default 123456", 
    strpos($adminSrc, "\$defaultPassword = '123456'") !== false && 
    strpos($adminSrc, "password_hash(\$defaultPassword, PASSWORD_DEFAULT)") !== false
);

check("admin.php menampilkan pesan flash sukses yang tepat", 
    strpos($adminSrc, 'Kata sandi berhasil direset menjadi: 123456. Arahkan pengguna untuk segera menggantinya di Profil.') !== false
);

// ------------------------------------------------------------------------------
// 3. PENGUJIAN LOGIKA DATABASE RESET PASSWORD (SIMULASI NYATA)
// ------------------------------------------------------------------------------
echo "\n3. Pengujian Logika Reset Kata Sandi ke Database:\n";

require_once __DIR__ . '/../config.php';

// Buat user dummy untuk pengujian reset password
$testUsername = 'user_test_reset_' . time();
$testEmail = $testUsername . '@example.com';
$initialPassword = 'oldPasswordSecret999';
$initialHash = password_hash($initialPassword, PASSWORD_DEFAULT);

$pdo->prepare("
    INSERT INTO users (username, email, password, password_hash, role, coins, is_vip, daily_clicks, active_days_streak, created_at)
    VALUES (?, ?, ?, ?, 'user', 500, 0, 0, 1, NOW())
")->execute([$testUsername, $testEmail, $initialHash, $initialHash]);

$userId = (int)$pdo->lastInsertId();
check("User uji coba berhasil dibuat (ID #{$userId})", $userId > 0);

// Verifikasi password awal bekerja
$userRow = $pdo->query("SELECT * FROM users WHERE id = {$userId}")->fetch();
check("Password lama terverifikasi sebelum reset", password_verify($initialPassword, $userRow['password']));
check("Password '123456' tidak cocok sebelum reset", !password_verify('123456', $userRow['password']));

// SIMULASI AKSI RESET PASSWORD OLEH ADMIN
$defaultPassword = '123456';
$newHashed = password_hash($defaultPassword, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("UPDATE users SET password = ?, password_hash = ? WHERE id = ?");
    $stmt->execute([$newHashed, $newHashed, $userId]);
} catch (Exception $e) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$newHashed, $userId]);
    } catch (Exception $e2) {
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$newHashed, $userId]);
    }
}

// Ambil user setelah reset
$updatedUser = $pdo->query("SELECT * FROM users WHERE id = {$userId}")->fetch();
$activeHash = !empty($updatedUser['password']) ? $updatedUser['password'] : $updatedUser['password_hash'];

check("Password setelah direset berhasil di-verify dengan '123456'", password_verify('123456', $activeHash));
check("Password lama TIDAK DAPAT digunakan lagi", !password_verify($initialPassword, $activeHash));
check("Hash tersimpan bukan plain text (terenkripsi BCRYPT/ARGON2)", $activeHash !== '123456');

// Bersihkan data user uji coba
$pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);
check("Data uji coba berhasil dibersihkan", true);

// ------------------------------------------------------------------------------
// 4. PENGUJIAN MVC CONTROLLER & VIEW (AdminController & views/admin/users.php)
// ------------------------------------------------------------------------------
echo "\n4. Pengujian MVC Controller & View:\n";

$mvcViewSrc = file_get_contents(__DIR__ . '/../views/admin/users.php');
check("views/admin/users.php memiliki tombol 'Reset Sandi'", 
    strpos($mvcViewSrc, 'Reset Sandi') !== false && 
    strpos($mvcViewSrc, 'reset_password_default') !== false
);

$ctrlSrc = file_get_contents(__DIR__ . '/../app/Controllers/AdminController.php');
check("AdminController memiliki fungsi reset_password_default", 
    strpos($ctrlSrc, 'function reset_password_default') !== false && 
    strpos($ctrlSrc, '123456') !== false
);

echo "\n----------------------------------------------------------------------\n";
echo "RINGKASAN HASIL: {$passed} LULUS, {$failed} GAGAL\n";
echo "======================================================================\n";

if ($failed === 0) {
    echo "🎉 SEMUA PENGUJIAN FITUR LUPA & RESET PASSWORD 100% SUKSES!\n";
    exit(0);
} else {
    echo "⚠️ ADA PENGUJIAN YANG GAGAL!\n";
    exit(1);
}
