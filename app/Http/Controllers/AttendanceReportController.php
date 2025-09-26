<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AttendanceReportController extends Controller
{
    /**
     * Menampilkan halaman laporan absensi.
     */
    public function index(Request $request)
    {
        // 1. Ambil tanggal dari filter, jika tidak ada, gunakan tanggal hari ini
        $filterDate = $request->input('date', Carbon::today()->toDateString());

        // 2. Ambil semua siswa, urutkan berdasarkan nama
        $students = Student::orderBy('name')->get();

        // 3. Ambil data absensi HANYA untuk tanggal yang dipilih
        // Gunakan keyBy('student_id') agar mudah dicari
        $attendances = Attendance::whereDate('created_at', $filterDate)
                                ->get()
                                ->keyBy('student_id');

        // 4. Gabungkan data siswa dengan data absensi
        $reportData = $students->map(function ($student) use ($attendances) {
            $attendanceRecord = $attendances->get($student->id);

            return [
                'id' => $student->id,
                'name' => $student->name,
                'class' => $student->class,
                'status' => $attendanceRecord ? $attendanceRecord->status : 'Alfa', // Jika tidak ada catatan, anggap Alfa
                'scan_time' => $attendanceRecord ? Carbon::parse($attendanceRecord->created_at)->format('H:i:s') : '-',
            ];
        });

        // 5. Kirim data yang sudah diolah ke halaman Vue
        return Inertia::render('Attendance/Reports/Index', [
            'reportData' => $reportData,
            'filterDate' => $filterDate, // Kirim tanggal filter agar bisa ditampilkan di input
        ]);
    }

    public function updateStatus(Request $request)
    {
        // 1. Validasi data yang masuk
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'status' => 'required|string|in:Hadir,Terlambat,Izin,Sakit,Alfa',
        ]);

        // 2. Gunakan updateOrCreate untuk efisiensi
        // - Cari record berdasarkan student_id dan tanggal.
        // - Jika ditemukan, update statusnya.
        // - Jika tidak ditemukan (awalnya Alfa), buat record baru dengan status tersebut.
        Attendance::updateOrCreate(
            [
                'student_id' => $request->student_id,
                // Mencocokkan hanya berdasarkan tanggal, tanpa memperhatikan waktu
                'created_at' => Carbon::parse($request->date)->startOfDay()
            ],
            [
                'status' => $request->status,
            ]
        );

        // 3. Kembalikan ke halaman laporan dengan pesan sukses
        return to_route('reports.attendance.index', ['date' => $request->date])
            ->with('message', 'Status berhasil diperbarui.');
    }
}