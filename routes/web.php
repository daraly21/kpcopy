<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePictureController;
use App\Http\Controllers\RapotController;
use App\Http\Controllers\RecapController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherGradeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaliKelasStudentController;

// =====================
// Halaman Awal (Welcome)
// =====================
Route::get('/', function () {
    return view('auth.login');
});

// =====================
// DASHBOARD (Gabungan Admin, Wali Kelas, Guru Mata Pelajaran)
// =====================
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// =====================
// ADMIN ONLY
// =====================
Route::middleware(['auth', 'role:Admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('kelas', ClassController::class);
        Route::resource('teachers', TeacherController::class);
        Route::resource('mapel', SubjectController::class)->except(['create', 'edit', 'show']);
        Route::get('siswa', [StudentController::class, 'pilihKelas'])->name('siswa.kelas');
        Route::get('siswa/kelas/{class}', [StudentController::class, 'index'])->name('siswa.index');
        Route::get('siswa/list/{class}', [StudentController::class, 'list'])->name('siswa.list');
        Route::resource('siswa', StudentController::class)
            ->parameters(['siswa' => 'student'])
            ->except(['index'])
            ->names([
                'create' => 'siswa.create',
                'store' => 'siswa.store',
                'show' => 'siswa.show',
                'edit' => 'siswa.edit',
                'update' => 'siswa.update',
                'destroy' => 'siswa.destroy',
            ]);
    });

// =====================
// WALI KELAS & GURU MATA PELAJARAN
// =====================
Route::middleware(['auth'])->group(function () {
    // Rute untuk input nilai (memerlukan permission 'kelola nilai')
    Route::middleware(['permission:kelola nilai'])->group(function () {
        Route::get('grades/create/', [GradeController::class, 'create'])->name('grades.create');
        Route::post('grades/store/', [GradeController::class, 'store'])->name('grades.store');
        Route::post('grades/batch', [GradeController::class, 'store_batch'])->name('grades.store_batch');
        Route::put('grade-tasks/{id}', [GradeController::class, 'update'])->name('grade_tasks.update');
        Route::delete('grade-tasks/{id}', [GradeController::class, 'destroy'])->name('grade_tasks.destroy');
    
        Route::get('rekap-nilai', [GradeController::class, 'index'])->name('grades.list');
        Route::get('grades/rekap/', [GradeController::class, 'rekap'])->name('grades.rekap');
        Route::post('grades/export', [GradeController::class, 'export'])->name('grades.export');
        Route::get('/grades/export', [RecapController::class, 'index'])->name('grades.export.index');
        Route::post('/grades/generate-export', [RecapController::class, 'generateExport'])->name('grades.generate-export');
   
    });

   
    

    // Rute untuk notifikasi (memerlukan permission 'kirim notifikasi orang tua')
    Route::middleware(['permission:kirim notifikasi orang tua'])->group(function () {
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/send', [NotificationController::class, 'sendNotification'])->name('notifications.send');
        Route::post('notifications/reset', [NotificationController::class, 'resetNotificationStatus'])->name('notifications.reset');
    });
});

// =====================
// WALI KELAS ONLY
// =====================
Route::middleware(['auth', 'role:Wali Kelas'])->group(function () {
    Route::get('rapor', [RapotController::class, 'index'])->name('rapor.index');
    Route::get('kelas/{classId}/students', [WaliKelasStudentController::class, 'index'])->name('walikelas.students.index');
    Route::put('siswa/{student}/update-phone', [WaliKelasStudentController::class, 'updateParentPhone'])->name('students.update-phone');
});


// Route::middleware(['auth', 'role:Guru Mata Pelajaran', 'permission:kelola nilai'])->group(function () {
//     // Teacher grade management routes

//     Route::get('teacher/grades/select-class', [TeacherGradeController::class, 'selectClass'])->name('teacher.grades.select-class');
//     Route::get('teacher/grades/{subjectId}/create', [TeacherGradeController::class, 'create'])
//         ->name('teacher.grades.create');
//     Route::post('teacher/grades/{subjectId}', [TeacherGradeController::class, 'store'])
//         ->name('teacher.grades.store');
//     Route::get('teacher/grades/{subjectId}', [TeacherGradeController::class, 'index'])->name('teacher.grades.index');

//     Route::post('teacher/grades/batch', [TeacherGradeController::class, 'storeBatch'])->name('teacher.grades.store-batch');
//     Route::put('teacher/grades/{gradeId}', [TeacherGradeController::class, 'update'])->name('teacher.grades.update');
//     Route::delete('teacher/grades/{gradeId}', [TeacherGradeController::class, 'destroy'])->name('teacher.grades.destroy');
// });

Route::middleware(['auth'])->prefix('teacher')->name('teacher.')->group(function () {
    
    // Teacher Grades Management Routes
    Route::prefix('grades')->name('grades.')->group(function () {
        // Select class page
        Route::get('/select-class', [TeacherGradeController::class, 'selectClass'])->name('select-class');
        
        // Create grades page
        Route::get('/{subjectId}/create', [TeacherGradeController::class, 'create'])->name('create');
        
        // Store batch grades (untuk form create)
        Route::post('/store-batch', [TeacherGradeController::class, 'storeBatch'])->name('store-batch');
        
        // Main grades index page
        Route::get('/{subjectId}', [TeacherGradeController::class, 'index'])->name('index');
        
        // Individual grade operations (AJAX)
        Route::post('/{subjectId}/store', [TeacherGradeController::class, 'store'])->name('store');
        Route::put('/{id}', [TeacherGradeController::class, 'update'])->name('update');
        Route::delete('/{id}', [TeacherGradeController::class, 'destroy'])->name('destroy');
        
        // Get student grades for modal
        Route::get('/student/{studentId}/grades', [TeacherGradeController::class, 'getStudentGrades'])->name('student.grades');
    });
});

// =====================
// PROFILE (Semua User Login)
// =====================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/picture/edit', [ProfilePictureController::class, 'edit'])->name('profile.picture.edit');
    Route::put('/profile/picture/update', [ProfilePictureController::class, 'update'])->name('profile.picture.update');
});

// 🔐 Otentikasi
require __DIR__ . '/auth.php';