<?php

namespace App\Helpers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class DataPreparationHelper
{
    /**
     * Persiapkan data untuk kartu identitas siswa
     *
     * @param \App\Models\Student $student
     * @return array
     */
    public static function prepareCardData($student)
    {
        // 🔹 Konversi foto siswa ke base64 (jika ada)
        $studentPhotoBase64 = null;
        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            $path = Storage::disk('public')->path($student->photo);
            $studentPhotoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($path));
        }

        // 🔹 Generate QR Code (misalnya link ke detail siswa)
        $qrCodeImage = QrCode::format('svg')
            ->size(100)
            ->generate(route('students.idcard', $student->id));

        return [
            'student' => $student,
            'studentPhotoBase64' => $studentPhotoBase64,
            'qrCodeImage' => $qrCodeImage,
        ];
    }
}
