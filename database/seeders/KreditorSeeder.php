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
                'lotusID' => 1001, // 🟢 Ændret fra string til int
            ],
            [
                'navn'    => 'Sønderborg Byg & Anlæg ApS',
                'lotusID' => 1002, // 🟢 Ændret fra string til int
            ],
            [
                'navn'    => 'Danmark IT Services A/S',
                'lotusID' => 1003, // 🟢 Ændret fra string til int
            ],
            [
                'navn'    => 'Als Energi & Varme A.m.b.a.',
                'lotusID' => 1004, // 🟢 Ændret fra string til int
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