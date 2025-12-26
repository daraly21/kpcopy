<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG: Academic Years & Classes ===\n\n";

$years = \App\Models\AcademicYear::orderBy('id')->get();

foreach ($years as $year) {
    $classCount = $year->classes()->count();
    $active = $year->is_active ? 'ACTIVE' : 'inactive';
    
    echo "ID: {$year->id} | Name: {$year->name} | Status: {$active} | Classes: {$classCount}\n";
    
    if ($classCount > 0) {
        $classes = $year->classes;
        foreach ($classes as $class) {
            echo "  - {$class->name} (ID: {$class->id})\n";
        }
    }
    echo "\n";
}

echo "Total classes in database: " . \App\Models\ClassModel::count() . "\n";
