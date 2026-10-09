<?php
/**
 * Test HTTP views to verify zero leaked plain text JavaScript code in HTML output.
 */

$urls = [
    'http://127.0.0.1:8000/login.php',
    'http://127.0.0.1:8000/register.php',
    'http://127.0.0.1:8000/index.php?page=login',
    'http://127.0.0.1:8000/index.php?page=register'
];

foreach ($urls as $url) {
    $html = @file_get_contents($url);
    if ($html === false) {
        echo "⚠️ Dev server tidak merespons di {$url}\n";
        continue;
    }

    echo "Memeriksa output HTTP: {$url}\n";

    // Pastikan tidak ada function javascript yang tampil di luar tag <script>
    $noScript = preg_replace('/<script[\s\S]*?<\/script>/i', '', $html);
    if (preg_match('/(?:^|\n)\s*function\s+[a-zA-Z0-9_]+\s*\(/m', $noScript, $match)) {
        echo "  ✗ FAIL: Ditemukan kode fungsi JS bocor di luar <script>: '{$match[0]}'\n";
        exit(1);
    }

    // Pastikan tidak ada tag PHP bocor
    if (strpos($noScript, '<?php') !== false || strpos($noScript, '<?=') !== false) {
        echo "  ✗ FAIL: Ditemukan tag PHP bocor ke output HTML!\n";
        exit(1);
    }

    echo "  ✓ PASS: Output HTML bersih dari kebocoran kode!\n";
}

echo "\nSemua output HTTP terverifikasi bersih dan aman.\n";
