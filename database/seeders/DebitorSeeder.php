<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Debitorer;

class DebitorSeeder extends Seeder
{
    public function run(): void
    {
        if (Debitorer::count() === 0) {
            $danskeByer = [
                ['postnr' => 6400, 'by' => 'Sønderborg'],
                ['postnr' => 5000, 'by' => 'Odense C'],
                ['postnr' => 8000, 'by' => 'Aarhus C'],
            ];
            $tilfaeldigeNavne = ['Lars Jensen', 'Anna Hansen', 'Peter Nielsen', 'Mette Jensen', 'Henrik Poulsen'];
            $gader = ['Nørregade', 'Søndergade', 'Hovedgaden', 'Kirkegade', 'Bredgade'];
            $bemaerkninger = [
                'Ønsker afdragsordning.',
                'Har rykket for betaling flere gange.',
                'Ingen svar på telefon eller mail.',
                'Lovet betaling d. 1. i næste måned.',
                null
            ];

            for ($d = 1; $d <= 100; $d++) {
                $tilfaeldigLokation = $danskeByer[array_rand($danskeByer)];
                $navn = $tilfaeldigeNavne[array_rand($tilfaeldigeNavne)] . ' ' . rand(1, 99);

                Debitorer::create([
                    'navn' => $navn,
                    'pnr' => rand(100000, 999999) . '-' . rand(1000, 9999),
                    'adresse' => rand(1, 150) . ' ' . $gader[array_rand($gader)],
                    'postnr' => $tilfaeldigLokation['postnr'],
                    'tlf' => '20' . rand(100000, 999999),
                    'mobil' => '50' . rand(100000, 999999),
                    'email' => strtolower(Str::slug($navn)) . rand(1, 999) . '@gmail.com',
                    'kontakt_bemaerkning' => $bemaerkninger[array_rand($bemaerkninger)],
                ]);
            }
        }
    }
}