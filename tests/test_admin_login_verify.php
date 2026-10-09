<?php
require_once __DIR__ . '/../config.php';

// Pastikan ada user admin di database
$newHash = password_hash('admin123', PASSWORD_BCRYPT);
$pdo->prepare("UPDATE users SET password = ?, password_hash = ?, role = 'admin' WHERE username = 'admin'")->execute([$newHash, $newHash]);

$user = $pdo->query("SELECT * FROM users WHERE username = 'admin'")->fetch();
echo "User Admin found: " . ($user ? 'YES' : 'NO') . "\n";
echo "Role: " . ($user['role'] ?? 'none') . "\n";
echo "Password verify: " . (password_verify('admin123', $user['password']) ? 'MATCH' : 'MISMATCH') . "\n";
