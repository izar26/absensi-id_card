<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::query()
            // Terapkan filter pencarian jika ada input 'search'
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%")
                      ->orWhere('class', 'like', "%{$search}%");
            })
            // Ambil data, 10 item per halaman
            ->paginate(10)
            // Pastikan parameter 'search' tetap ada di link paginasi
            ->withQueryString();

        return Inertia::render('Students/Index', [
            'students' => $students,
            'filters' => $request->only(['search']), // Kirim kembali filter ke Vue
        ]);
    }
}