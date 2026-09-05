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

        foreach ($records as $record) {
            // 1. Slå op i den GAMLE 'sager' tabel for at finde sagsnr ud fra pnummer
            $oldSag = DB::table('sager')->where('pnummer', $record->pnummer)->first();

            if (!$oldSag || empty($oldSag->sagsnr)) {
                $failCount++;
                continue;
            }

            // 2. Find den NYE sag i 'sagers' tabellen ved hjælp af sagsnr
            $sag = Sager::where('sagsnr', $oldSag->sagsnr)->first();

            if (!$sag) {
                $failCount++;
                continue;
            }

            $fileNameEncoded = rawurlencode($record->file_name);
            $fileUrl = $ftpBasePath . $fileNameEncoded;

            try {
                $fileContent = @file_get_contents($fileUrl);

                if ($fileContent === false) {
                    $fileContent = @file_get_contents($ftpBasePath . $record->file_name);
                }

                if ($fileContent !== false) {
                    $folder = 'dokumenter/' . $sag->id;
                    $path = $folder . '/' . $record->file_name;

                    Storage::disk('public')->put($path, $fileContent);

                    Dokument::firstOrCreate(
                        [
                            'sag_id'    => $sag->id,
                            'file_name' => $record->file_name,
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