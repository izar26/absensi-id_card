<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Middleware\SyncApiAuth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Endpoint untuk aplikasi sinkronisasi Electron
Route::prefix('')->group(function () {
    // BARU: Route untuk cek koneksi server (tidak perlu auth)
    Route::get('/ping', [StudentController::class, 'ping']);
    
    // Middleware group untuk endpoint yang butuh token
    Route::middleware(SyncApiAuth::class)->group(function () {
        Route::post('/siswa/sync', [StudentController::class, 'sync']);
        // BARU: Route untuk validasi token (perlu auth)
        Route::get('/validate-token', [StudentController::class, 'validateToken']);
        
        // Letakkan route sync lain di sini jika ada (misal: /rombel/sync, /gtk/sync, dll)
    });
});


// Endpoint lain untuk aplikasi Anda (jika ada)
Route::apiResource('students', StudentController::class)->names('api.students');
Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');