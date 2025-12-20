<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== GRADE DETAILS BY SUBJECT ===\n\n";

// Get all subjects
$subjects = \App\Models\Subject::all();
echo "All Subjects:\n";
foreach ($subjects as $subject) {
    echo "  ID: {$subject->id}, Name: {$subject->name}\n";
}
echo "\n";

// Get all grades grouped by subject
echo "Grades by Subject (Latest 10):\n";
$grades = \App\Models\Grade::with('subject')
    ->latest()
    ->take(10)
    ->get();

foreach ($grades as $grade) {
    $subjectName = $grade->subject ? $grade->subject->name : 'NO SUBJECT';
    echo "  Grade ID: {$grade->id}, Subject: {$subjectName} (ID: {$grade->subject_id}), ";
    echo "Student: {$grade->student_id}, Semester: {$grade->semester}, ";
    echo "AcadYear: " . ($grade->academic_year_id ?? 'NULL') . "\n";
}
echo "\n";

// Check grade tasks
echo "Grade Tasks (Latest 10):\n";
$tasks = \App\Models\GradeTask::with('subject')
    ->latest()
    ->take(10)
    ->get();

foreach ($tasks as $task) {
    $subjectName = $task->subject ? $task->subject->name : 'NO SUBJECT';
    echo "  Task: {$task->task_name}, Subject: {$subjectName} (ID: {$task->subject_id}), ";
    echo "Student: {$task->student_id}, Grade ID: {$task->grades_id}\n";
}
