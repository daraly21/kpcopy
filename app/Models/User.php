<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
    'name',
    'nip',
    'nuptk', 
    'email',
    'password',
    'role_id',
    'class_id',
    'subject_id',
    'profile_picture',
    ];
    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
       public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_id');
    }

    
        public function getAssignedSubjects()
    {
        if ($this->hasRole('Guru Mata Pelajaran') && $this->subject_id) {
            return Subject::where('id', $this->subject_id)->get();
        }
        return Subject::all(); // Admin dan Wali Kelas akses semua
    }
}

