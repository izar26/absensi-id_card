<?php

namespace App\Jobs;

use App\Models\Student;
use App\Models\JobProgress;
use App\Helpers\DataPreparationHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class GenerateAllIdCardsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $batchSize = 100; // jumlah siswa per batch
        $students = Student::query()->orderBy('id')->get();
        $totalBatches = (int) ceil($students->count() / $batchSize);

        // Progress record baru
        $progress = JobProgress::create([
            'job_type' => 'generate_all_idcards',
            'total_batches' => $totalBatches,
            'completed_batches' => 0,
            'percentage_complete' => 0,
            'status' => 'processing',
        ]);

        $batchFiles = [];

        foreach ($students->chunk($batchSize) as $index => $chunk) {
            $data = ['students' => $chunk->map(fn($s) => DataPreparationHelper::prepareCardData($s))];

            // render PDF untuk batch ini
            $pdf = Pdf::loadView('pdf.id_cards_batch', $data)
                ->setPaper([0, 0, 242.6, 153], 'portrait');

            $fileName = "idcards_batch_" . ($index + 1) . ".pdf";
            $filePath = "tmp/idcards/$fileName";

            Storage::put($filePath, $pdf->output());
            $batchFiles[] = storage_path("app/$filePath");

            // update progress
            $progress->update([
                'completed_batches' => $index + 1,
                'percentage_complete' => round((($index + 1) / $totalBatches) * 100),
            ]);
        }

        // gabungkan semua batch PDF
        $finalPath = storage_path("app/public/idcards/all_idcards.pdf");
        $this->mergePdfs($batchFiles, $finalPath);

        // update progress akhir
        $progress->update([
            'status' => 'completed',
            'file_path' => 'storage/idcards/all_idcards.pdf',
            'percentage_complete' => 100,
        ]);
    }

    private function mergePdfs(array $files, string $outputPath): void
    {
        $pdf = new Fpdi();

        foreach ($files as $file) {
            $pageCount = $pdf->setSourceFile($file);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);
            }
        }

        $pdf->Output($outputPath, 'F');
    }
}
