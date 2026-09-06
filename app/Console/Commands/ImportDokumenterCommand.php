<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Sager;
use App\Models\Dokument;
use Illuminate\Support\Facades\Schema;

class ImportDokumenterCommand extends Command
{
    protected $signature = 'import:dokumenter';
    protected $description = 'Henter filer fra FTP og scanner efter sagsnr i databasen';

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
        $missingInNew = 0;
        $missingInBoth = 0;

        foreach ($records as $record) {
            $sagsnr = trim($record->pnummer);

            // 1. Scan om sagsnr findes i den nye 'sagers' tabel
            $sag = Sager::where('sagsnr', $sagsnr)->first();
            $sagId = $sag ? $sag->id : null;

            // 2. Hvis ikke den findes i den nye tabel, tjek om den findes i den gamle 'sager' tabel (hvis den findes)
            if (!$sagId && Schema::hasTable('sager')) {
                $oldSag = DB::table('sager')->where('sagsnr', $sagsnr)->orWhere('pnummer', $sagsnr)->first();
                if ($oldSag) {
                    // Hvis den findes i gammel, men ikke ny, kan vi evt. oprette den eller slå op via sagsnr i ny
                    $sag = Sager::where('sagsnr', $oldSag->sagsnr)->first();
                    $sagId = $sag ? $sag->id : null;
                }
            }

            if (!$sagId) {
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
                    $folder = 'dokumenter/' . $sagId;
                    $path = $folder . '/' . $fileName;

                    Storage::disk('public')->put($path, $fileContent);

                    Dokument::firstOrCreate(
                        [
                            'sag_id'    => $sagId,
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