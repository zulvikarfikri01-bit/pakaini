Add-Type -AssemblyName System.IO.Compression.FileSystem

$testDir = 'E:\aplikasi pakaini\test_extracted_package'
if (Test-Path $testDir) { Remove-Item $testDir -Recurse -Force }
New-Item -ItemType Directory -Path $testDir | Out-Null

Write-Host "Extracting pakaini_clean_deployment.zip into $testDir..."
[System.IO.Compression.ZipFile]::ExtractToDirectory('E:\aplikasi pakaini\pakaini_clean_deployment.zip', $testDir)

# Verifikasi file-file kritis yang sebelumnya error
$criticalFiles = @(
    'config/database.php',
    'config/config.php',
    'app/Core/Router.php',
    'app/Core/Controller.php',
    'app/Core/Model.php',
    'app/Core/Auth.php',
    'index.php',
    'login.php',
    'register.php',
    'dashboard.php',
    'earn_coins.php',
    'add_task.php',
    'claim.php',
    'load_tasks.php',
    'panel_pusat_pakaini_99x.php',
    'database.sql'
)

$allPassed = $true
foreach ($cf in $criticalFiles) {
    $full = Join-Path $testDir $cf
    if (Test-Path $full) {
        Write-Host " [OK] Exists: $cf"
    } else {
        Write-Host " [FAIL] Missing: $cf" -ForegroundColor Red
        $allPassed = $false
    }
}

# Linting seluruh file hasil ekstraksi
Write-Host "`nLinting all extracted PHP files..."
$phpFiles = Get-ChildItem -Path $testDir -Recurse -Filter *.php
$lintErrors = 0
foreach ($pf in $phpFiles) {
    $res = php -l $pf.FullName 2>&1
    if ($LASTEXITCODE -ne 0) {
        Write-Host " [SYNTAX ERROR] $($pf.FullName): $res" -ForegroundColor Red
        $lintErrors++
    }
}

if ($lintErrors -eq 0) {
    Write-Host " [PASS] All $($phpFiles.Count) extracted PHP files passed syntax check!" -ForegroundColor Green
} else {
    Write-Host " [FAIL] Found $lintErrors syntax errors!" -ForegroundColor Red
}

# Test load index.php dari extracted folder
Write-Host "`nTesting execution of extracted index.php..."
$testRun = php -d display_errors=1 "$testDir\index.php" 2>&1
if ($testRun -match "Fatal error" -or $testRun -match "Parse error") {
    Write-Host " [FAIL] Fatal error when loading extracted index.php: $testRun" -ForegroundColor Red
} else {
    Write-Host " [PASS] Extracted index.php ran cleanly without fatal errors!" -ForegroundColor Green
}

# Bersihkan test directory
Remove-Item $testDir -Recurse -Force
Write-Host "`nTest Extracted Package Finished Successfully!"
