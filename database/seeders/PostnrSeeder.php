<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostnrSeeder extends Seeder
{
    public function run(): void
    {
        // Vi tjekker om tabellen allerede har data for at undgå duplikeringer
        if (DB::table('postnr')->count() > 0) {
            return;
        }

        $postnumre = [
            // Storkøbenhavn & Nordsjælland
            ['postnr' => 1050, 'by' => 'København K'],
            ['postnr' => 1200, 'by' => 'København K'],
            ['postnr' => 1400, 'by' => 'København K'],
            ['postnr' => 1500, 'by' => 'København V'],
            ['postnr' => 1800, 'by' => 'Frederiksberg C'],
            ['postnr' => 2100, 'by' => 'København Ø'],
            ['postnr' => 2200, 'by' => 'København N'],
            ['postnr' => 2300, 'by' => 'København S'],
            ['postnr' => 2400, 'by' => 'København NV'],
            ['postnr' => 2500, 'by' => 'Valby'],
            ['postnr' => 2600, 'by' => 'Glostrup'],
            ['postnr' => 2700, 'by' => 'Brønshøj'],
            ['postnr' => 2800, 'by' => 'Kongens Lyngby'],
            ['postnr' => 2900, 'by' => 'Hellerup'],
            ['postnr' => 3000, 'by' => 'Helsingør'],
            ['postnr' => 3400, 'by' => 'Hillerød'],
            ['postnr' => 3800, 'by' => 'Eksisterende / Andet'],

            // Sjælland & Øerne
            ['postnr' => 4000, 'by' => 'Roskilde'],
            ['postnr' => 4100, 'by' => 'Ringsted'],
            ['postnr' => 4200, 'by' => 'Slagelse'],
            ['postnr' => 4300, 'by' => 'Holbæk'],
            ['postnr' => 4700, 'by' => 'Næstved'],
            ['postnr' => 4900, 'by' => 'Nakskov'],

            // Fyn & omliggende øer
            ['postnr' => 5000, 'by' => 'Odense C'],
            ['postnr' => 5200, 'by' => 'Odense V'],
            ['postnr' => 5700, 'by' => 'Svendborg'],
            ['postnr' => 5800, 'by' => 'Nyborg'],
            ['postnr' => 5900, 'by' => 'Rudkøbing'],

            // Sønderjylland & Syddanmark (lokalområdet)
            ['postnr' => 6000, 'by' => 'Kolding'],
            ['postnr' => 6100, 'by' => 'Haderslev'],
            ['postnr' => 6200, 'by' => 'Aabenraa'],
            ['postnr' => 6300, 'by' => 'Gråsten'],
            ['postnr' => 6330, 'by' => 'Padborg'],
            ['postnr' => 6400, 'by' => 'Sønderborg'],
            ['postnr' => 6430, 'by' => 'Nordborg'],
            ['postnr' => 6470, 'by' => 'Sydals'],
            ['postnr' => 6500, 'by' => 'Vojens'],
            ['postnr' => 6600, 'by' => 'Vejen'],
            ['postnr' => 6700, 'by' => 'Esbjerg'],
            ['postnr' => 6800, 'by' => 'Varde'],
            ['postnr' => 7000, 'by' => 'Fredericia'],
            ['postnr' => 7100, 'by' => 'Vejle'],

            // Midtjylland
            ['postnr' => 8000, 'by' => 'Aarhus C'],
            ['postnr' => 8200, 'by' => 'Aarhus N'],
            ['postnr' => 8600, 'by' => 'Silkeborg'],
            ['postnr' => 8700, 'by' => 'Horsens'],
            ['postnr' => 8800, 'by' => 'Viborg'],
            ['postnr' => 8900, 'by' => 'Randers C'],

            // Nordjylland
            ['postnr' => 9000, 'by' => 'Aalborg'],
            ['postnr' => 9300, 'by' => 'Sæby'],
            ['postnr' => 9800, 'by' => 'Hjørring'],
            ['postnr' => 9900, 'by' => 'Frederikshavn'],
        ];

        DB::table('postnr')->insert($postnumre);
    }
}