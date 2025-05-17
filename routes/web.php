<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\GradeExportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePictureController;
use App\Http\Controllers\RapotController;
use App\Http\Controllers\RecapController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaliKelasStudentController;

// =====================
// 🎉 Halaman Awal (Welcome)
// =====================
Route::get('/', function () {
    return view('welcome');
});

// =====================
// 📊 DASHBOARD (Gabungan Admin & Wali Kelas)
// =====================
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// =====================
// 👑 ADMIN ONLY
// =====================
Route::middleware(['auth', 'role:Admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('kelas', ClassController::class);
        // Halaman awal → pilih kelas dulu
        Route::get('siswa', [StudentController::class, 'pilihKelas'])->name('siswa.kelas');
        // Daftar siswa berdasarkan kelas (index view)
        Route::get('siswa/kelas/{class}', [StudentController::class, 'index'])->name('siswa.index');
        // Untuk tampilan tabel siswa per kelas
        Route::get('siswa/list/{class}', [StudentController::class, 'list'])->name('siswa.list');
        // Resource siswa (tanpa index karena ditangani khusus per kelas)
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
// 🧑‍🏫 WALI KELAS ONLY
// =====================
Route::middleware(['auth', 'role:Wali Kelas'])->group(function () {

    Route::get('rekap-nilai', [GradeController::class, 'index'])->name('grades.list');
    Route::post('grades/export', [GradeController::class, 'export'])->name('grades.export');
    Route::get('grades/create', [GradeController::class, 'create'])->name('grades.create');
    Route::post('grades/store', [GradeController::class, 'store'])->name('grades.store');
    Route::post('grades/batch', [GradeController::class, 'store_batch'])->name('grades.store_batch');
    Route::put('grade-tasks/{id}', [GradeController::class, 'update'])->name('grade_tasks.update');
    Route::delete('grade-tasks/{id}', [GradeController::class, 'destroy'])->name('grade_tasks.destroy');

    Route::get('rapor', [RapotController::class, 'index'])->name('rapor.index');
    // 🔔 Notifikasi
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/send', [NotificationController::class, 'sendNotification'])->name('notifications.send');
    Route::post('notifications/reset', [NotificationController::class, 'resetNotificationStatus'])->name('notifications.reset');

    // Expirt Nilai
    Route::get('/grades/export', [RecapController::class, 'index'])->name('grades.export');
    Route::post('/grades/generate-export', [RecapController::class, 'generateExport'])->name('grades.generate-export');
    
   // 📱 Manajemen Data Siswa (tambahan baru)
   Route::get('kelas/{classId}/students', [WaliKelasStudentController::class, 'index'])->name('walikelas.students.index');
   Route::put('siswa/{student}/update-phone', [WaliKelasStudentController::class, 'updateParentPhone'])->name('students.update-phone');
});

// =====================
// 🔐 PROFILE (Semua User Login)
// =====================
Route::middleware('auth')->group(function () {
    // Pengaturan Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Ubah Foto Profil
    Route::get('/profile/picture/edit', [ProfilePictureController::class, 'edit'])->name('profile.picture.edit');
    Route::put('/profile/picture/update', [ProfilePictureController::class, 'update'])->name('profile.picture.update');
});

// 🔐 Otentikasi (Breeze / Fortify)
require __DIR__ . '/auth.php';
