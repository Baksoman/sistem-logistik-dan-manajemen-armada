@extends('layouts.logistik')

@section('title', 'Shipment Details')

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>

    <x-topbar />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('shipments.index') }}" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <p class="text-gray-500 text-lg font-medium">Shipment Detail</p>
                <h1 class="text-2xl font-black text-gray-800 tracking-wider">{{ $shipment->shipment_number }}</h1>
            </div>
        </div>
        
        <div class="flex items-center gap-4" x-data="{ showUnloadModal: false }">
            @if($shipment->status === 'Pending')
            <form action="{{ route('shipments.start', $shipment->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-3 bg-blue-500 text-white font-black rounded-2xl shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:bg-blue-600 transition-all uppercase tracking-widest text-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Start Journey
                </button>
            </form>
            @endif

            @if($shipment->status === 'On Process')
            <button @click="showUnloadModal = true" type="button" class="px-6 py-3 bg-indigo-500 text-white font-black rounded-2xl shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:bg-indigo-600 transition-all uppercase tracking-widest text-sm flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                Unload di Transit
            </button>
            @endif

            @if(in_array($shipment->status, ['Pending', 'On Process']))
            <form action="{{ route('shipments.complete', $shipment->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-3 bg-emerald-500 text-white font-black rounded-2xl shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:bg-emerald-600 transition-all uppercase tracking-widest text-sm flex items-center gap-2" onclick="return confirm('Apakah Anda yakin menandai ini selesai? (Gunakan Unload jika ini rute transit)')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Mark as Completed
                </button>
            </form>
            @endif

            <!-- Unload Modal -->
            <div x-show="showUnloadModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black/50 backdrop-blur-sm" x-cloak>
                <div @click.away="showUnloadModal = false" class="bg-gray-50 w-full max-w-2xl rounded-3xl p-8 m-4 shadow-2xl">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-black text-gray-800">Unload Shipment</h2>
                        <button @click="showUnloadModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form action="{{ route('shipments.unload', $shipment->id) }}" method="POST">
                        @csrf
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Lokasi Gudang Tujuan Transit/Dropoff</label>
                            <select name="dropoff_warehouse_id" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0">
                                <option value="">-- Pilih Gudang Tempat Anda Berada --</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-2">Pilih gudang tempat barang diturunkan. Status paket akan berubah menjadi "Arrived at Hub".</p>
                        </div>

                        <div class="mb-8">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Paket yang Diturunkan</label>
                            <div class="space-y-3 max-h-60 overflow-y-auto pr-2">
                                @foreach($shipment->orders as $order)
                                    @if($order->pivot->status === 'Loaded')
                                    <label class="flex items-center gap-3 p-4 rounded-xl bg-gray-100 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] cursor-pointer">
                                        <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" checked class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500 border-gray-300">
                                        <div class="flex-1">
                                            <p class="font-bold text-gray-800">{{ $order->order_number }}</p>
                                            <p class="text-xs text-gray-500">{{ $order->customer->name }} &bull; {{ $order->total_weight }} kg</p>
                                        </div>
                                    </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-end gap-4">
                            <button type="button" @click="showUnloadModal = false" class="px-6 py-3 rounded-xl font-bold text-gray-600 hover:bg-gray-200 transition-all">Batal</button>
                            <button type="submit" class="px-6 py-3 bg-indigo-500 text-white font-black rounded-xl shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:bg-indigo-600 transition-all">Proses Unload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @php
        $routeGeojson = $shipment->routeVersion->polyline_geojson ?? 'null';
        if (is_string($routeGeojson) && $routeGeojson !== 'null') {
            $routeGeojsonObj = json_decode($routeGeojson);
        } else {
            $routeGeojsonObj = $routeGeojson;
        }

        $ordersJson = $shipment->orders->map(function($o) {
            return [
                'id' => $o->id,
                'number' => $o->order_number,
                'lat' => (float)$o->destination_latitude,
                'lng' => (float)$o->destination_longitude,
                'address' => $o->destination_address,
                'status' => $o->status
            ];
        })->values()->toJson();
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: Info & Orders -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Fleet Info -->
                <x-card class="h-full border border-gray-100">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Fleet & Driver</h3>
                    </div>
                    
                    <div class="space-y-4 text-sm mt-6">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Vehicle</p>
                            <p class="font-bold text-gray-800 text-lg">{{ $shipment->vehicle->plate_number ?? '-' }}</p>
                            <p class="text-gray-500">{{ $shipment->vehicle->brand ?? '' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Driver</p>
                            <p class="font-bold text-gray-800 text-lg">{{ $shipment->driver->user->name ?? '-' }}</p>
                        </div>
                    </div>
                </x-card>

                <!-- Status & Route -->
                <x-card class="h-full border border-gray-100 bg-gradient-to-br from-blue-50 to-white">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">Route Status</h3>
                        </div>
                        @php
                            $slaClass = 'text-gray-700 bg-gray-200';
                            if ($shipment->sla_status === 'On Time') $slaClass = 'text-emerald-700 bg-emerald-100';
                            if ($shipment->sla_status === 'Late' || $shipment->sla_status === 'Late (Ongoing)') $slaClass = 'text-red-700 bg-red-100';
                            if ($shipment->sla_status === 'At Risk') $slaClass = 'text-orange-700 bg-orange-100';
                            if ($shipment->sla_status === 'On Track') $slaClass = 'text-blue-700 bg-blue-100';
                        @endphp
                        <span class="px-3 py-1 text-xs font-black uppercase tracking-widest rounded-xl {{ $slaClass }}">
                            SLA: {{ $shipment->sla_status }}
                        </span>
                    </div>
                    
                    <div class="space-y-4 text-sm mt-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Current Status</p>
                                <span class="inline-block font-black tracking-widest text-xs px-3 py-1 rounded-lg uppercase {{ $shipment->status === 'Delivered' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $shipment->status }}
                                </span>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">SLA Target Time</p>
                                <p class="font-bold text-gray-800">{{ $shipment->sla_target_at ? $shipment->sla_target_at->format('d M, H:i') : 'Not Started' }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Route Path</p>
                            <p class="font-bold text-gray-800">{{ $shipment->routeVersion->route->origin_name ?? '-' }}</p>
                            <div class="my-1 text-gray-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                            </div>
                            <p class="font-bold text-gray-800">{{ $shipment->routeVersion->route->destination_name ?? '-' }}</p>
                        </div>
                    </div>
                </x-card>
            </div>

            <x-card>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Loaded Orders</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-gray-400 text-xs tracking-widest uppercase border-b-2 border-gray-100">
                                <th class="py-3 px-2 font-bold">Order No.</th>
                                <th class="py-3 px-2 font-bold">Customer</th>
                                <th class="py-3 px-2 font-bold">Destination</th>
                                <th class="py-3 px-2 font-bold text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach($shipment->orders as $order)
                                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                    <td class="py-4 px-2 font-black text-gray-800">
                                        {{ $order->order_number }}
                                        <div class="text-xs text-gray-500 font-normal mt-1">{{ $order->total_weight }} kg | {{ $order->total_volume }} cbm</div>
                                    </td>
                                    <td class="py-4 px-2 font-medium text-gray-700">{{ $order->customer->company_name ?? '-' }}</td>
                                    <td class="py-4 px-2">
                                        <div class="truncate max-w-[200px]" title="{{ $order->destination_address }}">{{ $order->destination_address }}</div>
                                    </td>
                                    <td class="py-4 px-2 text-right">
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold {{ $order->pivot->status === 'Delivered' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-700' }}">
                                            {{ $order->pivot->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        <!-- Right Column: Map Tracker -->
        <div class="lg:col-span-5 xl:col-span-4 relative">
            <div class="sticky top-6">
                <x-card class="p-2 shadow-[8px_8px_16px_#d1d5db,-8px_-8px_16px_#ffffff] border-4 border-white mb-6">
                    <div id="tracker-map" class="w-full h-[500px] rounded-2xl z-0"></div>
                </x-card>
                
                <div class="text-center text-gray-400 text-xs font-bold uppercase tracking-widest">
                    <span class="inline-block w-3 h-3 rounded-full bg-blue-500 mr-1 align-middle"></span> Master Route Line
                    <span class="inline-block w-3 h-3 rounded-full bg-red-500 ml-4 mr-1 align-middle"></span> Package Drops
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            const mapData = {
                route: @json($routeGeojsonObj),
                orders: {!! $ordersJson !!}
            };

            const map = L.map('tracker-map').setView([-2.5489, 118.0149], 5);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            const bounds = [];

            if (mapData.route) {
                const routeLayer = L.geoJSON(mapData.route, {
                    style: { color: '#3b82f6', weight: 5, opacity: 0.8 }
                }).addTo(map);
                bounds.push(routeLayer.getBounds());
            }

            mapData.orders.forEach(order => {
                if (order.lat && order.lng) {
                    const marker = L.circleMarker([order.lat, order.lng], {
                        color: order.status === 'Completed' || order.status === 'Arrived at Hub' ? '#10b981' : '#ef4444',
                        fillColor: order.status === 'Completed' || order.status === 'Arrived at Hub' ? '#10b981' : '#ef4444',
                        fillOpacity: 1,
                        radius: 7
                    }).bindPopup(`<b>${order.number}</b><br>${order.address}`).addTo(map);
                    bounds.push(L.latLng(order.lat, order.lng));
                }
            });

            if (bounds.length > 0) {
                const group = new L.featureGroup(bounds.map(b => b instanceof L.LatLngBounds ? L.rectangle(b) : L.marker(b)));
                map.fitBounds(group.getBounds(), { padding: [30, 30] });
            }
        });
    </script>
@endsection
