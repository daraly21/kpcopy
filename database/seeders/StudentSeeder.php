<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Student::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = \Faker\Factory::create('id_ID');

        for ($classId = 1; $classId <= 8; $classId++) {
            for ($i = 0; $i < 10; $i++) {
                $gender = $faker->randomElement(['L', 'P']);

                $phone = $faker->unique()->numberBetween(100000000, 999999999);
                $phone = '628' . $phone;

                Student::create([
                    'name'         => $gender === 'L'
                        ? $faker->firstNameMale . ' ' . $faker->lastName
                        : $faker->firstNameFemale . ' ' . $faker->lastName,
                    'nis'          => $faker->unique()->numerify('2025####'),
                    'gender'       => $gender,
                    'class_id'     => $classId,
                    'parent_phone' => $phone,
                    'parent_name'  => $faker->name,
                    'birth_place'  => $faker->city,
                    'birth_date'   => $faker->dateTimeBetween('-18 years', '-14 years')->format('Y-m-d'),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }

            $this->command->info("Kelas {$classId} → 10 siswa selesai");
        }

        $this->command->info('SELESAI! 8 kelas × 10 siswa = 80 data siswa dummy sudah masuk.');
    }
}