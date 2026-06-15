@extends('layouts.logistik')

@section('title', 'Create Order')

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <x-topbar />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('orders.index') }}" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <p class="text-gray-500 text-lg font-medium">Create New Order</p>
        </div>
    </div>



    @if ($errors->any())
        <div class="mb-8 p-4 bg-red-100 text-red-800 rounded-xl shadow-sm border border-red-200">
            <ul class="list-disc list-inside font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" x-data="orderForm()">
        <!-- Order Form Section -->
        <div>
            <x-card>
                <form action="{{ route('orders.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Customer</label>
                        <select name="customer_id" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
                            <option value="">-- Pilih Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->company_name }} ({{ $customer->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Origin Warehouse</label>
                        <select name="origin_warehouse_id" x-model="warehouseId" @change="fetchWarehouseItems()" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
                            <option value="">-- Pilih Gudang Asal --</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Destination (Omni Search)</label>
                        <div @dest-selected.window="setDestination($event.detail)">
                            <x-omni-search name="destination_omni" placeholder="Search Customer Address..." event-name="dest-selected" />
                        </div>
                        
                        <!-- Hidden inputs for form submission -->
                        <input type="hidden" name="destination_address" x-model="destinationAddress">
                        <input type="hidden" name="destination_latitude" x-model="destinationLat">
                        <input type="hidden" name="destination_longitude" x-model="destinationLng">
                        <input type="hidden" name="estimated_distance_km" x-model="distanceKm">
                        <input type="hidden" name="quoted_price" x-model="quotedPrice">
                        
                        <div x-show="destinationAddress" class="mt-2 text-xs font-medium text-emerald-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Destination selected.
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-300">
                        <div class="flex justify-between items-center mb-4">
                            <p class="text-sm font-bold text-gray-700">Order Items</p>
                            <button type="button" @click="addItem()" class="text-blue-600 text-xs font-bold flex items-center gap-1 hover:text-blue-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Add Item
                            </button>
                        </div>
                        
                        <div x-show="loadingItems" class="text-xs text-gray-500 font-medium animate-pulse mb-4">
                            Memuat daftar barang...
                        </div>

                        <div x-show="!warehouseId" class="text-xs text-gray-400 font-bold mb-4">
                            Silakan pilih Origin Warehouse terlebih dahulu.
                        </div>

                        <div x-show="warehouseId && !loadingItems" class="space-y-4">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex items-center gap-2 p-3 rounded-xl bg-gray-100 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff]">
                                    <div class="flex-1">
                                        <select x-model="item.stock_item_id" :name="`items[${index}][stock_item_id]`" @change="updateItemDetails(index)" required class="w-full text-xs bg-transparent border-b border-gray-300 focus:border-blue-500 focus:ring-0 p-1 font-medium text-gray-700">
                                            <option value="">-- Pilih Barang --</option>
                                            <template x-for="stock in availableStockItems" :key="stock.id">
                                                <option :value="stock.id" x-text="`${stock.sku} - ${stock.name} (Avail: ${stock.available_qty})`"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="w-24">
                                        <input type="number" x-model="item.quantity" :name="`items[${index}][quantity]`" min="1" :max="item.max_qty" required class="w-full text-xs bg-transparent border-b border-gray-300 focus:border-blue-500 focus:ring-0 p-1 text-center font-bold" placeholder="Qty">
                                    </div>
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-red-500 hover:text-red-700 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="pt-6 mt-6">
                        <button type="submit" class="w-full py-4 rounded-2xl font-bold text-gray-100 bg-blue-600 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#1e3a8a] transition-all uppercase tracking-widest text-sm">
                            Create Order
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
        
        <!-- Right Column: Order Summary & Map -->
        <div class="space-y-6">
            <!-- Map Card -->
            <x-card class="p-0 overflow-hidden">
                <div id="map" class="w-full h-64 z-0"></div>
                <div class="p-4 bg-white border-t border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Estimated Distance</p>
                        <p class="text-lg font-black text-gray-800" x-text="distanceKm ? distanceKm + ' KM' : '-'"></p>
                    </div>
                    <div x-show="isCalculating" class="flex items-center gap-2 text-blue-600 text-sm font-bold">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Routing...
                    </div>
                </div>
            </x-card>

            <!-- Summary Card -->
            <x-card>
                <div class="flex items-center gap-3 mb-6 relative z-10">
                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff]">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold tracking-wide text-gray-800">Order Summary</h3>
                </div>

                <div class="space-y-4 mb-6 relative z-10">
                    <div class="flex justify-between items-center border-b border-gray-300 pb-4">
                        <span class="text-gray-600 font-medium">Total Items</span>
                        <span class="font-bold text-lg text-gray-800" x-text="items.length"></span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-300 pb-4">
                        <span class="text-gray-600 font-medium">Total Weight</span>
                        <span class="font-bold text-lg text-gray-800" x-text="totalWeight.toFixed(2) + ' KG'"></span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-300 pb-4">
                        <span class="text-gray-600 font-medium">Total Volume</span>
                        <span class="font-bold text-lg text-gray-800" x-text="totalVolume.toFixed(2) + ' CBM'"></span>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-300 relative z-10">
                    <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-2">Estimated Quotation</p>
                    <p class="text-4xl font-black text-blue-600 drop-shadow-sm" x-text="'Rp ' + quotedPrice.toLocaleString('id-ID')"></p>
                    <p class="text-[10px] text-gray-400 mt-3">*Calculated from Distance (KM) and Volumetric Weight</p>
                </div>
            </x-card>
        </div>
    </div>

    <script>
        const warehouses = @json($warehouses->map(fn($w) => ['id' => $w->id, 'lat' => $w->latitude, 'lng' => $w->longitude])->values());
        const defaultTariff = @json($defaultTariff);

        function orderForm() {
            return {
                warehouseId: '',
                warehouseLat: null,
                warehouseLng: null,
                destinationAddress: '',
                destinationLat: '',
                destinationLng: '',
                availableStockItems: [],
                loadingItems: false,
                items: [
                    { stock_item_id: '', quantity: 1, max_qty: null, unit: 'pcs', weight_kg: 0, volume_cbm: 0 }
                ],
                map: null,
                originMarker: null,
                destMarker: null,
                routeLayer: null,
                isCalculating: false,
                distanceKm: 0,

                get totalWeight() {
                    return this.items.reduce((sum, item) => sum + (item.quantity * item.weight_kg), 0);
                },

                get totalVolume() {
                    return this.items.reduce((sum, item) => sum + (item.quantity * item.volume_cbm), 0);
                },

                get quotedPrice() {
                    if (!defaultTariff) return 0;
                    let price = parseFloat(defaultTariff.fixed_price) || 0;
                    price += (parseFloat(this.distanceKm) || 0) * (parseFloat(defaultTariff.price_per_km) || 0);
                    price += this.totalWeight * (parseFloat(defaultTariff.price_per_kg) || 0);
                    price += this.totalVolume * (parseFloat(defaultTariff.price_per_cbm) || 0);
                    return price;
                },

                init() {
                    this.initMap();
                },

                initMap() {
                    this.map = L.map('map').setView([-2.5489, 118.0149], 5);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OSM'
                    }).addTo(this.map);

                    this.map.on('click', (e) => {
                        this.setDestination({
                            lat: e.latlng.lat,
                            lng: e.latlng.lng,
                            name: `Lat: ${e.latlng.lat.toFixed(4)}, Lng: ${e.latlng.lng.toFixed(4)}`
                        });
                    });
                },

                setDestination(detail) {
                    this.destinationLat = detail.lat;
                    this.destinationLng = detail.lng;
                    this.destinationAddress = detail.name || detail.address || `${detail.lat}, ${detail.lng}`;
                    this.drawMarkers();
                    this.calculateRoute();
                },

                async fetchWarehouseItems() {
                    this.availableStockItems = [];
                    this.items = [{ stock_item_id: '', quantity: 1, max_qty: null, unit: 'pcs', weight_kg: 0, volume_cbm: 0 }];
                    
                    if (!this.warehouseId) {
                        this.warehouseLat = null;
                        this.warehouseLng = null;
                        this.drawMarkers();
                        return;
                    }

                    let wh = warehouses.find(w => w.id === this.warehouseId);
                    if (wh) {
                        this.warehouseLat = wh.lat;
                        this.warehouseLng = wh.lng;
                        this.drawMarkers();
                        this.calculateRoute();
                    }

                    this.loadingItems = true;
                    try {
                        const response = await fetch(`{{ url('logistik-panel/orders/warehouse-items') }}/${this.warehouseId}`);
                        const data = await response.json();
                        this.availableStockItems = data;
                    } catch (error) {
                        console.error("Error fetching items:", error);
                        Toastify({text: "Gagal mengambil data stok dari gudang.", duration: 3000, style:{background:"#fee2e2", color:"#991b1b"}}).showToast();
                    } finally {
                        this.loadingItems = false;
                    }
                },

                addItem() {
                    this.items.push({ stock_item_id: '', quantity: 1, max_qty: null, unit: 'pcs', weight_kg: 0, volume_cbm: 0 });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },

                updateItemDetails(index) {
                    const selectedId = this.items[index].stock_item_id;
                    const stockItem = this.availableStockItems.find(s => s.id === selectedId);
                    if (stockItem) {
                        this.items[index].max_qty = stockItem.available_qty;
                        this.items[index].unit = stockItem.unit;
                        this.items[index].weight_kg = parseFloat(stockItem.weight_kg) || 0;
                        this.items[index].volume_cbm = parseFloat(stockItem.volume_cbm) || 0;
                        if (this.items[index].quantity > stockItem.available_qty) {
                            this.items[index].quantity = stockItem.available_qty;
                        }
                    } else {
                        this.items[index].max_qty = null;
                        this.items[index].unit = 'pcs';
                        this.items[index].weight_kg = 0;
                        this.items[index].volume_cbm = 0;
                    }
                },

                drawMarkers() {
                    if (this.originMarker) this.map.removeLayer(this.originMarker);
                    if (this.destMarker) this.map.removeLayer(this.destMarker);

                    let group = [];

                    if (this.warehouseLat && this.warehouseLng) {
                        this.originMarker = L.circleMarker([this.warehouseLat, this.warehouseLng], {
                            color: 'green', fillOpacity: 0.8, radius: 6
                        }).addTo(this.map);
                        group.push([this.warehouseLat, this.warehouseLng]);
                    }

                    if (this.destinationLat && this.destinationLng) {
                        this.destMarker = L.circleMarker([this.destinationLat, this.destinationLng], {
                            color: 'red', fillOpacity: 0.8, radius: 6
                        }).addTo(this.map);
                        group.push([this.destinationLat, this.destinationLng]);
                    }

                    if (group.length > 0) {
                        this.map.fitBounds(L.latLngBounds(group), { padding: [50, 50] });
                    }
                },

                async calculateRoute() {
                    if (!this.warehouseLat || !this.warehouseLng || !this.destinationLat || !this.destinationLng) return;
                    
                    this.isCalculating = true;
                    if (this.routeLayer) this.map.removeLayer(this.routeLayer);

                    try {
                        const response = await fetch('{{ route("routes.calculate-preview") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                route_type: 'combined',
                                waypoints: [
                                    [parseFloat(this.warehouseLng), parseFloat(this.warehouseLat)],
                                    [parseFloat(this.destinationLng), parseFloat(this.destinationLat)]
                                ]
                            })
                        });

                        const data = await response.json();
                        if (data.distance_km) {
                            this.distanceKm = data.distance_km;
                        }
                        
                        if (data.polyline_geojson) {
                            this.routeLayer = L.geoJSON(data.polyline_geojson, {
                                style: { color: '#2563eb', weight: 4, opacity: 0.8 }
                            }).addTo(this.map);
                            this.map.fitBounds(this.routeLayer.getBounds(), { padding: [50, 50] });
                        }
                    } catch (error) {
                        console.error('Routing failed', error);
                        Toastify({text: "Gagal menghitung rute via OSRM/Searoute.", duration: 3000, style:{background:"#fee2e2", color:"#991b1b"}}).showToast();
                    } finally {
                        this.isCalculating = false;
                    }
                }
            }
        }
    </script>
@endsection
