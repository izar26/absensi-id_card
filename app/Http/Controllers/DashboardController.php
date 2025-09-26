<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalStudents = Student::count();
        $presentToday = Attendance::whereDate('created_at', $today)->count();
        $tardyToday = Attendance::whereDate('created_at', $today)->where('status', 'Terlambat')->count();
        $absentToday = $totalStudents - $presentToday;
        
        $attendancePercentage = $totalStudents > 0 ? ($presentToday / $totalStudents) * 100 : 0;

        // Ambil 5 absensi terakhir hari ini
        $recentScans = Attendance::with('student')
                                ->whereDate('created_at', $today)
                                ->latest()
                                ->limit(5)
                                ->get();

        $stats = [
            'totalStudents' => $totalStudents,
            'presentToday' => $presentToday,
            'tardyToday' => $tardyToday,
            'absentToday' => $absentToday,
            'attendancePercentage' => round($attendancePercentage, 2),
            'recentScans' => $recentScans,
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    }
}