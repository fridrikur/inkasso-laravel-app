<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $medarbejderRole = Role::firstOrCreate(['name' => 'Medarbejder']);
        $kreditorRole = Role::firstOrCreate(['name' => 'Kreditor']);

        $createSagerPermission = Permission::firstOrCreate(['name' => 'create sager']);
        $editSagerPermission   = Permission::firstOrCreate(['name' => 'edit sager']);
        $deleteSagerPermission = Permission::firstOrCreate(['name' => 'delete sager']);

        $adminRole->syncPermissions([$createSagerPermission, $editSagerPermission, $deleteSagerPermission]);
        $medarbejderRole->syncPermissions([$createSagerPermission, $editSagerPermission]);
        $kreditorRole->syncPermissions([$createSagerPermission]);

        // 🔹 Assign first user as Admin
        $admin = User::first();
        if ($admin) {
            $admin->assignRole($adminRole);
        }
    }
}