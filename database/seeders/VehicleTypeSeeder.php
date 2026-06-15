<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VehicleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Truck' => 200000, 
            'Pickup' => 100000, 
            'Van' => 120000, 
            'Kapal' => 500000
        ];

        foreach ($types as $type => $driverFee) {
            DB::table('vehicle_types')->insert([
                'id' => Str::uuid(),
                'name' => $type,
                'driver_fee' => $driverFee,
            ]);
        }
    }
}
