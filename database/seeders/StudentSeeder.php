<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            ['name' => 'Budi Santoso', 'class_id' => 3, 'parent_phone' => '083113355381'],
            ['name' => 'Siti Aminah', 'class_id' => 3, 'parent_phone' => '081462260074'],
            ['name' => 'Ahmad Fauzi', 'class_id' => 3, 'parent_phone' => '081563639791'],
        ];

        foreach ($students as $student) {
            // Konversi nomor HP ke format 62
            $student['parent_phone'] = preg_replace('/^0/', '62', $student['parent_phone']);
            Student::create($student);
        }
    }
}
