<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class ImportDokumenter extends Command
{
    protected $signature = 'import:dokumenter';
    protected $description = 'Importerer filer fra file_records over i dokumenter tabellen via sager og sagers';

    public function handle()
    {
        // Forlæng tidsgrænse og hukommelse til store datamængder
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $this->info('Starter import af dokumenter...');

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

            // Vi bruger en direkte INSERT SELECT uden COLLATE, 
            // hvis tabellernes kollation nu er ens, hvilket undgår PDO-syntaksfejl.
            $affectedRows = DB::statement("
                INSERT INTO dokumenter (sag_id, file_name, file_path, file_size, uploaded_date, created_at, updated_at)
                SELECT 
                    sagers.id AS sag_id,
                    file_records.file_name,
                    CONCAT('/storage/', file_records.file_name) AS file_path,
                    file_records.file_size,
                    file_records.uploaded_date,
                    NOW() AS created_at,
                    NOW() AS updated_at
                FROM file_records
                JOIN sager ON sager.pnummer = file_records.pnummer
                JOIN sagers ON sagers.sagsnr = sager.sagsnr
            ");

            DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

            $this->info("Import gennemført succesfuldt!");

        } catch (Throwable $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
            $this->error("Fejl under import: " . $e->getMessage());
        }
    }
}