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
        $paginatedStudents = Student::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        // 3. Ambil ID siswa HANYA dari halaman saat ini
        $studentIdsOnCurrentPage = $paginatedStudents->pluck('id');

        // 4. Ambil data absensi HANYA untuk siswa di halaman ini dan pada tanggal yang dipilih
        $attendances = Attendance::whereIn('student_id', $studentIdsOnCurrentPage)
                                ->whereDate('created_at', $filterDate)
                                ->get()
                                ->keyBy('student_id');

        // 5. Gabungkan data dengan cara yang lebih aman untuk konsistensi
        $reportData = $paginatedStudents->setCollection(
            $paginatedStudents->getCollection()->map(function ($student) use ($attendances) {
                $attendanceRecord = $attendances->get($student->id);
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'class' => $student->class,
                    'status' => $attendanceRecord ? $attendanceRecord->status : 'Alfa',
                    'scan_time' => $attendanceRecord ? Carbon::parse($attendanceRecord->created_at)->format('H:i:s') : '-',
                ];
            })
        );

        // 6. Kirim data ke Vue
        return Inertia::render('Attendance/Reports/Index', [
            'reportData' => $reportData,
            'filterDate' => $filterDate,
            'filters' => $request->only(['search']),
        ]);
    }

    public function updateStatus(Request $request)
{
    $request->validate([
        'student_id' => 'required|exists:students,id',
        'date' => 'required|date',
        'status' => 'required|string|in:Hadir,Terlambat,Izin,Sakit,Alfa',
    ]);

    $attendance = Attendance::where('student_id', $request->student_id)
        ->whereDate('created_at', $request->date)
        ->first();

    if ($attendance) {
        $attendance->update([
            'status' => $request->status,
        ]);
    } else {
        Attendance::create([
            'student_id' => $request->student_id,
            'status' => $request->status,
            'created_at' => Carbon::parse($request->date)->startOfDay(),
        ]);
    }

    return to_route('reports.attendance.index', ['date' => $request->date])
        ->with('message', 'Status berhasil diperbarui.');
}

}