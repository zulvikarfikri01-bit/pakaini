Add-Type -AssemblyName System.IO.Compression.FileSystem
$zip = [System.IO.Compression.ZipFile]::OpenRead('E:\aplikasi pakaini\pakaini_clean_deployment.zip')
Write-Host "=== ROOT FILES IN ZIP ==="
foreach ($e in $zip.Entries) {
    if (-not $e.FullName.Contains('\') -and -not $e.FullName.Contains('/')) {
        Write-Host " - $($e.FullName)"
    }
}
$zip.Dispose()
