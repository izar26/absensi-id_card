<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class IdCardController extends Controller
{
    public function generateIdCard(Student $student)
    {
        // 1. Foto siswa -> Base64
        $studentPhotoBase64 = $this->getImageBase64(storage_path('app/public/' . $student->photo));

        // 2. Buat QR Code (PNG kalau Imagick ada, fallback ke SVG kalau tidak ada)
        if (extension_loaded('imagick')) {
            $renderer = new ImageRenderer(
                new RendererStyle(200),
                new ImagickImageBackEnd() // PNG
            );
            $mime = 'image/png';
        } else {
            $renderer = new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd() // fallback SVG
            );
            $mime = 'image/svg+xml';
        }

        $writer = new Writer($renderer);
        $qrCodeImage = $writer->writeString($student->nis);

        $qrCodeBase64 = 'data:' . $mime . ';base64,' . base64_encode($qrCodeImage);

         $logoPath = public_path('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTfX_FWrunaZa9NRuRJPcjE3lDCHGK4W2ArfQ&s'); // Asumsi Anda menaruh logo.png di folder /public
        $schoolLogoBase64 = $this->getImageBase64($logoPath);

        // 3. Data untuk Blade
        $data = [
            'student' => $student,
            'studentPhotoBase64' => $studentPhotoBase64,
            'qrCodeBase64' => $qrCodeBase64,
            'schoolLogoBase64' => $schoolLogoBase64,
        ];

        // 4. Render PDF
        $pdf = Pdf::loadView('pdf.id_card', $data)
                  ->setPaper([0, 0, 242.6, 153], 'portrait');

        $fileName = 'ID-Card-' . $student->name . '.pdf';

        return $pdf->download($fileName);
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
