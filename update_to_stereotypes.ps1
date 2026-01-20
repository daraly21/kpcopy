# Script untuk update semua diagram dengan stereotype icon

$sourceFolder = "plantuml\sequence"
$files = Get-ChildItem -Path $sourceFolder -Filter "*.puml"

foreach ($file in $files) {
    Write-Host "Updating $($file.Name)..."
    
    $content = Get-Content -Path $file.FullName -Raw
    
    # Replace participant dengan stereotype yang sesuai
    # Actor: Admin, User, Wali Kelas, Guru Mapel
    $content = $content -replace 'participant "Admin" as Dapodik', 'actor "Admin" as Dapodik'
    $content = $content -replace 'participant "User" as Dapodik', 'actor "User" as Dapodik'
    $content = $content -replace 'participant "Wali Kelas" as Dapodik', 'actor "Wali Kelas" as Dapodik'
    $content = $content -replace 'participant "Guru Mata Pelajaran" as Dapodik', 'actor "Guru Mata Pelajaran" as Dapodik'
    
    # Boundary: View (semua yang mengandung "Halaman")
    $content = $content -replace 'participant "Halaman ([^"]+)" as View', 'boundary "Halaman $1" as View'
    
    # Control: All Controllers
    $content = $content -replace 'participant "(\w+Controller)" as Controller', 'control "$1" as Controller'
    
    # Entity: All Models
    $content = $content -replace 'participant "AcademicYear" as AY', 'entity "AcademicYear" as AY'
    $content = $content -replace 'participant "Student" as S', 'entity "Student" as S'
    $content = $content -replace 'participant "StudentClass" as SC', 'entity "StudentClass" as SC'
    $content = $content -replace 'participant "ClassModel" as CM', 'entity "ClassModel" as CM'
    $content = $content -replace 'participant "Subject" as SUB', 'entity "Subject" as SUB'
    $content = $content -replace 'participant "Grade" as G', 'entity "Grade" as G'
    $content = $content -replace 'participant "GradeTask" as GT', 'entity "GradeTask" as GT'
    $content = $content -replace 'participant "Teacher" as T', 'entity "Teacher" as T'
    $content = $content -replace 'participant "User" as U', 'entity "User" as U'
    $content = $content -replace 'participant "Role" as R', 'entity "Role" as R'
    
    # Save updated content
    Set-Content -Path $file.FullName -Value $content -NoNewline
    
    Write-Host "  Updated!"
}

Write-Host "`nAll files updated successfully!"
