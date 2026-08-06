<?php

namespace App\Http\Controllers\Sager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Kreditorer;

class ImportPreviewController extends Controller
{
    public function show(Request $request, Kreditorer $kreditor)
    {
        $path = $request->query('path');

        abort_unless($path, 404);

        // IMPORTANT: use disk path, NOT storage_path()
        $data = Excel::toArray([], $path)[0];

        $headers = array_map('trim', $data[0]);
        $rows = array_slice($data, 1);

        // Detect duplicates
        $sagsnumre = collect($rows)
            ->map(fn ($row) => $row[array_search('Kontraktnummer', $headers)] ?? null)
            ->filter()
            ->toArray();

        $counts = array_count_values($sagsnumre);
        $duplicateCount = count(array_filter($counts, fn ($c) => $c > 1));

        return view('sager.import.preview', [
            'headers'        => $headers,
            'rows'           => array_slice($rows, 0, 10),
            'duplicateCount' => $duplicateCount,
            'path'           => $path,
            'kreditor'       => $kreditor,   
        ]);
    }
}
