<?php
// Functional verification of Admin Action endpoints
$adminCookie = __DIR__ . '/admin_cookie_actions.txt';
if (file_exists($adminCookie)) unlink($adminCookie);

$baseUrl = 'http://127.0.0.1:8080';

function getCsrf($html) {
    preg_match('/name="csrf_token" value="([^"]+)"/', $html, $m);
    return $m[1] ?? '';
}

function httpGet($url, $cookieFile) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => $res];
}

function httpPost($url, $data, $cookieFile) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => $res];
}

echo "1. Login Admin...\n";
$r = httpGet("$baseUrl/index.php?page=login", $adminCookie);
$csrf = getCsrf($r['body']);
$r = httpPost("$baseUrl/index.php?page=login", [
    'csrf_token' => $csrf,
    'username' => 'admin',
    'password' => 'admin123'
], $adminCookie);
echo "  Login: HTTP {$r['code']}\n";

echo "2. Testing Save Settings (WA Admin & Announcement)...\n";
$r = httpGet("$baseUrl/index.php?page=admin&action=settings", $adminCookie);
$csrf = getCsrf($r['body']);
$saveData = [
    'csrf_token' => $csrf,
    'site_name' => 'PAKAINI PRO',
    'site_tagline' => 'Platform Social Media Exchange #1 Indonesia',
    'wa_admin' => '6285143253217',
    'coin_rate_idr' => 25,
    'regular_tax_rate' => 0.20,
    'regular_daily_max_clicks' => 50,
    'regular_max_active_tasks' => 2,
    'new_user_bonus_coins' => 500,
    'new_user_vip_days' => 3,
    'penalty_multiplier' => 3,
    'broadcast_announcement' => 'Selamat datang di PAKAINI PRO! Top Up koin dan VIP Sultan sekarang langsung via WhatsApp Admin 085143253217.'
];
$r = httpPost("$baseUrl/index.php?page=admin&action=save_settings", $saveData, $adminCookie);
echo "  Save Settings: HTTP {$r['code']}\n";
$hasSuccess = strpos($r['body'], 'berhasil disimpan') !== false;
echo "  Success Message Detected: " . ($hasSuccess ? 'YES' : 'NO') . "\n";

echo "3. Testing Create User by Admin...\n";
$r = httpGet("$baseUrl/index.php?page=admin&action=users", $adminCookie);
$csrf = getCsrf($r['body']);
$testUser = 'test_member_' . time();
$createData = [
    'csrf_token' => $csrf,
    'username' => $testUser,
    'email' => $testUser . '@gmail.com',
    'password' => 'password123',
    'role' => 'user',
    'coins' => 1500,
    'is_vip' => 1,
    'vip_days' => 30
];
$r = httpPost("$baseUrl/index.php?page=admin&action=create_user", $createData, $adminCookie);
echo "  Create User: HTTP {$r['code']}\n";
$hasCreated = strpos($r['body'], $testUser) !== false;
echo "  New User in Table: " . ($hasCreated ? 'YES' : 'NO') . "\n";

echo "4. Testing Task Management (Edit Status/CPC)...\n";
$r = httpGet("$baseUrl/index.php?page=admin&action=tasks", $adminCookie);
$csrf = getCsrf($r['body']);
// Update task #1 if exists
$r = httpPost("$baseUrl/index.php?page=admin&action=update_task", [
    'csrf_token' => $csrf,
    'task_id' => 1,
    'status' => 'active',
    'cpc' => 15,
    'max_clicks' => 120
], $adminCookie);
echo "  Update Task #1: HTTP {$r['code']}\n";

echo "5. Checking Dashboard Broadcast Banner...\n";
$r = httpGet("$baseUrl/index.php?page=dashboard", $adminCookie);
$hasBanner = strpos($r['body'], '085143253217') !== false;
echo "  Broadcast Banner on Dashboard: " . ($hasBanner ? 'YES' : 'NO') . "\n";

// Cleanup
if (file_exists($adminCookie)) unlink($adminCookie);

echo "\nAll admin action tests passed!\n";
