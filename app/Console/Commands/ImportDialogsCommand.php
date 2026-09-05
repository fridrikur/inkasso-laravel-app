<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ImportDialogsCommand extends Command
{
    protected $signature = 'import:dialoger {--file=storage/dialoger.sql} {--token-file=storage/token.sql}';
    protected $description = 'Importerer token og dialoger, og filtrerer ugyldige sags-koblinger fra';

    public function handle()
    {
        $statusFile = storage_path('app/import_status.json');
        $dbConfig = config('database.connections.' . config('database.default'));
        
        // Hent stierne og tjek om de er relative (fra terminalen) eller absolutte (fra Livewire)
        $tokenOpt = $this->option('token-file');
        $fileOpt = $this->option('file');

        $tokenFilePath = str_starts_with($tokenOpt, '/') ? $tokenOpt : base_path($tokenOpt);
        $filePath = str_starts_with($fileOpt, '/') ? $fileOpt : base_path($fileOpt);

        // 1. Importér token.sql først (hvis filen findes)
        if (file_exists($tokenFilePath)) {
            File::put($statusFile, json_encode(['status' => 'running', 'progress' => 10, 'message' => 'Indlæser token.sql...']));
            DB::statement('DROP TABLE IF EXISTS token;');
            
            $tokenCmd = sprintf(
                'mysql -h %s -u %s %s %s < %s',
                escapeshellarg($dbConfig['host']),
                escapeshellarg($dbConfig['username']),
                $dbConfig['password'] ? '-p' . escapeshellarg($dbConfig['password']) : '',
                escapeshellarg($dbConfig['database']),
                escapeshellarg($tokenFilePath)
            );
            system($tokenCmd, $tokenResult);
            if ($tokenResult !== 0) {
                $this->warn('Kunne ikke importere token.sql, fortsætter uden...');
            }
        }

        // 2. Tjek om den rå 'dialog'-tabel findes
        $hasRawData = false;
        try {
            if (Schema::hasTable('dialog') && DB::table('dialog')->count() > 0) {
                $hasRawData = true;
            }
        } catch (\Exception $e) {
            $hasRawData = false;
        }

        if (!$hasRawData) {
            File::put($statusFile, json_encode(['status' => 'running', 'progress' => 30, 'message' => 'Indlæser dialoger.sql-fil...']));

            if (!file_exists($filePath)) {
                File::put($statusFile, json_encode(['status' => 'error', 'progress' => 0, 'message' => 'dialoger.sql blev ikke fundet på stien: ' . $filePath]));
                return 1;
            }
            
            DB::statement('DROP TABLE IF EXISTS dialog;');
            
            $command = sprintf(
                'mysql -h %s -u %s %s %s < %s',
                escapeshellarg($dbConfig['host']),
                escapeshellarg($dbConfig['username']),
                $dbConfig['password'] ? '-p' . escapeshellarg($dbConfig['password']) : '',
                escapeshellarg($dbConfig['database']),
                escapeshellarg($filePath)
            );

            system($command, $resultCode);

            if ($resultCode !== 0) {
                File::put($statusFile, json_encode(['status' => 'error', 'progress' => 0, 'message' => 'Fejl under MySQL import af dialoger.sql.']));
                return 1;
            }
        } else {
            File::put($statusFile, json_encode(['status' => 'running', 'progress' => 30, 'message' => 'Rå dialog-tabel findes allerede. Springer fil-indlæsning over...']));
        }

        // 3. Konvertering til nye tabeller
        File::put($statusFile, json_encode(['status' => 'running', 'progress' => 50, 'message' => 'Nulstiller tabeller...']));
        
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('dialog_participants')->truncate();
        DB::table('dialog_messages')->truncate();
        DB::table('dialogs')->delete();

        File::put($statusFile, json_encode(['status' => 'running', 'progress' => 70, 'message' => 'Opretter hoved-dialoger (filtrerer ugyldige fra)...']));
        
        DB::statement("
            INSERT INTO dialogs (id, sag_id, type, created_at, updated_at)
            SELECT 
                DISTINCT d.dialogID AS id, 
                t.brugerID AS sag_id, 
                CASE d.typeID 
                    WHEN 1 THEN 'bogholderi' 
                    WHEN 2 THEN 'historik' 
                    ELSE 'klientinformation' 
                END AS type,
                NOW(),
                NOW()
            FROM dialog d
            INNER JOIN token t ON t.token = d.token
            WHERE d.dialogID IS NOT NULL 
              AND t.brugerID IS NOT NULL 
              AND TRIM(t.brugerID) != '' 
              AND t.brugerID != '';
        ");

        File::put($statusFile, json_encode(['status' => 'running', 'progress' => 85, 'message' => 'Overfører relaterede dialog-beskeder...']));
        
        DB::statement("
            INSERT INTO dialog_messages (dialog_id, sender_id, tekst, dato, created_at, updated_at)
            SELECT 
                d.dialogID, 
                COALESCE(NULLIF(d.kreditorID, 0), 1), 
                d.tekst, 
                d.dato, 
                NOW(), 
                NOW()
            FROM dialog d
            INNER JOIN dialogs dg ON dg.id = d.dialogID
            WHERE d.tekst IS NOT NULL AND TRIM(d.tekst) != '';
        ");

        File::put($statusFile, json_encode(['status' => 'running', 'progress' => 95, 'message' => 'Opretter dialog_participants...']));
        
        DB::statement("
            INSERT IGNORE INTO dialog_participants (dialog_id, user_type, user_id, created_at, updated_at)
            SELECT DISTINCT 
                d.dialogID AS dialog_id,
                CASE WHEN d.kreditorID > 0 THEN 'kreditor' ELSE 'konsulent' END AS user_type, 
                COALESCE(
                    CASE WHEN d.kreditorID > 0 THEN d.kreditorID ELSE NULL END, 
                    u.id, 
                    1
                ) AS user_id,
                NOW(),
                NOW()
            FROM dialog d
            INNER JOIN dialogs dg ON dg.id = d.dialogID
            LEFT JOIN users u ON u.name COLLATE utf8mb4_unicode_ci = d.brugernavn COLLATE utf8mb4_unicode_ci
            WHERE d.dialogID IS NOT NULL;
        ");

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        File::put($statusFile, json_encode(['status' => 'completed', 'progress' => 100, 'message' => 'Dialoger og tokens blev importeret succesfuldt!']));
        return 0;
    }
}