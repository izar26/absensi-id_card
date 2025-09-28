<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\IdCardController;
use App\Http\Controllers\AttendanceSessionController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\AttendanceExceptionController;

// =======================
// 👨‍🎓 STUDENT ROUTES
// =======================
Route::middleware(['auth', 'verified'])->group(function () {
    // CRUD siswa
    Route::resource('students', StudentController::class)->except(['show']);

    // Cetak per siswa
    Route::get('/students/{student}/idcard', [IdCardController::class, 'perStudent'])
        ->name('students.idcard');

    // Cetak semua siswa (pakai queue + progress bar)
    Route::post('/students/idcard/start-generation', [IdCardController::class, 'startGeneration'])
        ->name('students.idcards.start');
    Route::get('/students/idcard/progress', [IdCardController::class, 'checkProgress'])
        ->name('students.idcards.progress');
    Route::post('/students/idcard/cancel', [IdCardController::class, 'cancelGeneration'])
        ->name('students.idcards.cancel');
});

// =======================
// 📅 ABSENSI
// =======================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('sessions', AttendanceSessionController::class);

    Route::resource('exceptions', AttendanceExceptionController::class)
        ->except(['show', 'edit', 'update']);

    Route::get('/reports/attendance', [AttendanceReportController::class, 'index'])
        ->name('reports.attendance.index');
    Route::post('/reports/attendance/update-status', [AttendanceReportController::class, 'updateStatus'])
        ->name('reports.attendance.updateStatus');
});

// =======================
// DASHBOARD & PROFILE
// =======================
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =======================
// MISC
// =======================
Route::get('/attendance/scan', function () {
    return Inertia::render('Attendance/Scanner');
})->middleware(['auth', 'verified'])->name('attendance.scan');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
});

require __DIR__.'/auth.php';
