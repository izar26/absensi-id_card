<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode; // <-- Kita akan gunakan Facade ini

class IdCardController extends Controller
{
    public function generateIdCard(Student $student)
    {
        $data = $this->prepareCardData($student);

        $pdf = Pdf::loadView('pdf.id_card', $data)
                   ->setPaper([0, 0, 242.6, 153], 'portrait');

        $fileName = 'ID-Card-' . $student->name . '.pdf';

        return $pdf->download($fileName);
    }

    public function generateAllIdCards()
    {
        $students = Student::all();
        $allCardsHtml = '';

        foreach ($students as $student) {
            $data = $this->prepareCardData($student);
            $cardHtml = view('pdf.id_card', $data)->render();
            $allCardsHtml .= $cardHtml . '<div style="page-break-after: always;"></div>';
        }

        $pdf = Pdf::loadHTML($allCardsHtml)
                   ->setPaper([0, 0, 242.6, 153], 'portrait');
        
        $fileName = 'Semua-ID-Card-Siswa.pdf';

        return $pdf->download($fileName);
    }

    private function prepareCardData(Student $student)
    {
        $studentPhotoBase64 = $this->getImageBase64(storage_path('app/public/' . $student->photo));
        $logoPath = public_path('logo.png'); 
        $schoolLogoBase64 = $this->getImageBase64($logoPath);

        // --- INI BAGIAN UTAMA YANG DIPERBAIKI ---
        // Kita kembali menggunakan Facade QrCode dan meminta format SVG secara eksplisit
        $qrCodeImage = QrCode::format('svg')->size(200)->generate($student->nis);
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeImage);

        return [
            'student' => $student,
            'studentPhotoBase64' => $studentPhotoBase64,
            'qrCodeBase64' => $qrCodeBase64,
            'schoolLogoBase64' => $schoolLogoBase64,
        ];
    }

    private function getImageBase64($path)
    {
        if (file_exists($path) && is_file($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        return '';
    }
}