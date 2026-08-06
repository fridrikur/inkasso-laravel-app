<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AssignAdminRoleToUser extends Seeder
{
    public function run()
    {
        $user = User::find(1);
        $user->assignRole('Admin');
    }
}