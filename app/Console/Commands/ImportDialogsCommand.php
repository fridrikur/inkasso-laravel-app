<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ImportDialogsCommand extends Command
{
    protected $signature = 'import:dialoger {--file=storage/dialoger.sql}';
    protected $description = 'Importerer og konverterer dialoger med progress-bar opdatering';

    public function handle()
    {
        $statusFile = storage_path('app/import_status.json');
        
        $hasRawData = false;
        try {
            if (Schema::hasTable('dialog')) {
                if (DB::table('dialog')->count() > 0) {
                    $hasRawData = true;
                }
            }
        } catch (\Exception $e) {
            $hasRawData = false;
        }

        if (!$hasRawData) {
            File::put($statusFile, json_encode(['status' => 'running', 'progress' => 10, 'message' => 'Indlæser 2.5 GB SQL-fil i MySQL...']));

            $filePath = base_path($this->option('file'));
            if (!file_exists($filePath)) {
                File::put($statusFile, json_encode(['status' => 'error', 'progress' => 0, 'message' => 'SQL-filen blev ikke fundet.']));
                return 1;
            }

            $dbConfig = config('database.connections.' . config('database.default'));
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
                File::put($statusFile, json_encode(['status' => 'error', 'progress' => 0, 'message' => 'Fejl under MySQL import af den rå fil.']));
                return 1;
            }
        } else {
            File::put($statusFile, json_encode(['status' => 'running', 'progress' => 30, 'message' => 'Rå dialog-tabel findes allerede. Springer fil-indlæsning over...']));
        }

        File::put($statusFile, json_encode(['status' => 'running', 'progress' => 40, 'message' => 'Nulstiller tabeller og fjerner foreign keys...']));
        
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('dialog_participants')->truncate();
        DB::table('dialog_messages')->truncate();
        DB::table('dialogs')->delete();

        File::put($statusFile, json_encode(['status' => 'running', 'progress' => 60, 'message' => 'Opretter hoved-dialoger og knytter sager via token...']));
        
        DB::statement("
            INSERT INTO dialogs (id, sag_id, type, created_at, updated_at)
            SELECT 
                DISTINCT d.dialogID AS id, 
                COALESCE(t.brugerID, 2) AS sag_id, 
                CASE d.typeID 
                    WHEN 1 THEN 'bogholderi' 
                    WHEN 2 THEN 'historik' 
                    ELSE 'klientinformation' 
                END AS type,
                NOW(),
                NOW()
            FROM dialog d
            LEFT JOIN token t ON t.token = d.token
            WHERE d.dialogID IS NOT NULL;
        ");

        File::put($statusFile, json_encode(['status' => 'running', 'progress' => 80, 'message' => 'Overfører dialog-beskeder...']));
        
        DB::statement("
            INSERT INTO dialog_messages (dialog_id, sender_id, tekst, dato, created_at, updated_at)
            SELECT 
                dialogID, 
                COALESCE(NULLIF(kreditorID, 0), 1), 
                tekst, 
                dato, 
                NOW(), 
                NOW()
            FROM dialog
            WHERE tekst IS NOT NULL AND TRIM(tekst) != '';
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
            LEFT JOIN users u ON u.name COLLATE utf8mb4_unicode_ci = d.brugernavn COLLATE utf8mb4_unicode_ci
            WHERE d.dialogID IS NOT NULL;
        ");

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        File::put($statusFile, json_encode(['status' => 'completed', 'progress' => 100, 'message' => 'Dialoger, beskeder og deltagere blev importeret succesfuldt!']));
        return 0;
    }
}