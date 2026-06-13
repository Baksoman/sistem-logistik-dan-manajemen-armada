<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DriverProfileSeeder extends Seeder
{
    public function run(): void
    {
        $driver1 = DB::table('users')->where('email', 'driver@logistik.app')->first();

        $profiles = [];

        if ($driver1) {
            $profiles[] = [
                'id' => Str::uuid(),
                'user_id' => $driver1->id,
                'nik' => '3578012345678901',
                'phone' => '081234567890',
                'address' => 'Jl. Rungkut Industri No. 10, Surabaya',
                'license_number' => '123456789012',
                'license_type' => 'B1',
                'license_expired_at' => Carbon::now()->addYears(3)->toDateString(),
                'rating' => 4.8,
                'status' => 'available',
                'joined_at' => Carbon::now()->subYears(2)->toDateString(),
            ];
        }

        if (!empty($profiles)) {
            DB::table('driver_profiles')->insert($profiles);
        }
    }
}
