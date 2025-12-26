<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GradeTask;

class GradeTaskSeeder extends Seeder
{
    public function run(): void
    {
        GradeTask::create([
            'student_id' => 2,
            'subject_id' => 5,
            'task_name' => 'Nilai Harian 1',
            'type' => 'written',
            'grades_id' => 1,
            'score' => 99.00,
        ]);

        GradeTask::create([
            'student_id' => 2,
            'subject_id' => 5,
            'task_name' => 'Jalan Sehat',
            'type' => 'observation',
            'grades_id' => 1,
            'score' => 88.00,
        ]);

        GradeTask::create([
            'student_id' => 2,
            'subject_id' => 5,
            'task_name' => 'UTS',
            'type' => 'sumatif',
            'grades_id' => 1,
            'score' => 56.00,
        ]);

        GradeTask::create([
            'student_id' => 2,
            'subject_id' => 5,
            'task_name' => 'UAS',
            'type' => 'sumatif',
            'grades_id' => 1,
            'score' => 77.00,
        ]);

        GradeTask::create([
            'student_id' => 2,
            'subject_id' => 5,
            'task_name' => 'Nilai Harian 2',
            'type' => 'written',
            'grades_id' => 1,
            'score' => 77.00,
        ]);

        GradeTask::create([
            'student_id' => 2,
            'subject_id' => 5,
            'task_name' => 'Jalan Sehat2',
            'type' => 'observation',
            'grades_id' => 1,
            'score' => 65.00,
        ]);

        GradeTask::create([
            'student_id' => 2,
            'subject_id' => 1,
            'task_name' => 'Pertambahan',
            'type' => 'written',
            'grades_id' => 7,
            'score' => 77.00,
        ]);

        GradeTask::create([
            'student_id' => 1,
            'subject_id' => 7,
            'task_name' => 'Tugas Menculik Ayam2',
            'type' => 'written',
            'grades_id' => 8,
            'score' => 99.00,
        ]);

        GradeTask::create([
            'student_id' => 2,
            'subject_id' => 5,
            'task_name' => 'Sakit Dadaku',
            'type' => 'written',
            'grades_id' => 1,
            'score' => 78.00,
        ]);

        GradeTask::create([
            'student_id' => 2,
            'subject_id' => 5,
            'task_name' => 'Test',
            'type' => 'written',
            'grades_id' => 1,
            'score' => 33.00,
        ]);

        GradeTask::create([
            'student_id' => 2,
            'subject_id' => 4,
            'task_name' => 'testting',
            'type' => 'written',
            'grades_id' => 11,
            'score' => 55.00,
        ]);

        GradeTask::create([
            'student_id' => 2,
            'subject_id' => 2,
            'task_name' => 'Nyanyi',
            'type' => 'observation',
            'grades_id' => 9,
            'score' => 88.00,
        ]);

        GradeTask::create([
            'student_id' => 2,
            'subject_id' => 2,
            'task_name' => 'Nilai Harian 1',
            'type' => 'written',
            'grades_id' => 9,
            'score' => 77.00,
        ]);

        GradeTask::create([
            'student_id' => 2,
            'subject_id' => 7,
            'task_name' => 'Tugas Menculik Ayam2',
            'type' => 'written',
            'grades_id' => 12,
            'score' => 99.00,
        ]);

        GradeTask::create([
            'student_id' => 2,
            'subject_id' => 7,
            'task_name' => 'Nilai Harian 2',
            'type' => 'observation',
            'grades_id' => 12,
            'score' => 69.00,
        ]);

    }
}
