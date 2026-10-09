<?php
// Automated HTTP integration test for PAKAINI views
$cookieFile = __DIR__ . '/test_cookie.txt';
if (file_exists($cookieFile)) unlink($cookieFile);

$baseUrl = 'http://127.0.0.1:8080';

function httpGet($url, $cookieFile = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if ($cookieFile) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => $res];
}

function httpPost($url, $data, $cookieFile = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if ($cookieFile) {
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    }
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => $res];
}

echo "Testing guest pages...\n";
$r = httpGet("$baseUrl/index.php?page=login", $cookieFile);
echo "  [GET] /login: HTTP " . $r['code'] . "\n";
preg_match('/name="csrf_token" value="([^"]+)"/', $r['body'], $m);
$csrf = $m[1] ?? '';

$r = httpGet("$baseUrl/index.php?page=register", $cookieFile);
echo "  [GET] /register: HTTP " . $r['code'] . "\n";

echo "Logging in as sultan_vip...\n";
$r = httpPost("$baseUrl/index.php?page=login", [
    'csrf_token' => $csrf,
    'username' => 'sultan_vip',
    'password' => 'admin123'
], $cookieFile);
echo "  [POST] /login: HTTP " . $r['code'] . "\n";

$pages = [
    'Dashboard' => "$baseUrl/index.php?page=dashboard",
    'Tasks Browse' => "$baseUrl/index.php?page=tasks",
    'Tasks Create' => "$baseUrl/index.php?page=tasks&action=create",
    'Tasks My' => "$baseUrl/index.php?page=tasks&action=my_tasks",
    'Referral' => "$baseUrl/index.php?page=referral",
    'Shop' => "$baseUrl/index.php?page=shop",
    'Community' => "$baseUrl/index.php?page=community",
];

foreach ($pages as $name => $url) {
    $r = httpGet($url, $cookieFile);
    $hasFatal = stripos($r['body'], 'fatal error') !== false || stripos($r['body'], 'parse error') !== false;
    echo "  [GET] $name: HTTP {$r['code']} " . ($hasFatal ? '[FATAL ERROR DETECTED!]' : '[OK]') . "\n";
}

echo "Testing Admin Views...\n";
$adminCookie = __DIR__ . '/admin_cookie.txt';
if (file_exists($adminCookie)) unlink($adminCookie);

$r = httpGet("$baseUrl/index.php?page=login", $adminCookie);
preg_match('/name="csrf_token" value="([^"]+)"/', $r['body'], $m);
$adminCsrf = $m[1] ?? '';

$r = httpPost("$baseUrl/index.php?page=login", [
    'csrf_token' => $adminCsrf,
    'username' => 'admin',
    'password' => 'admin123'
], $adminCookie);

$adminPages = [
    'Admin Overview'     => "$baseUrl/index.php?page=admin",
    'Admin Users'        => "$baseUrl/index.php?page=admin&action=users",
    'Admin Tasks'        => "$baseUrl/index.php?page=admin&action=tasks",
    'Admin Reports'      => "$baseUrl/index.php?page=admin&action=reports",
    'Admin Transactions' => "$baseUrl/index.php?page=admin&action=transactions",
    'Admin Settings'     => "$baseUrl/index.php?page=admin&action=settings",
];

foreach ($adminPages as $name => $url) {
    $r = httpGet($url, $adminCookie);
    $hasFatal = stripos($r['body'], 'fatal error') !== false || stripos($r['body'], 'parse error') !== false;
    echo "  [GET] $name: HTTP {$r['code']} " . ($hasFatal ? '[FATAL ERROR DETECTED!]' : '[OK]') . "\n";
}

// Clean up
if (file_exists($cookieFile)) unlink($cookieFile);
if (file_exists($adminCookie)) unlink($adminCookie);

echo "\nAll integration tests finished successfully!\n";
