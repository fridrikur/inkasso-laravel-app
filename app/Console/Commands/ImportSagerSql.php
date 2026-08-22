<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportSagerSql extends Command
{
    protected $signature = 'import:sager {file : Stien til SQL filen}';
    protected $description = 'Importerer SQL filen direkte i DB og flytter data via SQL queries (Super hurtigt)';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("Filen blev ikke fundet: {$filePath}");
            return 1;
        }

        $this->info("Trin 1: Importerer hele SQL-filen til 'sager' tabellen i databasen...");

        // Hent databaseforbindelse detaljer fra config
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host     = config('database.connections.mysql.host');
        $port     = config('database.connections.mysql.port', 3306);

        // Kør MySQL kommandoen direkte i systemet (importerer alt råt uden PHP hukommelses-overhead)
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

        $this->info("Trin 2: Overfører sager fra 'sager' til 'sagers' tabellen med korrekte felt-mappings...");

        // 1. Indsæt selve sagerne i 'sagers' tabellen
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
                hovedstol, 
                renter, 
                gebyr, 
                ialt, 
                startgebyr, 
                statistik AS restgaeld_dkg, 
                indbetalt, 
                n_mdlydelse, 
                stelnr, 
                aktiv, 
                fakturanr, 
                restgaeld AS restgaeld_kreditor, 
                kode, 
                NOW(), 
                NOW()
            FROM sager
        ");

        $this->info("Trin 3: Opdaterer pivot-tabeller (relationer) via SQL joins...");

        // 1. Kreditor relation
        DB::statement("
            INSERT IGNORE INTO sager_kreditor (sag_id, kreditor_id)
            SELECT ny.id, k.id 
            FROM sager s
            JOIN sagers ny ON ny.sagsnr COLLATE utf8mb4_unicode_ci = s.sagsnr COLLATE utf8mb4_unicode_ci
            JOIN kreditors k ON k.lotusID = s.kreditorID
            WHERE s.kreditorID IS NOT NULL AND s.kreditorID != '' AND s.kreditorID != 0
        ");

        // 2. Status relation
        DB::statement("
            INSERT IGNORE INTO sager_status (sag_id, status_id)
            SELECT sagers.id, sager.status 
            FROM sager 
            JOIN sagers ON sagers.sagsnr COLLATE utf8mb4_unicode_ci = sager.sagsnr COLLATE utf8mb4_unicode_ci
            WHERE sager.status IS NOT NULL AND sager.status != '' AND sager.status != 0
        ");

        // 3. Debitor relation
        DB::statement("
            INSERT IGNORE INTO sager_debitor (sag_id, debitor_id)
            SELECT sagers.id, sager.debitorid 
            FROM sager 
            JOIN sagers ON sagers.sagsnr COLLATE utf8mb4_unicode_ci = sager.sagsnr COLLATE utf8mb4_unicode_ci
            WHERE sager.debitorid IS NOT NULL AND sager.debitorid != '' AND sager.debitorid != 0
        ");

        // 4. KTR relation
        DB::statement("
            INSERT IGNORE INTO sager_ktr (sag_id, ktr_id)
            SELECT sagers.id, sager.ktr 
            FROM sager 
            JOIN sagers ON sagers.sagsnr COLLATE utf8mb4_unicode_ci = sager.sagsnr COLLATE utf8mb4_unicode_ci
            WHERE sager.ktr IS NOT NULL AND sager.ktr != '' AND sager.ktr != 0
        ");

        // 5. Udlæg relation
        DB::statement("
            INSERT IGNORE INTO sager_udlaeg (sag_id, udlaeg_id)
            SELECT sagers.id, sager.finanseringstypeID 
            FROM sager 
            JOIN sagers ON sagers.sagsnr COLLATE utf8mb4_unicode_ci = sager.sagsnr COLLATE utf8mb4_unicode_ci
            WHERE sager.finanseringstypeID IS NOT NULL AND sager.finanseringstypeID != '' AND sager.finanseringstypeID != 0
        ");

        // 6. Afslutning relation
        DB::statement("
            INSERT IGNORE INTO sager_afslutning (sag_id, afslutning_id)
            SELECT sagers.id, sager.afleveret 
            FROM sager 
            JOIN sagers ON sagers.sagsnr COLLATE utf8mb4_unicode_ci = sager.sagsnr COLLATE utf8mb4_unicode_ci
            WHERE sager.afleveret IS NOT NULL AND sager.afleveret != '' AND sager.afleveret != 0
        ");

        // 7. Bemærkning relation (rettet til 'bemaerkning')
        DB::statement("
            INSERT IGNORE INTO sager_bemaerkning (sag_id, bemaerkning_id)
            SELECT sagers.id, sager.bemaerkning 
            FROM sager 
            JOIN sagers ON sagers.sagsnr COLLATE utf8mb4_unicode_ci = sager.sagsnr COLLATE utf8mb4_unicode_ci
            WHERE sager.bemaerkning IS NOT NULL AND sager.bemaerkning != '' AND sager.bemaerkning != 0
        ");

        // Knyttelse af sagsbehandlere til sager i pivot-tabellen (bruger det gamle 'sager' tabellayout fra dumpet)
        $this->info("Udfylder sager_sagsbehandler (med fallback til kreditors hovedsagsbehandler)...");
        DB::statement("
            INSERT IGNORE INTO sager_sagsbehandler (sag_id, sagsbehandler_id, created_at, updated_at)
            SELECT 
                ny.id, 
                COALESCE(
                    -- 1. Prøv først at finde sagsbehandleren direkte fra sagen
                    sb_direct.id, 
                    -- 2. Hvis null/0, brug kreditorens hovedsagsbehandler som fallback
                    khs.sagsbehandler_id
                ), 
                NOW(), 
                NOW()
            FROM sager s
            JOIN sagers ny ON ny.sagsnr COLLATE utf8mb4_unicode_ci = s.sagsnr COLLATE utf8mb4_unicode_ci
            -- Find kreditor ID baseret på lotusID
            JOIN kreditors k ON k.lotusID = s.kreditorID
            -- Forsøg at matche direkte sagsbehandler
            LEFT JOIN sagsbehandlere old_sb ON old_sb.sbID COLLATE utf8mb4_unicode_ci = s.sagsbehandler COLLATE utf8mb4_unicode_ci
            LEFT JOIN sagsbehandlers sb_direct ON sb_direct.navn COLLATE utf8mb4_unicode_ci = old_sb.sagsbehandler COLLATE utf8mb4_unicode_ci
            -- Hent kreditors hovedsagsbehandler som fallback
            LEFT JOIN kreditor_hoved_sagsbehandler khs ON khs.kreditor_id = k.id
            WHERE (
                (s.sagsbehandler IS NOT NULL AND s.sagsbehandler != 0)
                OR khs.sagsbehandler_id IS NOT NULL
            )
        ");

        // Knyttelse af konsulenter til sager i pivot-tabellen
        $this->info("Udfylder sager_konsulent ved at matche på navne...");
        DB::statement("
            INSERT IGNORE INTO sager_konsulent (sag_id, konsulent_id, created_at, updated_at)
            SELECT ny.id, k.id, NOW(), NOW()
            FROM sager s
            JOIN sagers ny ON ny.sagsnr COLLATE utf8mb4_unicode_ci = s.sagsnr COLLATE utf8mb4_unicode_ci
            JOIN sagsbehandlere st ON st.sbID = s.konsulentid
            JOIN konsulenters k ON k.navn COLLATE utf8mb4_unicode_ci = st.sagsbehandler COLLATE utf8mb4_unicode_ci
            WHERE s.konsulentid IS NOT NULL AND s.konsulentid != 0
        ");
        
        $this->info("Trin 4: Sletter den midlertidige 'sager' tabel...");
        DB::statement("DROP TABLE IF EXISTS sager");

        $this->info("Hele processen er fuldført med succes!");
        return 0;
    }
}