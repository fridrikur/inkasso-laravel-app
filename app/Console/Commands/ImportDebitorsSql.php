<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportDebitorsSql extends Command
{
    protected $signature = 'import:debitorer {file : Stien til SQL filen med debitorer}';
    protected $description = 'Importerer debitorer direkte via MySQL rå-import og SQL-queries (Super hurtigt)';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("Filen blev ikke fundet: {$filePath}");
            return 1;
        }

        $this->info("Trin 1: Importerer hele SQL-filen til den midlertidige 'debitor' tabel...");

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host     = config('database.connections.mysql.host');
        $port     = config('database.connections.mysql.port', 3306);

        $command = sprintf(
            'mysql -h %s -P %s -u %s %s %s < %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $password ? '-p' . escapeshellarg($password) : '',
            escapeshellarg($database),
            escapeshellarg($filePath)
        );

        system($command, $resultCode);

        if ($resultCode !== 0) {
            $this->error("Fejl under rå SQL-import af debitorer via terminal.");
            return 1;
        }

        $this->info("Trin 2: Overfører debitorer fra 'debitor' til 'debitors' tabellen...");

        // Indsæt data og map 'debitorid' over på 'id' (eller hvad din tabel bruger)
        // Tilret kolonnenavnene herunder hvis den gamle tabel bruger andre navne end standard
        DB::statement("
            INSERT IGNORE INTO debitors (
                id, navn, co, adresse, postnr, email, tlf, mobil, adropl, pnr, created_at, updated_at
            )
            SELECT 
                debitorid AS id, 
                navn, 
                co, 
                adresse, 
                postnr, 
                email, 
                tlf, 
                mobil, 
                adropl, 
                pnr, 
                NOW(), 
                NOW()
            FROM debitor
        ");

        $this->info("Trin 3: Sletter den midlertidige 'debitor' tabel...");
        DB::statement("DROP TABLE IF EXISTS debitor");

        $this->info("Debitor import fuldført med lynets hast!");
        return 0;
    }
}