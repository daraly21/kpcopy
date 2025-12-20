<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\GradeTask;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\AcademicYear;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeSeeder extends Seeder
{
    public function run()
    {
        $activeYear = AcademicYear::where('is_active', 1)->first();
        if (!$activeYear) {
            echo "No active academic year found.\n";
            return;
        }

        // Ambil siswa dari kelas 3
        $studentIds = StudentClass::where('class_id', 3)
            ->where('academic_year_id', $activeYear->id)
            ->limit(5)
            ->pluck('student_id');

        if ($studentIds->isEmpty()) {
            echo "Tidak ada siswa di kelas 3 untuk tahun ajaran ini.\n";
            return;
        }

        // Ambil mata pelajaran
        $subjects = Subject::all();

        foreach ($studentIds as $studentId) {
            foreach ($subjects as $subject) {
                // Generate Score
                $score = rand(70, 95);

                // Create Grade (using updateOrCreate to avoid duplicates)
                $grade = Grade::updateOrCreate(
                    [
                        'student_id'       => $studentId,
                        'subject_id'       => $subject->id,
                        'semester'         => 'Odd',
                        'academic_year_id' => $activeYear->id,
                    ],
                    [
                        'average_written'     => $score,
                        'average_observation' => $score - rand(0, 5),
                        'midterm_score'       => $score - rand(0, 5),
                        'final_exam_score'    => $score,
                        'final_score'         => $score,
                        'grade_letter'        => $score >= 85 ? 'A' : 'B',
                        'academic_year_id'    => $activeYear->id, // Redundant but safe
                    ]
                );

                // Create Tasks for this Grade
                GradeTask::create([
                    'student_id'   => $studentId,
                    'subject_id'   => $subject->id,
                    'task_name'    => 'Tugas Harian 1',
                    'type'         => 'written',
                    'grades_id'    => $grade->id,
                    'score'        => $score,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);

                GradeTask::create([
                    'student_id'   => $studentId,
                    'subject_id'   => $subject->id,
                    'task_name'    => 'Tugas Harian 2',
                    'type'         => 'observation',
                    'grades_id'    => $grade->id,
                    'score'        => $score - rand(0, 5),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }
        
        $this->command->info("Seeded grades for " . $studentIds->count() . " students.");
    }
}
