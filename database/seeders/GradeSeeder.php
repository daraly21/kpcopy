<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Grade;

class GradeSeeder extends Seeder
{
    public function run(): void
    {
        Grade::create([
            'student_id' => 2,
            'subject_id' => 5,
            'semester' => 'Odd',
            'average_written' => 71.75,
            'average_observation' => 76.50,
            'average_homework' => null,
            'midterm_score' => 56.00,
            'final_exam_score' => 77.00,
            'final_score' => 70.31,
            'grade_letter' => null,
            'academic_year_id' => 2,
        ]);

        Grade::create([
            'student_id' => 2,
            'subject_id' => 1,
            'semester' => 'Odd',
            'average_written' => 77.00,
            'average_observation' => null,
            'average_homework' => null,
            'midterm_score' => null,
            'final_exam_score' => null,
            'final_score' => 77.00,
            'grade_letter' => null,
            'academic_year_id' => 2,
        ]);

        Grade::create([
            'student_id' => 1,
            'subject_id' => 7,
            'semester' => 'Odd',
            'average_written' => null,
            'average_observation' => null,
            'average_homework' => null,
            'midterm_score' => null,
            'final_exam_score' => null,
            'final_score' => 0.00,
            'grade_letter' => null,
            'academic_year_id' => 2,
        ]);

        Grade::create([
            'student_id' => 2,
            'subject_id' => 2,
            'semester' => 'Odd',
            'average_written' => 77.00,
            'average_observation' => 88.00,
            'average_homework' => null,
            'midterm_score' => null,
            'final_exam_score' => null,
            'final_score' => 82.50,
            'grade_letter' => null,
            'academic_year_id' => 2,
        ]);

        Grade::create([
            'student_id' => 2,
            'subject_id' => 4,
            'semester' => 'Odd',
            'average_written' => 55.00,
            'average_observation' => null,
            'average_homework' => null,
            'midterm_score' => null,
            'final_exam_score' => null,
            'final_score' => 55.00,
            'grade_letter' => null,
            'academic_year_id' => 2,
        ]);

        Grade::create([
            'student_id' => 2,
            'subject_id' => 7,
            'semester' => 'Odd',
            'average_written' => 99.00,
            'average_observation' => 69.00,
            'average_homework' => null,
            'midterm_score' => null,
            'final_exam_score' => null,
            'final_score' => 84.00,
            'grade_letter' => null,
            'academic_year_id' => 2,
        ]);

    }
}
