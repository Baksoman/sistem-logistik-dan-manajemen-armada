<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocationSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (!$query || strlen($query) < 3) {
            return response()->json([]);
        }

        $results = [];

        // 1. Search Warehouses
        $warehouses = Warehouse::where('name', 'like', "%{$query}%")->limit(5)->get();
        foreach ($warehouses as $warehouse) {
            $results[] = [
                'type' => 'warehouse',
                'id' => $warehouse->id,
                'name' => $warehouse->name . ' (Warehouse)',
                'lat' => $warehouse->latitude,
                'lng' => $warehouse->longitude,
            ];
        }

        // 2. Search Customers
        $customers = Customer::where('company_name', 'like', "%{$query}%")->limit(5)->get();
        foreach ($customers as $customer) {
            $results[] = [
                'type' => 'customer',
                'id' => $customer->id,
                'name' => $customer->company_name . ' (Customer)',
                'lat' => $customer->latitude,
                'lng' => $customer->longitude,
            ];
        }

        // 3. Search OSM Nominatim API
        try {
            $osmResponse = Http::withHeaders([
                'User-Agent' => 'WFD-Logistic-App/1.0',
            ])->get('https://nominatim.openstreetmap.org/search', [
                'format' => 'json',
                'q' => $query,
                'limit' => 5,
            ]);

            if ($osmResponse->successful()) {
                $osmData = $osmResponse->json();
                foreach ($osmData as $place) {
                    $results[] = [
                        'type' => 'public',
                        'id' => null,
                        'name' => $place['display_name'],
                        'lat' => $place['lat'],
                        'lng' => $place['lon'],
                    ];
                }
            }
        } catch (\Exception $e) {
            // Ignore OSM errors so local results still show
        }

        return response()->json($results);
    }
}
