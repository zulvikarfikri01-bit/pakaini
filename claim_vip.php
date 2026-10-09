<?php
/**
 * PAKAINI - Klaim Uji Coba VIP 3 Hari & 500 Koin melalui Grup WhatsApp
 * 
 * Logika Bisnis:
 * 1. Pengguna wajib login.
 * 2. Cek apakah has_claimed_wa_vip == 0.
 * 3. Jika belum klaim:
 *    - Update is_vip = 1
 *    - Update vip_expired_at = +3 hari dari sekarang
 *    - Update has_claimed_wa_vip = 1 (mencegah klaim ganda)
 *    - Berikan bonus saldo 500 Koin
 *    - Catat transaksi mutasi ke tabel transactions
 * 4. Redirect langsung ke grup WhatsApp: https://chat.whatsapp.com/J6M0TwrjuvoGm4Mq1eJz2G
 */

require_once __DIR__ . '/config.php';

// Wajib Login
$user = require_login($pdo);
$userId = (int)$user['id'];

// Ambil status klaim terkini dari database
$stmt = $pdo->prepare("SELECT has_claimed_wa_vip, is_vip, vip_expired_at FROM users WHERE id = ?");
$stmt->execute([$userId]);
$userData = $stmt->fetch();

$hasClaimed = (int)($userData['has_claimed_wa_vip'] ?? 0);

if ($hasClaimed === 0) {
    try {
        $pdo->beginTransaction();

        $vipExpiredAt = date('Y-m-d H:i:s', strtotime('+3 days'));
        $bonusCoins = 500;

        // Update status VIP, masa berlaku, has_claimed_wa_vip, dan bonus koin
        $updateStmt = $pdo->prepare("
            UPDATE users 
            SET is_vip = 1,
                vip_expired_at = ?,
                has_claimed_wa_vip = 1,
                coins = coins + ?
            WHERE id = ?
        ");
        $updateStmt->execute([$vipExpiredAt, $bonusCoins, $userId]);

        // Catat buku kas transaksi
        $txStmt = $pdo->prepare("
            INSERT INTO transactions (user_id, type, amount, date, description)
            VALUES (?, 'bonus_wa_vip', ?, NOW(), ?)
        ");
        $txStmt->execute([
            $userId,
            $bonusCoins,
            'Bonus Bergabung Grup WhatsApp (500 Koin + VIP Sultan 3 Hari)'
        ]);

        $pdo->commit();

        set_flash('success', '🎉 Selamat! Bonus 500 Koin dan VIP Sultan 3 Hari berhasil diklaim!');
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        set_flash('error', 'Gagal memproses klaim VIP: ' . $e->getMessage());
    }
}

// Redirect ke tautan resmi Grup WhatsApp PAKAINI
$waGroupUrl = "https://chat.whatsapp.com/J6M0TwrjuvoGm4Mq1eJz2G";
header("Location: " . $waGroupUrl);
exit;
