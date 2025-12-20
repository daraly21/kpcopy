<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== STUDENT & CLASS DEBUG ===\n\n";

// Check Student 1
$student = \App\Models\Student::find(1);
echo "Student 1: " . ($student ? $student->name : 'NOT FOUND') . "\n";

// Check Active Year
$activeYear = \App\Models\AcademicYear::where('is_active', 1)->first();
echo "Active Year: " . ($activeYear ? $activeYear->name . " (ID: {$activeYear->id})" : 'NONE') . "\n";

// Check Student Class for Active Year
$studentClass = \App\Models\StudentClass::where('student_id', 1)
    ->where('academic_year_id', $activeYear->id)
    ->with('classModel')
    ->first();

if ($studentClass) {
    echo "Student 1 is in Class: {$studentClass->classModel->name} (ID: {$studentClass->class_id})\n";
} else {
    echo "Student 1 is NOT enrolled in any class for the active academic year!\n";
}

// Check Users (Wali Kelas) and their classes
echo "\nWali Kelas Users:\n";
$users = \App\Models\User::where('role_id', 2)->get(); // Assuming role 2 is Wali Kelas
foreach ($users as $user) {
    echo "  User: {$user->name}, Assigned Class ID: {$user->class_id}\n";
}
