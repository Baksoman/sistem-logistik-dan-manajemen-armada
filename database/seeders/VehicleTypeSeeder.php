<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VehicleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['Truck', 'Pickup', 'Van', 'Kapal'];

        foreach ($types as $type) {
            DB::table('vehicle_types')->insert([
                'id' => Str::uuid(),
                'name' => $type,
            ]);
        }
    }
}
