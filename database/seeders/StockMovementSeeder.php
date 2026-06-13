<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class StockMovementSeeder extends Seeder
{
    public function run(): void
    {
        $warehouseUser = DB::table('users')->where('email', 'warehouse@logistik.app')->first();
        $budi = DB::table('users')->where('email', 'budi@logistik.app')->first();
        
        $stockTV = DB::table('stock_items')->where('name', 'LED TV 43 Inch')->first();
        $stockBiskuit = DB::table('stock_items')->where('name', 'Biskuit Kaleng')->first();

        $movements = [];

        if ($warehouseUser && $stockTV) {
            $movements[] = [
                'id' => Str::uuid(),
                'stock_item_id' => $stockTV->id,
                'type' => 'inbound',
                'quantity' => 150,
                'reference_number' => 'PO-2026-001',
                'notes' => 'Initial stock inbound',
                'created_by' => $warehouseUser->id,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ];
            
            $movements[] = [
                'id' => Str::uuid(),
                'stock_item_id' => $stockTV->id,
                'type' => 'outbound',
                'quantity' => 5,
                'reference_number' => 'SO-2026-001',
                'notes' => 'Sample outbound',
                'created_by' => $warehouseUser->id,
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ];
        }

        if ($budi && $stockBiskuit) {
            $movements[] = [
                'id' => Str::uuid(),
                'stock_item_id' => $stockBiskuit->id,
                'type' => 'inbound',
                'quantity' => 500,
                'reference_number' => 'PO-2026-SDA-001',
                'notes' => 'Stock awal dari pabrik',
                'created_by' => $budi->id,
                'created_at' => Carbon::now()->subHours(5),
                'updated_at' => Carbon::now()->subHours(5),
            ];
        }

        if (!empty($movements)) {
            DB::table('stock_movements')->insert($movements);
        }
    }
}
