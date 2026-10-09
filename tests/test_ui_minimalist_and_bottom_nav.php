<?php
/**
 * Test Suite: Verifikasi Desain Premium Minimalis & Menu Komunitas di Navigasi Bawah
 */
require_once __DIR__ . '/../config.php';

echo "======================================================================\n";
echo "💎 PENGUJIAN DESAIN PREMIUM MINIMALIS & 5 MENU BOTTOM NAV MOBILE\n";
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

// 1. Footer Bottom Navigation Bar 5 Menu
echo "1. Pengujian 5 Menu Bottom Navigation Bar Mobile (footer.php):\n";
$footerSrc = file_get_contents(__DIR__ . '/../footer.php');

check("Layout menggunakan grid-cols-5 untuk pembagian rata 5 menu", strpos($footerSrc, 'grid-cols-5') !== false);

// Urutan: Home, Cari Koin, Tambah Iklan, Komunitas, Profil
$posHome = strpos($footerSrc, 'Home');
$posCariKoin = strpos($footerSrc, 'Cari Koin');
$posTambahIklan = strpos($footerSrc, 'Tambah Iklan');
$posKomunitas = strpos($footerSrc, 'Komunitas');
$posProfil = strpos($footerSrc, 'Profil');

check("Urutan menu #1 adalah 'Home'", $posHome !== false);
check("Urutan menu #2 adalah 'Cari Koin'", $posCariKoin !== false && $posCariKoin > $posHome);
check("Urutan menu #3 adalah 'Tambah Iklan'", $posTambahIklan !== false && $posTambahIklan > $posCariKoin);
check("Urutan menu #4 adalah 'Komunitas'", $posKomunitas !== false && $posKomunitas > $posTambahIklan);
check("Urutan menu #5 adalah 'Profil'", $posProfil !== false && $posProfil > $posKomunitas);

check("Komunitas terhubung ke 'community.php'", strpos($footerSrc, 'href="community.php"') !== false);
check("Ukuran teks responsif layar kecil (text-[9px] truncate)", strpos($footerSrc, 'text-[9px]') !== false);
check("Ikon navigasi aktif menggunakan aksen logo (purple-400 / blue-400)", 
    strpos($footerSrc, 'text-purple-400') !== false || strpos($footerSrc, 'text-blue-400') !== false
);

// 2. Skema Monokrom Gelap Elegan & Hapus Kesan Warna-Warni
echo "\n2. Pengujian Skema Monokrom Gelap Elegan (navbar.php & dashboard.php):\n";
$navbarSrc = file_get_contents(__DIR__ . '/../navbar.php');
$dashSrc = file_get_contents(__DIR__ . '/../dashboard.php');

check("Background utama menggunakan warna gelap pekat #0B0F19", strpos($navbarSrc, '#0B0F19') !== false);
check("Kartu glass-card menggunakan latar gelap abu-abu pekat #111111", strpos($navbarSrc, 'background: #111111') !== false);
check("Border tipis halus rgba(255, 255, 255, 0.08)", strpos($navbarSrc, 'rgba(255, 255, 255, 0.08)') !== false);
check("Dashboard kartu metrik menggunakan bg-[#111111]", strpos($dashSrc, 'bg-[#111111]') !== false);
check("Dashboard tombol utama menggunakan gradasi logo from-blue-500 to-purple-600", strpos($dashSrc, 'from-blue-500 to-purple-600') !== false);

// 3. Tombol Aksi Utama di earn_coins.php, add_task.php, topup.php
echo "\n3. Pengujian Aksen Gradasi Logo Pada Tombol Aksi Utama:\n";
$earnSrc = file_get_contents(__DIR__ . '/../earn_coins.php');
$addTaskSrc = file_get_contents(__DIR__ . '/../add_task.php');
$topupSrc = file_get_contents(__DIR__ . '/../topup.php');

check("earn_coins.php tombol Kerjakan menggunakan gradasi from-blue-500 to-purple-600", strpos($earnSrc, 'bg-gradient-to-r from-blue-500 to-purple-600') !== false);
check("add_task.php tombol Terbitkan Kampanye menggunakan gradasi from-blue-500 to-purple-600", strpos($addTaskSrc, 'bg-gradient-to-r from-blue-500 to-purple-600') !== false);
check("topup.php tombol Beli Sekarang menggunakan gradasi from-blue-500 to-purple-600", strpos($topupSrc, 'bg-gradient-to-r from-blue-500 to-purple-600') !== false);

echo "\n----------------------------------------------------------------------\n";
echo "RINGKASAN HASIL: {$pass} LULUS, {$fail} GAGAL\n";
echo "======================================================================\n";
if ($fail === 0) {
    echo "🎉 SEMUA PERSYARATAN DESAIN MINIMALIS & 5 MENU BOTTOM NAV SUKSES 100%!\n";
} else {
    exit(1);
}
