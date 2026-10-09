<?php
/**
 * PAKAINI - 21st.dev / MagicUI Dark Mode Aesthetic Verification Test Suite
 * Memverifikasi 5 Pedoman Desain:
 * 1. Background Gelap Pekat Eksklusif (#0B0F19 / bg-slate-950) & Tipografi Modern (Inter/Outfit).
 * 2. Glassmorphism Komponen (bg-white/5 backdrop-blur border-white/10 rounded-2xl / rounded-3xl).
 * 3. Aksen Neon Gradasi Brand (from-blue-500 to-purple-600 & hover:shadow-[0_0_20px_rgba(139,92,246,0.4)]).
 * 4. Mobile-First Bottom Navigation (fixed bottom-0 w-full backdrop-blur-md bg-slate-950/80 border-t border-white/10).
 * 5. Micro-Interactions Zero-Reload (Fetch API, card fade out, real-time balance update).
 */

$testPassed = 0;
$testFailed = 0;

function assertCheck($condition, $message) {
    global $testPassed, $testFailed;
    if ($condition) {
        echo "  ✅ PASS: " . $message . "\n";
        $testPassed++;
    } else {
        echo "  ❌ FAIL: " . $message . "\n";
        $testFailed++;
    }
}

echo "======================================================================\n";
echo "💎 PENGUJIAN DESAIN ELEGAN 21ST.DEV / MAGICUI DARK MODE & SPA\n";
echo "======================================================================\n\n";

// Baca file-file utama
$indexContent     = file_get_contents(__DIR__ . '/../index.php');
$dashboardContent = file_get_contents(__DIR__ . '/../dashboard.php');
$earnContent      = file_get_contents(__DIR__ . '/../earn_coins.php');
$addTaskContent   = file_get_contents(__DIR__ . '/../add_task.php');
$navbarContent    = file_get_contents(__DIR__ . '/../navbar.php');
$footerContent    = file_get_contents(__DIR__ . '/../footer.php');

// 1. Latar Belakang & Tipografi Premium
echo "1. Pengujian Latar Belakang Gelap Pekat & Tipografi Premium:\n";
assertCheck(strpos($indexContent, '#0B0F19') !== false || strpos($indexContent, 'bg-[#0B0F19]') !== false || strpos($indexContent, 'bg-slate-950') !== false, "index.php menggunakan background gelap eksklusif (#0B0F19 / bg-slate-950)");
assertCheck(strpos($navbarContent, '#0B0F19') !== false, "navbar.php menggunakan background gelap pekat eksklusif #0B0F19");
assertCheck(strpos($navbarContent, 'Inter') !== false && strpos($navbarContent, 'Outfit') !== false, "navbar.php menyertakan font modern Inter dan Outfit");
assertCheck(strpos($indexContent, 'Inter') !== false && strpos($indexContent, 'Outfit') !== false, "index.php menyertakan font modern Inter dan Outfit");
assertCheck(strpos($indexContent, 'text-slate-400') !== false || strpos($indexContent, 'text-slate-200') !== false, "index.php menerapkan palet teks terang text-slate-200 / text-slate-400");

// 2. Glassmorphism pada Komponen
echo "\n2. Pengujian Efek Glassmorphism (Kaca Buram & Border Halus):\n";
assertCheck(strpos($navbarContent, 'backdrop-filter: blur') !== false || strpos($navbarContent, 'backdrop-blur') !== false, "navbar.php memiliki rule glass-card dengan backdrop-filter blur");
assertCheck(strpos($indexContent, 'backdrop-filter: blur') !== false || strpos($indexContent, 'backdrop-blur') !== false, "index.php memiliki rule glass-card dengan backdrop-filter blur");
assertCheck(strpos($dashboardContent, 'glass-card') !== false && (strpos($dashboardContent, 'rounded-3xl') !== false || strpos($dashboardContent, 'rounded-2xl') !== false), "dashboard.php mengaplikasikan glass-card dengan sudut modern rounded-2xl / rounded-3xl");
assertCheck(strpos($earnContent, 'glass-card') !== false, "earn_coins.php mengaplikasikan glass-card transparan pada kartu tugas");
assertCheck(strpos($addTaskContent, 'glass-card') !== false, "add_task.php mengaplikasikan glass-card transparan pada form & daftar tugas");

