<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nis',
        'gender',
        'parent_name',
        'parent_phone',
        'birth_place',
        'birth_date',
        'class_id',
    ];
    
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
    public function gradeTasks()
    {
        return $this->hasMany(GradeTask::class);
    }
    
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
    
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'student_id');
    }
}
