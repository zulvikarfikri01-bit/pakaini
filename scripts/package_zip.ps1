$zipPath = 'E:\aplikasi pakaini\pakaini_clean_deployment.zip'
if (Test-Path $zipPath) { Remove-Item $zipPath -Force }

$tempDeploy = 'E:\aplikasi pakaini\temp_deploy'
if (Test-Path $tempDeploy) { Remove-Item $tempDeploy -Recurse -Force }
New-Item -ItemType Directory -Path $tempDeploy | Out-Null

# 1. Daftar file root penting
$rootFiles = @(
    '.htaccess', 'index.php', 'login.php', 'register.php', 'dashboard.php',
    'earn_coins.php', 'add_task.php', 'claim.php', 'load_tasks.php',
    'panel_pusat_pakaini_99x.php', 'topup.php', 'profile.php', 'referral.php',
    'community.php', 'edit_profile.php', 'claim_vip.php', 'logout.php',
    'admin.php', 'navbar.php', 'footer.php', 'logo.png', 'config.php',
    'database.sql', 'install.php', 'README.md'
)

foreach ($f in $rootFiles) {
    $src = Join-Path 'E:\aplikasi pakaini' $f
    if (Test-Path $src) {
        Copy-Item $src -Destination $tempDeploy -Force
    }
}

# 2. Daftar folder sistem
$folders = @('app', 'config', 'views', 'assets')
foreach ($fld in $folders) {
    $src = Join-Path 'E:\aplikasi pakaini' $fld
    if (Test-Path $src) {
        Copy-Item $src -Destination $tempDeploy -Recurse -Force
    }
}

# 3. Storage folder (hanya index.html)
$storageTarget = Join-Path $tempDeploy 'storage'
New-Item -ItemType Directory -Path $storageTarget -Force | Out-Null
Copy-Item 'E:\aplikasi pakaini\storage\index.html' -Destination $storageTarget -Force

# 4. Kompresi ZIP bersih
Compress-Archive -Path "$tempDeploy\*" -DestinationPath $zipPath -Force

# 5. Bersihkan direktori sementara
Remove-Item $tempDeploy -Recurse -Force

if (Test-Path $zipPath) {
    $size = (Get-Item $zipPath).Length
    $sizeKb = [math]::Round($size / 1024, 2)
    Write-Host "ZIP Created successfully! Size: $size bytes ($sizeKb KB)"
} else {
    Write-Host "Failed to create ZIP!"
}
