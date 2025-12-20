<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentClass extends Model
{
    protected $fillable = [
        'student_id',
        'class_id',
        'academic_year_id'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function classModel(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Scope untuk filter berdasarkan tahun ajaran aktif
     */
    public function scopeActiveYear($query)
    {
        return $query->whereHas('academicYear', function ($q) {
            $q->where('is_active', 1);
        });
    }

    /**
     * Scope untuk filter berdasarkan tahun ajaran tertentu
     */
    public function scopeByAcademicYear($query, $yearId)
    {
        return $query->where('academic_year_id', $yearId);
    }
}