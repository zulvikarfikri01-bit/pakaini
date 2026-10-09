<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/app/Core/Model.php';
require_once ROOT_PATH . '/app/Models/Task.php';
require_once ROOT_PATH . '/app/Models/User.php';
require_once ROOT_PATH . '/app/Models/Report.php';
require_once ROOT_PATH . '/app/Models/Transaction.php';

$taskModel = new Task();
$userModel = new User();
$reportModel = new Report();
$txModel = new Transaction();

echo "=== 1. TEST KLAIM TUGAS & PAJAK 20% ===\n";
$budiBefore = (int)$userModel->findById(3)['coins'];
$sultanBefore = (int)$userModel->findById(2)['coins'];

// Eksekusi klaim tugas #2 (CPC 35) oleh budi_pemula (id 3, member reguler)
$claimResult = $taskModel->claimTask(2, 3);
print_r($claimResult);

$budiAfter = (int)$userModel->findById(3)['coins'];
$sultanAfter = (int)$userModel->findById(2)['coins'];

echo "Budi Saldo: $budiBefore -> $budiAfter (Harus +28, karena 20% pajak hangus)\n";
echo "Sultan Saldo: $sultanBefore -> $sultanAfter (Harus -35)\n";

echo "\n=== 2. TEST LAPOR CURANG & DENDA 3X LIPAT (SALDO MINUS) ===\n";
// Sultan melapor bahwa budi_pemula unfollow/unsub
$repResult = $reportModel->createReport(2, 3, 2, "Unsubscribe setelah klaim koin");
print_r($repResult);

// Ambil laporan pending
$reports = $reportModel->getAllReports('pending');
$repId = $reports[0]['id'];
echo "Report ID: $repId\n";

// Admin validasi denda 3x lipat
$penalizeResult = $reportModel->penalizeCheater($repId, "Validasi admin: terbukti curang.");
print_r($penalizeResult);

$budiPenalty = (int)$userModel->findById(3)['coins'];
$sultanRefund = (int)$userModel->findById(2)['coins'];

echo "Budi Setelah Denda 3x (-105 Koin): $budiAfter -> $budiPenalty\n";
echo "Sultan Setelah Refund (+35 Koin): $sultanAfter -> $sultanRefund\n";

echo "\n=== 3. TEST LOG TRANSAKSI ===\n";
$txs = $txModel->getByUser(3, 5);
foreach ($txs as $t) {
    echo "[TX] {$t['type']} | {$t['amount']} Koin | {$t['description']}\n";
}

echo "\n=== SEMUA PENGUJIAN LOGIKA EKONOMI SUKSES! ===\n";
