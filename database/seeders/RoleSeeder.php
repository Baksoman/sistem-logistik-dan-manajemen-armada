<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'description' => 'Memiliki akses penuh terhadap seluruh sistem.',
            ],
            [
                'name' => 'Admin Logistik',
                'description' => 'Bertanggung jawab terhadap proses pengiriman dan manajemen armada.',
            ],
            [
                'name' => 'Staff Warehouse',
                'description' => 'Bertanggung jawab terhadap operasional gudang dan inventaris.',
            ],
            [
                'name' => 'Driver',
                'description' => 'Bertanggung jawab terhadap proses pengiriman barang.',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }
    }
}
