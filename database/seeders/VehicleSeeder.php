<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $truckType = DB::table('vehicle_types')->where('name', 'Truck')->first();
        $pickupType = DB::table('vehicle_types')->where('name', 'Pickup')->first();

        $vehicles = [];

        if ($truckType) {
            $vehicles[] = [
                'id' => Str::uuid(),
                'vehicle_type_id' => $truckType->id,
                'plate_number' => 'L 8001 AA', // Surabaya
                'brand' => 'Hino',
                'model' => 'Dutro 130 HD',
                'year' => 2020,
                'capacity_kg' => 5000,
                'capacity_volume_cbm' => 15,
                'fuel_type' => 'Diesel',
                'status' => 'available',
                'kir_expired_at' => Carbon::now()->addMonths(6)->toDateString(),
                'stnk_expired_at' => Carbon::now()->addYears(1)->toDateString(),
            ];
        }

        if ($pickupType) {
            $vehicles[] = [
                'id' => Str::uuid(),
                'vehicle_type_id' => $pickupType->id,
                'plate_number' => 'W 9123 BB', // Sidoarjo/Gresik area
                'brand' => 'Mitsubishi',
                'model' => 'L300',
                'year' => 2021,
                'capacity_kg' => 1500,
                'capacity_volume_cbm' => 4,
                'fuel_type' => 'Diesel',
                'status' => 'available',
                'kir_expired_at' => Carbon::now()->addMonths(8)->toDateString(),
                'stnk_expired_at' => Carbon::now()->addYears(2)->toDateString(),
            ];
        }

        if (!empty($vehicles)) {
            DB::table('vehicles')->insert($vehicles);
        }
    }
}
