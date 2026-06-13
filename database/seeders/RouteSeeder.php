<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RouteSeeder extends Seeder
{
    public function run(): void
    {
        $routes = [
            [
                'id' => Str::uuid(),
                'route_code' => 'RT-SUB-SDA',
                'route_type' => 'land',
                'origin_name' => 'Gudang Utama Surabaya',
                'destination_name' => 'Sidoarjo Kota',
            ],
            [
                'id' => Str::uuid(),
                'route_code' => 'RT-SUB-MLG',
                'route_type' => 'land',
                'origin_name' => 'Gudang Utama Surabaya',
                'destination_name' => 'Malang Kota',
            ],
            [
                'id' => Str::uuid(),
                'route_code' => 'RT-SUB-BPN',
                'route_type' => 'sea',
                'origin_name' => 'Pelabuhan Tanjung Perak, Surabaya',
                'destination_name' => 'Pelabuhan Semayang, Balikpapan',
            ],
        ];

        DB::table('routes')->insert($routes);
    }
}
