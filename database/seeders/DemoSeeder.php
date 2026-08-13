<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,     // 1. Opretter Admin, Medarbejdere og Kreditor-brugere
            KreditorSeeder::class, // 2. Opretter Kreditorer og forbinder brugere
            SagerSeeder::class,      // 3. Opretter Sager knyttet til de oprettede Kreditorer
        ]);
    }
}