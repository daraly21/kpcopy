<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CLEANUP EMPTY GRADES ===\n\n";

// Find grades with no tasks
$emptyGrades = \App\Models\Grade::doesntHave('gradeTasks')->get();

echo "Found " . $emptyGrades->count() . " empty grades (no tasks).\n";

if ($emptyGrades->count() > 0) {
    echo "Deleting...\n";
    foreach ($emptyGrades as $grade) {
        $subjectName = $grade->subject ? $grade->subject->name : 'Unknown Subject';
        echo "  Deleting Grade ID {$grade->id} (Student: {$grade->student_id}, Subject: {$subjectName}, Semester: {$grade->semester})\n";
        $grade->delete();
    }
    echo "Deletion complete.\n";
} else {
    echo "No empty grades found to clean up.\n";
}

echo "\nChecking status for Student 2, Subject 2 (Bahasa Indonesia)...\n";
$biGrades = \App\Models\Grade::where('student_id', 2)
    ->where('subject_id', 2)
    ->with('gradeTasks')
    ->get();

foreach ($biGrades as $g) {
    echo "Grade ID: {$g->id}, Tasks: {$g->gradeTasks->count()}\n";
}
