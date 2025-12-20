<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== BAHASA INDONESIA DEBUG ===\n\n";

// Get Bahasa Indonesia subject
$bahasaIndonesia = \App\Models\Subject::where('name', 'Bahasa Indonesia')->first();
if (!$bahasaIndonesia) {
    echo "Bahasa Indonesia subject not found!\n";
    exit;
}

echo "Bahasa Indonesia Subject ID: {$bahasaIndonesia->id}\n\n";

// Get active academic year
$activeYear = \App\Models\AcademicYear::where('is_active', 1)->first();
echo "Active Academic Year: ID {$activeYear->id}, Name: {$activeYear->name}\n\n";

// Get all grades for Bahasa Indonesia
echo "All Grades for Bahasa Indonesia:\n";
$grades = \App\Models\Grade::where('subject_id', $bahasaIndonesia->id)
    ->with('gradeTasks')
    ->get();

foreach ($grades as $grade) {
    echo "  Grade ID: {$grade->id}, Student: {$grade->student_id}, Semester: {$grade->semester}, ";
    echo "AcadYear: " . ($grade->academic_year_id ?? 'NULL') . ", Tasks: {$grade->gradeTasks->count()}\n";
    
    foreach ($grade->gradeTasks as $task) {
        echo "    - Task: {$task->task_name}, Score: {$task->score}\n";
    }
}

echo "\n";

// Query that RecapController would use
echo "Query RecapController uses (Semester Odd, AcadYear {$activeYear->id}):\n";
$recapGrades = \App\Models\Grade::where('subject_id', $bahasaIndonesia->id)
    ->where('semester', 'Odd')
    ->where('academic_year_id', $activeYear->id)
    ->with('gradeTasks')
    ->get();

echo "Found {$recapGrades->count()} grades\n";
foreach ($recapGrades as $grade) {
    echo "  Grade ID: {$grade->id}, Student: {$grade->student_id}, Tasks: {$grade->gradeTasks->count()}\n";
}
