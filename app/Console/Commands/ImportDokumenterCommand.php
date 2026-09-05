<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Sager;
use App\Models\Dokument;

class ImportDokumenterCommand extends Command
{
    protected $signature = 'import:dokumenter';
    protected $description = 'Henter filer fra FTP og knytter dem til sager';

    public function handle()
    {
        $this->info('Starter import af dokumenter fra FTP...');

        $records = DB::table('file_records')->get();

        if ($records->isEmpty()) {
            $this->warn('Ingen poster fundet i file_records.');
            return 0;
        }

        $ftpBasePath = 'ftp://linux22.curanet.dk/www/dkg-root/upload/';
        $successCount = 0;
        $failCount = 0;

        foreach ($records as $record) {
            $sagsnr = trim($record->pnummer);

            // 1. Tjek om sagen overhovedet findes
            $sag = Sager::where('sagsnr', $sagsnr)->first();

            if (!$sag) {
                if ($failCount < 3) {
                    $this->warn("Debug: Fandt IKKE sag i databasen med sagsnr: '{$sagsnr}' (file_records.pnummer)");
                }
                $failCount++;
                continue;
            }

            $fileName = trim($record->file_name);
            $fileNameEncoded = rawurlencode($fileName);
            $fileUrl = $ftpBasePath . $fileNameEncoded;

            try {
                $fileContent = @file_get_contents($fileUrl);

                if ($fileContent === false) {
                    $fileContent = @file_get_contents($ftpBasePath . $fileName);
                }

                if ($fileContent !== false) {
                    $folder = 'dokumenter/' . $sag->id;
                    $path = $folder . '/' . $fileName;

                    Storage::disk('public')->put($path, $fileContent);

                    Dokument::firstOrCreate(
                        [
                            'sag_id'    => $sag->id,
                            'file_name' => $fileName,
                        ],
                        [
                            'file_path'     => $path,
                            'file_size'     => strlen($fileContent),
                            'uploaded_date' => $record->uploaded_date ?? now(),
                        ]
                    );

                    $successCount++;
                } else {
                    if ($failCount < 3) {
                        $this->error("Debug: Kunne IKKE hente fil fra FTP for sagsnr {$sagsnr}: {$fileUrl}");
                    }
                    $failCount++;
                }
            } catch (\Exception $e) {
                $failCount++;
            }
        }

        $this->info("Import afsluttet! Succes: {$successCount}, Fejlede/Ikke fundet: {$failCount}");
        return 0;
    }
}