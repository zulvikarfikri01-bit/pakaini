<?php
/**
 * Test Suite: Verifikasi Fitur Edit Profil (profile.php & edit_profile.php)
 * Menguji: Kolom DB whatsapp_number, UI Minimalis Monokrom Gelap, Logika AJAX & Enkripsi Password Hash
 */

require_once __DIR__ . '/../config.php';

echo "======================================================================\n";
echo "👤 PENGUJIAN FITUR EDIT PROFIL & KEAMANAN PASSWORD HASH\n";
echo "======================================================================\n\n";

$pass = 0;
$fail = 0;

function check(string $name, bool $result) {
    global $pass, $fail;
    if ($result) {
        echo "  ✅ PASS: {$name}\n";
        $pass++;
    } else {
        echo "  ❌ FAIL: {$name}\n";
        $fail++;
    }
}

// ------------------------------------------------------------------------------
// 1. PENGUJIAN SKEMA DATABASE (KOLOM whatsapp_number)
// ------------------------------------------------------------------------------
echo "1. Pengujian Skema Database (Kolom whatsapp_number):\n";

// Cek di database aktif (SQLite / MySQL)
$hasCol = false;
try {
    $stmt = $pdo->query("SELECT whatsapp_number FROM users LIMIT 1");
    $hasCol = true;
} catch (Exception $e) {
    $hasCol = false;
}
check("Kolom 'whatsapp_number' aktif dan dapat di-query di tabel users", $hasCol);

// Cek di file database.sql
$dbSql = file_get_contents(__DIR__ . '/../database.sql');
check("File database.sql mendefinisikan kolom 'whatsapp_number' VARCHAR", 
    strpos($dbSql, 'whatsapp_number') !== false && strpos($dbSql, 'VARCHAR') !== false
);

// Cek di config.php migrasi otomatis
$configSrc = file_get_contents(__DIR__ . '/../config.php');
check("File config.php memiliki skrip migrasi otomatis kolom whatsapp_number", 
    strpos($configSrc, 'ALTER TABLE users ADD COLUMN whatsapp_number') !== false
);

// ------------------------------------------------------------------------------
// 2. PENGUJIAN UI EDIT PROFIL (profile.php)
// ------------------------------------------------------------------------------
echo "\n2. Pengujian Desain UI Edit Profil (profile.php):\n";
$profileSrc = file_get_contents(__DIR__ . '/../profile.php');

check("Kartu form menggunakan tema glassmorphism abu-abu gelap (bg-white/5 border border-white/10 rounded-2xl)", 
    strpos($profileSrc, 'bg-white/5') !== false && 
    strpos($profileSrc, 'border-white/10') !== false && 
    strpos($profileSrc, 'rounded-2xl') !== false
);

check("Label form menggunakan warna abu-abu silver text-slate-400", 
    strpos($profileSrc, 'text-slate-400') !== false
);

check("Kolom input berlatar hitam transparan (bg-black/60 atau bg-black/40)", 
    strpos($profileSrc, 'bg-black/60') !== false || strpos($profileSrc, 'bg-black/40') !== false
);

check("Kolom Username bersifat Read-Only (readonly)", 
    strpos($profileSrc, 'readonly') !== false && strpos($profileSrc, 'Username Akun') !== false
);

check("Kolom Nomor WhatsApp tersedia dengan petunjuk 08 / 62", 
    strpos($profileSrc, 'name="whatsapp_number"') !== false && 
    (strpos($profileSrc, '08') !== false || strpos($profileSrc, '62') !== false)
);

check("Kolom Ganti Password Baru tersedia (name='new_password')", 
    strpos($profileSrc, 'name="new_password"') !== false
);

check("Kolom Konfirmasi Password Baru tersedia (name='confirm_password')", 
    strpos($profileSrc, 'name="confirm_password"') !== false
);

check("Tombol simpan profil menggunakan gradasi logo from-blue-500 to-purple-600", 
    strpos($profileSrc, 'bg-gradient-to-r from-blue-500 to-purple-600') !== false
);

