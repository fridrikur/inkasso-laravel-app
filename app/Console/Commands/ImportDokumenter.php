<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportDokumenter extends Command
{
    protected $signature = 'import:dokumenter';
    protected $description = 'Importerer filer fra file_records over i dokumenter tabellen via sager og sagers';

    public function handle()
    {
        $this->info('Starter import af dokumenter...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

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
            JOIN sagers ON sagers.sagsnr COLLATE utf8mb4_unicode_ci = sager.sagsnr COLLATE utf8mb4_unicode_ci
        ");

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info("Import gennemført succesfuldt!");
    }
}