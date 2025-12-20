<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== AGAMA DEBUG ===\n\n";

// Get Agama subject
$subjectName = 'Agama';
$subject = \App\Models\Subject::where('name', 'LIKE', "%{$subjectName}%")->first();
if (!$subject) {
    echo "Subject '{$subjectName}' not found!\n";
    exit;
}

echo "Subject: {$subject->name} (ID: {$subject->id})\n";

// Get active academic year
$activeYear = \App\Models\AcademicYear::where('is_active', 1)->first();
echo "Active Academic Year: ID {$activeYear->id}\n\n";

// Get ALL grades for this subject
echo "All Grades for {$subject->name}:\n";
$grades = \App\Models\Grade::where('subject_id', $subject->id)
    ->with('gradeTasks')
    ->get();

foreach ($grades as $grade) {
    echo "  Grade ID: {$grade->id}, Student: {$grade->student_id}, Semester: {$grade->semester}, ";
    echo "AcadYear: " . ($grade->academic_year_id ?? 'NULL') . ", Tasks: {$grade->gradeTasks->count()}\n";
    
    foreach ($grade->gradeTasks as $task) {
        echo "    - Task: {$task->task_name}, Score: {$task->score}, Created: {$task->created_at}\n";
    }
}
echo "\n";

// Check for duplicates
echo "Checking for duplicates (same student, subject, semester, acad_year):\n";
$duplicates = \App\Models\Grade::select('student_id', 'subject_id', 'semester', 'academic_year_id')
    ->where('subject_id', $subject->id)
    ->where('academic_year_id', $activeYear->id)
    ->groupBy('student_id', 'subject_id', 'semester', 'academic_year_id')
    ->havingRaw('COUNT(*) > 1')
    ->get();

if ($duplicates->count() > 0) {
    echo "Found duplicate sets:\n";
    foreach ($duplicates as $dup) {
        echo "  Student: {$dup->student_id}, Semester: {$dup->semester}\n";
        
        $records = \App\Models\Grade::where('student_id', $dup->student_id)
            ->where('subject_id', $subject->id)
            ->where('semester', $dup->semester)
            ->where('academic_year_id', $activeYear->id)
            ->get();
            
        foreach ($records as $r) {
            echo "    - Grade ID: {$r->id}, Tasks: " . $r->gradeTasks()->count() . "\n";
        }
    }
} else {
    echo "No duplicates found.\n";
}
