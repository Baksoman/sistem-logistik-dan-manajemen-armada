<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tariff;
use App\Models\Route;

class TariffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Direct Delivery Global Tariff (route_id = null)
        Tariff::create([
            'price_per_km' => 15000,
            'price_per_kg' => 2500,
            'price_per_cbm' => 0,
            'fixed_price' => 100000,
            'is_active' => true,
        ]);
    }
}
