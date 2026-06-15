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
            VehicleMaintenanceSeeder::class,
            
            // Warehouse Domain
            WarehouseSeeder::class,
            ZoneAndRackSeeder::class,
            ItemCategorySeeder::class,
            UnitTypeSeeder::class,
            StockItemSeeder::class,
            StockMovementSeeder::class,
            
            // General / Master Domain
            CustomerSeeder::class,
            // RouteSeeder::class,
            CostCategorySeeder::class,
            TariffSeeder::class,
        ]);
    }
}
