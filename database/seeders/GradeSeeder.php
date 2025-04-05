<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeSeeder extends Seeder
{
    public function run()
    {
        // Ambil 3 siswa dari kelas 3
        $students = DB::table('students')->where('class_id', 3)->limit(3)->get();

        if ($students->count() < 3) {
            echo "Tidak cukup siswa di kelas 3. Tambahkan siswa terlebih dahulu.\n";
            return;
        }

        $subjects = ['Matematika', 'IPA', 'Bahasa Indonesia'];

        foreach ($students as $index => $student) {
            DB::table('grades')->insert([
                'student_id'  => $student->id,
                'subject'     => $subjects[$index],
                'task_avg'    => rand(70, 90),
                'mid_exam'    => rand(70, 90),
                'final_exam'  => rand(70, 90),
                'final_score' => round((rand(70, 90) * 0.4) + (rand(70, 90) * 0.3) + (rand(70, 90) * 0.3)), // Perhitungan contoh
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }
}
