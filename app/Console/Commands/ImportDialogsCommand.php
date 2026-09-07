<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ImportDialogsCommand extends Command
{
    protected $signature = 'import:dialoger {--file=storage/dialoger.sql} {--token-file=storage/token.sql}';
    protected $description = 'Importerer token og dialoger sikkert, og sikrer korrekt relation via sager_tokens og sagers.id';

    public function handle()
    {
        $this->info('Starter dialog-import kommando...');

        $statusFile = storage_path('app/import_status.json');
        $dbConfig = config('database.connections.' . config('database.default'));
        
        $tokenOpt = $this->option('token-file');
        $fileOpt = $this->option('file');

        $tokenFilePath = str_starts_with($tokenOpt, '/') ? $tokenOpt : base_path($tokenOpt);
        $filePath = str_starts_with($fileOpt, '/') ? $fileOpt : base_path($fileOpt);

        // 1. Importér token.sql
        if (file_exists($tokenFilePath)) {
            $this->info('Finder og importerer token.sql...');
            File::put($statusFile, json_encode(['status' => 'running', 'progress' => 10, 'message' => 'Indlæser token.sql...']));
            DB::statement('DROP TABLE IF EXISTS token;');
            
            // Brug en midlertidig konfigurationsfil til mysql for at undgå password-advarsler og fastlåsning
            $cnfFile = storage_path('app/mysql_temp.cnf');
            File::put($cnfFile, "[client]\nhost=\"{$dbConfig['host']}\"\nuser=\"{$dbConfig['username']}\"\npassword=\"{$dbConfig['password']}\"\ndatabase=\"{$dbConfig['database']}\"");
            
            $tokenCmd = sprintf('mysql --defaults-file=%s < %s 2>&1', escapeshellarg($cnfFile), escapeshellarg($tokenFilePath));
            system($tokenCmd);
            @unlink($cnfFile);
        }

        // 2. Tjek om den rå 'dialog'-tabel findes med data
        $hasRawData = false;
        try {
            if (Schema::hasTable('dialog') && DB::table('dialog')->count() > 0) {
                $hasRawData = true;
            }
        } catch (\Exception $e) {
            $hasRawData = false;
        }

        if (!$hasRawData) {
            $this->info('Importerer gigantisk dialoger.sql fil (dette kan tage et øjeblik)...');
            File::put($statusFile, json_encode(['status' => 'running', 'progress' => 30, 'message' => 'Indlæser dialoger.sql-fil...']));

            if (!file_exists($filePath)) {
                $this->error('dialoger.sql blev ikke fundet på stien: ' . $filePath);
                File::put($statusFile, json_encode(['status' => 'error', 'progress' => 0, 'message' => 'dialoger.sql blev ikke fundet.']));
                return 1;
            }
            
            DB::statement('DROP TABLE IF EXISTS dialog;');

            // Brug den lynhurtige system-import via sikker cnf-fil i stedet for sløv PHP-parser
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

        // 🟢 TRIN 2.5: Opret indekser sikkert
        $this->info('Opretter indekser...');
        File::put($statusFile, json_encode(['status' => 'running', 'progress' => 40, 'message' => 'Opretter indekser...']));
        
        try { DB::statement('ALTER TABLE dialog ADD INDEX idx_dialog_token (token(50))'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE dialog ADD INDEX idx_dialog_dialogid (dialogID)'); } catch (\Exception $e) {}
        if (Schema::hasTable('token')) {
            try { DB::statement('ALTER TABLE token ADD INDEX idx_token_token (token(50))'); } catch (\Exception $e) {}
        }

        // 🟢 TRIN 2.8: Synkroniser tokens og sager_tokens med COLLATE
        if (Schema::hasTable('token')) {
            $this->info('Synkroniserer tokens og sager_tokens...');
            File::put($statusFile, json_encode(['status' => 'running', 'progress' => 45, 'message' => 'Synkroniserer tokens og sager_tokens...']));
            
            DB::statement("
                INSERT IGNORE INTO tokens (token, created_at, updated_at)
                SELECT DISTINCT token, NOW(), NOW()
                FROM token
                WHERE token IS NOT NULL AND TRIM(token) != '';
            ");

            DB::statement("
                INSERT IGNORE INTO sager_tokens (sag_id, token_id, created_at, updated_at)
                SELECT DISTINCT t.brugerID AS sag_id, tk.id AS token_id, NOW(), NOW()
                FROM token t
                INNER JOIN tokens tk ON tk.token COLLATE utf8mb4_unicode_ci = t.token COLLATE utf8mb4_unicode_ci
                WHERE t.brugerID IS NOT NULL;
            ");
        }

        // 3. Konvertering til nye tabeller
        $this->info('Nulstiller tabeller og konverterer data...');
        File::put($statusFile, json_encode(['status' => 'running', 'progress' => 50, 'message' => 'Nulstiller tabeller...']));
        
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('dialog_participants')->truncate();
        DB::table('dialog_messages')->truncate();
        DB::table('dialogs')->delete();

        // 🟢 TRIN 4: Opret hoved-dialoger med COLLATE
        $this->info('Opretter hoved-dialoger via sagertokens...');
        File::put($statusFile, json_encode(['status' => 'running', 'progress' => 70, 'message' => 'Opretter hoved-dialoger...']));
        
        DB::statement("
            INSERT INTO dialogs (id, sag_id, type, created_at, updated_at)
            SELECT 
                DISTINCT d.dialogID AS id, 
                st.sag_id AS sag_id, 
                CASE d.typeID 
                    WHEN 1 THEN 'bogholderi' 
                    WHEN 2 THEN 'historik' 
                    ELSE 'klientinformation' 
                END AS type,
                NOW(),
                NOW()
            FROM dialog d
            INNER JOIN tokens tk ON tk.token COLLATE utf8mb4_unicode_ci = d.token COLLATE utf8mb4_unicode_ci
            INNER JOIN sager_tokens st ON st.token_id = tk.id
            WHERE d.dialogID IS NOT NULL 
              AND st.sag_id IS NOT NULL;
        ");

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

        $this->info('Import fuldført succesfuldt!');
        File::put($statusFile, json_encode(['status' => 'completed', 'progress' => 100, 'message' => 'Dialoger og tokens blev importeret succesfuldt!']));
        return 0;
    }
}