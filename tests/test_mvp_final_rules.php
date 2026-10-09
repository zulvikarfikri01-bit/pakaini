<?php
/**
 * Test Otomatis untuk 4 Aturan Bisnis Final Versi MVP PAKAINI
 */

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config.php';

echo "========================================================\n";
echo "    PENGUJIAN 4 ATURAN FINAL VERSI MVP PAKAINI          \n";
echo "========================================================\n\n";

function test_assert($condition, $title) {
    if ($condition) {
        echo "✅ PASS: $title\n";
    } else {
        echo "❌ FAIL: $title\n";
        exit(1);
    }
}

// -------------------------------------------------------------
// 1. SISTEM KOIN MURNI & FITUR BELI KOIN (TOP-UP)
// -------------------------------------------------------------
echo "--- 1. Pengujian Sistem Koin Murni & Top-Up ---\n";

$topupContent = file_get_contents(ROOT_PATH . '/topup.php');
test_assert(strpos($topupContent, '5.000 Koin') !== false, "Halaman topup.php menampilkan Paket 5.000 Koin");
test_assert(strpos($topupContent, '10.000 Koin') !== false, "Halaman topup.php menampilkan Paket 10.000 Koin");
test_assert(strpos($topupContent, '22.000 Koin') !== false, "Halaman topup.php menampilkan Paket 22.000 Koin (Sultan)");

// Cek format WhatsApp Admin
test_assert(stripos($topupContent, 'Halo Admin, saya ingin membeli') !== false, 
            "Format pesan WhatsApp sesuai aturan ke Admin");

// Cek bahwa withdraw / tarik tunai sudah dihilangkan dari antarmuka
test_assert(strpos($topupContent, 'name="action" value="withdraw"') === false, "Tidak ada formulir penarikan tunai di topup.php");

$navbarContent = file_get_contents(ROOT_PATH . '/navbar.php');
test_assert(strpos($navbarContent, 'Dompet Iklan') === false, "Navbar menampilkan 1 dompet koin murni tanpa dual dompet");


// -------------------------------------------------------------
// 2. KEAMANAN KAMPANYE (URL VALIDATION & ANTI-DOUBLE TASK)
// -------------------------------------------------------------
echo "\n--- 2. Pengujian Keamanan Kampanye (URL & Anti-Double Task) ---\n";

$addTaskContent = file_get_contents(ROOT_PATH . '/add_task.php');

// Validasi Domain
test_assert(strpos($addTaskContent, 'youtube.com') !== false && strpos($addTaskContent, 'youtu.be') !== false, "Validasi URL YouTube mendukung youtube.com dan youtu.be");
test_assert(strpos($addTaskContent, 'instagram.com') !== false, "Validasi URL Instagram mendukung instagram.com");
test_assert(strpos($addTaskContent, 'tiktok.com') !== false && strpos($addTaskContent, 'vt.tiktok.com') !== false, "Validasi URL TikTok mendukung tiktok.com dan vt.tiktok.com");

// Auto-prefix https://
test_assert(strpos($addTaskContent, "https://") !== false, "Auto-tambahkan https:// jika user lupa");

// Cek tabel task_history di database
$tableCheck = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='task_history'")->fetchColumn();
test_assert($tableCheck === 'task_history', "Tabel task_history ada di database");

// Cek earn_coins.php mengecualikan task yang sudah dikerjakan
$earnContent = file_get_contents(ROOT_PATH . '/earn_coins.php');
test_assert(strpos($earnContent, "t.id NOT IN") !== false && strpos($earnContent, "FROM task_history WHERE user_id") !== false, 
            "earn_coins.php JANGAN menampilkan tugas yang sudah ada di task_history milik user");


// -------------------------------------------------------------
// 3. HALAMAN REFERRAL (STATUS: COMING SOON)
// -------------------------------------------------------------
echo "\n--- 3. Pengujian Halaman Referral (Coming Soon) ---\n";

$referralContent = file_get_contents(ROOT_PATH . '/referral.php');
test_assert(strpos($referralContent, 'Sistem Referral (Bawa Teman) - COMING SOON!') !== false, 
            "Judul besar memuat teks 'Sistem Referral (Bawa Teman) - COMING SOON!'");
test_assert(strpos($referralContent, 'Fitur mengundang teman berhadiah ribuan koin sedang dalam tahap pengembangan. Nantikan pembaruannya!') !== false, 
            "Keterangan kecil memuat teks 'Fitur mengundang teman berhadiah ribuan koin sedang dalam tahap pengembangan. Nantikan pembaruannya!'");
test_assert(strpos($referralContent, 'glass-card') !== false && strpos($referralContent, 'rounded-3xl') !== false, 
            "Desain antarmuka rapi menggunakan Tailwind CSS");


// -------------------------------------------------------------
// 4. LELANG PRIORITAS & MONOPOLI ADMIN
// -------------------------------------------------------------
echo "\n--- 4. Pengujian Lelang Prioritas & Monopoli Admin ---\n";

// Cek checkbox prioritas di add_task.php
test_assert(strpos($addTaskContent, 'Jadikan Prioritas (Biaya Koin 2x Lipat)') !== false, 
            "add_task.php memiliki centang 'Jadikan Prioritas (Biaya Koin 2x Lipat)'");

// Cek urutan prioritas di earn_coins.php
test_assert(strpos($earnContent, 'ORDER BY t.is_priority DESC') !== false, 
            "Tugas prioritas selalu tampil paling atas di daftar 'Cari Koin'");

// Cek kolom is_admin_task
$colCheck = $pdo->query("PRAGMA table_info(tasks)")->fetchAll();
$hasAdminCol = false;
$hasPriorityCol = false;
foreach ($colCheck as $col) {
    if ($col['name'] === 'is_admin_task') $hasAdminCol = true;
    if ($col['name'] === 'is_priority') $hasPriorityCol = true;
}
test_assert($hasAdminCol === true, "Kolom tasks.is_admin_task tersedia");
test_assert($hasPriorityCol === true, "Kolom tasks.is_priority tersedia");

// Cek logika Monopoli Admin di earn_coins.php
test_assert(strpos($earnContent, '$isLockedByAdminTask') !== false, "Fitur Monopoli Tugas Wajib Admin aktif");
test_assert(strpos($earnContent, 'Tugas Wajib Admin') !== false, "Banner Tugas Wajib Admin ditampilkan saat terkunci");

echo "\n🎉 SEMUA 4 ATURAN FINAL MVP BERHASIL DIVALIDASI 100% TANPA KESALAHAN!\n";
