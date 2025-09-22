<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Student;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\IdCardController;

Route::get('/students', function () {
    return Inertia::render('Students/Index', [
        'students' => Student::latest()->get()
    ]);
})->middleware(['auth', 'verified'])->name('students.page');
Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update')->middleware('auth');
Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy')->middleware('auth');
// RUTE INI KHUSUS UNTUK MEMANGGIL CONTROLLER PDF
Route::get('/students/{student}/id-card', [IdCardController::class, 'generateIdCard'])
    ->name('students.idcard')
    ->middleware('auth');
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
