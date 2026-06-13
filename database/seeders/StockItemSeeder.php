<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            $items = [
                [
                    'id' => Str::uuid(),
                    'warehouse_id' => $warehouseSby->id,
                    'category_id' => $catElektronik->id,
                    'unit_type_id' => $unitBox->id,
                    'sku' => 'ELK-TV-001',
                    'name' => 'LED TV 43 Inch',
                    'quantity' => 150,
                    'min_quantity' => 20,
                    'weight_kg' => 8.5,
                    'volume_cbm' => 0.12,
                    'zone' => 'A',
                    'bin_location' => 'A-01-01',
                ],
                [
                    'id' => Str::uuid(),
                    'warehouse_id' => $warehouseSby->id,
                    'category_id' => $catMakanan->id,
                    'unit_type_id' => $unitBox->id,
                    'sku' => 'MKN-MIE-001',
                    'name' => 'Mie Instan Goreng 1 Dus',
                    'quantity' => 1000,
                    'min_quantity' => 100,
                    'weight_kg' => 3.5,
                    'volume_cbm' => 0.05,
                    'zone' => 'C',
                    'bin_location' => 'C-05-02',
                ],
                [
                    'id' => Str::uuid(),
                    'warehouse_id' => $warehouseSby->id,
                    'category_id' => $catElektronik->id,
                    'unit_type_id' => $unitPcs->id,
                    'sku' => 'ELK-AC-002',
                    'name' => 'AC Split 1 PK',
                    'quantity' => 80,
                    'min_quantity' => 10,
                    'weight_kg' => 25.0,
                    'volume_cbm' => 0.3,
                    'zone' => 'A',
                    'bin_location' => 'A-02-01',
                ]
            ];

            DB::table('stock_items')->insert($items);
        }
    }
}
