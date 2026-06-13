<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UnitTypeSeeder extends Seeder
{
    public function run(): void
    {
        $units = ['PCS', 'BOX', 'KG', 'TON', 'PALLET', 'CBM'];

        foreach ($units as $unit) {
            DB::table('unit_types')->insert([
                'id' => Str::uuid(),
                'name' => $unit,
            ]);
        }
    }
}
