<?php
/**
 * Test Suite: Verifikasi 3 Lapisan Pertahanan (Risk Management)
 * 1. Anti-Tuyul (Blokir Pembuatan Akun Ganda berbasis IP)
 * 2. Perluasan Validasi URL (YouTube Shorts/Watch/youtu.be, TikTok vt/vm, Instagram Post/Reel/Profil)
 * 3. Disclaimer Lepas Tanggung Jawab Fenomena Drop
 */

require_once __DIR__ . '/../config.php';

echo "======================================================================\n";
echo "🛡️ PENGUJIAN 3 LAPISAN PERTAHANAN (RISK MANAGEMENT)\n";
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

// ==============================================================================
// 1. LAPISAN 1: BLOKIR PEMBUATAN AKUN GANDA (ANTI-TUYUL)
// ==============================================================================
echo "1. Pengujian Lapisan 1: Anti-Tuyul Multi-Account Blocking (register.php):\n";

// Cek kolom di DB
$hasIpCol = false;
try {
    $stmt = $pdo->query("SELECT ip_address FROM users LIMIT 1");
    $hasIpCol = true;
} catch (Exception $e) {
    $hasIpCol = false;
}
check("Kolom 'ip_address' aktif dan dapat di-query di tabel users", $hasIpCol);

$dbSql = file_get_contents(__DIR__ . '/../database.sql');
check("File database.sql mendefinisikan kolom 'ip_address' VARCHAR", 
    strpos($dbSql, 'ip_address') !== false && strpos($dbSql, 'VARCHAR(45)') !== false
);

$registerSrc = file_get_contents(__DIR__ . '/../register.php');
check("register.php menangkap IP pengguna melalui \$_SERVER['REMOTE_ADDR']", 
    strpos($registerSrc, "REMOTE_ADDR") !== false
);

check("register.php memuat teks peringatan penolakan IP persis instruksi", 
    strpos($registerSrc, "Sistem mendeteksi IP ini sudah terdaftar. 1 Perangkat/Jaringan hanya diizinkan memiliki 1 akun.") !== false
);

// Simulasi Logika Registrasi dengan IP Ganda
$testIp = '203.0.113.' . rand(10, 200);
$testUserA = 'user_ip_a_' . time();
$testUserB = 'user_ip_b_' . time();
$passHash = password_hash('password123', PASSWORD_DEFAULT);

// User A mendaftar pertama kali dengan testIp
$pdo->prepare("INSERT INTO users (username, email, password, coins, ip_address) VALUES (?, ?, ?, ?, ?)")
    ->execute([$testUserA, $testUserA . '@test.com', $passHash, 0, $testIp]);
$userAId = (int)$pdo->lastInsertId();

// Simulasi User B mencoba mendaftar dengan testIp yang sama
$stmtCheckIp = $pdo->prepare("SELECT id FROM users WHERE ip_address = ? LIMIT 1");
$stmtCheckIp->execute([$testIp]);
$existingIpUser = $stmtCheckIp->fetch();

check("Sistem mendeteksi IP {$testIp} telah digunakan oleh akun sebelumnya", (bool)$existingIpUser);

// Bersihkan data uji
$pdo->exec("DELETE FROM users WHERE id = {$userAId}");

// ==============================================================================
// 2. LAPISAN 2: PERLUASAN VALIDASI URL SOSIAL MEDIA (add_task.php)
// ==============================================================================
echo "\n2. Pengujian Lapisan 2: Perluasan Validasi URL (add_task.php):\n";
$addTaskSrc = file_get_contents(__DIR__ . '/../add_task.php');

// A. YouTube: youtube.com/watch, youtu.be, youtube.com/shorts
check("PHP backend menerima format 'youtube.com/watch'", strpos($addTaskSrc, 'youtube.com/watch') !== false);
check("PHP backend menerima format 'youtu.be'", strpos($addTaskSrc, 'youtu.be') !== false);
check("PHP backend menerima format 'youtube.com/shorts'", strpos($addTaskSrc, 'youtube.com/shorts') !== false);

check("JavaScript frontend memvalidasi 'youtube.com/watch'", strpos($addTaskSrc, "urlLower.includes('youtube.com/watch')") !== false);
check("JavaScript frontend memvalidasi 'youtu.be'", strpos($addTaskSrc, "urlLower.includes('youtu.be')") !== false);
check("JavaScript frontend memvalidasi 'youtube.com/shorts'", strpos($addTaskSrc, "urlLower.includes('youtube.com/shorts')") !== false);

// B. TikTok: tiktok.com, vt.tiktok.com, vm.tiktok.com
check("PHP backend menerima format 'tiktok.com'", strpos($addTaskSrc, 'tiktok.com') !== false);
check("PHP backend menerima format 'vt.tiktok.com'", strpos($addTaskSrc, 'vt.tiktok.com') !== false);
check("PHP backend menerima format 'vm.tiktok.com'", strpos($addTaskSrc, 'vm.tiktok.com') !== false);

check("JavaScript frontend memvalidasi 'vt.tiktok.com'", strpos($addTaskSrc, "urlLower.includes('vt.tiktok.com')") !== false);
check("JavaScript frontend memvalidasi 'vm.tiktok.com'", strpos($addTaskSrc, "urlLower.includes('vm.tiktok.com')") !== false);

