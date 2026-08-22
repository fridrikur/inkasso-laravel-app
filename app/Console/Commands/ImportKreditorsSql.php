<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Kreditorer;

class ImportKreditorsSql extends Command
{
    protected $signature = 'import:kreditorer {file : Stien til SQL filen med kreditorer}';
    protected $description = 'Importerer kreditorer via lotusID og håndterer unikke navne';

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

        $this->info("Trin 1: Indlæser SQL-filen i den midlertidige 'kreditor' tabel...");
        try {
            DB::statement("DROP TABLE IF EXISTS kreditor");
            $sql = file_get_contents($filePath);
            DB::unprepared($sql);
        } catch (\Exception $e) {
            $this->error("Fejl ved indlæsning af SQL: " . $e->getMessage());
            return 1;
        }

        $this->info("Trin 2: Overfører rigtige kreditorer til 'kreditors' tabellen...");

        $gamleKreditorer = DB::table('kreditor')
            ->where('kreditorID', '!=', 0)
            ->whereNotNull('kreditorID')
            ->get();

        foreach ($gamleKreditorer as $række) {
            $navn = trim($række->firmanavn);
            $lotusId = $række->kreditorID;

            // Tjek om navnet allerede findes på en *anden* kreditor (ekskl. samme lotusID)
            $eksisterende = Kreditorer::where('navn', $navn)
                ->where('lotusID', '!=', $lotusId)
                ->exists();

            if ($eksisterende) {
                // Tilføj lotusID i parentes for at gøre navnet unikt
                $navn = "{$navn} ({$lotusId})";
            }

            Kreditorer::updateOrCreate(
                ['lotusID' => $lotusId], 
                ['navn' => $navn]
            );
        }

        $this->info("Trin 3: Sletter midlertidig tabel...");
        DB::statement("DROP TABLE IF EXISTS kreditor");

        $this->info("Kreditorimport fuldført uden dublet-fejl!");
        return 0;
    }
}