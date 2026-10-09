<?php
/**
 * Migration: Kunci Fisik Saldo Koin (MySQL UNSIGNED / Hard Limit Non-Negatif)
 */
require_once __DIR__ . '/../config.php';

$driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
echo "Memulai migrasi kunci fisik saldo koin. Driver aktif: {$driver}\n";

if ($driver === 'mysql') {
    try {
        $pdo->exec("ALTER TABLE users MODIFY COLUMN coins INT UNSIGNED NOT NULL DEFAULT 0;");
        echo "[SUCCESS] Query MySQL 'ALTER TABLE users MODIFY COLUMN coins INT UNSIGNED NOT NULL DEFAULT 0;' berhasil dieksekusi!\n";
    } catch (Exception $e) {
        echo "[ERROR MySQL] " . $e->getMessage() . "\n";
    }
} else {
    // Driver SQLite (Localhost / Fallback)
    try {
        $pdo->exec("
            CREATE TRIGGER IF NOT EXISTS prevent_negative_coins_update
            BEFORE UPDATE OF coins ON users
            FOR EACH ROW
            WHEN NEW.coins < 0
            BEGIN
                SELECT RAISE(FAIL, 'Hard Limit: Saldo koin tidak boleh bernilai negatif!');
            END;
        ");
        $pdo->exec("
            CREATE TRIGGER IF NOT EXISTS prevent_negative_coins_insert
            BEFORE INSERT ON users
            FOR EACH ROW
            WHEN NEW.coins < 0
            BEGIN
                SELECT RAISE(FAIL, 'Hard Limit: Saldo koin tidak boleh bernilai negatif!');
            END;
        ");
        echo "[SUCCESS] Trigger SQLite 'prevent_negative_coins' berhasil dibuat untuk mengunci saldo non-negatif!\n";
    } catch (Exception $e) {
        echo "[ERROR SQLite] " . $e->getMessage() . "\n";
    }
}

echo "Migrasi Kunci Fisik Saldo Koin Selesai.\n";
