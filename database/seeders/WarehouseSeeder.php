<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            [
                'id' => Str::uuid(),
                'code' => 'WH-SUB-01',
                'name' => 'Gudang Utama Surabaya',
                'address' => 'Kawasan Industri Rungkut, Surabaya, Jawa Timur',
                'latitude' => -7.3323,
                'longitude' => 112.7601,
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'code' => 'WH-SDA-01',
                'name' => 'Gudang Transit Sidoarjo',
                'address' => 'Kawasan Industri Berbek, Sidoarjo, Jawa Timur',
                'latitude' => -7.3592,
                'longitude' => 112.7538,
                'is_active' => true,
            ],
            [
                'id' => Str::uuid(),
                'code' => 'WH-MLG-01',
                'name' => 'Gudang Distribusi Malang',
                'address' => 'Jl. Raya Singosari, Malang, Jawa Timur',
                'latitude' => -7.8890,
                'longitude' => 112.6644,
                'is_active' => true,
            ],
        ];

        DB::table('warehouses')->insert($warehouses);

        // Mapping user ke gudang (staff warehouse)
        $warehouseUser = DB::table('users')->where('email', 'warehouse@logistik.app')->first();
        $budi = DB::table('users')->where('email', 'budi@logistik.app')->first();
        $andi = DB::table('users')->where('email', 'andi@logistik.app')->first();

        $warehouseSby = DB::table('warehouses')->where('code', 'WH-SUB-01')->first();
        $warehouseSda = DB::table('warehouses')->where('code', 'WH-SDA-01')->first();
        $warehouseMlg = DB::table('warehouses')->where('code', 'WH-MLG-01')->first();

        if ($warehouseUser && $warehouseSby) {
            DB::table('warehouse_users')->updateOrInsert(
                ['warehouse_id' => $warehouseSby->id, 'user_id' => $warehouseUser->id]
            );
        }

        if ($budi && $warehouseSda) {
            DB::table('warehouse_users')->updateOrInsert(
                ['warehouse_id' => $warehouseSda->id, 'user_id' => $budi->id]
            );
        }

        if ($andi && $warehouseMlg) {
            DB::table('warehouse_users')->updateOrInsert(
                ['warehouse_id' => $warehouseMlg->id, 'user_id' => $andi->id]
            );
        }
    }
}
