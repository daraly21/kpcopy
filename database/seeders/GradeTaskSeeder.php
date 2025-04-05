<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeTaskSeeder extends Seeder
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
        $tasks = ['Tugas Harian 1', 'Tugas Harian 2', 'Tugas Harian 3'];

        foreach ($students as $student) {
            foreach ($tasks as $index => $task) {
                DB::table('grade_tasks')->insert([
                    'student_id' => $student->id,
                    'subject'    => $subjects[$index], // Mata pelajaran berbeda
                    'task_name'  => $task, // Nama tugas
                    'score'      => rand(70, 100), // Nilai tugas acak
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