// 3. Aksen Neon Gradasi (Brand Identity & Glow Effect)
echo "\n3. Pengujian Aksen Neon Gradasi & Glow Effect:\n";
assertCheck(strpos($indexContent, 'from-blue-500 to-purple-600') !== false, "index.php tombol CTA menggunakan gradasi resmi from-blue-500 to-purple-600");
assertCheck(strpos($indexContent, 'hover:shadow-[0_0_20px_rgba(139,92,246,0.4)]') !== false || strpos($indexContent, 'hover:shadow-[0_0_25px_rgba(139,92,246,0.5)]') !== false, "index.php tombol CTA memiliki efek glow menyala hover:shadow");
assertCheck(strpos($dashboardContent, 'from-blue-500 to-purple-600') !== false, "dashboard.php tombol utama menggunakan gradasi from-blue-500 to-purple-600");
assertCheck(strpos($dashboardContent, 'hover:shadow-[0_0_20px_rgba(139,92,246,0.4)]') !== false, "dashboard.php tombol utama memiliki efek glow neon hover:shadow-[0_0_20px_rgba(139,92,246,0.4)]");
assertCheck(strpos($earnContent, 'from-blue-500 to-purple-600') !== false, "earn_coins.php tombol kerjakan/klaim menggunakan gradasi from-blue-500 to-purple-600");
assertCheck(strpos($earnContent, 'hover:shadow-[0_0_20px_rgba(139,92,246,0.4)]') !== false, "earn_coins.php tombol memiliki efek glow neon hover:shadow-[0_0_20px_rgba(139,92,246,0.4)]");
assertCheck(strpos($addTaskContent, 'from-blue-500 to-purple-600') !== false, "add_task.php tombol terbitkan kampanye menggunakan gradasi from-blue-500 to-purple-600");
assertCheck(strpos($addTaskContent, 'hover:shadow-[0_0_20px_rgba(139,92,246,0.4)]') !== false, "add_task.php tombol submit memiliki efek glow neon hover:shadow-[0_0_20px_rgba(139,92,246,0.4)]");

// 4. Mobile-First Bottom Navigation
echo "\n4. Pengujian Mobile-First Bottom Navigation Bar:\n";
assertCheck(strpos($footerContent, 'fixed bottom-0 w-full backdrop-blur-md bg-slate-950/80 border-t border-white/10') !== false, "footer.php memiliki dock navigasi bawah dengan glassmorphism persis pedoman");
assertCheck(strpos($indexContent, 'fixed bottom-0 w-full backdrop-blur-md bg-slate-950/80 border-t border-white/10') !== false, "index.php memiliki dock navigasi bawah dengan glassmorphism persis pedoman");
assertCheck(strpos($footerContent, 'drop-shadow-[0_0_8px_rgba(168,85,247,0.8)]') !== false || strpos($footerContent, 'shadow-[0_0_15px_rgba(139,92,246,0.3)]') !== false, "footer.php bottom nav memiliki ikon yang menyala/glowing saat menu aktif");

// 5. Micro-Interactions (Zero Reload)
echo "\n5. Pengujian Micro-Interactions (Zero Reload AJAX & Real-Time Sync):\n";
assertCheck(strpos($earnContent, 'fetch(\'earn_coins.php\'') !== false, "earn_coins.php submitClaim menggunakan Fetch API");
assertCheck(strpos($earnContent, 'window.location.reload()') === false, "earn_coins.php TIDAK melakukan reload halaman sama sekali");
assertCheck(strpos($earnContent, 'user-coin-balance') !== false, "earn_coins.php menyinkronkan saldo koin ke elemen user-coin-balance real-time");
assertCheck(strpos($earnContent, 'cardEl.style.opacity = \'0\'') !== false || strpos($earnContent, 'cardEl.style.transform') !== false, "earn_coins.php memberikan animasi fade-out saat tugas selesai");
assertCheck(strpos($earnContent, 'showToast') !== false, "earn_coins.php memunculkan notifikasi in-app toast halus");

echo "\n----------------------------------------------------------------------\n";
echo "RINGKASAN HASIL: {$testPassed} LULUS, {$testFailed} GAGAL\n";
echo "======================================================================\n";

if ($testFailed === 0) {
    echo "🎉 SEMUA PEDOMAN DESAIN 21ST.DEV & MAGICUI BERHASIL DIVERIFIKASI 100%!\n";
    exit(0);
} else {
    echo "⚠️ ADA PENGUJIAN YANG GAGAL. SILAKAN PERIKSA DETAIL DI ATAS.\n";
    exit(1);
}
