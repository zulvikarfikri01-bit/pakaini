<?php
require_once __DIR__ . '/../config.php';

// Simulasi: buat tabel uji coba 'test_users_hash_only' yang hanya memiliki password_hash
$pdo->exec("CREATE TABLE IF NOT EXISTS test_users_hash_only (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE,
    email TEXT UNIQUE,
    password_hash TEXT,
    role TEXT DEFAULT 'user',
    coins INTEGER DEFAULT 0,
    is_vip INTEGER DEFAULT 0,
    referred_by TEXT,
    daily_clicks INTEGER DEFAULT 0,
    active_days_streak INTEGER DEFAULT 1,
    last_active_date TEXT,
    ip_address TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

$passHash = password_hash('tester123', PASSWORD_BCRYPT);

// Test insert jika hanya ada password_hash
try {
    $stmt = $pdo->prepare("INSERT INTO test_users_hash_only (username, email, password_hash, role, coins, is_vip, referred_by, daily_clicks, active_days_streak, last_active_date, ip_address, created_at) VALUES (?, ?, ?, 'user', 0, 0, NULL, 0, 1, '2026-10-07', '127.0.0.1', datetime('now'))");
    $stmt->execute(['test_hash_only_' . time(), 'test_' . time() . '@test.com', $passHash]);
    echo "TEST 1 (Hash Only Table): SUCCESS\n";
} catch (Exception $e) {
    echo "TEST 1 FAILED: " . $e->getMessage() . "\n";
}

// Bersihkan tabel uji
$pdo->exec("DROP TABLE IF EXISTS test_users_hash_only");
