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

// Tambahan controller baru
use App\Http\Controllers\AcademicYearController;

use App\Http\Controllers\StudentClassController;


// =====================
// Halaman Awal (Welcome)
// =====================
Route::get('/', function () {
    return view('auth.login');
});

// =====================
// DASHBOARD
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

        // === CRUD User / Guru / Mapel / Siswa ===
        Route::resource('users', UserController::class);
        Route::resource('kelas', ClassController::class);
        Route::resource('teachers', TeacherController::class);
        Route::resource('mapel', SubjectController::class)->except(['create', 'edit', 'show']);

         // === SISWA ===
        Route::get('siswa', [StudentController::class, 'pilihKelas'])->name('siswa.kelas');
        Route::get('siswa/kelas/{class}', [StudentController::class, 'list'])->name('siswa.list');
        
        Route::post('siswa', [StudentController::class, 'store'])->name('siswa.store');
        Route::put('siswa/{student}', [StudentController::class, 'update'])->name('siswa.update');
        Route::delete('siswa/{student}', [StudentController::class, 'destroy'])->name('siswa.destroy');
        
        // Siswa - Pindah Kelas Manual

        
        Route::get('siswa/kelas/{class}/promotion', [StudentController::class, 'promotion'])->name('siswa.promotion');
        Route::post('siswa/promotion', [StudentController::class, 'processPromotion'])->name('siswa.process-promotion');
        Route::post('siswa/promote-all', [StudentController::class, 'promoteAllClasses'])->name('siswa.promote-all');
            


        // =======================
        // 📌 TAHUN AJARAN (BARU)
        // =======================
        Route::prefix('academic-years')->name('academic-years.')->group(function () {

            Route::get('/', [AcademicYearController::class, 'index'])
                ->name('index');

            Route::post('/store', [AcademicYearController::class, 'store'])
                ->name('store');

            Route::put('/{id}', [AcademicYearController::class, 'update'])
                ->name('update');

            Route::post('/{id}/set-active', [AcademicYearController::class, 'setActive'])
                ->name('setActive');

            Route::delete('/{id}', [AcademicYearController::class, 'destroy'])
                ->name('destroy');
        });


        // =======================
        // 📌 PESERTA KELAS / ROMBEL (BARU)
        // =======================
        Route::prefix('peserta-kelas')->name('peserta-kelas.')->group(function () {

            // Daftar kelas dan jumlah siswa per tahun ajaran aktif
            Route::get('/', [StudentClassController::class, 'index'])->name('index');

            // Lihat siswa dalam kelas tertentu
            Route::get('/kelas/{classId}', [StudentClassController::class, 'show'])
                ->name('show');

            // Tambah siswa ke kelas
            Route::post('/add', [StudentClassController::class, 'addStudent'])
                ->name('add');

            // Hapus siswa dari kelas
            Route::delete('/remove/{id}', [StudentClassController::class, 'removeStudent'])
                ->name('remove');

            // Naik kelas otomatis
            Route::post('/promote', [StudentClassController::class, 'promoteStudents'])
                ->name('promote');
        });

    });

// =====================
// WALI KELAS & GURU MAPEL
// =====================
Route::middleware(['auth'])->group(function () {

    // === Input Nilai ===
    Route::middleware(['permission:kelola nilai'])->group(function () {

        Route::get('grades/create/', [GradeController::class, 'create'])->name('grades.create');
        Route::post('grades/store/', [GradeController::class, 'store'])->name('grades.store');
        Route::post('grades/batch', [GradeController::class, 'store'])->name('grades.store_batch');
        Route::put('grade-tasks/{id}', [GradeController::class, 'update'])->name('grade_tasks.update');
        Route::delete('grade-tasks/{id}', [GradeController::class, 'destroy'])->name('grade_tasks.destroy');

        Route::get('rekap-nilai', [GradeController::class, 'index'])->name('grades.list');
        Route::get('grades/rekap/', [GradeController::class, 'rekap'])->name('grades.rekap');
        Route::post('grades/export', [GradeController::class, 'export'])->name('grades.export');
        Route::get('/grades/export', [RecapController::class, 'index'])->name('grades.export.index');
        Route::post('/grades/generate-export', [RecapController::class, 'generateExport'])->name('grades.generate-export');
    });


    // === Notifikasi ===
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

// =====================
// GURU MAPEL — TEACHER
// =====================
Route::middleware(['auth'])->prefix('teacher')->name('teacher.')->group(function () {

    Route::prefix('grades')->name('grades.')->group(function () {

        Route::get('/select-class', [TeacherGradeController::class, 'selectClass'])->name('select-class');
        Route::get('/{subjectId}/create', [TeacherGradeController::class, 'create'])->name('create');
        Route::post('/store-batch', [TeacherGradeController::class, 'store'])->name('store-batch');
        Route::get('/{subjectId}', [TeacherGradeController::class, 'index'])->name('index');
        Route::post('/{subjectId}/store', [TeacherGradeController::class, 'store'])->name('store');
        Route::put('/{id}', [TeacherGradeController::class, 'update'])->name('update');
        Route::delete('/{id}', [TeacherGradeController::class, 'destroy'])->name('destroy');
        Route::get('/student/{studentId}/grades', [TeacherGradeController::class, 'getStudentGrades'])->name('student.grades');

    });
});

// =====================
// PROFILE
// =====================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::put('/profile/picture/update', [ProfilePictureController::class, 'update'])->name('profile.picture.update');
    Route::get('/profile/picture/{userId?}', [ProfilePictureController::class, 'show'])->name('profile.picture.show');
    Route::delete('/profile/picture', [ProfilePictureController::class, 'destroy'])->name('profile.picture.destroy');
});

// 🔐 Otentikasi
require __DIR__ . '/auth.php';
