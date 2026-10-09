<?php
/**
 * Test Suite: Verifikasi Perombakan UI Mobile-First SPA, Bottom Nav, dan Zero-Reload AJAX
 */
require_once __DIR__ . '/../config.php';

echo "======================================================================\n";
echo "📱 PENGUJIAN PEROMBAKAN UI MOBILE-FIRST SPA & ZERO-RELOAD AJAX\n";
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

// 1. Verifikasi Bottom Navigation Bar di footer.php
echo "1. Pengujian Bottom Navigation Bar (footer.php):\n";
$footerSrc = file_get_contents(__DIR__ . '/../footer.php');

check("Bottom Navigation Bar memiliki tab 'Home'", strpos($footerSrc, 'Home') !== false && strpos($footerSrc, 'dashboard.php') !== false);
check("Bottom Navigation Bar memiliki tab 'Cari Koin'", strpos($footerSrc, 'Cari Koin') !== false && strpos($footerSrc, 'earn_coins.php') !== false);
check("Bottom Navigation Bar memiliki tab 'Pasang Iklan'", strpos($footerSrc, 'Pasang Iklan') !== false && strpos($footerSrc, 'add_task.php') !== false);
check("Bottom Navigation Bar memiliki tab 'Profil'", strpos($footerSrc, 'Profil') !== false && strpos($footerSrc, 'referral.php') !== false);
check("Bottom Navigation Bar menggunakan ikon yang tepat (fa-house, fa-coins, fa-bullhorn, fa-circle-user)", 
    strpos($footerSrc, 'fa-house') !== false && 
    strpos($footerSrc, 'fa-coins') !== false && 
    strpos($footerSrc, 'fa-bullhorn') !== false && 
    strpos($footerSrc, 'fa-circle-user') !== false
);
check("Tersedia helper global window.showToast()", strpos($footerSrc, 'window.showToast') !== false);

// 2. Verifikasi Header & Navbar di navbar.php
echo "\n2. Pengujian Navbar & Real-Time Balance Counter (navbar.php):\n";
$navbarSrc = file_get_contents(__DIR__ . '/../navbar.php');

check("Navbar memiliki elemen saldo koin dengan ID 'navbarCoinsAmount'", strpos($navbarSrc, 'id="navbarCoinsAmount"') !== false);
check("Navbar memiliki kelas penanda 'user-coin-balance'", strpos($navbarSrc, 'user-coin-balance') !== false);
check("Navbar memiliki container pill koin 'navbarCoinsPill'", strpos($navbarSrc, 'id="navbarCoinsPill"') !== false);
check("Tombol register menggunakan gradasi logo bg-gradient-to-r from-blue-500 to-purple-600", strpos($navbarSrc, 'from-blue-500 to-purple-600') !== false);

// 3. Verifikasi earn_coins.php (Tombol Gradasi & Zero-Reload AJAX)
echo "\n3. Pengujian earn_coins.php (Tombol Gradasi & Zero-Reload AJAX):\n";
$earnSrc = file_get_contents(__DIR__ . '/../earn_coins.php');

check("Tombol Kerjakan tugas menggunakan gradasi from-blue-500 to-purple-600", strpos($earnSrc, 'bg-gradient-to-r from-blue-500 to-purple-600') !== false);
check("Tombol Klaim Koin menggunakan gradasi from-blue-500 to-purple-600", strpos($earnSrc, 'claimRewardBtn') !== false && strpos($earnSrc, 'from-blue-500 to-purple-600') !== false);
check("submitClaim TIDAK menggunakan window.location.reload()", strpos($earnSrc, 'window.location.reload()') === false);
check("submitClaim menghapus kartu tugas dengan animasi transisi & fade-out", strpos($earnSrc, 'cardEl.style.transition') !== false && strpos($earnSrc, 'cardEl.remove()') !== false);
check("submitClaim memperbarui saldo koin navbar secara real-time", strpos($earnSrc, 'navbarCoinsAmount') !== false || strpos($earnSrc, 'user-coin-balance') !== false);
check("submitClaim memicu animasi kilau koin pada navbarCoinsPill", strpos($earnSrc, 'navbarCoinsPill') !== false);
check("submitClaim menampilkan notifikasi elegan showToast", strpos($earnSrc, 'showToast') !== false);

// 4. Verifikasi dashboard.php (Mobile-First Layout & Gradasi Logo)
echo "\n4. Pengujian dashboard.php (Mobile-First Layout & Gradasi Logo):\n";
$dashSrc = file_get_contents(__DIR__ . '/../dashboard.php');

check("dashboard.php menggunakan kartu melengkung modern rounded-3xl", strpos($dashSrc, 'rounded-3xl') !== false);
check("dashboard.php memiliki tombol utama dengan gradasi from-blue-500 to-purple-600", strpos($dashSrc, 'from-blue-500 to-purple-600') !== false);
check("dashboard.php menyertakan kelas user-coin-balance pada saldo utama", strpos($dashSrc, 'user-coin-balance') !== false);

// 5. Verifikasi add_task.php (Gradasi Logo & Rounded Controls)
echo "\n5. Pengujian add_task.php (Gradasi Logo & Rounded Controls):\n";
$addSrc = file_get_contents(__DIR__ . '/../add_task.php');

check("add_task.php menggunakan kontrol input melengkung rounded-2xl", strpos($addSrc, 'rounded-2xl') !== false);
check("add_task.php tombol submit Terbitkan Kampanye menggunakan gradasi from-blue-500 to-purple-600", strpos($addSrc, 'from-blue-500 to-purple-600') !== false);
check("add_task.php kartu formulir menggunakan rounded-3xl", strpos($addSrc, 'rounded-3xl') !== false);

echo "\n----------------------------------------------------------------------\n";
echo "RINGKASAN HASIL: {$pass} LULUS, {$fail} GAGAL\n";
echo "======================================================================\n";
if ($fail === 0) {
    echo "🎉 SELURUH PERSYARATAN UI MOBILE-FIRST SPA & ZERO-RELOAD BERHASIL DIVERIFIKASI 100%!\n";
} else {
    echo "⚠️ ADA PENGUJIAN YANG GAGAL. SILAKAN PERIKSA DETAIL DI ATAS.\n";
    exit(1);
}
