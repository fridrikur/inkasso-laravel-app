<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportSagersSql extends Command
{
    protected $signature = 'import:sager {file : Stien til SQL filen}';
    protected $description = 'Importerer rå sager og kobler primære ID-relationer';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("Filen blev ikke fundet: {$filePath}");
            return 1;
        }

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host     = config('database.connections.mysql.host');
        $port     = config('database.connections.mysql.port', 3306);

        // 1. Importér token.sql hvis den findes i mappen
        $tokenFilePath = dirname($filePath) . '/token.sql';
        if (file_exists($tokenFilePath)) {
            $this->info("Trin 0: Importerer token.sql...");
            DB::statement("DROP TABLE IF EXISTS token");
            
            $tokenCmd = sprintf(
                'mysql -h %s -P %s -u %s %s %s < %s',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                $password ? '-p' . escapeshellarg($password) : '',
                escapeshellarg($database),
                escapeshellarg($tokenFilePath)
            );
            system($tokenCmd, $tokenResult);
            
            if ($tokenResult === 0) {
                DB::statement("ALTER TABLE token CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                DB::statement("ALTER TABLE token ADD INDEX idx_token_token (token(50))");
            }
        }

        // 1.5. Importér sagsbehandlere.sql midlertidigt for at kunne oversætte gamle sbID til navne
        $sbFilePath = dirname($filePath) . '/sagsbehandlere.sql';
        if (file_exists($sbFilePath)) {
            $this->info("Trin 0.5: Importerer sagsbehandlere.sql (midlertidig)...");
            DB::statement("DROP TABLE IF EXISTS sagsbehandlere");
            
            $sbCmd = sprintf(
                'mysql -h %s -P %s -u %s %s %s < %s',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                $password ? '-p' . escapeshellarg($password) : '',
                escapeshellarg($database),
                escapeshellarg($sbFilePath)
            );
            system($sbCmd, $sbResult);
            
            if ($sbResult === 0) {
                DB::statement("ALTER TABLE sagsbehandlere CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                DB::statement("ALTER TABLE sagsbehandlere ADD INDEX idx_sb_sbid (sbID)");
            }
        }

        // 2. Ryd op og importér sager-filen
        $this->info("Rydder op og fjerner evt. eksisterende midlertidig 'sager' tabel...");
        DB::statement("DROP TABLE IF EXISTS sager");

        $this->info("Trin 1: Importerer hele SQL-filen til 'sager' tabellen...");
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
            $this->error("Fejl under rå SQL-import via terminal.");
            return 1;
        }

        // Deaktiver streng datomode for at tillade '0000-00-00'
        DB::statement("SET SESSION sql_mode = ''");
        
        $this->info("Trin 1.2: Konverterer kollation på 'sager' tabellen...");
        DB::statement("ALTER TABLE sager CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        $this->info("Trin 1.5: Opretter indekser for ydeevne...");
        DB::statement("ALTER TABLE sager ADD INDEX idx_tmp_sagsnr (sagsnr(50))");
        DB::statement("ALTER TABLE sager ADD INDEX idx_tmp_pnummer (pnummer(50))");
        DB::statement("ALTER TABLE sager ADD INDEX idx_tmp_kreditorid (kreditorID)");
        DB::statement("ALTER TABLE sager ADD INDEX idx_tmp_debitorid (debitorid)");

        // Ryd eksisterende sager og tilhørende pivot-tabeller rent først
        DB::statement("SET FOREIGN_KEY_CHECKS=0;");
        DB::statement("TRUNCATE TABLE sagers;");
        DB::statement("TRUNCATE TABLE sager_kreditor;");
        DB::statement("TRUNCATE TABLE sager_status;");
        DB::statement("TRUNCATE TABLE sager_debitor;");
        DB::statement("TRUNCATE TABLE sager_ktr;");
        DB::statement("TRUNCATE TABLE sager_udlaeg;");
        DB::statement("TRUNCATE TABLE sager_afslutning;");
        DB::statement("TRUNCATE TABLE sager_bemaerkning;");
        DB::statement("TRUNCATE TABLE sager_tokens;");
        DB::statement("TRUNCATE TABLE sager_sagsbehandler;");
        DB::statement("TRUNCATE TABLE sager_konsulent;");
        DB::statement("SET FOREIGN_KEY_CHECKS=1;");

        $this->info("Trin 2: Overfører sager til 'sagers' tabellen...");
        DB::statement("
            INSERT IGNORE INTO sagers (
                sagsnr, afsluttet, faktureret, betalt, fakturadato, modtaget, 
                senesterapport, opgivet, hovedstol, renter, gebyr, ialt, 
                startgebyr, restgaeld_dkg, indbetalt, n_mdlydelse, stelnr, 
                aktiv, fakturanr, restgaeld_kreditor, kode, created_at, updated_at
            )
            SELECT 
                sagsnr, 
                NULLIF(NULLIF(afsluttet, '1970-01-01'), ''), 
                NULLIF(NULLIF(faktureret, '1970-01-01'), ''), 
                NULLIF(NULLIF(betalt, '1970-01-01'), ''), 
                NULLIF(NULLIF(fakturadato, '1970-01-01'), ''), 
                NULLIF(NULLIF(modtaget, '1970-01-01'), ''), 
                NULLIF(NULLIF(senesterapport, '1970-01-01'), ''), 
                NULLIF(NULLIF(opgivet, '1970-01-01'), ''), 
                hovedstol, renter, gebyr, ialt, startgebyr, 
                statistik AS restgaeld_dkg, 
                indbetalt, n_mdlydelse, stelnr, aktiv, fakturanr, 
                restgaeld AS restgaeld_kreditor, 
                kode, NOW(), NOW()
            FROM sager
        ");

        $this->info("Trin 3: Opdaterer primære relationer...");

        // Kreditor relation
        DB::statement("
            INSERT IGNORE INTO sager_kreditor (sag_id, kreditor_id)
            SELECT ny.id, k.id 
            FROM sager s
            INNER JOIN sagers ny ON ny.sagsnr = s.sagsnr
            INNER JOIN kreditors k ON k.lotusID = s.kreditorID
            WHERE s.kreditorID IS NOT NULL AND s.kreditorID != '' AND s.kreditorID != 0
        ");

        // Status relation
        DB::statement("
            INSERT IGNORE INTO sager_status (sag_id, status_id)
            SELECT ny.id, s.status 
            FROM sager s 
            INNER JOIN sagers ny ON ny.sagsnr = s.sagsnr
            WHERE s.status IS NOT NULL AND s.status != '' AND s.status != 0
        ");

        // Debitor relation
        DB::statement("
            INSERT IGNORE INTO sager_debitor (sag_id, debitor_id)
            SELECT ny.id, s.debitorid 
            FROM sager s 
            INNER JOIN sagers ny ON ny.sagsnr = s.sagsnr
            WHERE s.debitorid IS NOT NULL AND s.debitorid != '' AND s.debitorid != 0
        ");

        // KTR relation
        DB::statement("
            INSERT IGNORE INTO sager_ktr (sag_id, ktr_id)
            SELECT ny.id, s.ktr 
            FROM sager s 
            INNER JOIN sagers ny ON ny.sagsnr = s.sagsnr
            WHERE s.ktr IS NOT NULL AND s.ktr != '' AND s.ktr != 0
        ");

        // Udlæg relation
        DB::statement("
            INSERT IGNORE INTO sager_udlaeg (sag_id, udlaeg_id)
            SELECT ny.id, s.finanseringstypeID 
            FROM sager s 
            INNER JOIN sagers ny ON ny.sagsnr = s.sagsnr
            WHERE s.finanseringstypeID IS NOT NULL AND s.finanseringstypeID != '' AND s.finanseringstypeID != 0
        ");

        // Afslutning relation
        DB::statement("
            INSERT IGNORE INTO sager_afslutning (sag_id, afslutning_id)
            SELECT ny.id, s.afleveret 
            FROM sager s 
            INNER JOIN sagers ny ON ny.sagsnr = s.sagsnr
            WHERE s.afleveret IS NOT NULL AND s.afleveret != '' AND s.afleveret != 0
        ");

        // Bemærkning relation
        DB::statement("
            INSERT IGNORE INTO sager_bemaerkning (sag_id, bemaerkning_id)
            SELECT ny.id, s.bemaerkning 
            FROM sager s 
            INNER JOIN sagers ny ON ny.sagsnr = s.sagsnr
            WHERE s.bemaerkning IS NOT NULL AND s.bemaerkning != '' AND s.bemaerkning != 0
        ");

        // Token relation med kollations-sikring
        DB::statement("
            INSERT IGNORE INTO sager_tokens (sag_id, token_id, created_at, updated_at)
            SELECT ny.id, t.id, NOW(), NOW()
            FROM sager s
            INNER JOIN sagers ny ON ny.sagsnr = s.sagsnr
            INNER JOIN token t ON t.token COLLATE utf8mb4_unicode_ci = s.pnummer COLLATE utf8mb4_unicode_ci
            WHERE s.pnummer IS NOT NULL AND s.pnummer != ''
        ");

        // Sagsbehandler relation
        $this->info("Udfylder sager_sagsbehandler...");
        DB::statement("
            INSERT IGNORE INTO sager_sagsbehandler (sag_id, sagsbehandler_id, created_at, updated_at)
            SELECT ny.id, sb_new.id, NOW(), NOW()
            FROM sager s
            INNER JOIN sagers ny ON ny.sagsnr = s.sagsnr
            LEFT JOIN sagsbehandlere old_sb ON old_sb.sbID = s.sagsbehandler
            INNER JOIN sagsbehandlers sb_new ON sb_new.navn COLLATE utf8mb4_unicode_ci = old_sb.sagsbehandler COLLATE utf8mb4_unicode_ci
            WHERE s.sagsbehandler IS NOT NULL AND s.sagsbehandler != '' AND s.sagsbehandler != 0
        ");

        // Konsulent relation med kollations-sikring
        $this->info("Udfylder sager_konsulent...");
        DB::statement("
            INSERT IGNORE INTO sager_konsulent (sag_id, konsulent_id, created_at, updated_at)
            SELECT ny.id, k.id, NOW(), NOW()
            FROM sager s
            INNER JOIN sagers ny ON ny.sagsnr = s.sagsnr
            INNER JOIN konsulenters k ON k.id COLLATE utf8mb4_unicode_ci = s.konsulentid COLLATE utf8mb4_unicode_ci
            WHERE s.konsulentid IS NOT NULL AND s.konsulentid != '' AND s.konsulentid != 0
        ");
        
        // Slet de midlertidige tabeller igen
        $this->info("Trin 4: Sletter midlertidige tabeller ('sager' og 'sagsbehandlere')...");
        DB::statement("DROP TABLE IF EXISTS sager");
        DB::statement("DROP TABLE IF EXISTS sagsbehandlere");

        $this->info("🎉 Sager og relationer er fuldført med succes!");
        return 0;
    }
}