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
        // Kører som standard grundlæggende roller og Admin-bruger
        $this->call([
            UserSeeder::class,
        ]);
    }
}