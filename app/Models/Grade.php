<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'subject_id', 'final_score'];

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
}
