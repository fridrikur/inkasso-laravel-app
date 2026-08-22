<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportUsersSql extends Command
{
    protected $signature = 'import:users {file : Stien til SQL filen med brugere}';
    protected $description = 'Importerer brugere og tildeler roller og kreditor-relationer';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            if (file_exists(storage_path($filePath))) {
                $filePath = storage_path($filePath);
            } else {
                $this->error("Filen blev ikke fundet: {$filePath}");
                return 1;
            }
        }

        $this->info("Trin 1: Indlæser SQL-filen i databasen...");
        try {
            $sql = file_get_contents($filePath);
            DB::unprepared($sql);
        } catch (\Exception $e) {
            $this->error("Fejl ved indlæsning af SQL: " . $e->getMessage());
            return 1;
        }

        $this->info("Trin 2: Overfører brugere til 'users' tabellen...");
        DB::statement("
            INSERT IGNORE INTO users (id, name, email, password, created_at, updated_at)
            SELECT 
                brugerID, 
                CONCAT(fornavn, ' ', efternavn), 
                email, 
                kodeord, 
                NOW(), 
                NOW()
            FROM brugere
        ");

        $this->info("Trin 3: Tildeler Spatie-roller (Admin, Medarbejder, Kreditor)...");
        $modelType = 'App\Models\User';

        // Admin
        DB::statement("
            INSERT IGNORE INTO model_has_roles (role_id, model_type, model_id)
            SELECT r.id, ?, b.brugerID
            FROM brugere b
            JOIN roles r ON r.name = 'Admin'
            WHERE b.admin = 1
        ", [$modelType]);

        // Kreditor
        DB::statement("
            INSERT IGNORE INTO model_has_roles (role_id, model_type, model_id)
            SELECT r.id, ?, b.brugerID
            FROM brugere b
            JOIN roles r ON r.name = 'Kreditor'
            WHERE (b.admin = 0 OR b.admin IS NULL) AND b.kreditorID IS NOT NULL AND b.kreditorID != 0
        ", [$modelType]);

        // Medarbejder
        DB::statement("
            INSERT IGNORE INTO model_has_roles (role_id, model_type, model_id)
            SELECT r.id, ?, b.brugerID
            FROM brugere b
            JOIN roles r ON r.name = 'Medarbejder'
            WHERE (b.admin = 0 OR b.admin IS NULL) AND (b.kreditorID IS NULL OR b.kreditorID = 0)
        ", [$modelType]);

        $this->info("Trin 4: Knytter brugere til kreditorer via pivot-tabellen...");
        DB::statement("
            INSERT IGNORE INTO kreditor_user (kreditor_id, user_id, created_at, updated_at)
            SELECT k.id, u.id, NOW(), NOW()
            FROM brugere b
            JOIN users u ON u.id = b.brugerID
            JOIN kreditors k ON k.lotusID = b.kreditorID
            WHERE b.kreditorID IS NOT NULL 
              AND b.kreditorID != 0 
              AND b.brugerID IS NOT NULL
        ");

        $this->info("Trin 5: Sletter midlertidig tabel...");
        DB::statement("DROP TABLE IF EXISTS brugere");

        $this->info("Bruger-import fuldført med succes!");
        return 0;
    }
}