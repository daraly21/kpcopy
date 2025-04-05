<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\GradeReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePictureController;

Route::get('/', function () {
    return view('welcome');
});

// Login & Logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Gabungan (Admin & Wali Kelas)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// =====================
// 👑 ADMIN ONLY
// =====================

Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
});

// Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
//     Route::get('/users', [UserController::class, 'index'])->name('users.index');
//     Route::post('/users', [UserController::class, 'store'])->name('users.store');
//     Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
//     Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
//     Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
// });
// =====================
// 🧑‍🏫 WALI KELAS ONLY
// =====================
Route::middleware(['auth', 'role:Wali Kelas'])->group(function () {
    // Input Nilai
    Route::get('grades/create', [GradeController::class, 'create'])->name('grades.create');
    Route::post('grades/store', [GradeController::class, 'store'])->name('grades.store');
    Route::post('/grades/batch', [GradeController::class, 'store_batch'])->name('grades.store_batch');
    Route::put('/grade-tasks/{id}', [GradeController::class, 'update'])->name('grade_tasks.update');
    Route::delete('/grade-tasks/{id}', [GradeController::class, 'destroy'])->name('grade_tasks.destroy');
    // Rekap Nilai
    Route::get('/rekap-nilai', [GradeReportController::class, 'index'])->name('grades.recap');
    Route::post('/grades/export', [GradeReportController::class, 'export'])->name('grades.export');

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/send', [NotificationController::class, 'sendNotification'])->name('notifications.send');
    Route::post('/notifications/reset', [NotificationController::class, 'resetNotificationStatus'])->name('notifications.reset');
});

// =====================
// 🔐 PROFILE (untuk semua user login)
// =====================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Ubah Foto Profil
    Route::get('/profile/picture/edit', [ProfilePictureController::class, 'edit'])->name('profile.picture.edit');
    Route::put('/profile/picture/update', [ProfilePictureController::class, 'update'])->name('profile.picture.update');
});

// Otentikasi bawaan Laravel Breeze/Fortify
require __DIR__.'/auth.php';
