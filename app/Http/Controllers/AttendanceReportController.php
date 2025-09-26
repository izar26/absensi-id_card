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
        // 1. Ambil filter tanggal dan search
        $filterDate = $request->input('date', Carbon::today()->toDateString());
        
        // 2. Lakukan query ke siswa dengan paginasi dan search
        $students = Student::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15) // Kita tampilkan 15 siswa per halaman
            ->withQueryString();

        // 3. Ambil ID siswa HANYA dari halaman saat ini
        $studentIdsOnCurrentPage = $students->pluck('id');

        // 4. Ambil data absensi HANYA untuk siswa di halaman ini dan pada tanggal yang dipilih
        $attendances = Attendance::whereIn('student_id', $studentIdsOnCurrentPage)
                                ->whereDate('created_at', $filterDate)
                                ->get()
                                ->keyBy('student_id');

        // 5. Gabungkan data (sekarang dilakukan pada hasil paginasi)
        $reportData = $students->through(function ($student) use ($attendances) {
            $attendanceRecord = $attendances->get($student->id);
            return [
                'id' => $student->id,
                'name' => $student->name,
                'class' => $student->class,
                'status' => $attendanceRecord ? $attendanceRecord->status : 'Alfa',
                'scan_time' => $attendanceRecord ? Carbon::parse($attendanceRecord->created_at)->format('H:i:s') : '-',
            ];
        });

        // 6. Kirim data ke Vue
        return Inertia::render('Attendance/Reports/Index', [
            'reportData' => $reportData, // Ini sekarang adalah objek paginator
            'filterDate' => $filterDate,
            'filters' => $request->only(['search']),
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