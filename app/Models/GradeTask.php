<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeTask extends Model
{
    use HasFactory;

    protected $fillable = ['subject_id', 'task_name', 'type', 'score', 'student_id' , 'grades_id'];

    // Relasi ke tabel students
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Relasi ke tabel subjects
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class, 'grades_id');
    }
}
