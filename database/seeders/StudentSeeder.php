<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\ClassModel;
use App\Models\AcademicYear;
use App\Models\StudentClass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Student::truncate();
        StudentClass::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = \Faker\Factory::create('id_ID');

        // Get Active Academic Year
        $year = AcademicYear::where('is_active', 1)->first();

        if (!$year) {
            $this->command->error("No active academic year found! Run AcademicYearSeeder first.");
            return;
        }

        // Ensure we have classes
        $classes = ClassModel::all();
        if ($classes->isEmpty()) {
            $this->command->warn('No classes found. Please run ClassSeeder first.');
            return;
        }

        $this->command->info("Seeding for Academic Year: {$year->name}");
        
        foreach ($classes as $class) {
            for ($i = 0; $i < 10; $i++) {
                $gender = $faker->randomElement(['L', 'P']);
                $phone = '628' . $faker->unique()->numberBetween(100000000, 999999999);

                // Create Student
                $student = Student::create([
                    'name'         => $gender === 'L'
                        ? $faker->firstNameMale . ' ' . $faker->lastName
                        : $faker->firstNameFemale . ' ' . $faker->lastName,
                    'nis'          => $faker->unique()->numerify('2024####'), // Changed to 2024 pref
                    'gender'       => $gender,
                    'parent_phone' => $phone,
                    'parent_name'  => $faker->name,
                    'birth_place'  => $faker->city,
                    'birth_date'   => $faker->dateTimeBetween('-12 years', '-7 years')->format('Y-m-d'),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);

                // Link to Class and Year
                StudentClass::create([
                    'student_id' => $student->id,
                    'class_id' => $class->id,
                    'academic_year_id' => $year->id
                ]);
            }
            $this->command->info("  - Class {$class->name}: 10 students seeded.");
        }

        $totalStudents = Student::count();
        $this->command->info("SELESAI! Total {$totalStudents} data siswa dummy berhasil dibuat untuk tahun {$year->name}.");
    }
}