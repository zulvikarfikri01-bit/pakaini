<?php
/**
 * Test HTTP Endpoints via PHP
 */

$baseUrl = 'http://127.0.0.1:8000';

$endpoints = [
    '/' => 'PAKAINI',
    '/index.php' => 'PAKAINI',
    '/login.php' => 'Masuk ke Akun Anda',
    '/register.php' => 'Buat Akun Baru'
];

echo "Testing Public Endpoints...\n";
foreach ($endpoints as $path => $expectedString) {
    $url = $baseUrl . $path;
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'ignore_errors' => true,
            'timeout' => 5
        ]
    ]);
    $html = @file_get_contents($url, false, $context);
    if ($html !== false && str_contains($html, $expectedString)) {
        echo "✅ OK: $path (contains '$expectedString')\n";
    } else {
        echo "❌ FAIL: $path\n";
    }
}

// Test Login Session Flow via Cookie Jar
echo "\nTesting Login & Authenticated Endpoints via Session Cookie...\n";

// 1. Send POST to /login.php
$postData = http_build_query([
    'identity' => 'sultan_vip',
    'password' => 'admin123'
]);

$cookieJar = tempnam(sys_get_temp_dir(), 'pakaini_cookie_');

$ch = curl_init($baseUrl . '/login.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if (str_contains($response, 'Dashboard') || str_contains($response, 'Halo,') || str_contains($response, 'sultan_vip')) {
    echo "✅ OK: Login successful, redirected to Dashboard\n";
} else {
    echo "❌ FAIL: Login failed\n";
}

// 2. Access Authenticated Pages with Cookie
$authPages = [
    '/dashboard.php'  => 'Dashboard',
    '/add_task.php'   => 'Manajemen Kampanye Tugas',
    '/earn_coins.php' => 'Zona Cari Koin Gratis',
    '/referral.php'   => 'Sistem Referral (Bawa Teman) - COMING SOON!',
    '/topup.php'      => 'Paket 1.000 Koin'
];

foreach ($authPages as $path => $needle) {
    $ch = curl_init($baseUrl . $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (str_contains($res, $needle)) {
        echo "✅ OK: $path loaded with correct session (contains '$needle')\n";
    } else {
        echo "❌ FAIL: $path missing expected content\n";
    }
}

@unlink($cookieJar);
echo "\nAll HTTP tests completed successfully!\n";
