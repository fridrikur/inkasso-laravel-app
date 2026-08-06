<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        
        User::factory()->create([
            'name' => 'ADMIN user',
            'email' => 'fridrikur@egmail.com',
            'password' => '?Fel200468?',
        ]);

        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $user->assignRole($adminRole);
        }

        $this->call(RoleSeeder::class);
    }
    
}
