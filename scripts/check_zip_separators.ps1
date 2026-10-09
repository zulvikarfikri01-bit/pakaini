Add-Type -AssemblyName System.IO.Compression.FileSystem
$zip = [System.IO.Compression.ZipFile]::OpenRead('E:\aplikasi pakaini\pakaini_clean_deployment.zip')
foreach ($e in $zip.Entries | Select-Object -First 15) {
    Write-Host "Entry: '$($e.FullName)' - HasBackslash: $($e.FullName.Contains('\'))"
}
$zip.Dispose()
