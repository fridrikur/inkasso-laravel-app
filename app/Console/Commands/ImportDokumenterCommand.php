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
    protected $description = 'Henter filer fra FTP og knytter dem til sager via file_records og den gamle sager-tabel pnummer';

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

        $this->info("Antal poster i file_records: " . count($records));

        foreach ($records as $record) {
            // Trim evt. whitespace af pnummer for at undgå mismatch
            $pnummer = trim($record->pnummer);

            // 1. Slå op i den gamle 'sager' tabel
            $oldSag = DB::table('sager')->where('pnummer', $pnummer)->first();

            if (!$oldSag) {
                // Udskriv de første par fejl for at se om pnummer overhovedet matcher
                if ($failCount < 5) {
                    $this->warn("Intet match i 'sager' tabellen for pnummer: '{$pnummer}'");
                }
                $failCount++;
                continue;
            }

            if (empty($oldSag->sagsnr)) {
                $failCount++;
                continue;
            }

            // 2. Find den nye sag i 'sagers' tabellen
            $sag = Sager::where('sagsnr', trim($oldSag->sagsnr))->first();

            if (!$sag) {
                if ($failCount < 5) {
                    $this->warn("Fandt 'oldSag' med sagsnr '{$oldSag->sagsnr}', men ingen match i ny 'sagers' tabel.");
                }
                $failCount++;
                continue;
            }

            $fileNameEncoded = rawurlencode(trim($record->file_name));
            $fileUrl = $ftpBasePath . $fileNameEncoded;

            try {
                $fileContent = @file_get_contents($fileUrl);

                if ($fileContent === false) {
                    $fileContent = @file_get_contents($ftpBasePath . trim($record->file_name));
                }

                if ($fileContent !== false) {
                    $folder = 'dokumenter/' . $sag->id;
                    $path = $folder . '/' . trim($record->file_name);

                    Storage::disk('public')->put($path, $fileContent);

                    Dokument::firstOrCreate(
                        [
                            'sag_id'    => $sag->id,
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
                    if ($failCount < 5) {
                        $this->error("Kunne ikke hente fil fra FTP: {$fileUrl}");
                    }
                    $failCount++;
                }
            } catch (\Exception $e) {
                if ($failCount < 5) {
                    $this->error("Fejl for fil {$record->file_name}: " . $e->getMessage());
                }
                $failCount++;
            }
        }

        $this->info("Import afsluttet! Succes: {$successCount}, Fejlede/Ikke fundet: {$failCount}");
        return 0;
    }
}