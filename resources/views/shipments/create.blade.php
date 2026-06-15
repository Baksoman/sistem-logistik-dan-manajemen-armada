@extends('layouts.logistik')

@section('title', 'Consolidate Shipment')

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
            <p class="text-gray-500 text-lg font-medium">Consolidate Orders into Shipment</p>
        </div>
    </div>



    @php
        $ordersJson = $orders->map(function($o) {
            return [
                'id' => $o->id, 
                'order_number' => $o->order_number,
                'weight' => $o->total_weight, 
                'volume' => $o->total_volume,
                'location_id' => $o->current_warehouse_id ?? $o->origin_warehouse_id,
                'origin_name' => $o->originWarehouse->name ?? '-',
                'is_transit' => $o->current_warehouse_id && $o->current_warehouse_id !== $o->origin_warehouse_id,
                'destination_address' => $o->destination_address,
                'lat' => (float) $o->destination_latitude,
                'lng' => (float) $o->destination_longitude
            ];
        })->values()->toJson();
        
        $vehiclesJson = $vehicles->map(function($v) {
            return [
                'id' => $v->id, 
                'vehicle_type_id' => $v->vehicle_type_id,
                'capacity_kg' => $v->capacity_kg, 
                'capacity_volume' => $v->capacity_volume_cbm
            ];
        })->values()->toJson();
        
        $warehousesJson = $warehouses->map(function($w) {
            return [
                'id' => $w->id,
                'name' => $w->name,
                'lat' => (float) $w->latitude,
                'lng' => (float) $w->longitude
            ];
        })->values()->toJson();
        
        $routesJson = $routeVersions->map(function($rv) {
            return [
                'id' => $rv->id,
                'route_id' => $rv->route_id,
                'route_code' => $rv->route->route_code,
                'calculated_date' => $rv->calculated_at ? $rv->calculated_at->format('Y-m-d') : 'Unknown',
                'origin_name' => $rv->route->origin_name,
                'destination_name' => $rv->route->destination_name,
                'geojson' => $rv->polyline_geojson ? (is_string($rv->polyline_geojson) ? json_decode($rv->polyline_geojson) : $rv->polyline_geojson) : null
            ];
        })->values()->toJson();

        $tariffsJson = $tariffs->map(function($t) {
            return [
                'id' => $t->id,
                'route_id' => $t->route_id,
                'vehicle_type_id' => $t->vehicle_type_id,
                'price_per_km' => (float) $t->price_per_km,
                'price_per_kg' => (float) $t->price_per_kg,
                'fixed_price' => (float) $t->fixed_price,
            ];
        })->values()->toJson();
    @endphp

    <script>
        const ordersData = {!! $ordersJson !!};
        const vehiclesData = {!! $vehiclesJson !!};
        const warehousesData = {!! $warehousesJson !!};
        const routesData = {!! $routesJson !!};
        const tariffsData = {!! $tariffsJson !!};

        document.addEventListener('alpine:init', () => {
            Alpine.data('shipmentForm', () => ({
                routeMode: 'transit',
                selectedOriginId: '',
                selectedOrders: [],
                selectedVehicleId: '',
                selectedRouteVersionId: '',
                
                totalWeight: 0,
                totalVolume: 0,
                maxWeight: 0,
                maxVolume: 0,
                
                map: null,
                markers: [],
                routeLayer: null,
                
                proximityWarning: null,
                
                estimatedDistanceKm: 0,
                estimatedCost: 0,
                
                orderDistances: {},

                init() {
                    try {
                        this.initMap();
                    } catch(e) {
                        console.error('Leaflet init error', e);
                    }
                },
                
                initMap() {
                    this.map = L.map('map').setView([-2.5489, 118.0149], 5);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(this.map);
                },

                setMode(mode) {
                    this.routeMode = mode;
                    this.resetSelectedOrders();
                },

                get filteredOrders() {
                    if (!this.selectedOriginId) return [];
                    return ordersData.filter(o => o.location_id === this.selectedOriginId);
                },

                get filteredRouteVersions() {
                    if (!this.selectedOriginId) return [];
                    const originWH = warehousesData.find(w => w.id === this.selectedOriginId);
                    if (!originWH) return [];
                    return routesData.filter(r => r.origin_name.startsWith(originWH.name));
                },

                resetSelectedOrders() {
                    this.selectedOrders = [];
                    this.proximityWarning = null;
                    this.calculateTotals();
                    this.drawMarkers();
                    this.calculateDistancesForTable();
                },
                
                calculateDistancesForTable() {
                    if (!this.selectedOriginId) return;
                    const originWH = warehousesData.find(w => w.id === this.selectedOriginId);
                    if (!originWH || !originWH.lat) return;
                    const originPt = turf.point([originWH.lng, originWH.lat]);
                    
                    let destPt = null;
                    if (this.routeMode === 'transit' && this.selectedRouteVersionId) {
                        const route = routesData.find(r => r.id === this.selectedRouteVersionId);
                        if (route && route.geojson) {
                            let coords = null;
                            if (route.geojson.type === 'FeatureCollection') {
                                const f = route.geojson.features.find(feat => feat.geometry.type === 'LineString' || feat.geometry.type === 'MultiLineString');
                                if (f && f.geometry.type === 'LineString') coords = f.geometry.coordinates;
                                else if (f && f.geometry.type === 'MultiLineString') coords = f.geometry.coordinates[f.geometry.coordinates.length - 1];
                            } else if (route.geojson.type === 'LineString') {
                                coords = route.geojson.coordinates;
                            }
                            if (coords && coords.length > 0) {
                                const lastCoord = coords[coords.length - 1];
                                destPt = turf.point(lastCoord);
                            }
                        }
                    }
                    
                    this.filteredOrders.forEach(order => {
                        if (order.lat && order.lng) {
                            const orderPt = turf.point([order.lng, order.lat]);
                            let distStr = `<div class="mb-1">${turf.distance(orderPt, originPt, {units: 'kilometers'}).toFixed(1)} KM <span class="text-xs text-gray-400 font-normal">from Origin</span></div>`;
                            if (destPt) {
                                distStr += `<div class="text-purple-600">${turf.distance(orderPt, destPt, {units: 'kilometers'}).toFixed(1)} KM <span class="text-xs opacity-75 font-normal">from Dest</span></div>`;
                            }
                            this.orderDistances[order.id] = distStr;
                        }
                    });
                },

                onOrderSelectionChange() {
                    this.calculateTotals();
                    this.drawMarkers();
                    this.runProximityCheck();
                },

                onRouteSelectionChange() {
                    this.drawRouteLine();
                    this.runProximityCheck();
                    this.calculateCost();
                    this.validateRouteOrigin();
                    this.calculateDistancesForTable();
                },

                validateRouteOrigin() {
                    if (this.routeMode === 'transit' && this.selectedRouteVersionId && this.selectedOriginId) {
                        const route = routesData.find(r => r.id === this.selectedRouteVersionId);
                        const originWH = warehousesData.find(w => w.id === this.selectedOriginId);
                        if (route && originWH && !route.origin_name.startsWith(originWH.name)) {
                            this.proximityWarning = `<b>Rute Tidak Valid!</b><br>Master Route yang dipilih berangkat dari <b>${route.origin_name}</b>, sedangkan Gudang Asal yang Anda pilih adalah <b>${originWH.name}</b>. Silakan sesuaikan.`;
                        } else if (this.proximityWarning && this.proximityWarning.includes('Rute Tidak Valid')) {
                            this.proximityWarning = null;
                        }
                    }
                },

                get weightPercentage() {
                    if (this.maxWeight == 0) return 0;
                    const p = (this.totalWeight / this.maxWeight) * 100;
                    return p > 100 ? 100 : p;
                },

                get volumePercentage() {
                    if (this.maxVolume == 0) return 0;
                    const p = (this.totalVolume / this.maxVolume) * 100;
                    return p > 100 ? 100 : p;
                },

                get isWeightOverload() {
                    return this.maxWeight > 0 && this.totalWeight > this.maxWeight;
                },

                get isVolumeOverload() {
                    return this.maxVolume > 0 && this.totalVolume > this.maxVolume;
                },

                calculateTotals() {
                    let w = 0;
                    let v = 0;
                    this.selectedOrders.forEach(id => {
                        const order = ordersData.find(o => o.id === id);
                        if (order) {
                            w += parseFloat(order.weight);
                            v += parseFloat(order.volume);
                        }
                    });
                    this.totalWeight = w;
                    this.totalVolume = v;
                    this.calculateCost();
                },

                validateCapacity() {
                    if (!this.selectedVehicleId) {
                        this.maxWeight = 0;
                        this.maxVolume = 0;
                        return;
                    }
                    const vehicle = vehiclesData.find(v => v.id === this.selectedVehicleId);
                    if (vehicle) {
                        this.maxWeight = parseFloat(vehicle.capacity_kg);
                        this.maxVolume = parseFloat(vehicle.capacity_volume);
                    }
                    this.calculateCost();
                },
                
                calculateCost() {
                    this.estimatedCost = 0;
                    if (this.selectedOrders.length === 0) return;
                    
                    // Fallback tarif jika belum ada di database (5000/KM, 500/KG)
                    let tariff = { price_per_km: 5000, price_per_kg: 500, fixed_price: 0 };
                    
                    let selectedVehicleTypeId = null;
                    if (this.selectedVehicleId) {
                        const v = vehiclesData.find(veh => veh.id === this.selectedVehicleId);
                        if (v) selectedVehicleTypeId = v.vehicle_type_id;
                    }
                    
                    if (this.routeMode === 'direct') {
                        // Cari general tariff (route_id = null)
                        const globalTariffs = tariffsData.filter(t => t.route_id === null);
                        let matchedTariff = globalTariffs.find(t => t.vehicle_type_id === selectedVehicleTypeId);
                        if (!matchedTariff) matchedTariff = globalTariffs.find(t => t.vehicle_type_id === null);
                        
                        if (matchedTariff) tariff = matchedTariff;
                        
                        const distanceCost = this.estimatedDistanceKm * tariff.price_per_km;
                        const weightCost = this.totalWeight * tariff.price_per_kg;
                        this.estimatedCost = distanceCost + weightCost;
                    } 
                    else if (this.routeMode === 'transit' && this.selectedRouteVersionId) {
                        const routeVersion = routesData.find(r => r.id === this.selectedRouteVersionId);
                        if (routeVersion) {
                            const routeTariffs = tariffsData.filter(t => t.route_id === routeVersion.route_id);
                            let matchedTariff = routeTariffs.find(t => t.vehicle_type_id === selectedVehicleTypeId);
                            if (!matchedTariff) matchedTariff = routeTariffs.find(t => t.vehicle_type_id === null);
                            
                            if (matchedTariff) tariff = matchedTariff;
                            else tariff.fixed_price = 1500000; // default fixed price for transit
                            
                            this.estimatedCost = tariff.fixed_price + (this.totalWeight * tariff.price_per_kg);
                        }
                    }
                },
                
                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(number);
                },
                
                drawMarkers() {
                    if (!this.map) return;
                    this.markers.forEach(m => this.map.removeLayer(m));
                    this.markers = [];
                    
                    const bounds = [];

                    if (this.selectedOriginId) {
                        const originWH = warehousesData.find(w => w.id === this.selectedOriginId);
                        if (originWH && originWH.lat && originWH.lng) {
                            const marker = L.circleMarker([originWH.lat, originWH.lng], {
                                color: 'green', fillColor: 'green', fillOpacity: 1, radius: 8
                            }).bindPopup(`<b>${originWH.name}</b><br>Origin Warehouse`).addTo(this.map);
                            this.markers.push(marker);
                            bounds.push([originWH.lat, originWH.lng]);
                        }
                    }

                    this.selectedOrders.forEach(id => {
                        const order = ordersData.find(o => o.id === id);
                        if (order && order.lat && order.lng) {
                            const marker = L.circleMarker([order.lat, order.lng], {
                                color: 'red', fillColor: 'red', fillOpacity: 0.8, radius: 6
                            }).bindPopup(`<b>${order.order_number}</b><br>${order.destination_address}`).addTo(this.map);
                            this.markers.push(marker);
                            bounds.push([order.lat, order.lng]);
                        }
                    });
                    
                    if (bounds.length === 1) {
                        this.map.setView(bounds[0], 12);
                    } else if (bounds.length > 1 && !this.routeLayer) {
                        this.map.fitBounds(bounds, { padding: [50, 50] });
                    }
                },
                
                drawRouteLine() {
                    if (!this.map) return;
                    if (this.routeLayer) {
                        this.map.removeLayer(this.routeLayer);
                        this.routeLayer = null;
                    }
                    if (!this.selectedRouteVersionId || this.routeMode !== 'transit') return;
                    
                    const route = routesData.find(r => r.id === this.selectedRouteVersionId);
                    if (route && route.geojson) {
                        this.routeLayer = L.geoJSON(route.geojson, {
                            style: { color: 'blue', weight: 4, opacity: 0.6 }
                        }).addTo(this.map);
                        this.map.fitBounds(this.routeLayer.getBounds(), { padding: [30, 30] });
                    }
                },
                
                async runProximityCheck() {
                    this.proximityWarning = null;
                    this.estimatedDistanceKm = 0;
                    
                    if (this.selectedOrders.length === 0 || !this.selectedOriginId) return;

                    const originWH = warehousesData.find(w => w.id === this.selectedOriginId);
                    if (!originWH || !originWH.lat) return;
                    const originPt = turf.point([originWH.lng, originWH.lat]);

                    if (this.routeMode === 'direct') {
                        const orderId = this.selectedOrders[0];
                        const order = ordersData.find(o => o.id === orderId);
                        if (!order || !order.lat) return;

                        const customerPt = turf.point([order.lng, order.lat]);
                        
                        try {
                            const osrmUrl = `http://router.project-osrm.org/route/v1/driving/${originWH.lng},${originWH.lat};${order.lng},${order.lat}?overview=full&geometries=geojson`;
                            const response = await fetch(osrmUrl);
                            const data = await response.json();
                            
                            if (data.code === 'Ok' && data.routes.length > 0) {
                                this.estimatedDistanceKm = data.routes[0].distance / 1000;
                                
                                if (this.routeLayer) {
                                    this.map.removeLayer(this.routeLayer);
                                }
                                this.routeLayer = L.geoJSON(data.routes[0].geometry, {
                                    style: { color: 'red', weight: 4, opacity: 0.8, dashArray: '5, 10' }
                                }).addTo(this.map);
                                this.map.fitBounds(this.routeLayer.getBounds(), { padding: [30, 30] });
                            } else {
                                this.estimatedDistanceKm = turf.distance(customerPt, originPt, {units: 'kilometers'});
                            }
                        } catch (err) {
                            console.error('OSRM fetch failed', err);
                            this.estimatedDistanceKm = turf.distance(customerPt, originPt, {units: 'kilometers'});
                        }
                        
                        this.calculateCost(); // re-calc distance cost

                        let closestWH = originWH;
                        let minDistance = this.estimatedDistanceKm;

                        warehousesData.forEach(wh => {
                            if (wh.id !== originWH.id && wh.lat && wh.lng) {
                                const whPt = turf.point([wh.lng, wh.lat]);
                                const dist = turf.distance(customerPt, whPt, {units: 'kilometers'});
                                if (dist < minDistance) {
                                    minDistance = dist;
                                    closestWH = wh;
                                }
                            }
                        });

                        if (closestWH.id !== originWH.id && (this.estimatedDistanceKm - minDistance) > 10) { 
                            this.proximityWarning = `Alamat pengiriman Order ini berjarak <b>${this.estimatedDistanceKm.toFixed(1)} KM</b> dari gudang Anda saat ini.<br><br>Namun letaknya HANYA <b>${minDistance.toFixed(1)} KM</b> dari <b>${closestWH.name}</b>!<br><br>Silakan ganti Mode ke Transit dan kirim paket ini ke ${closestWH.name} terlebih dahulu.`;
                        }
                    } 
                    else if (this.routeMode === 'transit' && this.selectedRouteVersionId) {
                        const route = routesData.find(r => r.id === this.selectedRouteVersionId);
                        if (!route || !route.geojson) return;
                        
                        let badOrders = [];
                        
                        let routeLine = null;
                        if (route.geojson.type === 'FeatureCollection') {
                            routeLine = route.geojson.features.find(f => f.geometry.type === 'LineString' || f.geometry.type === 'MultiLineString');
                        } else if (route.geojson.type === 'LineString' || route.geojson.type === 'MultiLineString') {
                            routeLine = turf.feature(route.geojson);
                        } else if (route.geojson.geometry) {
                            routeLine = route.geojson;
                        }

                        if (!routeLine) return; 

                        this.selectedOrders.forEach(id => {
                            const order = ordersData.find(o => o.id === id);
                            if (order && order.lat && order.lng) {
                                const pt = turf.point([order.lng, order.lat]);
                                const distanceToLine = turf.pointToLineDistance(pt, routeLine, {units: 'kilometers'});
                                
                                if (distanceToLine > 30) {
                                    badOrders.push(`${order.order_number} (${distanceToLine.toFixed(1)} KM dari jalur)`);
                                }
                            }
                        });

                        if (badOrders.length > 0) {
                            this.proximityWarning = `<b>Route Deviation Terdeteksi!</b><br><br>Pesanan berikut posisinya melenceng terlalu jauh (Lebih dari 30 KM) dari Master Route yang Anda pilih:<br><br><ul class="list-disc pl-5 mt-2"><li>${badOrders.join('</li><li>')}</li></ul><br>Mohon keluarkan pesanan tersebut dari pengiriman ini.`;
                        }
                    }
                }
            }));
        });
    </script>

    <form action="{{ route('shipments.store') }}" method="POST" x-data="shipmentForm" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        @csrf
        <input type="hidden" name="route_mode" x-model="routeMode">
        <input type="hidden" name="total_cost" :value="estimatedCost">
        <input type="hidden" name="total_distance_km" :value="estimatedDistanceKm">
        
        <!-- Left Column: Controls & Data (Scrollable) -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-6">
            
            <!-- Routing Mode Toggle -->
            <x-card>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button type="button" @click="setMode('transit')" class="flex-1 py-3 px-6 rounded-2xl font-bold transition-all" :class="routeMode === 'transit' ? 'bg-blue-600 text-white shadow-lg' : 'bg-gray-100 text-gray-600 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] hover:bg-gray-200'">
                        Transit (Master Route)
                    </button>
                    <button type="button" @click="setMode('direct')" class="flex-1 py-3 px-6 rounded-2xl font-bold transition-all" :class="routeMode === 'direct' ? 'bg-purple-600 text-white shadow-lg' : 'bg-gray-100 text-gray-600 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] hover:bg-gray-200'">
                        Direct Delivery (Last-Mile)
                    </button>
                </div>
                <p class="text-center text-xs text-gray-500 font-medium mt-3" x-show="routeMode === 'transit'">
                    Mode Transit digunakan untuk pengiriman antar Hub. Dapat memuat BANYAK pesanan.
                </p>
                <p class="text-center text-xs text-gray-500 font-medium mt-3" x-show="routeMode === 'direct'" x-cloak>
                    Mode Direct digunakan untuk pengiriman langsung ke alamat Customer. HANYA UNTUK 1 PESANAN. Rute akan dibuat otomatis.
                </p>
            </x-card>

            <!-- Configuration Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Origin & Fleet Selection -->
                <x-card class="h-full">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        Origin & Fleet
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Origin Warehouse</label>
                            <select x-model="selectedOriginId" @change="resetSelectedOrders()" required class="w-full bg-gray-100 rounded-xl px-4 py-3 font-bold text-gray-700 border-none focus:ring-2 focus:ring-blue-500 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] appearance-none">
                                <option value="">-- Select Origin --</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Vehicle</label>
                            <select name="vehicle_id" x-model="selectedVehicleId" @change="validateCapacity()" required class="w-full bg-gray-100 rounded-xl px-4 py-3 font-bold text-gray-700 border-none focus:ring-2 focus:ring-blue-500 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] appearance-none">
                                <option value="">-- Select Vehicle --</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">{{ $vehicle->plate_number }} - {{ $vehicle->brand }} (Max: {{ $vehicle->capacity_kg }}kg / {{ $vehicle->capacity_volume_cbm }}cbm)</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Driver</label>
                            <select name="driver_id" required class="w-full bg-gray-100 rounded-xl px-4 py-3 font-bold text-gray-700 border-none focus:ring-2 focus:ring-blue-500 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] appearance-none">
                                <option value="">-- Select Driver --</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->user->name }} ({{ $driver->license_type }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div x-show="routeMode === 'transit'">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Master Route</label>
                            <select name="route_version_id" x-model="selectedRouteVersionId" @change="onRouteSelectionChange()" :required="routeMode === 'transit'" class="w-full bg-gray-100 rounded-xl px-4 py-3 font-bold text-gray-700 border-none focus:ring-2 focus:ring-blue-500 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] appearance-none">
                                <option value="">-- Select Master Route --</option>
                                <template x-for="route in filteredRouteVersions" :key="route.id">
                                    <option :value="route.id" x-text="`${route.route_code} (${route.calculated_date}) - ${route.origin_name} to ${route.destination_name}`"></option>
                                </template>
                            </select>
                            <p x-show="selectedOriginId && filteredRouteVersions.length === 0" class="text-xs font-bold text-red-500 mt-2">
                                ⚠️ Tidak ada Master Route yang berangkat dari gudang ini.
                            </p>
                        </div>
                    </div>
                </x-card>

                <!-- Cost & Capacity -->
                <x-card class="h-full flex flex-col justify-between bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-widest">Financial & Capacity</h3>
                        
                        <div class="space-y-4">
                            <!-- Weight Bar -->
                            <div>
                                <div class="flex justify-between text-xs font-bold mb-1">
                                    <span class="text-gray-500">Weight</span>
                                    <span :class="isWeightOverload ? 'text-red-600' : 'text-emerald-600'">
                                        <span x-text="totalWeight.toFixed(2)"></span> / <span x-text="maxWeight ? maxWeight : '-'"></span> kg
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 shadow-inner">
                                    <div class="h-2.5 rounded-full transition-all duration-500" :class="isWeightOverload ? 'bg-red-500' : 'bg-emerald-500'" :style="`width: ${weightPercentage}%`"></div>
                                </div>
                            </div>

                            <!-- Volume Bar -->
                            <div>
                                <div class="flex justify-between text-xs font-bold mb-1">
                                    <span class="text-gray-500">Volume</span>
                                    <span :class="isVolumeOverload ? 'text-red-600' : 'text-emerald-600'">
                                        <span x-text="totalVolume.toFixed(2)"></span> / <span x-text="maxVolume ? maxVolume : '-'"></span> cbm
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 shadow-inner">
                                    <div class="h-2.5 rounded-full transition-all duration-500" :class="isVolumeOverload ? 'bg-red-500' : 'bg-emerald-500'" :style="`width: ${volumePercentage}%`"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Est. Operational Cost</p>
                            <p class="text-2xl font-black text-gray-800" x-text="formatRupiah(estimatedCost)"></p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Orders Table -->
            <x-card>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        Select Orders
                    </h3>
                    
                    <div class="text-sm font-bold text-gray-500 bg-gray-100 py-1.5 px-4 rounded-xl shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff]">
                        Selected: <span x-text="selectedOrders.length" class="text-blue-600 text-lg ml-1"></span>
                    </div>
                </div>

                <div x-show="!selectedOriginId" class="py-12 text-center text-gray-400 font-bold border-2 border-dashed border-gray-200 rounded-2xl mb-6">
                    Silakan pilih Lokasi Gudang Asal terlebih dahulu.
                </div>

                <div class="overflow-x-auto" x-show="selectedOriginId" x-cloak>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-300 text-gray-500 text-xs tracking-widest uppercase">
                                <th class="py-3 px-3 font-bold w-10">Select</th>
                                <th class="py-3 px-3 font-bold">Order No.</th>
                                <th class="py-3 px-3 font-bold">Dest. Koordinat</th>
                                <th class="py-3 px-3 font-bold text-right">Est. Distance</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <template x-for="order in filteredOrders" :key="order.id">
                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition" :class="selectedOrders.includes(order.id) ? 'bg-blue-50/50' : ''">
                                    <td class="py-4 px-3">
                                        <input type="checkbox" name="order_ids[]" :value="order.id" x-model="selectedOrders" @change="onOrderSelectionChange()" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300" :disabled="routeMode === 'direct' && selectedOrders.length >= 1 && !selectedOrders.includes(order.id)">
                                    </td>
                                    <td class="py-4 px-3 font-bold text-gray-800">
                                        <span x-text="order.order_number"></span>
                                        <div class="text-xs text-gray-500 font-normal" x-text="`${order.weight}kg | ${order.volume}cbm`"></div>
                                    </td>
                                    <td class="py-4 px-3">
                                        <div class="truncate max-w-[200px]" :title="order.destination_address" x-text="order.destination_address"></div>
                                    </td>
                                    <td class="py-4 px-3 text-right font-mono font-bold text-orange-600">
                                        <div x-html="orderDistances[order.id] || '-'"></div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filteredOrders.length === 0">
                                <td colspan="4" class="py-8 text-center text-gray-400 font-medium">Tidak ada order yang siap dikirim dari gudang ini.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-card>
            
        </div>

        <!-- Right Column: Sticky Map & Final Action -->
        <div class="lg:col-span-5 xl:col-span-4 relative">
            <div class="sticky top-6 space-y-6">
                <!-- Proximity Validation Panel -->
                <div x-show="proximityWarning" class="p-5 bg-red-100 backdrop-blur text-red-800 text-sm rounded-3xl border-2 border-red-300 shadow-xl" x-cloak>
                    <div class="font-black text-lg flex items-center gap-2 mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        DITOLAK
                    </div>
                    <p class="font-medium" x-html="proximityWarning"></p>
                </div>

                <x-card class="p-2 overflow-hidden shadow-[8px_8px_16px_#d1d5db,-8px_-8px_16px_#ffffff] border-4 border-white">
                    <div id="map" class="w-full h-[500px] rounded-2xl z-0"></div>
                </x-card>

                <!-- Submit Button -->
                <button type="submit" :disabled="selectedOrders.length === 0 || isWeightOverload || isVolumeOverload || !selectedVehicleId || proximityWarning !== null" class="w-full py-5 rounded-3xl font-black text-gray-100 shadow-[8px_8px_16px_#d1d5db,-8px_-8px_16px_#ffffff] transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed uppercase tracking-widest text-lg flex justify-center items-center gap-3" :class="proximityWarning !== null ? 'bg-red-500' : 'bg-blue-600 hover:bg-blue-700 active:shadow-[inset_4px_4px_8px_#1e3a8a]'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Execute Shipment
                </button>
            </div>
        </div>
    </form>
@endsection
