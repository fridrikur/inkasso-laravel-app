<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kreditorer;
use App\Models\User;

class KreditorSeeder extends Seeder
{
    public function run(): void
    {
        $kreditorerData = [
            [
                'navn'    => 'Nordic Wholesale A/S',
                'lotusID' => 'LOT-1001',
            ],
            [
                'navn'    => 'Sønderborg Byg & Anlæg ApS',
                'lotusID' => 'LOT-1002',
            ],
            [
                'navn'    => 'Danmark IT Services A/S',
                'lotusID' => 'LOT-1003',
            ],
            [
                'navn'    => 'Als Energi & Varme A.m.b.a.',
                'lotusID' => 'LOT-1004',
            ],
        ];

        foreach ($kreditorerData as $data) {
            $kreditor = Kreditorer::firstOrCreate(
                ['navn' => $data['navn']],
                ['lotusID' => $data['lotusID']]
            );

            // Tilknyt kreditor-brugeren til 'Nordic Wholesale A/S'
            if ($data['navn'] === 'Nordic Wholesale A/S') {
                $kreditorUser = User::where('email', 'kreditor@nordicwholesale.dk')->first();
                if ($kreditorUser) {
                    $kreditor->users()->syncWithoutDetaching([$kreditorUser->id]);
                }
            }
        }
    }
}