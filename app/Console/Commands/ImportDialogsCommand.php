<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ImportDialogsCommand extends Command
{
    protected $signature = 'import:dialoger {--file=storage/dialoger.sql}';
    protected $description = 'Importerer rå dialoger og mapper dem direkte til sagers.id via sagers.pnummer = dialog.token';

    public function handle()
    {
        $this->info('Starter direkte dialog-import...');

        $statusFile = storage_path('app/import_status.json');
        $dbConfig = config('database.connections.' . config('database.default'));
        
        $fileOpt = $this->option('file');
        $filePath = str_starts_with($fileOpt, '/') ? $fileOpt : base_path($fileOpt);

        // 1. Tjek om den rå 'dialog'-tabel allerede findes med data
        $hasRawData = false;
        try {
            if (Schema::hasTable('dialog') && DB::table('dialog')->count() > 0) {
                $hasRawData = true;
            }
        } catch (\Exception $e) {
            $hasRawData = false;
        }

        if (!$hasRawData) {
            $this->info('Indlæser dialoger.sql-fil direkte i MySQL...');
            File::put($statusFile, json_encode(['status' => 'running', 'progress' => 20, 'message' => 'Indlæser dialoger.sql-fil...']));

            if (!file_exists($filePath)) {
                $this->error('dialoger.sql blev ikke fundet på stien: ' . $filePath);
                File::put($statusFile, json_encode(['status' => 'error', 'progress' => 0, 'message' => 'dialoger.sql blev ikke fundet.']));
                return 1;
            }
            
            DB::statement('DROP TABLE IF EXISTS dialog;');

            // Sikker system-import via midlertidig konfigurationsfil
            $cnfFile = storage_path('app/mysql_temp.cnf');
            File::put($cnfFile, "[client]\nhost=\"{$dbConfig['host']}\"\nuser=\"{$dbConfig['username']}\"\npassword=\"{$dbConfig['password']}\"\ndatabase=\"{$dbConfig['database']}\"");
            
            $command = sprintf('mysql --defaults-file=%s < %s 2>&1', escapeshellarg($cnfFile), escapeshellarg($filePath));
            system($command);
            @unlink($cnfFile);

            if (!Schema::hasTable('dialog') || DB::table('dialog')->count() === 0) {
                $this->error('Fejl under import: dialog-tabellen er tom eller blev ikke oprettet.');
                File::put($statusFile, json_encode(['status' => 'error', 'progress' => 0, 'message' => 'Fejl under import: tabellen er tom.']));
                return 1;
            }
        } else {
            $this->info('Rå dialog-tabel findes allerede. Springer fil-indlæsning over...');
        }

        // 2. Opret nødvendige indekser for lynhurtige joins
        $this->info('Opretter indekser på rå dialog-tabel...');
        File::put($statusFile, json_encode(['status' => 'running', 'progress' => 40, 'message' => 'Opretter indekser...']));
        
        try { DB::statement('ALTER TABLE dialog ADD INDEX idx_dialog_token (token(50))'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE dialog ADD INDEX idx_dialog_dialogid (dialogID)'); } catch (\Exception $e) {}

        // Sørg for at pnummer i sagers har et indeks for maksimal hastighed
        try { DB::statement('ALTER TABLE sagers ADD INDEX idx_sagers_pnummer (pnummer(50))'); } catch (\Exception $e) {}

        // 3. Nulstil produktionstabeller
        $this->info('Nulstiller eksisterende dialog-tabeller...');
        File::put($statusFile, json_encode(['status' => 'running', 'progress' => 50, 'message' => 'Nulstiller tabeller...']));
        
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('dialog_participants')->truncate();
        DB::table('dialog_messages')->truncate();
        DB::table('dialogs')->delete();

        // 4. Opret hoved-dialoger ved at mappe dialog.token direkte til sagers.pnummer
        $this->info('Opretter hoved-dialoger via sagers.pnummer = dialog.token...');
        File::put($statusFile, json_encode(['status' => 'running', 'progress' => 70, 'message' => 'Opretter hoved-dialoger...']));
        
        DB::statement("
            INSERT INTO dialogs (id, sag_id, type, created_at, updated_at)
            SELECT 
                DISTINCT d.dialogID AS id, 
                s.id AS sag_id, 
                CASE d.typeID 
                    WHEN 1 THEN 'bogholderi' 
                    WHEN 2 THEN 'historik' 
                    ELSE 'klientinformation' 
                END AS type,
                NOW(),
                NOW()
            FROM dialog d
            INNER JOIN sagers s ON s.pnummer COLLATE utf8mb4_unicode_ci = d.token COLLATE utf8mb4_unicode_ci
            WHERE d.dialogID IS NOT NULL;
        ");

        // 5. Overfør beskeder
        $this->info('Overfører relaterede dialog-beskeder...');
        File::put($statusFile, json_encode(['status' => 'running', 'progress' => 85, 'message' => 'Overfører beskedindhold...']));
        
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

        // 6. Overfør deltagere
        $this->info('Opretter dialog_participants...');
        File::put($statusFile, json_encode(['status' => 'running', 'progress' => 95, 'message' => 'Opretter deltagere...']));
        
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

        $this->info('Dialoger blev importeret og mappet succesfuldt via pnummer!');
        File::put($statusFile, json_encode(['status' => 'completed', 'progress' => 100, 'message' => 'Dialoger blev importeret succesfuldt via pnummer!']));
        return 0;
    }
}