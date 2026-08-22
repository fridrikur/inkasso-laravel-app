<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DropdownDataSeeder extends Seeder
{
    public function run()
    {
        // Vi sletter alt først for at have et rent miljø i demoen
        DB::table('status')->truncate();
        DB::table('afslutning')->truncate();
        DB::table('ktr')->truncate();
        DB::table('udlaeg')->truncate();
        DB::table('bemaerkning')->truncate();

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
    }
}