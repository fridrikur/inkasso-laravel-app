<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportDialogs extends Command
{
    protected $signature = 'import:dialoger {file?}';
    protected $description = 'Importerer kæmpe dialog-SQL-filer direkte via MySQL og fordeler dem i dialogs og dialog_messages.';

    public function handle()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(600); // Sæt tidsgrænsen op til 10 minutter for store filer

        $filePath = $this->argument('file') ?? storage_path('dialoger.sql');
        $filePath = file_exists($filePath) ? $filePath : storage_path($filePath);

        if (!file_exists($filePath)) {
            $this->error("Filen blev ikke fundet: {$filePath}");
            return 1;
        }

        $this->info("Forbereder database og rydder op i evt. eksisterende 'dialog' tabel...");

        // 🟢 Sørg for at slette den midlertidige dialog-tabel først, så vi ikke får "Table already exists" fejl
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement('DROP TABLE IF EXISTS dialog;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info("Starter effektiv MySQL-import af dialoger...");

        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port', '3306');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        // Byg mysql-kommandoen sikkert
        $passArg = $dbPass ? "-p" . escapeshellarg($dbPass) : "";
        $command = "mysql -h " . escapeshellarg($dbHost) . " -P " . escapeshellarg($dbPort) . " -u " . escapeshellarg($dbUser) . " {$passArg} " . escapeshellarg($dbName) . " < " . escapeshellarg($filePath);

        // Kør kommandoen på serverniveau
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error("Fejl under MySQL-import af filen (Returkode: {$returnVar}).");
            return 1;
        }

        $this->info("SQL-fil importeret til tabellen. Behandler og konverterer relationer...");

        // 1. Opret manglende 'dialogs'-beholdere ved at matche på token
        DB::statement("
            INSERT IGNORE INTO dialogs (sag_id, type, created_at, updated_at)
            SELECT DISTINCT s.id, 
                   CASE 
                       d.typeID WHEN 1 THEN 'historik' 
                                WHEN 2 THEN 'bogholderi' 
                                WHEN 3 THEN 'klientinformation' 
                   END, 
                   NOW(), NOW()
            FROM dialog d
            JOIN sager_tokens st ON st.token = d.token
            JOIN sagers s ON s.id = st.sager_id
            WHERE d.typeID IN (1, 2, 3)
        ");

        // 2. Indsæt beskeder i 'dialog_messages' ved at matche på token
        DB::statement("
            INSERT INTO dialog_messages (dialog_id, sender_id, tekst, dato, created_at, updated_at)
            SELECT 
                dg.id,
                COALESCE(u.id, 1),
                d.tekst,
                COALESCE(d.dato, NOW()),
                COALESCE(d.dato, NOW()),
                NOW()
            FROM dialog d
            JOIN sager_tokens st ON st.token = d.token
            JOIN sagers s ON s.id = st.sager_id
            JOIN dialogs dg ON dg.sag_id = s.id AND dg.type = CASE 
                d.typeID WHEN 1 THEN 'historik' 
                         WHEN 2 THEN 'bogholderi' 
                         WHEN 3 THEN 'klientinformation' 
            END
            LEFT JOIN users u ON u.name = d.brugernavn OR u.email = d.brugernavn
        ");

        // 3. Ryd op efter den midlertidige 'dialog'-tabel fra dumpet
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement("DROP TABLE IF EXISTS dialog");
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info("Dialog-import og konvertering fuldført med succes!");
        return 0;
    }
}