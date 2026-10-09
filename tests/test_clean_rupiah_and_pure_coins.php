<?php
/**
 * Test Suite: Pembersihan Sisa Tampilan Konversi Rupiah (Rp) dan Janji 50rb
 * Memverifikasi Konsep Sistem Koin Murni (Tanpa Tarik Tunai)
 */

$testCount = 0;
$passedCount = 0;

function it(string $description, bool $condition) {
    global $testCount, $passedCount;
    $testCount++;
    if ($condition) {
        $passedCount++;
        echo "  ✓ PASS: {$description}\n";
    } else {
        echo "  ✗ FAIL: {$description}\n";
    }
}

echo "\n--- [1] Memeriksa navbar.php (Header / Navbar Koin Murni) ---\n";
$navbarSrc = file_get_contents(__DIR__ . '/../navbar.php');
it("navbar.php tidak mengandung teks 'Rp'", strpos($navbarSrc, 'Rp') === false);
it("navbar.php menampilkan format Koin murni", strpos($navbarSrc, 'user-coin-balance') !== false && strpos($navbarSrc, 'Koin') !== false);
it("navbar.php memiliki styling neon glow amber", strpos($navbarSrc, 'shadow-[0_0_12px_rgba(245,158,11,0.18)]') !== false || strpos($navbarSrc, 'border-amber-500/30') !== false);

echo "\n--- [2] Memeriksa dashboard.php (Dashboard Pengguna) ---\n";
$dashSrc = file_get_contents(__DIR__ . '/../dashboard.php');
it("dashboard.php tidak mengandung teks 'Rp '", strpos($dashSrc, 'Rp ') === false);
it("dashboard.php menampilkan Saldo Koin Murni", strpos($dashSrc, 'Saldo Koin Murni') !== false);
it("dashboard.php kartu koin memiliki efek neon glow", strpos($dashSrc, 'shadow-[0_0_20px_rgba(245,158,11,0.06)]') !== false || strpos($dashSrc, 'border-amber-500/20') !== false);
it("dashboard.php tidak mengandung teks 'Rp 30rb'", strpos($dashSrc, 'Rp 30rb') === false);

echo "\n--- [3] Memeriksa referral.php & views/referral/index.php ---\n";
$refSrc = file_get_contents(__DIR__ . '/../referral.php');
$expectedRefText = "Fitur mengundang teman berhadiah ribuan Koin Gratis sedang dalam tahap pengembangan. Nantikan pembaruannya!";
it("referral.php teks coming soon tepat sesuai instruksi", strpos($refSrc, $expectedRefText) !== false);
it("referral.php tidak mengandung '50rb'", stripos($refSrc, '50rb') === false);
it("referral.php tidak mengandung 'Rp 50.000'", strpos($refSrc, 'Rp 50.000') === false);
it("referral.php tidak mengandung 'Rp'", strpos($refSrc, 'Rp') === false);

$viewRefSrc = file_get_contents(__DIR__ . '/../views/referral/index.php');
it("views/referral/index.php teks coming soon tepat sesuai instruksi", strpos($viewRefSrc, $expectedRefText) !== false);
it("views/referral/index.php tidak mengandung 'Rp'", strpos($viewRefSrc, 'Rp') === false);

echo "\n--- [4] Memeriksa views/dashboard/index.php ---\n";
$viewDashSrc = file_get_contents(__DIR__ . '/../views/dashboard/index.php');
it("views/dashboard/index.php tidak mengandung '≈ Rp'", strpos($viewDashSrc, '≈ Rp') === false);
it("views/dashboard/index.php tidak mengandung '• 1.000 = Rp'", strpos($viewDashSrc, '• 1.000 = Rp') === false);
it("views/dashboard/index.php tidak mengandung 'bounty Rp 50.000'", strpos($viewDashSrc, 'bounty Rp 50.000') === false);
it("views/dashboard/index.php menampilkan Saldo Koin Murni Aktif", strpos($viewDashSrc, 'Saldo Koin Murni Aktif') !== false);

echo "\n--- [5] Memeriksa Landing Page index.php ---\n";
$indexSrc = file_get_contents(__DIR__ . '/../index.php');
it("index.php tidak menjanjikan 'Rp 50.000 Tunai'", strpos($indexSrc, 'Rp 50.000 Tunai') === false);
it("index.php memuat Program Referral Ribuan Koin Gratis", strpos($indexSrc, 'Program Referral Ribuan Koin Gratis') !== false);

echo "\n--- [6] Memeriksa views/tasks/index.php & create.php ---\n";
$tasksIndexSrc = file_get_contents(__DIR__ . '/../views/tasks/index.php');
it("views/tasks/index.php tidak menampilkan 'Rp ' untuk reward", strpos($tasksIndexSrc, 'Rp ') === false);

$tasksCreateSrc = file_get_contents(__DIR__ . '/../views/tasks/create.php');
it("views/tasks/create.php tidak menampilkan Kurs Rp", strpos($tasksCreateSrc, 'Kurs: 1.000 = Rp') === false);
it("views/tasks/create.php simulator menampilkan Koin Murni", strpos($tasksCreateSrc, 'Koin Murni') !== false);

echo "\n--- [7] Memeriksa views/layouts/main.php & auth.php ---\n";
$layoutMainSrc = file_get_contents(__DIR__ . '/../views/layouts/main.php');
it("views/layouts/main.php footer tidak mengandung '1.000 Koin = Rp'", strpos($layoutMainSrc, '1.000 Koin = Rp') === false);

$layoutAuthSrc = file_get_contents(__DIR__ . '/../views/layouts/auth.php');
it("views/layouts/auth.php footer tidak mengandung '1.000 Koin = Rp'", strpos($layoutAuthSrc, '1.000 Koin = Rp') === false);
it("views/layouts/auth.php footer memuat teks visi platform", strpos($layoutAuthSrc, 'Pakaini © 2026 • Social Media Exchange & Growth Platform') !== false);

echo "\n--- [8] Memeriksa Keamanan login.php & views/auth/login.php ---\n";
$viewLoginSrc = file_get_contents(__DIR__ . '/../views/auth/login.php');
it("views/auth/login.php tidak memuat Pintasan Masuk Uji Coba Cepat", stripos($viewLoginSrc, 'Pintasan Masuk Uji Coba Cepat') === false);
it("views/auth/login.php tidak memuat fungsi bypass fillLogin", strpos($viewLoginSrc, 'fillLogin') === false);

$rootLoginSrc = file_get_contents(__DIR__ . '/../login.php');
it("login.php tidak memuat Akun Uji Coba Demo", stripos($rootLoginSrc, 'Akun Uji Coba Demo') === false);
it("login.php footer memuat teks visi platform", strpos($rootLoginSrc, 'Pakaini © 2026 • Social Media Exchange & Growth Platform') !== false);

$rootRegisterSrc = file_get_contents(__DIR__ . '/../register.php');
it("register.php footer memuat teks visi platform", strpos($rootRegisterSrc, 'Pakaini © 2026 • Social Media Exchange & Growth Platform') !== false);

echo "\n============================================\n";
echo "Hasil Pengujian: {$passedCount} / {$testCount} tes lolos.\n";
if ($passedCount === $testCount) {
    echo "SEMUA TES BERHASIL! Tampilan Rp dan janji 50rb telah bersih 100%.\n";
    exit(0);
} else {
    echo "ADA TES GAGAL!\n";
    exit(1);
}
