# Script untuk update warna dan nama

$sourceFolder = "plantuml\sequence"
$files = Get-ChildItem -Path $sourceFolder -Filter "*.puml"

foreach ($file in $files) {
    Write-Host "Updating $($file.Name)..."
    
    $content = Get-Content -Path $file.FullName -Raw
    
    # 1. Tambahkan warna #LightBlue ke semua stereotype
    $content = $content -replace '(actor|boundary|control|entity) "([^"]+)" as (\w+)$', '$1 "$2" as $3 #LightBlue'
    
    # 2. Ubah "Admin" menjadi "Dapodik" di label actor
    $content = $content -replace 'actor "Admin"', 'actor "Dapodik"'
    
    # 3. Tambahkan skinparam untuk frame alt menjadi hitam (jika belum ada)
    if ($content -notmatch 'skinparam sequenceGroupBorderColor') {
        $content = $content -replace '(skinparam sequenceMessageAlign center)', "`$1`nskinparam sequenceGroupBorderColor black"
    }
    
    # Save updated content
    Set-Content -Path $file.FullName -Value $content -NoNewline
    
    Write-Host "  Updated!"
}

Write-Host "`nAll files updated successfully!"
