<?php
$dir = dirname(__DIR__) . '/views';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$phpFiles = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

$errors = 0;
$count = 0;

foreach ($phpFiles as $file) {
    $filePath = $file[0];
    $count++;
    $output = [];
    $returnVar = 0;
    exec('"' . PHP_BINARY . '" -l "' . $filePath . '" 2>&1', $output, $returnVar);
    if ($returnVar !== 0) {
        echo "FAIL: $filePath\n" . implode("\n", $output) . "\n";
        $errors++;
    } else {
        echo "OK: " . basename(dirname($filePath)) . '/' . basename($filePath) . "\n";
    }
}

echo "\nChecked $count view files. Errors: $errors\n";
exit($errors > 0 ? 1 : 0);
