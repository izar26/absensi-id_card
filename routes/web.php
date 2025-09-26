<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Student;
use App\Http\Controllers\IdCardController;
use App\Http\Controllers\AttendanceSessionController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\AttendanceExceptionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;

Route::get('/students', [StudentController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('students.index');
Route::get('/students/print-all-id-cards', [IdCardController::class, 'generateAllIdCards'])
    ->name('students.idcard.all')
    ->middleware(['auth', 'verified']);
Route::resource('exceptions', AttendanceExceptionController::class)->except(['show', 'edit', 'update'])->middleware(['auth', 'verified']);
Route::post('/reports/attendance/update-status', [AttendanceReportController::class, 'updateStatus'])
    ->name('reports.attendance.updateStatus')
    ->middleware(['auth', 'verified']);
Route::get('/reports/attendance', [AttendanceReportController::class, 'index'])
    ->name('reports.attendance.index')
    ->middleware(['auth', 'verified']);
Route::resource('sessions', AttendanceSessionController::class)->middleware(['auth', 'verified']);
Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update')->middleware('auth');
Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy')->middleware('auth');
// RUTE INI KHUSUS UNTUK MEMANGGIL CONTROLLER PDF
Route::get('/students/{student}/id-card', [IdCardController::class, 'generateIdCard'])
    ->name('students.idcard')
    ->middleware('auth');
Route::get('/attendance/scan', function () {
    return Inertia::render('Attendance/Scanner');
})->middleware(['auth', 'verified'])->name('attendance.scan');
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
