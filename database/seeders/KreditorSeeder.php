<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kreditorer;

class KreditorSeeder extends Seeder
{
    public function run()
    {
        $kreditorer = [
            ['navn' => 'Santander Consumer Bank', 'lotusID' => '4'],
            ['navn' => 'PartnerLeasing A/S', 'lotusID' => '1'],
            ['navn' => 'AL Finans A/S', 'lotusID' => '11'],
            ['navn' => 'Nordania A/S', 'lotusID' => '17'],
            ['navn' => 'Kjærgården Auto A/S', 'lotusID' => '31'],
            ['navn' => 'Mithiof ApS', 'lotusID' => '10'],
            ['navn' => 'VVS Installatør Kurt Jeppson ApS', 'lotusID' => '14'],
            ['navn' => 'Arne Stubbe Automobiler A/S', 'lotusID' => '25'],
            ['navn' => 'Diverse', 'lotusID' => '15'],
            ['navn' => 'SalonSupport.dk', 'lotusID' => '29'],
            ['navn' => 'Tandlæge Birgit Haargaard', 'lotusID' => '5'],
            ['navn' => 'Centrumklinikken ApS', 'lotusID' => '6'],
            ['navn' => 'Boligkontoret', 'lotusID' => '19'],
            ['navn' => 'Jyske Finans A/S', 'lotusID' => '8'],
            ['navn' => 'PartnerLeasing A/S (gældspost)', 'lotusID' => '110'],
            // 🟢 Tilføj et lille ID i navnet (eller behold dem unikke) så databasen ikke fejler
            ['navn' => 'Santander Consumer Bank (44)', 'lotusID' => '44'],
            ['navn' => 'DKG ApS / Alm. Brand Leasing', 'lotusID' => '112'],
            ['navn' => 'Max Garage ApS', 'lotusID' => '33'],
            ['navn' => 'Morehouse A/S', 'lotusID' => '50'],
            ['navn' => 'Bays Revisionskontor', 'lotusID' => '151'],
            ['navn' => 'Bjæverskov & Ørslev VVS', 'lotusID' => '152'],
            ['navn' => 'OnDrive Leasing A/S', 'lotusID' => '35'],
            ['navn' => 'CA Auto Finance Danmark A/S', 'lotusID' => '45'],
            ['navn' => 'JustDrive ApS', 'lotusID' => '55'],
            ['navn' => 'Drivalia Lease Danmark A/S', 'lotusID' => '46'],
            ['navn' => 'GLARMESTERFIRMAET WORM A/S', 'lotusID' => '60'],
            ['navn' => 'Van Mossel Automotive Group Denmark A/S', 'lotusID' => '70'],
            ['navn' => 'LX Flexleasing A/S', 'lotusID' => '80'],
            ['navn' => 'Santander Consumer Bank (40)', 'lotusID' => '40'],
        ];

        foreach ($kreditorer as $kreditor) {
            Kreditorer::updateOrCreate(
                ['lotusID' => $kreditor['lotusID']], 
                ['navn' => $kreditor['navn']]
            );
        }
    }
}