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
        $this->call([
            // Auth Domain
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            
            // Vehicle Domain
            VehicleTypeSeeder::class,
            VehicleSeeder::class,
            DriverProfileSeeder::class,
            
            // Warehouse Domain
            WarehouseSeeder::class,
            ItemCategorySeeder::class,
            UnitTypeSeeder::class,
            StockItemSeeder::class,
            
            // General / Master Domain
            CustomerSeeder::class,
            RouteSeeder::class,
            CostCategorySeeder::class,
        ]);
    }
}
