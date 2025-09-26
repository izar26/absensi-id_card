<?php

namespace App\Http\Controllers;

use App\Models\AttendanceException;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AttendanceExceptionController extends Controller
{
    public function index()
    {
        return Inertia::render('Attendance/Exceptions/Index', [
            'exceptions' => AttendanceException::with('student')
                                ->latest('exception_date')
                                ->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Attendance/Exceptions/Create', [
            'students' => Student::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'exception_date' => 'required|date',
            'reason' => 'required|string|max:255',
        ]);

        AttendanceException::create($request->all());

        return to_route('exceptions.index')->with('message', 'Pengecualian berhasil ditambahkan.');
    }

    public function destroy(AttendanceException $exception)
    {
        $exception->delete();
        return to_route('exceptions.index')->with('message', 'Pengecualian berhasil dihapus.');
    }
}