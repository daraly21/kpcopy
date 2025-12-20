<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ACADEMIC YEAR DEBUG ===\n\n";

// Check active academic year
$activeYear = \App\Models\AcademicYear::where('is_active', 1)->first();
if ($activeYear) {
    echo "Active Academic Year:\n";
    echo "  ID: {$activeYear->id}\n";
    echo "  Name: {$activeYear->name}\n\n";
} else {
    echo "NO ACTIVE ACADEMIC YEAR!\n\n";
}

// Check all academic years
echo "All Academic Years:\n";
$allYears = \App\Models\AcademicYear::all();
foreach ($allYears as $year) {
    echo "  ID: {$year->id}, Name: {$year->name}, Active: " . ($year->is_active ? 'YES' : 'NO') . "\n";
}
echo "\n";

// Check latest 5 grades
echo "Latest 5 Grades:\n";
$grades = \App\Models\Grade::latest()->take(5)->get();
foreach ($grades as $grade) {
    echo "  Grade ID: {$grade->id}, Student: {$grade->student_id}, Subject: {$grade->subject_id}, ";
    echo "Semester: {$grade->semester}, AcadYear: " . ($grade->academic_year_id ?? 'NULL') . "\n";
}
echo "\n";

// Check latest 5 grade tasks
echo "Latest 5 Grade Tasks:\n";
$tasks = \App\Models\GradeTask::latest()->take(5)->get();
foreach ($tasks as $task) {
    echo "  Task ID: {$task->id}, Name: {$task->task_name}, Student: {$task->student_id}, ";
    echo "Subject: {$task->subject_id}, Grade ID: {$task->grades_id}\n";
}
