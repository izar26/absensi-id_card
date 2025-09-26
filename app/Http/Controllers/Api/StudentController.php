<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Student; // <-- Import model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // <-- Import untuk file handling
use Illuminate\Support\Facades\Validator; // <-- Import untuk validasi

class StudentController extends Controller
{
    // READ ALL
    public function index()
    {
        $students = Student::latest()->paginate(10);
        return response()->json([
            'success' => true,
            'message' => 'Daftar data siswa',
            'data' => $students
        ], 200);
    }

    // CREATE
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nis' => 'required|string|unique:students,nis|max:15',
            'class' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            // Simpan gambar ke storage/app/public/photos/students
            $photoPath = $request->file('photo')->store('photos/students', 'public');
        }

        $student = Student::create([
            'name' => $request->name,
            'nis' => $request->nis,
            'class' => $request->class,
            'photo' => $photoPath,
            // Isi kolom lain jika ada inputnya
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
        ]);

        return to_route('students.page')->with('message', 'Siswa berhasil ditambahkan!');
    }

    // READ ONE
    public function show(Student $student)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail data siswa',
            'data' => $student
        ], 200);
    }

    // UPDATE
    public function update(Request $request, Student $student)
{
    // 1. Validasi input
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        // Rule 'unique' harus mengabaikan NIS milik siswa yang sedang diedit
        'nis' => 'required|string|max:15|unique:students,nis,' . $student->id,
        'class' => 'required|string|max:255',
        'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // 2. Siapkan data untuk diupdate
    $updateData = $request->except('photo');

    // 3. Cek apakah ada foto baru yang diupload
    if ($request->hasFile('photo')) {
        // Hapus foto lama jika ada
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        // Upload foto baru dan simpan path-nya
        $photoPath = $request->file('photo')->store('photos/students', 'public');
        $updateData['photo'] = $photoPath;
    }

    // 4. Update data siswa di database
    $student->update($updateData);

    // 5. Kembalikan respons redirect dengan pesan sukses
    return to_route('students.page')->with('message', 'Data siswa berhasil diperbarui!');
}
    
    // DELETE
    public function destroy(Student $student)
    {
        // Hapus foto dari storage jika ada
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        $student->delete();

        return to_route('students.page')->with('message', 'Siswa berhasil dihapus.');
    }

    public function sync(Request $request)
    {
        // 1. Validasi data yang masuk, sesuaikan dengan nama field dari Dapodik
        $request->validate([
            '*.peserta_didik_id' => 'required',
            '*.nama' => 'required|string',
            '*.nisn' => 'nullable|string',
            '*.nama_rombel' => 'required|string',
        ]);

        $syncedCount = 0;
        $dataFromDapodik = $request->all();

        // 2. Loop melalui setiap data siswa yang dikirim
        foreach ($dataFromDapodik as $siswaDapodik) {
            
            // Lanjutkan hanya jika NISN tidak kosong
            if (!empty($siswaDapodik['nisn'])) {
                Student::updateOrCreate(
                    [
                        'nis' => $siswaDapodik['nisn'] // Kunci unik untuk mencari
                    ],
                    [
                        'name' => $siswaDapodik['nama'], // Data yang akan di-update atau dibuat
                        'class' => $siswaDapodik['nama_rombel'],
                    ]
                );
                $syncedCount++;
            }
        }

        // 4. Kembalikan respons sukses
        return response()->json([
            'message' => 'Sinkronisasi berhasil.',
            'total_data_diterima' => count($dataFromDapodik),
            'total_data_disinkronkan' => $syncedCount,
        ]);
    }
}