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
    protected $description = 'Henter filer fra FTP og knytter dem til sager via sag_id vha. sagsnr';

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
            $sagsnr = trim($record->pnummer); // file_records.pnummer indeholder sagsnummeret

            // Find den nye sag for at få dens rigtige ID (sag_id)
            $sag = Sager::where('sagsnr', $sagsnr)->first();

            if (!$sag) {
                $failCount++;
                continue;
            }

            // $sag->id er nu det korrekte sag_id, som dokumenter skal bruge
            $sagId = $sag->id;

            $fileNameEncoded = rawurlencode(trim($record->file_name));
            $fileUrl = $ftpBasePath . $fileNameEncoded;

            try {
                $fileContent = @file_get_contents($fileUrl);

                if ($fileContent === false) {
                    $fileContent = @file_get_contents($ftpBasePath . trim($record->file_name));
                }

                if ($fileContent !== false) {
                    $folder = 'dokumenter/' . $sagId;
                    $path = $folder . '/' . trim($record->file_name);

                    Storage::disk('public')->put($path, $fileContent);

                    Dokument::firstOrCreate(
                        [
                            'sag_id'    => $sagId, // Brug sag_id
                            'file_name' => trim($record->file_name),
                        ],
                        [
                            'file_path'     => $path,
                            'file_size'     => strlen($fileContent),
                            'uploaded_date' => $record->uploaded_date ?? now(),
                        ]
                    );

                    $successCount++;
                } else {
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