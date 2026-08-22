<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kreditorer;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class LegacyUserSeeder extends Seeder
{
    public function run()
    {
        $brugere = [
            ["brugernavn" => "admin", "fornavn" => "Fridrikur", "efternavn" => "Ellefsen", "email" => "fridrikur@gmail.com", "tlf" => "29609033", "kodeord" => '$2a$12$Zy/Rn3iZocHALqO0.5.LKObLBH4rR/KWrnXTfE0eKdCkhcg1Vju8G', "kreditorID" => "0", "admin" => "-1"],
            ["brugernavn" => "SCB-DKG-LOGIN", "fornavn" => "Jonas", "efternavn" => "Roest", "email" => "dkg@dkg-aps.dk", "tlf" => "22226860", "kodeord" => '$2y$10$3sxxLffcIWgKj31hL0fT5u3P2khu12sCFtlw.TUGfjIP/UaZU5mnm', "kreditorID" => "4", "admin" => "0"],
            ["brugernavn" => "Louise_RTIC", "fornavn" => "Louise", "efternavn" => "tandlæge", "email" => "kontor@ringsted-tandklinik.dk", "tlf" => "24448278", "kodeord" => '$2y$10$3GpEfIgU3dIZLamEVQDMh.g6StMRKcMczKUS4Gt6J5MjzcJ1dhOra', "kreditorID" => "5", "admin" => "0"],
            ["brugernavn" => "Diverse-dkg", "fornavn" => "Diverse", "efternavn" => "DKG", "email" => "diverse-dkg@dkg-aps.dk", "tlf" => "0", "kodeord" => Hash::make('password123'), "kreditorID" => "4", "admin" => "0"],
            ["brugernavn" => "Jeanette_Ekman-SCB", "fornavn" => "Jeanette", "efternavn" => "Ekman", "email" => "jeanette.ekman@santanderconsumer.dk", "tlf" => "40766766", "kodeord" => '$2y$10$MrbtBhWOKKp0Z6EgLmkE7O5hId51LLadraxqqntuc.fmcS5MIy8YW', "kreditorID" => "4", "admin" => "0"],
            ["brugernavn" => "DKG-ALFINANS-LOGIN", "fornavn" => "Jonas", "efternavn" => "Roest", "email" => "dkg@dkg-aps.dk", "tlf" => "22226860", "kodeord" => '$2y$10$3Ja386cv82KE21Z1FEOss.wd60fND/RDRrsNXjcTiwKFKn2jYZqGW', "kreditorID" => "11", "admin" => "0"],
            ["brugernavn" => "Pernille_centrum", "fornavn" => "Pernille", "efternavn" => "Pernille", "email" => "reception@centrumklinikken.dk", "tlf" => "57610086", "kodeord" => '$2y$10$rMcGR76k9XW2GFDfyRB4GOgyi7J0whNeKc.l1aNZY5iRomD76JGju', "kreditorID" => "6", "admin" => "0"],
            ["brugernavn" => "admin-ce", "fornavn" => "Carl Erik", "efternavn" => "Petersen", "email" => "cep@dkg-aps.dk", "tlf" => "40401499", "kodeord" => '$2y$10$HRXAHe/LSI0Tjf/VQhIlo.XrbDTYcIQRpXbG39aeKaZwpp1wdQ/86', "kreditorID" => "0", "admin" => "-1"],
            ["brugernavn" => "dkg-carl", "fornavn" => "Carl Erik", "efternavn" => "Petersen", "email" => "cep@dkg-aps.dk", "tlf" => "40401499", "kodeord" => '$2y$10$QKLE.8Mha0/WnlKAVwiwY.AqjuRfjoDzQHJIuKe/xNniO35diqD9S', "kreditorID" => "0", "admin" => "0"],
            ["brugernavn" => "dkg-majbrit", "fornavn" => "Majbrit", "efternavn" => "Petersen", "email" => "mlp@dkg-aps.dk", "tlf" => "22226861", "kodeord" => '$2y$10$SVKGRcRp7FruB9DFF/ziZuUPewuTB9KOqye4TiGBSFgM711nbJo8u', "kreditorID" => "0", "admin" => "0"],
            ["brugernavn" => "admin-jonas", "fornavn" => "Jonas", "efternavn" => "Roest", "email" => "jonas@dkg-aps.dk", "tlf" => "22226860", "kodeord" => '$2y$10$1lzsiEhhRj2v42Ly9AXM1uCd.vID6BMPvyvtSRORrVXKiZCQRlmCG', "kreditorID" => "0", "admin" => "-1"],
            ["brugernavn" => "administrator-dkg", "fornavn" => "Jonas", "efternavn" => "Roest", "email" => "admin@dkg-aps.dk", "tlf" => "22226860", "kodeord" => '$2y$10$9CrrCouc5eu7PThpx.DRieit0/8Y1jk./DhAbm6dE3/5Mmoubiak6', "kreditorID" => "0", "admin" => "-1"],
            ["brugernavn" => "Amanda_Weber-SCB", "fornavn" => "Amanda", "efternavn" => "Weber", "email" => "Amanda.Weber@santanderconsumer.dk", "tlf" => "60818802", "kodeord" => '$2y$10$EepmgiNkO827peYTtHFc9uxgcYqySKXhidAyCtM4/PU8Gn.gkWlc.', "kreditorID" => "4", "admin" => "0"],
            ["brugernavn" => "fridrikur", "fornavn" => "Fridrikur", "efternavn" => "Ellefsen", "email" => "fridrikur@hotmail.com", "tlf" => "29609033", "kodeord" => '$2a$12$Zy/Rn3iZocHALqO0.5.LKObLBH4rR/KWrnXTfE0eKdCkhcg1Vju8G', "kreditorID" => "0", "admin" => "0"],
        ];

        foreach ($brugere as $data) {
            if (empty($data['email'])) continue;

            $fullName = trim($data['fornavn'] . ' ' . $data['efternavn']);
            if (empty($fullName)) {
                $fullName = $data['brugernavn'];
            }

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $fullName,
                    'password' => $data['kodeord'],
                    'email_verified_at' => now(),
                ]
            );

            $roleName = 'Medarbejder';
            if ($data['admin'] == '-1') {
                $roleName = 'Admin';
            } elseif ($data['kreditorID'] != '0') {
                $roleName = 'Kreditor';
            }

            if (!$user->hasRole($roleName)) {
                $user->assignRole($roleName);
            }

            if ($data['kreditorID'] != '0') {
                $kreditor = Kreditorer::where('lotusID', $data['kreditorID'])->first();
                if ($kreditor) {
                    $user->kreditorer()->syncWithoutDetaching([$kreditor->id]);
                }
            }
        }
    }
}