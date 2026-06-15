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
            'price_per_km' => 2500, // Rp 2.500 per KM
            'price_per_kg' => 1500, // Rp 1.500 per KG
            'price_per_cbm' => 0,
            'fixed_price' => 0,
            'is_active' => true,
        ]);

        // 2. Specific Tariffs for Master Routes
        $routes = Route::all();
        foreach ($routes as $route) {
            Tariff::create([
                'route_id' => $route->id,
                'price_per_km' => 0, // Fixed cost logic used for Transit
                'price_per_kg' => 500, // Rp 500 per KG weight penalty
                'price_per_cbm' => 0,
                'fixed_price' => 500000, // Rp 500.000 fixed price per transit
                'is_active' => true,
            ]);
        }
    }
}
