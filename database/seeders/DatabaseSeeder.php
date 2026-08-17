<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Kører KUN roller og Admin-bruger ved standardsysteminstallation
        $this->call([
            UserSeeder::class,
        ]);
    }
}