<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RoutingService
{
    /**
     * Calculate Land Route using OSRM.
     * @param array $waypoints Array of [longitude, latitude]
     * @return array
     */
    public function calculateLandRoute(array $waypoints)
    {
        $osrmUrl = env('OSRM_URL', 'http://router.project-osrm.org/route/v1/driving');
        
        $coordinates = implode(';', array_map(function($wp) {
            return "{$wp[0]},{$wp[1]}";
        }, $waypoints));

        $url = "{$osrmUrl}/{$coordinates}?overview=full&geometries=geojson&steps=false";

        $response = Http::get($url);

        if ($response->failed() || $response->json('code') !== 'Ok') {
            Log::error('OSRM Route Calculation Failed', ['response' => $response->body()]);
            throw new \Exception("Failed to calculate land route from OSRM. Pastikan OSRM API dapat diakses.");
        }

        $data = $response->json();
        $route = $data['routes'][0];

        return [
            'source_api' => 'OSRM',
            'distance_km' => $route['distance'] / 1000,
            'duration_min' => $route['duration'] / 60,
            'geojson' => $route['geometry'],
            'waypoints' => $waypoints
        ];
    }

    /**
     * Calculate Sea Route using Searoute Microservice.
     * @param array $origin [longitude, latitude]
     * @param array $destination [longitude, latitude]
     * @return array
     */
    public function calculateSeaRoute(array $origin, array $destination)
    {
        $searouteUrl = env('SEAROUTE_API_URL', 'http://localhost:8001') . '/route/sea';

        try {
            $response = Http::timeout(10)->post($searouteUrl, [
                'origin_lon' => (float) $origin[0],
                'origin_lat' => (float) $origin[1],
                'destination_lon' => (float) $destination[0],
                'destination_lat' => (float) $destination[1],
                'units' => 'km'
            ]);

            if ($response->failed()) {
                Log::error('Searoute Calculation Failed', ['response' => $response->body()]);
                throw new \Exception("Failed to calculate sea route from Microservice. Pastikan Microservice Searoute berjalan.");
            }

            $data = $response->json();

            return [
                'source_api' => 'Searoute',
                'distance_km' => $data['distance'],
                'duration_min' => 0, // Searoute doesn't return duration
                'geojson' => $data['geojson']['geometry'],
                'waypoints' => [$origin, $destination]
            ];
        } catch (\Exception $e) {
            Log::error('Searoute Error: ' . $e->getMessage());
            throw new \Exception("Gagal menghubungi microservice Searoute. Pastikan docker-compose up sudah dijalankan untuk microservice.");
        }
    }

    /**
     * Calculate Combined Route (Auto Land -> Sea -> Land)
     * @param array $waypoints
     * @return array
     */
    public function calculateCombinedRoute(array $waypoints)
    {
        if (count($waypoints) !== 2) {
            throw new \Exception("Rute otomatis (Multimodal) saat ini hanya mendukung tepat 2 titik (Origin dan Destination).");
        }

        try {
            // Coba rute darat murni dulu (OSRM)
            return $this->calculateLandRoute($waypoints);
        } catch (\Exception $e) {
            // Jika gagal (kemungkinan beda pulau tanpa rute OSRM), proses Multimodal
            
            $origin = $waypoints[0];
            $destination = $waypoints[1];

            // 1. Rute Laut (Pelabuhan Terdekat)
            $seaRoute = $this->calculateSeaRoute($origin, $destination);
            $seaCoordinates = $seaRoute['geojson']['coordinates'];
            
            $seaStart = $seaCoordinates[0];
            $seaEnd = end($seaCoordinates);

            $totalDistance = $seaRoute['distance_km'];
            // Asumsi kecepatan kapal 37 km/h (20 knots) untuk hitung durasi laut
            $totalDuration = ($seaRoute['distance_km'] / 37) * 60;
            
            $combinedCoordinates = [];

            // 2. Rute Darat 1: Origin -> Sea Start
            try {
                $land1 = $this->calculateLandRoute([$origin, $seaStart]);
                $totalDistance += $land1['distance_km'];
                $totalDuration += $land1['duration_min'];
                $combinedCoordinates = array_merge($combinedCoordinates, $land1['geojson']['coordinates']);
            } catch (\Exception $ex) {
                // Tarik garis lurus jika rute darat gagal
                $combinedCoordinates[] = $origin;
                $combinedCoordinates[] = $seaStart;
            }

            // 3. Rute Laut
            $combinedCoordinates = array_merge($combinedCoordinates, $seaCoordinates);

            // 4. Rute Darat 2: Sea End -> Destination
            try {
                $land2 = $this->calculateLandRoute([$seaEnd, $destination]);
                $totalDistance += $land2['distance_km'];
                $totalDuration += $land2['duration_min'];
                $combinedCoordinates = array_merge($combinedCoordinates, $land2['geojson']['coordinates']);
            } catch (\Exception $ex) {
                // Tarik garis lurus jika rute darat gagal
                $combinedCoordinates[] = $seaEnd;
                $combinedCoordinates[] = $destination;
            }

            return [
                'source_api' => 'Combined (Land + Sea)',
                'distance_km' => $totalDistance,
                'duration_min' => $totalDuration,
                'geojson' => [
                    'type' => 'LineString',
                    'coordinates' => $combinedCoordinates
                ],
                'waypoints' => $waypoints
            ];
        }
    }
}
