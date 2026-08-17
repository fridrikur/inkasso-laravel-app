<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
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

        // 2. Opret KUN System Admin
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
    }
}