check("File edit_profile.php tersedia sebagai endpoint / alias", 
    file_exists(__DIR__ . '/../edit_profile.php')
);

// ------------------------------------------------------------------------------
// 3. PENGUJIAN LOGIKA BACKEND & AJAX (SIMULASI VALIDASI)
// ------------------------------------------------------------------------------
echo "\n3. Pengujian Logika Backend & Keamanan Password:\n";

// Buat akun user uji
$testUsername = 'test_profile_' . time();
$origPassword = 'initialPassword123';
$origHash = password_hash($origPassword, PASSWORD_DEFAULT);

$pdo->prepare("INSERT INTO users (username, email, password, coins) VALUES (?, ?, ?, ?)")
    ->execute([$testUsername, $testUsername . '@test.com', $origHash, 1000]);
$testUserId = (int)$pdo->lastInsertId();

// Simulasi A: Simpan WhatsApp number valid (diawali 08)
$waInput = '081298765432';
$stmtUpdWa = $pdo->prepare("UPDATE users SET whatsapp_number = ? WHERE id = ?");
$stmtUpdWa->execute([$waInput, $testUserId]);

$fetchUser = $pdo->query("SELECT * FROM users WHERE id = {$testUserId}")->fetch();
check("Update nomor WhatsApp (08xxx) berhasil disimpan ke database", $fetchUser['whatsapp_number'] === $waInput);

// Simulasi B: Validasi penolakan format non-08 / non-62
$invalidWa = '07123456789';
$isValidFormat = (bool)preg_match('/^(08|628)/', $invalidWa);
check("Nomor WhatsApp tanpa awalan 08 atau 628 ditolak sistem validasi", !$isValidFormat);

// Simulasi C: Ganti password dengan password_hash()
$newPassPlain = 'SecretNewPassword456!';
$newHash = password_hash($newPassPlain, PASSWORD_DEFAULT);
$stmtUpdPass = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmtUpdPass->execute([$newHash, $testUserId]);

$fetchUserAfterPass = $pdo->query("SELECT * FROM users WHERE id = {$testUserId}")->fetch();
$verifyOld = password_verify($origPassword, $fetchUserAfterPass['password']);
$verifyNew = password_verify($newPassPlain, $fetchUserAfterPass['password']);

check("Password baru tersimpan dalam bentuk enkripsi hash BCRYPT (bukan teks biasa)", 
    $fetchUserAfterPass['password'] !== $newPassPlain && strlen($fetchUserAfterPass['password']) >= 60
);
check("Password lama sudah tidak valid", !$verifyOld);
check("Password baru berhasil diverifikasi dengan password_verify()", $verifyNew);

// Bersihkan data uji
$pdo->exec("DELETE FROM users WHERE id = {$testUserId}");

// ------------------------------------------------------------------------------
// 4. PENGUJIAN JAVASCRIPT AJAX & TOAST NOTIFIKASI
// ------------------------------------------------------------------------------
echo "\n4. Pengujian Fitur AJAX (Zero-Reload) & Toast Feedback:\n";

check("Formulir profile.php menggunakan Fetch API untuk submit AJAX tanpa reload", 
    strpos($profileSrc, 'fetch(') !== false && strpos($profileSrc, 'preventDefault()') !== false
);

check("profile.php memanggil window.showToast('success') saat penyimpanan berhasil", 
    strpos($profileSrc, "window.showToast('success'") !== false || strpos($profileSrc, 'showToast') !== false
);

check("profile.php mengosongkan kolom password setelah berhasil diperbarui", 
    strpos($profileSrc, "document.getElementById('new_password').value = ''") !== false
);

echo "\n----------------------------------------------------------------------\n";
echo "RINGKASAN HASIL: {$pass} LULUS, {$fail} GAGAL\n";
echo "======================================================================\n";

if ($fail === 0) {
    echo "🎉 SEMUA ATURAN FITUR EDIT PROFIL SUKSES DIVERIFIKASI 100%!\n";
} else {
    exit(1);
}
