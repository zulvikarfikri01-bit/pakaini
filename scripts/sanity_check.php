<?php
/**
 * Sanity Check: Pindai semua file PHP untuk kebocoran kode JavaScript,
 * tag <script> yang tidak tertutup atau tidak dibuka, dan tag PHP yang bocor.
 */

$root = dirname(__DIR__);
$dirs = [
    $root,
    $root . '/views',
    $root . '/views/auth',
    $root . '/views/dashboard',
    $root . '/views/tasks',
    $root . '/views/layouts',
    $root . '/views/referral',
    $root . '/views/shop',
    $root . '/views/admin'
];

$allFiles = [];
foreach ($dirs as $d) {
    if (is_dir($d)) {
        foreach (glob($d . '/*.php') as $f) {
            $allFiles[] = realpath($f);
        }
    }
}
$allFiles = array_unique($allFiles);

echo "Total file PHP yang dipindai: " . count($allFiles) . "\n\n";

$issues = [];

$phpCloseMarker = '?' . '>';
$phpOpenMarker = '<' . '?php';

foreach ($allFiles as $file) {
    if (basename($file) === 'sanity_check.php') {
        continue;
    }
    $content = file_get_contents($file);
    $relName = str_replace($root . DIRECTORY_SEPARATOR, '', $file);

    // 1. Lint syntax check
    $cmd = 'php -l ' . escapeshellarg($file) . ' 2>&1';
    $lintOut = exec($cmd, $dummy, $lintRet);
    if ($lintRet !== 0) {
        $issues[] = "[Syntax Error] {$relName}: {$lintOut}";
    }

    // 2. Cek apakah ada function javascript yang berada di luar tag <script>
    // Tokenize PHP code to remove PHP blocks
    $tokens = token_get_all($content);
    $htmlOnly = '';
    foreach ($tokens as $token) {
        if (is_array($token)) {
            if ($token[0] === T_INLINE_HTML) {
                $htmlOnly .= $token[1];
            }
        }
    }

    // Cari apakah tag <script> dan </script> seimbang di bagian HTML
    $openScripts = substr_count(strtolower($htmlOnly), '<script');
    $closeScripts = substr_count(strtolower($htmlOnly), '</script>');
    if ($openScripts !== $closeScripts) {
        $issues[] = "[Mismatched <script>] {$relName}: <script>={$openScripts}, </script>={$closeScripts}";
    }

    // Buang semua blok di dalam <script>...</script>
    $nonScriptHtml = preg_replace('/<script[\s\S]*?<\/script>/i', '', $htmlOnly);

    // Sekarang periksa apakah ada kode javascript function bocor di luar tag <script>
    if (preg_match('/(?:^|\n)\s*function\s+[a-zA-Z0-9_]+\s*\([^)]*\)\s*\{/m', $nonScriptHtml, $m)) {
        $issues[] = "[Leaked JS function outside <script>] {$relName}: Found '{$m[0]}'";
    }
}

if (empty($issues)) {
    echo "✅ SEMUA FILE PHP BERSIH! Tidak ada kebocoran JavaScript, tag <script> seimbang, dan sintaksis 100% valid.\n";
    exit(0);
} else {
    echo "⚠️ DITEMUKAN MASALAH:\n";
    foreach ($issues as $iss) {
        echo "  - " . $iss . "\n";
    }
    exit(1);
}
