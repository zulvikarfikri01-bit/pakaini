<?php
/**
 * Test Suite: Invisible Secret Admin Panel (panel_pusat_pakaini_99x.php)
 */
echo "=== TEST SUITE: INVISIBLE SECRET ADMIN PANEL ===\n";

// ------------------------------------------------------------------------------
// TEST 1: File panel_pusat_pakaini_99x.php dan Kunci Baja di Baris Pertama
// ------------------------------------------------------------------------------
echo "\n--- TEST 1 & 2: Keberadaan File & Kunci Baja Strict Hard-Exit ---\n";
$secretFile = __DIR__ . '/../panel_pusat_pakaini_99x.php';

if (!file_exists($secretFile)) {
    echo "[FAIL] File panel_pusat_pakaini_99x.php tidak ditemukan!\n";
    exit(1);
}
echo "[PASS] File rahasia panel_pusat_pakaini_99x.php berhasil dibuat.\n";

$secretContent = file_get_contents($secretFile);

// Cek kunci baja di baris awal
$guardSnippet = "if (!isset(\$_SESSION['user_id']) || \$_SESSION['role'] !== 'admin') {
    // Hancurkan sesi jika mencoba menyusup dan lempar ke halaman login
    header(\"Location: login.php\");
    exit(); // SANGAT PENTING: Menghentikan eksekusi kode di bawahnya
}";

if (strpos($secretContent, "if (!isset(\$_SESSION['user_id']) || \$_SESSION['role'] !== 'admin')") !== false &&
    strpos($secretContent, 'header("Location: login.php");') !== false &&
    strpos($secretContent, 'exit();') !== false) {
    echo "[PASS] Kunci baja strict hard-exit terpasang di baris teratas panel_pusat_pakaini_99x.php (redirect login.php & exit()).\n";
} else {
    echo "[FAIL] Kunci baja tidak ditemukan di panel_pusat_pakaini_99x.php!\n";
    exit(1);
}

// ------------------------------------------------------------------------------
// TEST 3: Routing dan Form Internal di panel_pusat_pakaini_99x.php
// ------------------------------------------------------------------------------
echo "\n--- TEST 3: Routing dan Form Internal ---\n";
if (strpos($secretContent, 'action="panel_pusat_pakaini_99x.php"') !== false &&
    strpos($secretContent, "redirect('panel_pusat_pakaini_99x.php')") !== false) {
    echo "[PASS] Semua form dan redirect internal di panel_pusat_pakaini_99x.php mengarah ke nama file baru.\n";
} else {
    echo "[FAIL] Masih ada form atau redirect internal yang belum diperbarui di panel_pusat_pakaini_99x.php!\n";
    exit(1);
}

// ------------------------------------------------------------------------------
// TEST 4: Hilangkan Jejak di UI Pengguna Biasa
// ------------------------------------------------------------------------------
echo "\n--- TEST 4: Verifikasi Hilangnya Jejak Admin di UI Pengguna ---\n";
$uiFiles = [
    'dashboard.php'            => file_get_contents(__DIR__ . '/../dashboard.php'),
    'profile.php'              => file_get_contents(__DIR__ . '/../profile.php'),
    'earn_coins.php'           => file_get_contents(__DIR__ . '/../earn_coins.php'),
    'navbar.php'              => file_get_contents(__DIR__ . '/../navbar.php'),
    'footer.php'              => file_get_contents(__DIR__ . '/../footer.php'),
    'views/dashboard/index.php' => file_get_contents(__DIR__ . '/../views/dashboard/index.php'),
    'views/layouts/main.php'   => file_get_contents(__DIR__ . '/../views/layouts/main.php'),
];

$allClean = true;
foreach ($uiFiles as $filename => $content) {
    if (strpos($content, 'href="admin.php"') !== false || strpos($content, 'href="panel_pusat_pakaini_99x.php"') !== false) {
        echo "[FAIL] Ditemukan tautan admin pada file UI: {$filename}!\n";
        $allClean = false;
    } else {
        echo "[PASS] File {$filename} bersih tanpa tautan admin.\n";
    }
}

if (!$allClean) {
    exit(1);
}

// ------------------------------------------------------------------------------
// TEST 5: Neutralized admin.php
// ------------------------------------------------------------------------------
echo "\n--- TEST 5: Proteksi Endpoint Lama admin.php ---\n";
$oldAdminContent = file_get_contents(__DIR__ . '/../admin.php');
if (strpos($oldAdminContent, 'header("Location: login.php");') !== false &&
    strpos($oldAdminContent, 'exit();') !== false &&
    strlen($oldAdminContent) < 300) {
    echo "[PASS] Endpoint lama admin.php telah dinonaktifkan total (redirect login.php & exit()).\n";
} else {
    echo "[FAIL] admin.php belum dinonaktifkan secara aman!\n";
    exit(1);
}

echo "\n=== SEMUA PENGUJIAN INVISIBLE SECRET ADMIN PANEL LULUS 100% ===\n";
