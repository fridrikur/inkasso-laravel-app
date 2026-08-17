<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kreditorer;
use App\Models\Sagsbehandler;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Opret roller
        $roles = ['Admin', 'Medarbejder', 'Kreditor'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // 2. Opret Medarbejdere
        $medarbejdere = [
            ['name' => 'Anna Hansen', 'email' => 'anna@d1k2g3db.com'],
            ['name' => 'Lars Jensen', 'email' => 'lars@d1k2g3db.com'],
        ];

        foreach ($medarbejdere as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make('123456'),
                ]
            );
            if (! $user->hasRole('Medarbejder')) {
                $user->assignRole('Medarbejder');
            }
        }

        // Sørg for at de primære kreditorer eksisterer, så vi kan knytte til dem
        $kreditorNavne = ['Danske Bank Inkasso', 'Jyske Finans A/S'];
        foreach ($kreditorNavne as $navn) {
            Kreditorer::firstOrCreate(
                ['navn' => $navn],
                [
                    'cvr' => rand(10000000, 99999999),
                    'adresse' => 'Hovedgaden 1',
                    'postnr' => 6400,
                    'email' => strtolower(\Illuminate\Support\Str::slug($navn)) . '@inkasso.dk',
                    'tlf' => '70102030',
                ]
            );
        }

        // 3. Opret Sagsbehandlere og tilknyt dem til Kreditorer
        $sagsbehandlereData = [
            [
                'navn'          => 'Mette Frederiksen',
                'email'         => 'mette@inkasso.dk',
                'tlf'           => '30123456',
                'mobil'         => '50123456',
                'kreditor_navn' => 'Danske Bank Inkasso',
                'is_hoved'      => true,
            ],
            [
                'navn'          => 'Lars Løkke',
                'email'         => 'loekke@inkasso.dk',
                'tlf'           => '30654321',
                'mobil'         => '50654321',
                'kreditor_navn' => 'Jyske Finans A/S',
                'is_hoved'      => true,
            ],
        ];

        foreach ($sagsbehandlereData as $data) {
            $sagsbehandler = Sagsbehandler::firstOrCreate(
                ['email' => $data['email']],
                [
                    'navn'  => $data['navn'],
                    'tlf'   => $data['tlf'],
                    'mobil' => $data['mobil'] ?? null, // Undgå undefined index fejl
                ]
            );

            $kreditor = Kreditorer::where('navn', $data['kreditor_navn'])->first();
            
            if ($kreditor) {
                $kreditor->sagsbehandlere()->syncWithoutDetaching([$sagsbehandler->id]);

                if (!empty($data['is_hoved'])) {
                    $kreditor->hovedsagsbehandler()->syncWithoutDetaching([$sagsbehandler->id]);
                }
                
                logger()->info("Tilknyttede sagsbehandler {$sagsbehandler->navn} til kreditor {$kreditor->navn}");
            } else {
                logger()->warning("Kreditor blev IKKE fundet: " . $data['kreditor_navn']);
            }
        }

        // 4. Opret Kreditor-brugere og tilknyt dem til de rigtige Kreditorer
        $kreditorUsersData = [
            [
                'name'          => 'Danske Bank Bruger',
                'email'         => 'kreditor@danskebank.dk',
                'kreditor_navn' => 'Danske Bank Inkasso',
            ],
            [
                'name'          => 'Jyske Finans Bruger',
                'email'         => 'kontakt@jyskefinans.dk',
                'kreditor_navn' => 'Jyske Finans A/S',
            ],
        ];

        foreach ($kreditorUsersData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make('123456'),
                ]
            );

            if (! $user->hasRole('Kreditor')) {
                $user->assignRole('Kreditor');
            }

            $kreditor = Kreditorer::where('navn', $data['kreditor_navn'])->first();
            if ($kreditor) {
                $kreditor->users()->syncWithoutDetaching([$user->id]);
            }
        }
    }
}