@extends('layouts.logistik')

@section('title', 'Route Details')

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <x-topbar />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('routes.index') }}" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">{{ $route->route_code }}</h2>
            @php
                $badgeClass = 'text-gray-700 bg-gray-100';
                if ($route->route_type === 'land') $badgeClass = 'text-amber-700 bg-amber-100';
                if ($route->route_type === 'sea') $badgeClass = 'text-blue-700 bg-blue-100';
                if ($route->route_type === 'auto' || $route->route_type === 'combined') $badgeClass = 'text-purple-700 bg-purple-100';
            @endphp
        </div>
        <div class="flex items-center gap-4">
            <span class="px-3 py-1 text-xs font-bold rounded-full shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] {{ $badgeClass }} uppercase">
                {{ $route->route_type === 'auto' ? 'Combined' : $route->route_type }}
            </span>
            <a href="{{ route('routes.version.create', $route->id) }}" class="px-5 py-2 rounded-xl font-bold text-white bg-blue-600 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#1e3a8a] transition-all text-sm flex items-center gap-2 hover:bg-blue-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create New Version
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Details Section -->
        <div class="lg:col-span-1 space-y-6">
            <x-card>
                <h3 class="text-lg font-bold text-gray-800 mb-4">Route Info</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase">Origin</p>
                        <p class="text-md font-medium text-gray-800">{{ $route->origin_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase">Destination</p>
                        <p class="text-md font-medium text-gray-800">{{ $route->destination_name }}</p>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                        <span class="text-xs font-bold text-gray-500 uppercase">Toll Cost</span>
                        <span class="text-sm font-bold text-emerald-600">Rp {{ number_format($route->toll_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-500 uppercase">Ferry Cost</span>
                        <span class="text-sm font-bold text-emerald-600">Rp {{ number_format($route->ferry_cost, 0, ',', '.') }}</span>
                    </div>
                </div>
            </x-card>

            @php
                $latestVersion = $route->routeVersions->first();
            @endphp

            @if($latestVersion)
                <x-card>
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Latest Version Details</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                            <span class="text-sm font-bold text-gray-500">Source API</span>
                            <span class="text-sm font-bold text-gray-800">{{ $latestVersion->source_api }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                            <span class="text-sm font-bold text-gray-500">Total Distance</span>
                            <span class="text-sm font-bold text-gray-800">{{ number_format($latestVersion->distance_km, 2) }} km</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                            <span class="text-sm font-bold text-gray-500">Est. Duration</span>
                            @php
                                $duration = $latestVersion->duration_min;
                                $days = floor($duration / (24 * 60));
                                $hours = floor(($duration % (24 * 60)) / 60);
                                $minutes = round($duration % 60);
                                
                                $parts = [];
                                if ($days > 0) $parts[] = $days . ' hari';
                                if ($hours > 0) $parts[] = $hours . ' jam';
                                if ($minutes > 0 || empty($parts)) $parts[] = $minutes . ' menit';
                                $durationFormatted = implode(' ', $parts);
                            @endphp
                            <span class="text-sm font-bold text-gray-800">{{ $durationFormatted }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-2">
                            <span class="text-sm font-bold text-gray-500">Calculated At</span>
                            <span class="text-sm font-bold text-gray-800">{{ $latestVersion->calculated_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </x-card>
            @endif
        </div>

        <!-- Map Section -->
        <div class="lg:col-span-2 space-y-6">
            <x-card class="h-[600px] p-0 overflow-hidden">
                <div id="map" class="w-full h-full z-0"></div>
            </x-card>

            <x-card>
                <h3 class="text-lg font-bold text-gray-800 mb-4">Version History</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead>
                            <tr class="border-b border-gray-300 text-gray-500 text-xs tracking-widest uppercase">
                                <th class="py-3 px-4 font-bold">Version #</th>
                                <th class="py-3 px-4 font-bold">Distance</th>
                                <th class="py-3 px-4 font-bold">Duration</th>
                                <th class="py-3 px-4 font-bold">Source</th>
                                <th class="py-3 px-4 font-bold">Date</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm font-medium text-gray-700">
                            @foreach($route->routeVersions as $idx => $version)
                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                    <td class="py-3 px-4 font-bold text-blue-600">v{{ $route->routeVersions->count() - $idx }}</td>
                                    <td class="py-3 px-4">{{ number_format($version->distance_km, 2) }} km</td>
                                    <td class="py-3 px-4">{{ round($version->duration_min) }} min</td>
                                    <td class="py-3 px-4">{{ $version->source_api }}</td>
                                    <td class="py-3 px-4">{{ $version->calculated_at->format('d M Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>

    @if($latestVersion)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var map = L.map('map').setView([-2.5489, 118.0149], 5);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            var geojsonFeature = {!! json_encode($latestVersion->polyline_geojson) !!};
            var waypoints = {!! json_encode($latestVersion->waypoints) !!};
            
            var routeType = "{{ $route->route_type }}";
            var color = '#8b5cf6'; // auto
            if (routeType === 'land') color = '#dc2626';
            if (routeType === 'sea') color = '#3b82f6';

            var routeLayer = L.geoJSON(geojsonFeature, {
                style: {
                    color: color,
                    weight: 5,
                    opacity: 0.8
                }
            }).addTo(map);

            // Draw waypoints
            waypoints.forEach((wp, index) => {
                let wpColor = index === 0 ? 'green' : (index === waypoints.length - 1 ? 'red' : 'blue');
                L.circleMarker([wp[1], wp[0]], {
                    color: wpColor,
                    fillColor: wpColor,
                    fillOpacity: 0.8,
                    radius: 6
                }).bindPopup(index === 0 ? 'Origin' : (index === waypoints.length - 1 ? 'Destination' : 'Stop')).addTo(map);
            });

            // Fit map to route
            map.fitBounds(routeLayer.getBounds(), { padding: [50, 50] });
        });
    </script>
    @endif
@endsection
