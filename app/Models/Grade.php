<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'semester',
        'average_written',
        'average_observation',
        'average_sumatif',
        'midterm_score',
        'final_exam_score',
        'final_score',
        'grade_letter',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function gradeTasks()
    {
    return $this->hasMany(GradeTask::class, 'grades_id');
    }
    public function academicYear()
    {
    return $this->belongsTo(AcademicYear::class);
    }

}