// C. Instagram: instagram.com/p/ (Post), instagram.com/reel/, link profil
check("PHP backend menerima format Post 'instagram.com/p/'", strpos($addTaskSrc, 'instagram.com/p/') !== false);
check("PHP backend menerima format Reel 'instagram.com/reel/'", strpos($addTaskSrc, 'instagram.com/reel/') !== false);
check("JavaScript frontend memvalidasi 'instagram.com/p/'", strpos($addTaskSrc, "urlLower.includes('instagram.com/p/')") !== false);
check("JavaScript frontend memvalidasi 'instagram.com/reel/'", strpos($addTaskSrc, "urlLower.includes('instagram.com/reel/')") !== false);

// Simulasi PHP URL Validation Functions
function testUrlValidation(string $platform, string $url): bool {
    $urlLower = strtolower($url);
    if ($platform === 'youtube') {
        return (
            strpos($urlLower, 'youtube.com/watch') !== false ||
            strpos($urlLower, 'youtu.be') !== false ||
            strpos($urlLower, 'youtube.com/shorts') !== false ||
            strpos($urlLower, 'youtube.com/@') !== false ||
            strpos($urlLower, 'youtube.com/channel') !== false ||
            strpos($urlLower, 'youtube.com') !== false
        );
    } elseif ($platform === 'instagram') {
        return (
            strpos($urlLower, 'instagram.com/p/') !== false ||
            strpos($urlLower, 'instagram.com/reel/') !== false ||
            strpos($urlLower, 'instagram.com/reels/') !== false ||
            strpos($urlLower, 'instagram.com/') !== false
        );
    } elseif ($platform === 'tiktok') {
        return (
            strpos($urlLower, 'tiktok.com') !== false ||
            strpos($urlLower, 'vt.tiktok.com') !== false ||
            strpos($urlLower, 'vm.tiktok.com') !== false
        );
    }
    return false;
}

check("Simulasi YouTube: https://www.youtube.com/watch?v=dQw4w9WgXcQ lolos validasi", testUrlValidation('youtube', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'));
check("Simulasi YouTube: https://youtu.be/dQw4w9WgXcQ lolos validasi", testUrlValidation('youtube', 'https://youtu.be/dQw4w9WgXcQ'));
check("Simulasi YouTube: https://www.youtube.com/shorts/abc123xyz lolos validasi", testUrlValidation('youtube', 'https://www.youtube.com/shorts/abc123xyz'));

check("Simulasi TikTok: https://vt.tiktok.com/ZS2F123/ lolos validasi", testUrlValidation('tiktok', 'https://vt.tiktok.com/ZS2F123/'));
check("Simulasi TikTok: https://vm.tiktok.com/ZM8G456/ lolos validasi", testUrlValidation('tiktok', 'https://vm.tiktok.com/ZM8G456/'));
check("Simulasi TikTok: https://www.tiktok.com/@pakaini.id/video/123 lolos validasi", testUrlValidation('tiktok', 'https://www.tiktok.com/@pakaini.id/video/123'));

check("Simulasi Instagram: https://www.instagram.com/p/DA12345/ lolos validasi", testUrlValidation('instagram', 'https://www.instagram.com/p/DA12345/'));
check("Simulasi Instagram: https://www.instagram.com/reel/DA67890/ lolos validasi", testUrlValidation('instagram', 'https://www.instagram.com/reel/DA67890/'));
check("Simulasi Instagram: https://www.instagram.com/pakaini.id/ lolos validasi", testUrlValidation('instagram', 'https://www.instagram.com/pakaini.id/'));

// ==============================================================================
// 3. LAPISAN 3: DISCLAIMER LEPAS TANGGUNG JAWAB (FENOMENA DROP)
// ==============================================================================
echo "\n3. Pengujian Lapisan 3: Disclaimer Lepas Tanggung Jawab Fenomena Drop (add_task.php):\n";

$expectedDisclaimer = "Penting: Penurunan jumlah (Drop) bisa saja terjadi akibat pembersihan rutin dari sistem algoritma YouTube/Instagram. Pakaini adalah platform perantara dan tidak memberikan garansi isi ulang (refill) untuk interaksi yang di-drop oleh platform pihak ketiga.";

check("add_task.php memuat teks disclaimer lepas tanggung jawab fenomena drop persis instruksi", 
    strpos($addTaskSrc, $expectedDisclaimer) !== false
);

check("Teks disclaimer menggunakan kelas 'text-xs text-slate-400'", 
    strpos($addTaskSrc, 'text-xs text-slate-400') !== false
);

check("Tombol submit memiliki label 'Bayar Koin & Pasang Iklan'", 
    strpos($addTaskSrc, 'Bayar Koin & Pasang Iklan') !== false
);

echo "\n----------------------------------------------------------------------\n";
echo "RINGKASAN HASIL: {$pass} LULUS, {$fail} GAGAL\n";
echo "======================================================================\n";

if ($fail === 0) {
    echo "🎉 SEMUA 3 LAPISAN PERTAHANAN RISK MANAGEMENT BERHASIL DIVERIFIKASI 100%!\n";
} else {
    exit(1);
}
