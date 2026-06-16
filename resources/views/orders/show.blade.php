@extends('layouts.logistik')

@section('title', 'Order Details')

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>

    <x-topbar />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('orders.index') }}" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <p class="text-gray-500 text-lg font-medium">Order Detail</p>
                <h1 class="text-2xl font-black text-gray-800 tracking-wider">{{ $order->order_number }}</h1>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <span class="inline-block px-5 py-2 font-black text-sm tracking-widest uppercase rounded-xl shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] 
                @if(in_array($order->status, ['Completed', 'Delivered'])) bg-emerald-100 text-emerald-700
                @elseif($order->status == 'Cancelled') bg-red-100 text-red-700
                @elseif($order->status == 'Arrived at Hub') bg-purple-100 text-purple-700
                @else bg-blue-100 text-blue-700 @endif
            ">
                {{ $order->status }}
            </span>

            <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="flex items-center gap-2">
                @csrf
                @method('PATCH')
                <select name="status" class="bg-gray-100 rounded-xl px-4 py-2 font-bold text-gray-700 border-none focus:ring-2 focus:ring-blue-500 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] text-sm outline-none">
                    <option value="Pending Approval" {{ $order->status == 'Pending Approval' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="Confirmed" {{ $order->status == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="Assigned" {{ $order->status == 'Assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="Arrived at Hub" {{ $order->status == 'Arrived at Hub' ? 'selected' : '' }}>Arrived at Hub</option>
                    <option value="Completed" {{ $order->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Delivered" {{ $order->status == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="Cancelled" {{ $order->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#1e3a8a] transition-all text-sm">
                    Update
                </button>
            </form>
        </div>
    </div>

    @php
        $latestShipment = $order->shipments->sortByDesc('created_at')->first();
        $routeGeojsonObj = null;
        $routeWaypointsObj = null;
        if ($latestShipment && $latestShipment->routeVersion) {
            $routeGeojson = $latestShipment->routeVersion->polyline_geojson ?? 'null';
            $routeGeojsonObj = is_string($routeGeojson) && $routeGeojson !== 'null' ? json_decode($routeGeojson) : $routeGeojson;
            $routeWaypointsObj = $latestShipment->routeVersion->waypoints ?? [];
        }

        $mapData = [
            'origin' => [
                'name' => $order->originWarehouse->name ?? 'Origin',
                'lat' => (float)($order->originWarehouse->latitude ?? 0),
                'lng' => (float)($order->originWarehouse->longitude ?? 0),
            ],
            'current' => $order->currentWarehouse ? [
                'name' => $order->currentWarehouse->name,
                'lat' => (float)($order->currentWarehouse->latitude ?? 0),
                'lng' => (float)($order->currentWarehouse->longitude ?? 0),
            ] : null,
            'destination' => [
                'address' => $order->destination_address,
                'lat' => (float)$order->destination_latitude,
                'lng' => (float)$order->destination_longitude,
            ],
            'routeGeojson' => $routeGeojsonObj,
            'routeWaypoints' => $routeWaypointsObj
        ];
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: Info & Items -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Info -->
                <x-card class="h-full border border-gray-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Customer Info</h3>
                    </div>
                    
                    <div class="space-y-4 text-sm mt-6">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Company / Name</p>
                            <p class="font-bold text-gray-800 text-lg">{{ $order->customer->company_name ?? '-' }}</p>
                            <p class="text-gray-500">{{ $order->customer->name ?? '' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Destination Address</p>
                            <p class="font-bold text-gray-800">{{ $order->destination_address }}</p>
                        </div>
                    </div>
                </x-card>

                <!-- Logistics Progress -->
                <x-card class="h-full border border-gray-100 bg-gradient-to-br from-blue-50 to-white">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Logistics Track</h3>
                    </div>
                    
                    <div class="space-y-4 text-sm mt-6">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Origin Warehouse</p>
                            <p class="font-bold text-gray-800">{{ $order->originWarehouse->name ?? '-' }}</p>
                        </div>
                        @if($order->currentWarehouse && $order->currentWarehouse->id !== $order->origin_warehouse_id)
                        <div>
                            <p class="text-xs font-bold text-purple-400 uppercase tracking-widest mb-1">Current Hub</p>
                            <p class="font-black text-purple-700 text-lg">{{ $order->currentWarehouse->name }}</p>
                        </div>
                        @endif
                        
                        <div class="pt-2 border-t border-gray-200">
                            <p class="font-bold text-gray-800">Weight: {{ $order->total_weight }} kg</p>
                            <p class="font-bold text-gray-800">Volume: {{ $order->total_volume }} cbm</p>
                            <p class="font-bold text-gray-800 mt-2">Est. Distance: <span class="text-blue-600">{{ $order->estimated_distance_km ? $order->estimated_distance_km . ' KM' : '-' }}</span></p>
                            <p class="font-bold text-gray-800">Quoted Price: <span class="text-emerald-600 text-lg font-black">{{ $order->quoted_price ? 'Rp ' . number_format($order->quoted_price, 0, ',', '.') : '-' }}</span></p>
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Items -->
            <x-card>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-green-100 text-green-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Package Contents</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-gray-400 text-xs tracking-widest uppercase border-b-2 border-gray-100">
                                <th class="py-3 px-2 font-bold">Item SKU</th>
                                <th class="py-3 px-2 font-bold">Name</th>
                                <th class="py-3 px-2 font-bold text-right">Quantity</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach($order->items as $item)
                                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                    <td class="py-4 px-2 font-black text-gray-800">{{ $item->stockItem->sku ?? '-' }}</td>
                                    <td class="py-4 px-2 font-medium text-gray-700">{{ $item->stockItem->name ?? '-' }}</td>
                                    <td class="py-4 px-2 font-bold text-right text-lg text-blue-600">{{ $item->quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
            
            @if($order->shipments->count() > 0)
            <!-- Shipment History -->
            <x-card>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Shipment History</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-gray-400 text-xs tracking-widest uppercase border-b-2 border-gray-100">
                                <th class="py-3 px-2 font-bold">Shipment No.</th>
                                <th class="py-3 px-2 font-bold">Vehicle</th>
                                <th class="py-3 px-2 font-bold">Status</th>
                                <th class="py-3 px-2 font-bold text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach($order->shipments as $shipment)
                                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                    <td class="py-4 px-2 font-black text-gray-800">{{ $shipment->shipment_number }}</td>
                                    <td class="py-4 px-2 font-medium text-gray-700">{{ $shipment->vehicle->plate_number ?? '-' }}</td>
                                    <td class="py-4 px-2">
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold {{ $shipment->status === 'Delivered' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $shipment->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-2 text-right">
                                        <a href="{{ route('shipments.show', $shipment->id) }}" class="text-blue-500 font-bold hover:underline">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
            @endif
        </div>

        <!-- Right Column: Map Tracker -->
        <div class="lg:col-span-5 xl:col-span-4 relative">
            <div class="sticky top-6">
                <x-card class="p-2 shadow-[8px_8px_16px_#d1d5db,-8px_-8px_16px_#ffffff] border-4 border-white mb-6">
                    <div id="order-map" class="w-full h-[500px] rounded-2xl z-0"></div>
                </x-card>
                
                <div class="text-center text-gray-400 text-xs font-bold uppercase tracking-widest">
                    <span class="inline-block w-3 h-3 rounded-full bg-green-500 mr-1 align-middle"></span> Origin
                    @if($order->currentWarehouse && $order->currentWarehouse->id !== $order->origin_warehouse_id)
                        <span class="inline-block w-3 h-3 rounded-full bg-purple-500 ml-3 mr-1 align-middle"></span> Current Hub
                    @endif
                    <span class="inline-block w-3 h-3 rounded-full bg-red-500 ml-3 mr-1 align-middle"></span> Destination
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            const data = @json($mapData);

            const map = L.map('order-map').setView([data.origin.lat, data.origin.lng], 6);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            const bounds = [];

            // Origin
            if (data.origin.lat && data.origin.lng) {
                L.circleMarker([data.origin.lat, data.origin.lng], {
                    color: '#10b981', fillColor: '#10b981', fillOpacity: 1, radius: 8
                }).bindPopup(`<b>${data.origin.name}</b><br>Origin`).addTo(map);
                bounds.push(L.latLng(data.origin.lat, data.origin.lng));
            }
            
            // Current Hub
            if (data.current && data.current.lat && data.current.lng) {
                L.circleMarker([data.current.lat, data.current.lng], {
                    color: '#8b5cf6', fillColor: '#8b5cf6', fillOpacity: 1, radius: 9
                }).bindPopup(`<b>${data.current.name}</b><br>Current Location`).addTo(map);
                bounds.push(L.latLng(data.current.lat, data.current.lng));
            }

            // Destination
            if (data.destination.lat && data.destination.lng) {
                L.circleMarker([data.destination.lat, data.destination.lng], {
                    color: '#ef4444', fillColor: '#ef4444', fillOpacity: 1, radius: 8
                }).bindPopup(`<b>Destination</b><br>${data.destination.address}`).addTo(map);
                bounds.push(L.latLng(data.destination.lat, data.destination.lng));
                
                if (data.routeGeojson) {
                    // Draw actual Shipment Route
                    const routeLayer = L.geoJSON(data.routeGeojson, {
                        style: { color: '#3b82f6', weight: 4, opacity: 0.8 }
                    }).addTo(map);
                    bounds.push(routeLayer.getBounds());
                    
                    // Draw Shipment Waypoints
                    if (data.routeWaypoints && data.routeWaypoints.length > 0) {
                        data.routeWaypoints.forEach((wp, index) => {
                            L.circleMarker([wp[1], wp[0]], {
                                color: '#3b82f6', fillColor: '#ffffff', fillOpacity: 1, weight: 2, radius: 6
                            }).bindPopup(`<b>Shipment Waypoint ${index + 1}</b>`).addTo(map);
                            bounds.push(L.latLng(wp[1], wp[0]));
                        });
                        
                        // Draw dashed line from last shipment waypoint to final destination
                        const lastWp = data.routeWaypoints[data.routeWaypoints.length - 1];
                        L.polyline([
                            [lastWp[1], lastWp[0]],
                            [data.destination.lat, data.destination.lng]
                        ], { color: '#9ca3af', dashArray: '5, 10', weight: 3 }).addTo(map);
                    }
                    
                } else {
                    // Fallback: Draw straight dashed line from Origin/Current to Destination
                    const startLat = data.current ? data.current.lat : data.origin.lat;
                    const startLng = data.current ? data.current.lng : data.origin.lng;
                    
                    if (startLat && startLng) {
                        L.polyline([
                            [startLat, startLng],
                            [data.destination.lat, data.destination.lng]
                        ], { color: '#9ca3af', dashArray: '5, 10', weight: 3 }).addTo(map);
                    }
                }
            }

            if (bounds.length > 0) {
                const group = new L.featureGroup(bounds.map(b => L.marker(b)));
                map.fitBounds(group.getBounds(), { padding: [50, 50] });
            }
        });
    </script>
@endsection
