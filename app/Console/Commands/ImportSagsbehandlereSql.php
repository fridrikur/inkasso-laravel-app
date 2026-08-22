<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Konsulenter;

class ImportSagsbehandlereSql extends Command
{
    protected $signature = 'import:sagsbehandlere {file : Stien til SQL filen med sagsbehandlere}';
    protected $description = 'Importerer sagsbehandlere, konsulenter, hovedroller og sagsrelationer';

    public function handle()
    {
        $filePath = $this->argument('file');
        if (!file_exists($filePath) && file_exists(storage_path($filePath))) {
            $filePath = storage_path($filePath);
        }

        $this->info("Trin 1: Indlæser sagsbehandlere fra SQL...");
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement("DROP TABLE IF EXISTS sagsbehandlere");
        DB::unprepared(file_get_contents($filePath));

        // Ryd tabeller
        DB::table('sagsbehandlers')->delete();
        DB::table('konsulenters')->delete();
        DB::table('kreditor_sagsbehandler')->delete();
        DB::table('kreditor_hoved_sagsbehandler')->delete();

        // 1. Indsæt Sagsbehandlere med skudsikker dublet-håndtering på navne
        $this->info("Trin 2: Indsætter sagsbehandlere (og håndterer dublet-navne)...");
        $råData = DB::table('sagsbehandlere')
            ->where('kreditorID', '!=', 0)
            ->whereNotNull('kreditorID')
            ->get();

        $brugteNavne = [];

        foreach ($råData as $række) {
            $råNavn = trim($række->sagsbehandler);
            $nøgle = mb_strtolower($råNavn); // Gør den case-insensitive (behandler Diverse - dkg og Diverse - DKG ens)
            
            $uniktNavn = $råNavn;

            if (isset($brugteNavne[$nøgle])) {
                $brugteNavne[$nøgle]++;
                $uniktNavn = "{$råNavn}(" . $brugteNavne[$nøgle] . ")";
            } else {
                $brugteNavne[$nøgle] = 1;
            }

            // Vi tjekker også for en sikkerheds skyld om navnet allerede findes i databasen i forvejen
            $eksisterendeAntal = DB::table('sagsbehandlers')
                ->where('navn', 'like', $uniktNavn)
                ->where('id', '!=', $række->sbID)
                ->count();

            if ($eksisterendeAntal > 0) {
                $uniktNavn = "{$uniktNavn}(" . ($eksisterendeAntal + 1) . ")";
            }

            DB::table('sagsbehandlers')->updateOrInsert(
                ['id' => $række->sbID],
                [
                    'navn' => $uniktNavn,
                    'email' => trim($række->email) !== '' ? trim($række->email) : null,
                    'tlf' => ($række->tlf == 0 ? null : $række->tlf),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 2. Manuel indsættelse af de 7 konsulenter
        $this->info("Trin 3: Indsætter konsulenter manuelt...");
        $konsulenter = [
            ['id' => 41,  'navn' => 'Carl Erik',        'email' => 'dkg@dkg-aps.dk',        'tlf' => 22226860],
            ['id' => 42,  'navn' => 'Per',              'email' => 'per@dkg-aps.dk',        'tlf' => 22226862],
            ['id' => 94,  'navn' => 'Majbrit',          'email' => 'konsulent_94@dkg.local',  'tlf' => null],
            ['id' => 116, 'navn' => 'Annette',          'email' => 'sager@dkg-aps.dk',      'tlf' => null],
            ['id' => 136, 'navn' => 'Forskellige DKG',  'email' => 'konsulent_136@dkg.local', 'tlf' => null],
            ['id' => 141, 'navn' => 'Jonas',            'email' => 'jonas@dkg-aps.dk',        'tlf' => 22226894],
            ['id' => 389, 'navn' => 'Christina',        'email' => 'christina@dkg-aps.dk',  'tlf' => 22226892],
        ];

        foreach ($konsulenter as $k) {
            Konsulenter::updateOrCreate(['id' => $k['id']], $k);
        }

        // 3. Knytter sagsbehandlere til kreditorer
        $this->info("Trin 4: Knytter sagsbehandlere til kreditorer...");
        DB::statement("
            INSERT IGNORE INTO kreditor_sagsbehandler (kreditor_id, sagsbehandler_id, created_at, updated_at)
            SELECT DISTINCT k.id, st.sbID, NOW(), NOW()
            FROM sagsbehandlere st
            JOIN kreditors k ON k.lotusID = st.kreditorID
            WHERE st.kreditorID != 0 AND st.kreditorID IS NOT NULL
        ");

        // 4. Knytter hovedsagsbehandlere til kreditorer (hvor hsb er -1 eller 1)
        $this->info("Udfylder hovedsagsbehandlere for kreditorer...");
        DB::statement("
            INSERT IGNORE INTO kreditor_hoved_sagsbehandler (kreditor_id, sagsbehandler_id, created_at, updated_at)
            SELECT 
                k.id AS kreditor_id,
                sb.id AS sagsbehandler_id,
                NOW(),
                NOW()
            FROM sagsbehandlere old_sb
            JOIN kreditors k ON k.lotusID = old_sb.kreditorID
            JOIN sagsbehandlers sb ON sb.navn COLLATE utf8mb4_unicode_ci = old_sb.sagsbehandler COLLATE utf8mb4_unicode_ci
            WHERE old_sb.hsb = -1 
              AND old_sb.sagsbehandler IS NOT NULL 
              AND old_sb.sagsbehandler != ''
        ");

        //DB::statement("DROP TABLE IF EXISTS sagsbehandlere");
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info("Import og relationer fuldført med succes!");
        return 0;
    }
}