<?php

namespace App\Http\Controllers;

use App\Models\Sager;
use App\Models\Brev;
use App\Services\BrevMergeService;
use Barryvdh\DomPDF\Facade\Pdf;

class SagerBrevPdfController extends Controller
{
    /**
     * Generate and stream the PDF for a given Sager and Brev.
     * 
     * @param  \App\Models\Sager  $sag
     * @param  \App\Models\Brev   $brev
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Sager $sag, Brev $brev)
    {
        // Use the BrevMergeService to replace tokens
        $service = app(BrevMergeService::class);
        $result = $service->mergeWithMeta($brev->tekst, $sag);

        $html = $result['text']; // merged HTML content

        // Load PDF
        $pdf = Pdf::loadHTML($html);

        // Stream PDF to browser
        return $pdf->stream('brev-' . $sag->id . '.pdf');
    }
}
