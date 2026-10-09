Add-Type -AssemblyName System.IO.Compression.FileSystem
$zip = [System.IO.Compression.ZipFile]::OpenRead('E:\aplikasi pakaini\pakaini_clean_deployment.zip')
Write-Host "=== KEY SUBFOLDER ENTRIES IN ZIP ==="
$keyPatterns = @('config/', 'app/Core/', 'app/Controllers/', 'views/layouts/')
foreach ($e in $zip.Entries) {
    foreach ($kp in $keyPatterns) {
        if ($e.FullName.StartsWith($kp)) {
            Write-Host " - $($e.FullName) (Backslash: $($e.FullName.Contains('\')))"
        }
    }
}
$zip.Dispose()
