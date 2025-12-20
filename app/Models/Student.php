<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'name',
        'nis',
        'gender',
        'parent_phone',
        'parent_name',
        'birth_place',
        'birth_date'
    ];

    protected $casts = [
        'birth_date' => 'date'
    ];

    /**
     * Relasi ke student_classes (pivot dengan tahun ajaran)
     */
    public function studentClasses(): HasMany
    {
        return $this->hasMany(StudentClass::class);
    }

    /**
     * Relasi ke grades
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Relasi ke grade_tasks
     */
    public function gradeTasks(): HasMany
    {
        return $this->hasMany(GradeTask::class);
    }

    /**
     * Relasi ke notifications
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Helper: Ambil kelas siswa pada tahun ajaran tertentu
     */
    public function getClassByYear($academicYearId)
    {
        return $this->studentClasses()
            ->where('academic_year_id', $academicYearId)
            ->with('classModel')
            ->first();
    }

    /**
     * Helper: Ambil kelas siswa pada tahun ajaran aktif
     */
    public function getCurrentClass()
    {
        return $this->studentClasses()
            ->whereHas('academicYear', function ($q) {
                $q->where('is_active', 1);
            })
            ->with('classModel')
            ->first();
    }

    /**
     * Scope: Filter siswa berdasarkan kelas dan tahun ajaran
     */
    public function scopeByClassAndYear($query, $classId, $academicYearId)
    {
        return $query->whereHas('studentClasses', function ($q) use ($classId, $academicYearId) {
            $q->where('class_id', $classId)
              ->where('academic_year_id', $academicYearId);
        });
    }

    /**
     * Scope: Filter siswa yang terdaftar pada tahun ajaran aktif
     */
    public function scopeActiveYear($query)
    {
        return $query->whereHas('studentClasses', function ($q) {
            $q->whereHas('academicYear', function ($ay) {
                $ay->where('is_active', 1);
            });
        });
    }
}