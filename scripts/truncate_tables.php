<?php
require_once __DIR__ . '/../config.php';

echo "Mengosongkan tabel untuk fase siap rilis...\n";

$tables = ['users', 'tasks', 'task_history', 'transactions', 'referral_claims', 'withdrawals'];

foreach ($tables as $table) {
    try {
        $pdo->exec("DELETE FROM {$table}");
        // Reset autoincrement jika SQLite
        try {
            $pdo->exec("DELETE FROM sqlite_sequence WHERE name = '{$table}'");
        } catch (Exception $e) {}
        echo "  ✓ Tabel '{$table}' berhasil dikosongkan.\n";
    } catch (Exception $e) {
        echo "  - Gagal mengosongkan '{$table}': " . $e->getMessage() . "\n";
    }
}

$userCount = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$taskCount = (int)$pdo->query("SELECT COUNT(*) FROM tasks")->fetchColumn();

echo "\nStatus Database Saat Ini:\n";
echo "  - Total Users: {$userCount}\n";
echo "  - Total Tasks: {$taskCount}\n";
echo "Database 100% bersih dan siap menerima pendaftaran akun Admin pertama!\n";
