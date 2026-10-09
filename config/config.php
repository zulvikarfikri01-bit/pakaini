<?php
/**
 * PAKAINI - Configuration File
 * Sistem Ekonomi Koin Virtual & Pengaturan Platform
 */

// Hindari direct access jika diperlukan
defined('ROOT_PATH') or define('ROOT_PATH', dirname(__DIR__));

// Deteksi Base URL secara otomatis (Bekerja di localhost, subfolder, maupun domain shared hosting)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$baseUrl = rtrim($protocol . $host . $scriptDir, '/');

defined('BASE_URL') or define('BASE_URL', $baseUrl);
defined('APP_NAME') or define('APP_NAME', 'PAKAINI');
defined('APP_TAGLINE') or define('APP_TAGLINE', 'Platform Social Media Exchange #1 di Indonesia');
defined('APP_VERSION') or define('APP_VERSION', '1.0.0');

// Nomor WhatsApp Admin untuk Konfirmasi Top Up & VIP Manual (085143253217)
defined('ADMIN_WHATSAPP') or define('ADMIN_WHATSAPP', '6285143253217');

// Konfigurasi Database Terpusat di database.php
require_once __DIR__ . '/database.php';

// ==============================================================================
// ATURAN EKONOMI KOIN & NILAI TUKAR (EXCHANGE RATE - TANPA KATA PAJAK)
// ==============================================================================
defined('RATE_TOPUP_IDR') or define('RATE_TOPUP_IDR', 50);          // Harga Beli Koin Iklan: 1.000 Koin = Rp 50.000 (1 Koin = Rp 50)
defined('RATE_WITHDRAW_IDR') or define('RATE_WITHDRAW_IDR', 30);    // Nilai Tukar Pencairan: 1.000 Koin = Rp 30.000 (1 Koin = Rp 30)
defined('COIN_RATE_IDR') or define('COIN_RATE_IDR', 30);            // Referensi nilai pencairan tunai (1 Koin = Rp 30)
defined('NEW_USER_BONUS_COINS') or define('NEW_USER_BONUS_COINS', 500);    // Saldo awal pendaftaran
defined('NEW_USER_VIP_DAYS') or define('NEW_USER_VIP_DAYS', 3);         // Trial VIP untuk user baru (3 Hari)
defined('REGULAR_DAILY_MAX_CLICKS') or define('REGULAR_DAILY_MAX_CLICKS', 50); // Batas klik harian user biasa (VIP: Unlimited)
defined('REGULAR_MAX_ACTIVE_TASKS') or define('REGULAR_MAX_ACTIVE_TASKS', 2);  // Batas tugas aktif user biasa (VIP: Unlimited)
defined('REGULAR_TAX_RATE') or define('REGULAR_TAX_RATE', 0.0);        // Pajak ditiadakan (Nilai tukar murni)
defined('PENALTY_MULTIPLIER') or define('PENALTY_MULTIPLIER', 3);        // Denda 3x lipat nilai CPC jika terbukti curang (Unfollow)

// Aturan Referral FOMO (Maksimal Kuota 50 Orang & Bukti Medsos)
defined('REFERRAL_MAX_QUOTA') or define('REFERRAL_MAX_QUOTA', 50);             // Batas kuota klaim maksimal 50 referral
defined('REFERRAL_BOUNTY_CASH') or define('REFERRAL_BOUNTY_CASH', 50000);       // Hadiah tunai Rp 50.000
defined('REFERRAL_BOUNTY_ADS_COIN') or define('REFERRAL_BOUNTY_ADS_COIN', 1500); // Hadiah koin iklan 1.500 koin
defined('REFERRAL_MIN_ACTIVE_DAYS') or define('REFERRAL_MIN_ACTIVE_DAYS', 10); // Syarat lulus: streak >= 10 hari
defined('REFERRAL_INACTIVE_LIMIT_DAYS') or define('REFERRAL_INACTIVE_LIMIT_DAYS', 3); // Inaktif jika > 3 hari tidak login

// Minimal Harga CPC Berdasarkan Platform & Jenis Tindakan
$PLATFORM_MIN_CPC = [
    'youtube' => [
        'subscribers' => 150,
        'views'       => 30, // Durasi 30 detik
        'likes'       => 20,
    ],
    'facebook' => [
        'followers'   => 120,
        'likes'       => 20,
    ],
    'tiktok' => [
        'followers'   => 50,
        'likes'       => 20,
    ],
    'instagram' => [
        'followers'   => 50,
        'likes'       => 20,
    ],
];

// Paket Top Up Toko Koin Iklan & VIP (Monetisasi Manual via WhatsApp)
$TOPUP_PACKAGES = [
    'pemula' => [
        'name'    => 'Paket Pemula',
        'price'   => 50000,
        'coins'   => 1000,
        'bonus'   => 'Rp 50.000 = 1.000 Koin Iklan',
        'popular' => false,
        'badge'   => 'Starter'
    ],
    'standar' => [
        'name'    => 'Paket Populer',
        'price'   => 100000,
        'coins'   => 2000,
        'bonus'   => 'Rp 100.000 = 2.000 Koin Iklan',
        'popular' => true,
        'badge'   => 'Paling Populer'
    ],
    'sultan' => [
        'name'    => 'Paket Sultan',
        'price'   => 200000,
        'coins'   => 4500, // 4.000 + Bonus 500 Koin!
        'bonus'   => 'Termasuk Bonus 500 Koin Iklan Gratis!',
        'popular' => false,
        'badge'   => 'Mega Bonus'
    ],
    'vip_monthly' => [
        'name'    => 'VIP Sultan 1 Bulan',
        'price'   => 30000,
        'coins'   => 0,
        'days'    => 30,
        'bonus'   => 'Prioritas Penarikan Cepat, Klik Tanpa Batas, Border Emas',
        'popular' => false,
        'badge'   => 'VIP Sultan'
    ]
];

// Durasi Timer Validasi Tugas
define('TASK_DEFAULT_TIMER_SECONDS', 15);
define('TASK_YT_VIEW_TIMER_SECONDS', 30);
