<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kreditorer;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Opret roller
        $roles = ['Admin', 'Medarbejder', 'Kreditor'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // 2. Opret Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@d1k2g3db.com'],
            [
                'name'     => 'System Admin',
                'password' => Hash::make('123456'),
            ]
        );
        if (! $admin->hasRole('Admin')) {
            $admin->assignRole('Admin');
        }

        // 3. Opret Medarbejdere
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

        // 4. Opret Kreditor-brugere og tilknyt dem til de rigtige Kreditorer via Kreditorer-modellen
        $kreditorUsersData = [
            [
                'name'          => 'Nordic Wholesale Bruger',
                'email'         => 'kreditor@nordicwholesale.dk',
                'kreditor_navn' => 'Nordic Wholesale A/S',
            ],
            [
                'name'          => 'Sønderborg Byg Bruger',
                'email'         => 'kontakt@sonderborgbyg.dk',
                'kreditor_navn' => 'Sønderborg Byg & Anlæg ApS',
            ],
        ];

        foreach ($kreditorUsersData as $data) {
            // Opret selve brugeren
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make('123456'),
                ]
            );

            // Tildel rollen 'Kreditor'
            if (! $user->hasRole('Kreditor')) {
                $user->assignRole('Kreditor');
            }

            // Find den tilsvarende Kreditorer-model og tilknyt brugeren via relationen
            $kreditor = Kreditorer::where('navn', $data['kreditor_navn'])->first();
            if ($kreditor) {
                $kreditor->users()->syncWithoutDetaching([$user->id]);
            }
        }
    }
}