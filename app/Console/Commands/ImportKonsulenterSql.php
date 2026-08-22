<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportKonsulenterSql extends Command
{
    protected $signature = 'import:konsulenter {file : Stien til SQL filen med konsulenter}';
    protected $description = 'Importerer konsulenter (kreditorID = 0)';

    public function handle()
    {
        $filePath = file_exists($this->argument('file')) ? $this->argument('file') : storage_path($this->argument('file'));

        // 1. Slet tabellen hvis den findes
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement("DROP TABLE IF EXISTS sagsbehandlere");
        
        // 2. Kør den originale SQL-fil uden at ændre den
        DB::unprepared(file_get_contents($filePath));
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 3. Overfør data direkte fra den tabel filen lige har oprettet
        DB::statement("
            INSERT IGNORE INTO konsulenters (id, navn, email, tlf, created_at, updated_at)
            SELECT sbID, sagsbehandler, email, tlf, NOW(), NOW() 
            FROM sagsbehandlere
            WHERE kreditorID = 0 OR kreditorID IS NULL
        ");

        // 4. Ryd op
        DB::statement("DROP TABLE IF EXISTS sagsbehandlere");
        return 0;
    }
}