<?php

namespace Database\Seeders;

use App\Models\Route;
use App\Models\RouteVersion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RouteVersionSeeder extends Seeder
{
    public function run(): void
    {
        $routes = Route::all();
        
        foreach ($routes as $route) {
            RouteVersion::create([
                'id' => Str::uuid(),
                'route_id' => $route->id,
                'source_api' => 'Google Maps',
                'distance_km' => rand(50, 500),
                'duration_min' => rand(60, 600),
                'calculated_at' => now(),
            ]);
        }
    }
}
