<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@logistik.app',
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'Super Admin',
            ],
            [
                'name' => 'Admin Logistik',
                'email' => 'admin@logistik.app',
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'Admin Logistik',
            ],
            [
                'name' => 'Staff Warehouse',
                'email' => 'warehouse@logistik.app',
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'Staff Warehouse',
            ],
            [
                'name' => 'Driver Budi',
                'email' => 'driver@logistik.app',
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'Driver',
            ],
        ];

        foreach ($users as $userData) {
            $roleName = $userData['role'];
            unset($userData['role']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }
        }
    }
}
