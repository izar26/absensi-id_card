<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\JobProgress;
use App\Jobs\GenerateAllIdCardsJob;
use App\Helpers\DataPreparationHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class IdCardController extends Controller
{
    public function perStudent(Student $student)
    {
        $data = DataPreparationHelper::prepareCardData($student);

        $pdf = Pdf::loadView('pdf.id_card', $data)
                  ->setPaper([0, 0, 242.6, 153], 'portrait');

        return $pdf->download("ID-Card-{$student->name}.pdf");
    }

    public function startGeneration()
{
    GenerateAllIdCardsJob::dispatch();

    return response()->json(['message' => 'Proses pembuatan ID Card dimulai']);
}


    public function checkProgress()
    {
        $progress = JobProgress::where('job_type', 'generate_all_idcards')
                               ->latest()
                               ->first();

        return response()->json($progress);
    }

    public function cancelGeneration()
    {
        JobProgress::where('job_type', 'generate_all_idcards')->delete();
        return response()->json(['message' => 'Proses dibatalkan']);
    }
}
