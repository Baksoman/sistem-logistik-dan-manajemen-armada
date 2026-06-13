@extends('layouts.app')

@section('title', 'Create Route')

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <x-topbar />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('routes.index') }}" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <p class="text-gray-500 text-lg font-medium">Define Waypoints and Calculate Route</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="routeManager()">
        <!-- Form Section -->
        <div class="lg:col-span-1">
            <x-card>
                <form id="routeForm" action="{{ route('routes.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="waypoints" x-model="waypointsJson">

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Route Code</label>
                        <x-input type="text" name="route_code" placeholder="RTE-001" required />
                    </div>

                    <div class="hidden">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Route Type</label>
                        <select x-model="routeType" name="route_type" @change="resetWaypoints()" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none appearance-none">
                            <option value="combined">Auto Multimodal (Land + Sea)</option>
                            <option value="land">Land (OSRM)</option>
                            <option value="sea">Sea (Searoute)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Origin Name</label>
                        <x-input type="text" name="origin_name" placeholder="Warehouse A" required />
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Destination Name</label>
                        <x-input type="text" name="destination_name" placeholder="Warehouse B" required />
                    </div>

                    <div class="pt-4 border-t border-gray-300">
                        <p class="text-sm font-bold text-gray-700 mb-4">Waypoints (Click on Map to Add)</p>
                        
                        <template x-for="(wp, index) in waypoints" :key="index">
                            <div class="flex items-center gap-2 mb-2 p-2 rounded-xl bg-gray-100 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff]">
                                <div class="flex-1 text-xs font-medium text-gray-600">
                                    <span x-text="index === 0 ? 'Origin: ' : (index === waypoints.length - 1 && waypoints.length > 1 ? 'Dest: ' : 'Stop: ')"></span>
                                    <span x-text="wp[1].toFixed(5) + ', ' + wp[0].toFixed(5)"></span>
                                </div>
                                <button type="button" @click="removeWaypoint(index)" class="text-red-500 hover:text-red-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </template>

                        <div x-show="waypoints.length === 0" class="text-xs text-gray-400 italic">No waypoints added. Click the map.</div>
                    </div>

                    <!-- Metrics Display -->
                    <div x-show="calculationResult" class="pt-4 border-t border-gray-300" x-cloak>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-gray-100 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] text-center">
                                <p class="text-xs font-bold text-gray-500 uppercase">Distance</p>
                                <p class="text-lg font-bold text-gray-800" x-text="calculationResult ? calculationResult.distance_km.toFixed(2) + ' km' : '-'"></p>
                            </div>
                            <div class="p-4 rounded-2xl bg-gray-100 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] text-center">
                                <p class="text-xs font-bold text-gray-500 uppercase">Duration</p>
                                <p class="text-lg font-bold text-gray-800" x-text="calculationResult ? formatDuration(calculationResult.duration_min) : '-'"></p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 mt-6 flex gap-4">
                        <button type="button" @click="calculatePreview()" :disabled="waypoints.length < 2 || isLoading" class="flex-1 py-4 rounded-2xl font-bold text-gray-800 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db] transition-all disabled:opacity-50 uppercase tracking-widest text-sm">
                            <span x-show="!isLoading">Preview Route</span>
                            <span x-show="isLoading">Calculating...</span>
                        </button>
                        <button type="submit" :disabled="!calculationResult" class="flex-1 py-4 rounded-2xl font-bold text-gray-100 bg-blue-600 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#1e3a8a] transition-all disabled:opacity-50 uppercase tracking-widest text-sm">
                            Save
                        </button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Map Section -->
        <div class="lg:col-span-2">
            <x-card class="h-[600px] p-0 overflow-hidden relative">
                <div id="map" class="w-full h-full z-0"></div>
                <div class="absolute bottom-4 right-4 z-10 bg-white/80 backdrop-blur px-4 py-2 rounded-xl shadow-lg border border-gray-200 text-xs text-gray-600 font-bold pointer-events-none">
                    <span x-text="routeType === 'combined' ? 'Auto Multimodal Routing' : (routeType === 'land' ? 'OSRM Routing' : 'Searoute Microservice')"></span>
                </div>
            </x-card>
        </div>
    </div>

    <script>
        function routeManager() {
            return {
                routeType: 'combined',
                waypoints: [],
                calculationResult: null,
                isLoading: false,
                map: null,
                markers: [],
                routeLayer: null,

                get waypointsJson() {
                    return JSON.stringify(this.waypoints);
                },

                formatDuration(totalMinutes) {
                    if (!totalMinutes) return '0 menit';
                    let days = Math.floor(totalMinutes / (24 * 60));
                    let hours = Math.floor((totalMinutes % (24 * 60)) / 60);
                    let minutes = Math.round(totalMinutes % 60);
                    
                    let parts = [];
                    if (days > 0) parts.push(days + ' hari');
                    if (hours > 0) parts.push(hours + ' jam');
                    if (minutes > 0 || parts.length === 0) parts.push(minutes + ' menit');
                    return parts.join(' ');
                },

                init() {
                    this.initMap();
                },

                initMap() {
                    // Center roughly on Indonesia
                    this.map = L.map('map').setView([-2.5489, 118.0149], 5);
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(this.map);

                    this.map.on('click', (e) => {
                        if ((this.routeType === 'sea' || this.routeType === 'combined') && this.waypoints.length >= 2) {
                            Toastify({text: "Sea/Auto route only supports exactly 2 points (Origin & Destination) right now.", duration: 3000, style:{background:"#fee2e2", color:"#991b1b"}}).showToast();
                            return;
                        }
                        
                        const lon = e.latlng.lng;
                        const lat = e.latlng.lat;
                        this.addWaypoint([lon, lat]);
                    });
                },

                addWaypoint(coords) {
                    this.waypoints.push(coords);
                    this.drawMarkers();
                    this.calculationResult = null;
                    if(this.routeLayer) this.map.removeLayer(this.routeLayer);
                },

                removeWaypoint(index) {
                    this.waypoints.splice(index, 1);
                    this.drawMarkers();
                    this.calculationResult = null;
                    if(this.routeLayer) this.map.removeLayer(this.routeLayer);
                },

                resetWaypoints() {
                    this.waypoints = [];
                    this.calculationResult = null;
                    this.drawMarkers();
                    if(this.routeLayer) this.map.removeLayer(this.routeLayer);
                },

                drawMarkers() {
                    // Clear existing markers
                    this.markers.forEach(m => this.map.removeLayer(m));
                    this.markers = [];

                    this.waypoints.forEach((wp, index) => {
                        let color = index === 0 ? 'green' : (index === this.waypoints.length - 1 ? 'red' : 'blue');
                        let marker = L.circleMarker([wp[1], wp[0]], {
                            color: color,
                            fillColor: color,
                            fillOpacity: 0.8,
                            radius: 6
                        }).addTo(this.map);
                        this.markers.push(marker);
                    });
                },

                async calculatePreview() {
                    if (this.waypoints.length < 2) return;
                    this.isLoading = true;

                    try {
                        const response = await fetch('{{ route("routes.calculate-preview") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                route_type: this.routeType,
                                waypoints: this.waypoints
                            })
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.error || 'Terjadi kesalahan saat menghitung rute.');
                        }

                        this.calculationResult = data;
                        this.drawRoute();

                        Toastify({text: "Rute berhasil dihitung!", duration: 3000, style:{background:"#dcfce7", color:"#166534"}}).showToast();

                    } catch (error) {
                        Toastify({text: error.message, duration: 5000, style:{background:"#fee2e2", color:"#991b1b"}}).showToast();
                        this.calculationResult = null;
                    } finally {
                        this.isLoading = false;
                    }
                },

                drawRoute() {
                    if(this.routeLayer) this.map.removeLayer(this.routeLayer);

                    if (this.calculationResult && this.calculationResult.geojson) {
                        let color = '#8b5cf6'; // auto
                        if(this.routeType === 'land') color = '#f59e0b';
                        if(this.routeType === 'sea') color = '#3b82f6';
                        
                        this.routeLayer = L.geoJSON(this.calculationResult.geojson, {
                            style: {
                                color: color,
                                weight: 5,
                                opacity: 0.7
                            }
                        }).addTo(this.map);

                        this.map.fitBounds(this.routeLayer.getBounds(), { padding: [50, 50] });
                    }
                }
            }
        }
    </script>
@endsection
