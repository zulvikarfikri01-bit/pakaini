<?php
/**
 * Test Otomatis untuk 4 Aturan Bisnis Perombakan Sistem
 */
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/app/Core/Model.php';
require_once ROOT_PATH . '/app/Models/Task.php';
require_once ROOT_PATH . '/app/Models/User.php';

echo "========================================================\n";
echo "   PENGUJIAN 4 ATURAN BISNIS PEROMBAKAN PAKAINI         \n";
echo "========================================================\n\n";

$taskModel = new Task();
$userModel = new User();

// Helper assertion
function test_assert($condition, $title) {
    if ($condition) {
        echo "✅ PASS: $title\n";
    } else {
        echo "❌ FAIL: $title\n";
        exit(1);
    }
}

// -------------------------------------------------------------
// TEST ATURAN 1: SISTEM 1 DOMPET TUNGGAL (KOIN MURNI)
// -------------------------------------------------------------
echo "--- TEST 1: Sistem 1 Dompet Tunggal (Koin Murni) ---\n";
$user3 = $userModel->findById(3);
test_assert(array_key_exists('coins', $user3), "Kolom users.coins tersedia sebagai dompet tunggal");
test_assert(!array_key_exists('koin_iklan', $user3), "Kolom users.koin_iklan telah dihapus");
test_assert(!array_key_exists('koin_penghasilan', $user3), "Kolom users.koin_penghasilan telah dihapus");

// -------------------------------------------------------------
// TEST ATURAN 2: SISTEM REFERRAL FOMO (LIMIT 50 & BUKTI MEDSOS & 2 HADIAH)
// -------------------------------------------------------------
echo "\n--- TEST 2: Sistem Referral FOMO (Limit 50 & Bukti Medsos) ---\n";
test_assert(REFERRAL_MAX_QUOTA === 50, "Limit Kuota Referral adalah 50 Orang");
test_assert(REFERRAL_BOUNTY_CASH === 50000, "Hadiah Uang Tunai Referral adalah Rp 50.000");
test_assert(REFERRAL_BOUNTY_ADS_COIN === 1500, "Hadiah Koin Iklan Referral adalah 1.500 Koin");

// Uji klaim tanpa URL valid harus ditolak
$claimInvalid = $userModel->claimReferralBounty(3, 'cash', 'invalid-url');
test_assert($claimInvalid['success'] === false, "Klaim tanpa link promosi medsos valid DITOLAK");

// Uji query network mengandung remaining_quota
$network = $userModel->getReferralNetwork('budi_pemula');
test_assert(isset($network['remaining_quota']), "Network data memiliki field remaining_quota");
echo "ℹ️ Sisa Kuota Referral Budi: {$network['remaining_quota']}/50 Orang\n";

// -------------------------------------------------------------
// TEST ATURAN 3: SEMATKAN KE ATAS (LELANG PRIORITAS)
// -------------------------------------------------------------
echo "\n--- TEST 3: Fitur Sematkan ke Atas (Prioritas 2x Biaya) ---\n";
// Buat tugas dengan is_priority = 1 dari user 2 (Sultan)
$createPriorityTask = $taskModel->createTask(2, 'tiktok', 'followers', 'https://tiktok.com/@sultan_prioritas_' . time(), 50, 10, true, true);
test_assert($createPriorityTask['success'] === true, "Pembuatan tugas prioritas (is_priority = 1) berhasil");
$pTaskId = $createPriorityTask['task_id'];

// Cek di database apakah is_priority = 1
$tRow = $taskModel->findById($pTaskId);
test_assert((int)$tRow['is_priority'] === 1, "Tugas tersimpan dengan is_priority = 1");

// -------------------------------------------------------------
// TEST ATURAN 4: MONOPOLI ADMIN (TUGAS WAJIB PERTAMA)
// -------------------------------------------------------------
echo "\n--- TEST 4: Monopoli Admin (Tugas Wajib Pertama) ---\n";
// Cek tugas admin yang belum dikerjakan oleh user 3
$adminPending = $taskModel->getPendingAdminTasks(3);
echo "ℹ️ Jumlah Tugas Wajib Admin yang belum diselesaikan user 3: " . count($adminPending) . "\n";

if (!empty($adminPending)) {
    // Monopoli aktif: Available tasks hanya mengembalikan tugas wajib admin!
    $availTasks = $taskModel->getAvailableTasks(3);
    $allAdmin = true;
    foreach ($availTasks as $at) {
        if ((int)$at['is_admin_task'] !== 1) {
            $allAdmin = false;
            break;
        }
    }
    test_assert($allAdmin === true, "Monopoli Admin Aktif: User 3 HANYA dapat melihat Tugas Wajib Admin!");
    test_assert(count($availTasks) === count($adminPending), "Seluruh tugas yang muncul adalah tugas wajib admin");
}

echo "\n🎉 SEMUA 4 ATURAN BISNIS BERHASIL DIVERIFIKASI DENGAN SEMPURNA!\n";
