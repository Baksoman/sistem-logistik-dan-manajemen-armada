<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // User Management
            ['name' => 'manage_users', 'description' => 'Kelola pengguna sistem.'],
            ['name' => 'manage_roles', 'description' => 'Kelola role dan permission.'],

            // Fleet Management
            ['name' => 'view_vehicles', 'description' => 'Lihat data kendaraan.'],
            ['name' => 'manage_vehicles', 'description' => 'Kelola data kendaraan.'],
            ['name' => 'view_drivers', 'description' => 'Lihat data driver.'],
            ['name' => 'manage_drivers', 'description' => 'Kelola data driver.'],

            // Warehouse Management
            ['name' => 'view_warehouses', 'description' => 'Lihat data gudang.'],
            ['name' => 'manage_warehouses', 'description' => 'Kelola data gudang.'],
            ['name' => 'manage_inventory', 'description' => 'Kelola inventaris barang.'],
            ['name' => 'view_inventory', 'description' => 'Lihat inventaris barang.'],

            // Customer Management
            ['name' => 'view_customers', 'description' => 'Lihat data pelanggan.'],
            ['name' => 'manage_customers', 'description' => 'Kelola data pelanggan.'],

            // Order Management
            ['name' => 'view_orders', 'description' => 'Lihat data order.'],
            ['name' => 'create_order', 'description' => 'Buat order baru.'],
            ['name' => 'manage_orders', 'description' => 'Kelola semua order.'],

            // Shipment Management
            ['name' => 'view_shipments', 'description' => 'Lihat data pengiriman.'],
            ['name' => 'create_shipment', 'description' => 'Buat pengiriman baru.'],
            ['name' => 'update_shipment', 'description' => 'Update status pengiriman.'],
            ['name' => 'manage_shipments', 'description' => 'Kelola semua pengiriman.'],

            // Route Management
            ['name' => 'view_routes', 'description' => 'Lihat data rute.'],
            ['name' => 'manage_routes', 'description' => 'Kelola dan optimalkan rute.'],

            // GPS & Tracking
            ['name' => 'view_tracking', 'description' => 'Lihat live tracking armada.'],
            ['name' => 'send_gps_location', 'description' => 'Kirim lokasi GPS (driver).'],

            // Proof of Delivery
            ['name' => 'view_pod', 'description' => 'Lihat bukti pengiriman.'],
            ['name' => 'upload_pod', 'description' => 'Upload bukti pengiriman (driver).'],

            // Analytics
            ['name' => 'view_analytics', 'description' => 'Lihat dashboard analitik.'],
            ['name' => 'view_costs', 'description' => 'Lihat data biaya operasional.'],
            ['name' => 'manage_costs', 'description' => 'Kelola biaya operasional.'],

            // System
            ['name' => 'system_configuration', 'description' => 'Konfigurasi sistem.'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission['name']], $permission);
        }

        // Assign permissions to roles
        $superAdmin = Role::where('name', 'Super Admin')->first();
        $adminLogistik = Role::where('name', 'Admin Logistik')->first();
        $staffWarehouse = Role::where('name', 'Staff Warehouse')->first();
        $driver = Role::where('name', 'Driver')->first();

        $allPermissions = Permission::all();

        // Super Admin gets all permissions
        $superAdmin?->permissions()->sync($allPermissions->pluck('id'));

        // Admin Logistik
        $adminLogistikPermissions = Permission::whereIn('name', [
            'view_vehicles', 'view_drivers',
            'view_customers', 'manage_customers',
            'view_orders', 'create_order', 'manage_orders',
            'view_shipments', 'create_shipment', 'update_shipment', 'manage_shipments',
            'view_routes', 'manage_routes',
            'view_tracking',
            'view_pod',
            'view_analytics', 'view_costs', 'manage_costs',
            'view_warehouses', 'view_inventory',
        ])->get();
        $adminLogistik?->permissions()->sync($adminLogistikPermissions->pluck('id'));

        // Staff Warehouse
        $staffWarehousePermissions = Permission::whereIn('name', [
            'view_warehouses',
            'view_inventory', 'manage_inventory',
            'view_orders',
        ])->get();
        $staffWarehouse?->permissions()->sync($staffWarehousePermissions->pluck('id'));

        // Driver
        $driverPermissions = Permission::whereIn('name', [
            'view_shipments', 'update_shipment',
            'send_gps_location',
            'view_pod', 'upload_pod',
        ])->get();
        $driver?->permissions()->sync($driverPermissions->pluck('id'));
    }
}
