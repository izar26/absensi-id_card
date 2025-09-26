<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceSessionController extends Controller
{
    // Menampilkan halaman daftar sesi
    public function index()
    {
        return Inertia::render('Attendance/Sessions/Index', [
            'sessions' => AttendanceSession::latest('session_date')->get(),
        ]);
    }

    // Menampilkan halaman form untuk membuat sesi baru
    public function create()
    {
        return Inertia::render('Attendance/Sessions/Create');
    }

    // Menyimpan sesi baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'session_date' => 'required|date|unique:attendance_sessions,session_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'nullable|string|max:255',
        ]);

        AttendanceSession::create($request->all());

        return to_route('sessions.index')->with('message', 'Sesi absensi berhasil dibuat.');
    }

    // Menampilkan halaman form untuk mengedit sesi
    public function edit(AttendanceSession $session)
    {
        return Inertia::render('Attendance/Sessions/Edit', [
            'session' => $session,
        ]);
    }

    // Memperbarui data sesi di database
    public function update(Request $request, AttendanceSession $session)
    {
        $request->validate([
            'session_date' => 'required|date|unique:attendance_sessions,session_date,' . $session->id,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'nullable|string|max:255',
        ]);

        $session->update($request->all());

        return to_route('sessions.index')->with('message', 'Sesi absensi berhasil diperbarui.');
    }

    // Menghapus sesi dari database
    public function destroy(AttendanceSession $session)
    {
        $session->delete();

        return to_route('sessions.index')->with('message', 'Sesi absensi berhasil dihapus.');
    }
}