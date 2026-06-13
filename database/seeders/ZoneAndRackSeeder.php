<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Warehouse;
use App\Models\Zone;
use App\Models\Rack;

class ZoneAndRackSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = Warehouse::all();

        foreach ($warehouses as $warehouse) {
            // Create Zones
            $zoneA = Zone::create([
                'warehouse_id' => $warehouse->id,
                'name' => 'Zone A',
                'description' => 'Fast moving items',
            ]);
            $zoneB = Zone::create([
                'warehouse_id' => $warehouse->id,
                'name' => 'Zone B',
                'description' => 'Normal moving items',
            ]);
            $zoneC = Zone::create([
                'warehouse_id' => $warehouse->id,
                'name' => 'Zone C',
                'description' => 'Slow moving items',
            ]);

            // Create Racks for Zone A
            for ($i = 1; $i <= 3; $i++) {
                Rack::create([
                    'zone_id' => $zoneA->id,
                    'name' => 'A-0' . $i,
                    'description' => 'Rack A-0' . $i . ' in Zone A',
                ]);
            }

            // Create Racks for Zone B
            for ($i = 1; $i <= 3; $i++) {
                Rack::create([
                    'zone_id' => $zoneB->id,
                    'name' => 'B-0' . $i,
                    'description' => 'Rack B-0' . $i . ' in Zone B',
                ]);
            }

            // Create Racks for Zone C
            for ($i = 1; $i <= 2; $i++) {
                Rack::create([
                    'zone_id' => $zoneC->id,
                    'name' => 'C-0' . $i,
                    'description' => 'Rack C-0' . $i . ' in Zone C',
                ]);
            }
        }
    }
}
