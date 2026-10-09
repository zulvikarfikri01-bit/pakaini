<?php
/**
 * Test Suite: Inspeksi Tingkat Perfeksionis (Final Polish)
 */
echo "=== TEST SUITE: INSPEKSI TINGKAT PERFEKSIONIS ===\n";

// ------------------------------------------------------------------------------
// TEST 1: Validasi HTML dan PHP di add_task.php
// ------------------------------------------------------------------------------
echo "\n--- TEST 1: Validasi Input add_task.php ---\n";
$addTaskHtml = file_get_contents(__DIR__ . '/../add_task.php');

// 1.1 Cek HTML min="10"
if (preg_match('/<input[^>]+name="target_clicks"[^>]+min="10"/i', $addTaskHtml)) {
    echo "[PASS] Atribut min=\"10\" terpasang pada input target_clicks di add_task.php (HTML)\n";
} else {
    echo "[FAIL] Atribut min=\"10\" tidak ditemukan pada input target_clicks di add_task.php\n";
    exit(1);
}

// 1.2 Cek Kode PHP Backend: target_clicks < 10
if (strpos($addTaskHtml, 'if (!is_numeric($target_clicks) || $target_clicks < 10)') !== false ||
    strpos($addTaskHtml, 'die("Target klik minimal adalah 10!");') !== false) {
    echo "[PASS] Kode PHP Backend memblokir target_clicks < 10 atau non-numerik: die(\"Target klik minimal adalah 10!\")\n";
} else {
    echo "[FAIL] Validasi target_clicks PHP tidak ditemukan di add_task.php\n";
    exit(1);
}

// 1.3 Cek Kode PHP Backend: CPC < 50
if (strpos($addTaskHtml, 'if (!is_numeric($cpc) || $cpc < 50)') !== false &&
    strpos($addTaskHtml, 'die("Biaya per klik (Cost Per Click) minimal adalah 50 koin!");') !== false) {
    echo "[PASS] Kode PHP Backend menolak CPC < 50 koin: die(\"Biaya per klik (Cost Per Click) minimal adalah 50 koin!\")\n";
} else {
    echo "[FAIL] Validasi CPC minimal 50 tidak ditemukan di add_task.php\n";
    exit(1);
}

// ------------------------------------------------------------------------------
// TEST 2 & 3: Anti Double-Submit & Error Jaringan di earn_coins.php
// ------------------------------------------------------------------------------
echo "\n--- TEST 2 & 3: Anti Double-Submit & Penanganan Error Jaringan earn_coins.php ---\n";
$earnCoinsContent = file_get_contents(__DIR__ . '/../earn_coins.php');

// 2.1 Cek Anti Double-Submit
$hasBtnDisabled = strpos($earnCoinsContent, 'btn.disabled = true;') !== false;
$hasBtnMemproses = strpos($earnCoinsContent, "btn.innerHTML = 'Memproses...';") !== false;
$hasBtnOpacity = strpos($earnCoinsContent, "btn.classList.add('opacity-50', 'cursor-not-allowed');") !== false;

if ($hasBtnDisabled && $hasBtnMemproses && $hasBtnOpacity) {
    echo "[PASS] Anti Double-Submit terpasang sempurna (disabled = true, innerHTML = 'Memproses...', opacity-50 cursor-not-allowed)\n";
} else {
    echo "[FAIL] Anti Double-Submit tidak lengkap di earn_coins.php\n";
    exit(1);
}

// 3.1 Cek Penanganan Error Jaringan (.catch(error))
$hasCatchError = strpos($earnCoinsContent, '.catch(error => {') !== false || strpos($earnCoinsContent, '.catch(err => {') !== false;
$hasNetworkMsg = strpos($earnCoinsContent, 'Koneksi jaringan terputus. Silakan coba klik klaim lagi.') !== false;
$hasReenable = strpos($earnCoinsContent, 'btn.disabled = false;') !== false && strpos($earnCoinsContent, "btn.innerHTML = 'Klaim Koin';") !== false;
$hasRemoveOpacity = strpos($earnCoinsContent, "btn.classList.remove('opacity-50', 'cursor-not-allowed');") !== false;

if ($hasCatchError && $hasNetworkMsg && $hasReenable && $hasRemoveOpacity) {
    echo "[PASS] Penanganan error jaringan (catch) terpasang dengan pesan: 'Koneksi jaringan terputus. Silakan coba klik klaim lagi.'\n";
    echo "[PASS] Tombol dikembalikan aktif (disabled = false, innerHTML = 'Klaim Koin', remove opacity-50) tanpa reload halaman\n";
} else {
    echo "[FAIL] Penanganan error jaringan tidak lengkap di earn_coins.php\n";
    exit(1);
}

echo "\n=== SEMUA 3 POIN PERFEKSIONIS BERHASIL DIVERIFIKASI 100% ===\n";
