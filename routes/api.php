<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\AttendanceController;

Route::post('/siswa/sync', [StudentController::class, 'sync']);
Route::apiResource('students', StudentController::class)->names('api.students');
Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
