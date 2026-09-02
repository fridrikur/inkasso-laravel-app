<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DropdownDataSeeder extends Seeder
{
    public function run()
    {
        // Deaktiver fremmednøgler midlertidigt, så truncate ikke fejler
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('status')->truncate();
        DB::table('afslutning')->truncate();
        DB::table('ktr')->truncate();
        DB::table('udlaeg')->truncate();
        DB::table('bemaerkning')->truncate();
        DB::table('autotekst')->truncate();

        // Slå fremmednøgler til igen
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('status')->insert([
            ['tekst' => 'I gang', 'forkortelse' => 'I'],
            ['tekst' => 'Dialog', 'forkortelse' => 'D'],
            ['tekst' => 'Skal rykkes', 'forkortelse' => 'S'],
            ['tekst' => 'Betalingsaftale', 'forkortelse' => 'B'],
            ['tekst' => 'Misligholder betalingsaftale', 'forkortelse' => 'M'],
            ['tekst' => 'Bil skal retur', 'forkortelse' => 'BR'],
            ['tekst' => 'Mulig svindel', 'forkortelse' => 'MS'],
        ]);

        DB::table('afslutning')->insert([
            ['tekst' => 'Betalt', 'forkortelse' => 'b'],
            ['tekst' => 'Bil afleveret', 'forkortelse' => 'a'],
            ['tekst' => 'Opgivet/advokat', 'forkortelse' => 'o'],
            ['tekst' => 'Trukket tilbage', 'forkortelse' => 't'],
        ]);

        DB::table('ktr')->insert([
            ['tekst' => 'Købekontrakt', 'forkortelse' => 'K'],
            ['tekst' => 'Erhvervskøbekontrakt', 'forkortelse' => 'E'],
            ['tekst' => 'Leasingkontrakt', 'forkortelse' => 'L'],
            ['tekst' => 'FlexLeasingkontrakt', 'forkortelse' => 'F'],
            ['tekst' => 'Udlån', 'forkortelse' => 'U'],
            ['tekst' => 'Forbrugslån', 'forkortelse' => 'X'],
            ['tekst' => 'Personalelån', 'forkortelse' => 'P'],
            ['tekst' => 'Lejeaftale', 'forkortelse' => 'A'],
            ['tekst' => 'Blancolån', 'forkortelse' => 'B'],
            ['tekst' => 'Finansiel leasing', 'forkortelse' => 'S'],
            ['tekst' => 'Privat leasing', 'forkortelse' => 'pl'],
        ]);

        DB::table('udlaeg')->insert([
            ['tekst' => 'Ja', 'forkortelse' => '1'],
            ['tekst' => 'Nej', 'forkortelse' => '2'],
        ]);

        DB::table('bemaerkning')->insert([
            ['tekst' => 'Ingen', 'forkortelse' => 'I'],
            ['tekst' => 'Mulig svindel', 'forkortelse' => 'M'],
            ['tekst' => 'Delvist betalt', 'forkortelse' => 'D'],
        ]);

        DB::table('autotekst')->insert([
            ['id' => 1, 'tekst' => 'Sendt påkrav', 'dato' => '2020-10-29 00:29:57'],
            ['id' => 2, 'tekst' => 'Dialog – arbejder på en løsning', 'dato' => '2020-10-29 00:30:09'],
            ['id' => 3, 'tekst' => 'Forventer ajourbetaling', 'dato' => '2020-10-29 00:30:17'],
            ['id' => 4, 'tekst' => 'Forventer bil retur', 'dato' => '2020-10-29 00:30:28'],
            ['id' => 5, 'tekst' => 'Forsøgt opkald tlf / sms / mail', 'dato' => '2020-10-29 00:30:34'],
            ['id' => 6, 'tekst' => 'Indgået betalingsaftale', 'dato' => '2020-10-29 00:30:41'],
            ['id' => 7, 'tekst' => 'Besøgt – ingen hjemme', 'dato' => '2020-10-29 00:30:48'],
            ['id' => 8, 'tekst' => 'Besøgt - dialog', 'dato' => '2020-10-29 00:30:55'],
            ['id' => 9, 'tekst' => 'Klargjort til besøg', 'dato' => '2020-11-30 08:55:01'],
            ['id' => 10, 'tekst' => 'Betalt ajour', 'dato' => '2020-11-30 08:55:13'],
            ['id' => 11, 'tekst' => 'KD overholder aftalen', 'dato' => '2020-11-30 08:55:41'],
            ['id' => 12, 'tekst' => 'KD har gældsrådgiver på sagen', 'dato' => '2021-12-06 12:13:59'],
        ]);
    }
}