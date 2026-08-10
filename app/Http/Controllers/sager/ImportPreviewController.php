<?php

namespace App\Http\Controllers\Sager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kreditorer;
use App\Models\Sager;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportPreviewController extends Controller
{
    public function show(Kreditorer $kreditor, Request $request)
    {
        $path = $request->query('path');

        if (!$path || !Storage::exists($path)) {
            return redirect()->route('sager.import.form', $kreditor)
                ->with('error', 'Filen kunne ikke findes.');
        }

        $fullPath = Storage::path($path);

        $headers = [];
        $rows = [];
        $totalRows = 0;
        $duplicateCount = 0;

        // Tjek de første 4 bytes for at se, om det er en ZIP/Excel-container ("PK\x03\x04")
        $handle = fopen($fullPath, 'rb');
        $fileHeader = fread($handle, 4);
        fclose($handle);

        if ($fileHeader === "PK\x03\x04") {
            // =========================================================================
            // 1. EXCEL LÆSNING (.xlsx / .xls)
            // =========================================================================
            try {
                $spreadsheet = IOFactory::load($fullPath);
                $worksheet = $spreadsheet->getActiveSheet();
                
                // Hent alt indhold som et simpelt to-dimensionalt array
                $allRows = $worksheet->toArray(null, true, true, false);

                // Filtrér helt tomme rækker fra
                $allRows = array_filter($allRows, function ($row) {
                    return !empty(array_filter($row, fn($cell) => $cell !== null && trim((string)$cell) !== ''));
                });

                if (!empty($allRows)) {
                    $headers = array_shift($allRows); // Første række er kolonneoverskrifter
                    $totalRows = count($allRows);

                    // Gem de første 10 datarækker til forhåndsvisningstabellen
                    $rows = array_slice($allRows, 0, 10);

                    // Tjek for eksisterende dubletter baseret på sagsnr / kontraktnr
                    $sagsnrIndex = $this->findSagsnrIndex($headers);
                    if ($sagsnrIndex !== false) {
                        $sagsnrList = array_filter(array_column($allRows, $sagsnrIndex));
                        if (!empty($sagsnrList)) {
                            $duplicateCount = Sager::whereIn('sagsnr', array_unique($sagsnrList))->count();
                        }
                    }
                }
            } catch (\Exception $e) {
                return redirect()->route('sager.import.form', $kreditor)
                    ->with('error', 'Der opstod en fejl ved læsning af Excel-filen: ' . $e->getMessage());
            }

        } else {
            // =========================================================================
            // 2. CSV LÆSNING (.csv)
            // =========================================================================
            $delimiter = $this->detectDelimiter($fullPath);
            $file = fopen($fullPath, 'r');

            // Hent overskrifter fra første række
            if (($data = fgetcsv($file, 2048, $delimiter)) !== false) {
                $headers = $data;
            }

            $sagsnrIndex = $this->findSagsnrIndex($headers);
            $sagsnrList = [];

            while (($data = fgetcsv($file, 2048, $delimiter)) !== false) {
                // Spring over hvis rækken er helt tom
                if (empty(array_filter($data, fn($cell) => trim((string)$cell) !== ''))) {
                    continue;
                }

                $totalRows++;

                // Gem kun de første 10 rækker til visning
                if (count($rows) < 10) {
                    $rows[] = $data;
                }

                if ($sagsnrIndex !== false && isset($data[$sagsnrIndex]) && !empty(trim((string)$data[$sagsnrIndex]))) {
                    $sagsnrList[] = trim((string)$data[$sagsnrIndex]);
                }
            }
            fclose($file);

            if (!empty($sagsnrList)) {
                $duplicateCount = Sager::whereIn('sagsnr', array_unique($sagsnrList))->count();
            }
        }

        return view('sager.import.preview', [
            'kreditor'        => $kreditor,
            'path'            => $path,
            'headers'         => $headers,
            'rows'            => $rows,
            'totalRows'       => $totalRows,
            'duplicateCount'  => $duplicateCount,
        ]);
    }

    /**
     * Automatisk detektering af afgrænsningstegn i CSV (; eller ,)
     */
    private function detectDelimiter(string $filePath): string
    {
        $handle = fopen($filePath, 'r');
        $firstLine = fgets($handle);
        fclose($handle);

        if (!$firstLine) {
            return ';';
        }

        return substr_count($firstLine, ';') >= substr_count($firstLine, ',') ? ';' : ',';
    }

    /**
     * Hjælpemetode til at lokalisere sagsnummer-kolonnen i filens headers
     */
    private function findSagsnrIndex(array $headers): int|bool
    {
        foreach ($headers as $index => $header) {
            $normalized = mb_strtolower(trim((string)$header));
            if (in_array($normalized, ['sagsnr', 'sagsnummer', 'kontraktnr', 'kontraktnummer', 'sag_id'])) {
                return $index;
            }
        }
        return false;
    }
}