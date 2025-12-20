<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClassModel extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'name'
    ];

    /**
     * Relasi ke student_classes
     */
    public function studentClasses(): HasMany
    {
        return $this->hasMany(StudentClass::class, 'class_id');
    }

    /**
     * Relasi ke wali kelas (user dengan role_id = 2)
     */
    public function waliKelas(): HasOne
    {
        return $this->hasOne(User::class, 'class_id')->where('role_id', 2);
    }

    /**
     * Helper: Ambil jumlah siswa pada tahun ajaran tertentu
     */
    public function getStudentCount($academicYearId)
    {
        return $this->studentClasses()
            ->where('academic_year_id', $academicYearId)
            ->count();
    }

    /**
     * Helper: Ambil siswa pada tahun ajaran tertentu
     */
    public function getStudentsByYear($academicYearId)
    {
        return Student::byClassAndYear($this->id, $academicYearId)->get();
    }
}