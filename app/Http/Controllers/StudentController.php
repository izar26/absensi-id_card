<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule; // <-- Import Rule untuk validasi

class StudentController extends Controller
{
    /**
     * Menampilkan halaman daftar siswa dengan search dan paginasi.
     */
    public function index(Request $request)
    {
        $students = Student::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('nis', 'like', "%{$search}%")
                      ->orWhere('class', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Students/Index', [
            'students' => $students,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Menyimpan data siswa baru dari modal.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'required|string|max:15|unique:students,nis',
            'class' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('photos/students', 'public');
        }

        Student::create($validated);

        return to_route('students.index')->with('message', 'Siswa berhasil ditambahkan.');
    }

    /**
     * Memperbarui data siswa dari modal.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nis' => ['required', 'string', 'max:15', Rule::unique('students')->ignore($student->id)],
            'class' => 'required|string|max:255',
        ]);

        $student->update($validated);
        
        // Catatan: Logika untuk update foto sengaja dipisah agar lebih jelas
        // Jika Anda mengirim file foto baru, Anda perlu menambahkan logikanya di sini.

        return to_route('students.index')->with('message', 'Siswa berhasil diperbarui.');
    }

    /**
     * Menghapus data siswa.
     */
    public function destroy(Student $student)
    {
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }
        
        $student->delete();

        return to_route('students.index')->with('message', 'Siswa berhasil dihapus.');
    }
}