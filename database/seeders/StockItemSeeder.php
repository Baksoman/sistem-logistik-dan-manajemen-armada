<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\StockItem;
use App\Models\Zone;
use App\Models\Rack;

class StockItemSeeder extends Seeder
{
    public function run(): void
    {
        $warehouseSby = DB::table('warehouses')->where('code', 'WH-SUB-01')->first();
        
        $catElektronik = DB::table('item_categories')->where('name', 'Elektronik')->first();
        $catMakanan = DB::table('item_categories')->where('name', 'Makanan')->first();

        $unitBox = DB::table('unit_types')->where('name', 'BOX')->first();
        $unitPcs = DB::table('unit_types')->where('name', 'PCS')->first();

        if ($warehouseSby && $catElektronik && $catMakanan && $unitBox && $unitPcs) {
            StockItem::create([
                'warehouse_id' => $warehouseSby->id,
                'category_id' => $catElektronik->id,
                'unit_type_id' => $unitBox->id,
                'upc' => '8991234567001',
                'brand' => 'Samsung',
                'name' => 'LED TV 43 Inch',
                'quantity' => 150,
                'min_quantity' => 20,
                'weight_kg' => 8.5,
                'volume_cbm' => 0.12,
                'zone_id' => Zone::where('warehouse_id', $warehouseSby->id)->where('name', 'Zone A')->first()?->id,
                'rack_id' => Rack::whereHas('zone', fn($q) => $q->where('warehouse_id', $warehouseSby->id)->where('name', 'Zone A'))->first()?->id,
            ]);

            StockItem::create([
                'warehouse_id' => $warehouseSby->id,
                'category_id' => $catMakanan->id,
                'unit_type_id' => $unitBox->id,
                'upc' => '8991234567002',
                'brand' => 'Indomie',
                'name' => 'Mie Instan Goreng 1 Dus',
                'quantity' => 1000,
                'min_quantity' => 100,
                'weight_kg' => 3.5,
                'volume_cbm' => 0.05,
                'zone_id' => Zone::where('warehouse_id', $warehouseSby->id)->where('name', 'Zone C')->first()?->id,
                'rack_id' => Rack::whereHas('zone', fn($q) => $q->where('warehouse_id', $warehouseSby->id)->where('name', 'Zone C'))->first()?->id,
            ]);

            StockItem::create([
                'warehouse_id' => $warehouseSby->id,
                'category_id' => $catElektronik->id,
                'unit_type_id' => $unitPcs->id,
                'upc' => '8991234567003',
                'brand' => 'Daikin',
                'name' => 'AC Split 1 PK',
                'quantity' => 80,
                'min_quantity' => 10,
                'weight_kg' => 25.0,
                'volume_cbm' => 0.3,
                'zone_id' => Zone::where('warehouse_id', $warehouseSby->id)->where('name', 'Zone A')->first()?->id,
                'rack_id' => Rack::whereHas('zone', fn($q) => $q->where('warehouse_id', $warehouseSby->id)->where('name', 'Zone A'))->skip(1)->first()?->id,
            ]);

            $warehouseSda = DB::table('warehouses')->where('code', 'WH-SDA-01')->first();
            if ($warehouseSda) {
                StockItem::create([
                    'warehouse_id' => $warehouseSda->id,
                    'category_id' => $catMakanan->id,
                    'unit_type_id' => $unitBox->id,
                    'upc' => '8991234567004',
                    'brand' => 'Khong Guan',
                    'name' => 'Biskuit Kaleng',
                    'quantity' => 500,
                    'min_quantity' => 50,
                    'weight_kg' => 2.0,
                    'volume_cbm' => 0.03,
                    'zone_id' => Zone::where('warehouse_id', $warehouseSda->id)->where('name', 'Zone B')->first()?->id,
                    'rack_id' => Rack::whereHas('zone', fn($q) => $q->where('warehouse_id', $warehouseSda->id)->where('name', 'Zone B'))->first()?->id,
                ]);
            }

            $warehouseMlg = DB::table('warehouses')->where('code', 'WH-MLG-01')->first();
            if ($warehouseMlg) {
                StockItem::create([
                    'warehouse_id' => $warehouseMlg->id,
                    'category_id' => $catElektronik->id,
                    'unit_type_id' => $unitPcs->id,
                    'upc' => '8991234567005',
                    'brand' => 'Maspion',
                    'name' => 'Kipas Angin Berdiri',
                    'quantity' => 200,
                    'min_quantity' => 20,
                    'weight_kg' => 5.0,
                    'volume_cbm' => 0.1,
                    'zone_id' => Zone::where('warehouse_id', $warehouseMlg->id)->where('name', 'Zone A')->first()?->id,
                    'rack_id' => Rack::whereHas('zone', fn($q) => $q->where('warehouse_id', $warehouseMlg->id)->where('name', 'Zone A'))->first()?->id,
                ]);
            }
        }
    }
}
