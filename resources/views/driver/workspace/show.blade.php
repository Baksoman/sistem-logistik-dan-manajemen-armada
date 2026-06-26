@extends('layouts.driver-pwa')

@section('title', 'Journey Route')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Full screen layout overrider for this page */
    body { background-color: #f3f4f6; overflow: hidden; padding: 0 !important; }
    header, nav { display: none !important; } /* Hide the default PWA header & bottom nav */
    main { padding: 0 !important; margin: 0 !important; height: 100vh; width: 100vw; }
    
    #map { height: 100vh; width: 100vw; position: absolute; top: 0; left: 0; z-index: 10; }
    .bottom-sheet {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        background-color: #f3f4f6;
        border-top-left-radius: 2rem;
        border-top-right-radius: 2rem;
        z-index: 20;
        box-shadow: 0 -10px 25px rgba(0,0,0,0.1);
    }
    .bottom-sheet-content {
        max-height: 80vh;
        overflow-y: auto;
        padding-bottom: env(safe-area-inset-bottom, 2rem);
    }
    .back-btn {
        position: absolute;
        top: 20px;
        left: 20px;
        z-index: 20;
    }
</style>
@endpush

@section('content')
<div x-data="journeyTracker()">
    <!-- Back Button -->
    <a href="{{ route('driver.workspace.index') }}" class="back-btn w-12 h-12 rounded-full bg-white/90 backdrop-blur-md flex items-center justify-center text-gray-800 shadow-lg active:scale-95 transition-transform">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
    </a>

    <!-- The Map -->
    <div id="map"></div>

    <!-- The Bottom Sheet -->
    <div class="bottom-sheet pb-8" :style="{ transform: sheetTransform, transition: isDragging ? 'none' : 'transform 0.3s cubic-bezier(0.4, 0, 0.2, 1)' }">
        
        <!-- Floating Recenter Map Button (Attached to top of sheet) -->
        @if(in_array($shipment->status, ['On Process', 'Arrived at Hub']))
        <button @click="recenterMap" :class="{'text-blue-600': isTracking, 'text-gray-400': !isTracking}" class="absolute -top-16 right-4 z-[1000] w-12 h-12 rounded-full bg-white/90 backdrop-blur-md flex items-center justify-center shadow-[0_10px_25px_-5px_rgba(0,0,0,0.3)] active:scale-95 transition-all duration-300" aria-label="Recenter Map">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-current-location"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M4 12a8 8 0 1 0 16 0a8 8 0 1 0 -16 0" /><path d="M12 2l0 2" /><path d="M12 20l0 2" /><path d="M20 12l2 0" /><path d="M2 12l2 0" /></svg>
        </button>
        @endif

        <div class="bottom-sheet-content">
            <!-- Draggable Header Area -->
            <div class="cursor-pointer pt-2 px-6 touch-none select-none sticky top-0 bg-[#f3f4f6] z-10"
                 @touchstart="startDrag" @touchmove="doDrag" @touchend="endDrag"
                 @mousedown="startDrag" @mousemove="doDrag" @mouseup="endDrag" @mouseleave="endDrag"
                 @click="toggleSheet"
            >
                <!-- Drag Handle Indicator -->
                <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-6"></div>

                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl font-black text-gray-800 tracking-tight">{{ $shipment->shipment_number }}</h2>
                        <p class="text-sm font-bold text-gray-500 mt-1">Truck: {{ $shipment->vehicle->plate_number }}</p>
                    </div>
                    <div class="bg-blue-100 text-blue-700 text-xs font-black px-3 py-1 rounded-full uppercase tracking-widest">
                        {{ $shipment->status }}
                    </div>
                </div>
            </div>

            <!-- Scrollable Content -->
            <div class="px-6">

        @if($shipment->status === 'Pending')
            <!-- PRE-JOURNEY VIEW -->
            <div class="neu-flat rounded-2xl p-4 mb-6 bg-gray-100">
                <p class="text-sm font-medium text-gray-600 mb-2">You are about to start a new journey. Please ensure your cargo is secured and the vehicle is in good condition.</p>
                <div class="flex items-center gap-3 mt-4 text-sm font-bold text-gray-700">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    {{ $shipment->orders->count() }} Total Packages Loaded
                </div>
            </div>

            <form action="{{ route('driver.shipments.start', $shipment->id) }}" method="POST">
                @csrf
                <button type="submit" class="w-full neu-btn bg-blue-500 text-white font-black py-4 rounded-2xl neu-flat transition-all flex items-center justify-center gap-2 uppercase tracking-widest text-lg shadow-[0_10px_20px_rgba(59,130,246,0.3)]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Swipe to Start
                </button>
            </form>

        @elseif(in_array($shipment->status, ['On Process', 'Arrived at Hub']))
            <!-- ACTIVE JOURNEY VIEW -->
            
            <!-- Live GPS Status -->
            <div class="flex items-center justify-between mb-6 neu-pressed rounded-2xl p-4">
                <div class="flex items-center gap-3">
                    <div class="relative flex h-4 w-4">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-4 w-4 bg-emerald-500"></span>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">GPS Tracker</p>
                        <p class="text-sm font-black text-emerald-600">Broadcasting Live</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <!-- View Orders Button -->
                <a href="{{ route('driver.workspace.packages', $shipment->id) }}" class="neu-btn bg-gray-100 text-gray-800 font-bold py-4 rounded-2xl neu-flat transition-all flex flex-col items-center justify-center gap-2">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <span class="text-xs uppercase tracking-widest">Packages</span>
                </a>

                <!-- Add Cost Button -->
                <a href="{{ route('driver.workspace.costs', $shipment->id) }}" class="neu-btn bg-gray-100 text-gray-800 font-bold py-4 rounded-2xl neu-flat transition-all flex items-center justify-center gap-2">
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="text-xs uppercase tracking-widest">Cost</span>
                </a>
            </div>

            <!-- Complete/Unload Journey Action -->
            <a href="{{ route('driver.workspace.packages', $shipment->id) }}" class="w-full neu-btn bg-gray-800 text-white font-black py-4 rounded-2xl neu-flat transition-all flex items-center justify-center gap-2 uppercase tracking-widest text-lg shadow-xl">
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Scan / Arrive
            </a>
        @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('journeyTracker', () => ({
            map: null,
            marker: null,
            shipmentId: '{{ $shipment->id }}',
            status: '{{ $shipment->status }}',
            isTracking: true,
            
            // Bottom Sheet state
            sheetState: 'collapsed', // 'expanded' or 'collapsed'
            startY: 0,
            offsetY: 0,
            isDragging: false,
            
            // Map data
            routeJson: {!! json_encode($shipment->routeVersion ? $shipment->routeVersion->polyline_geojson : null) !!},
            waypointsJson: {!! json_encode($shipment->routeVersion ? $shipment->routeVersion->waypoints : null) !!},
            orders: {!! json_encode($shipment->orders->map(function($order) {
                return [
                    'lat' => $order->destination_latitude,
                    'lng' => $order->destination_longitude,
                    'number' => $order->order_number
                ];
            })) !!},

            get sheetTransform() {
                if (this.isDragging) {
                    let base = this.sheetState === 'collapsed' ? 'calc(100% - 100px)' : '0px';
                    return `translateY(calc(${base} + ${this.offsetY}px))`;
                }
                return this.sheetState === 'collapsed' ? 'translateY(calc(100% - 100px))' : 'translateY(0)';
            },

            startDrag(e) {
                this.isDragging = true;
                this.startY = e.touches ? e.touches[0].clientY : e.clientY;
            },

            doDrag(e) {
                if (!this.isDragging) return;
                let currentY = e.touches ? e.touches[0].clientY : e.clientY;
                let diff = currentY - this.startY;
                
                // Add resistance if pulling past limits
                if ((this.sheetState === 'collapsed' && diff > 0) || (this.sheetState === 'expanded' && diff < 0)) {
                    diff = diff * 0.2;
                }
                
                this.offsetY = diff;
                
                // Prevent scrolling map while dragging sheet
                if (e.cancelable) e.preventDefault();
            },

            endDrag() {
                if (!this.isDragging) return;
                this.isDragging = false;
                
                if (this.sheetState === 'expanded' && this.offsetY > 50) {
                    this.sheetState = 'collapsed';
                } else if (this.sheetState === 'collapsed' && this.offsetY < -50) {
                    this.sheetState = 'expanded';
                }
                
                this.startY = 0;
                this.offsetY = 0;
            },
            
            toggleSheet() {
                if (this.offsetY === 0) { // Only toggle if it wasn't a drag
                    this.sheetState = this.sheetState === 'expanded' ? 'collapsed' : 'expanded';
                }
            },

            init() {
                // Initialize Map
                this.map = L.map('map', { zoomControl: false }).setView([-2.5489, 118.0149], 5);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; OpenStreetMap &copy; CARTO'
                }).addTo(this.map);

                // Stop tracking on manual map drag
                this.map.on('dragstart', () => {
                    this.isTracking = false;
                });

                let parsedRoute = this.routeJson;
                if (typeof parsedRoute === 'string') {
                    try { parsedRoute = JSON.parse(parsedRoute); } catch(e) { parsedRoute = null; }
                }

                // Draw Master Route
                if (parsedRoute) {
                    try {
                        const routeLayer = L.geoJSON(parsedRoute, {
                            style: { color: '#3b82f6', weight: 5, opacity: 0.8 }
                        }).addTo(this.map);
                        this.map.fitBounds(routeLayer.getBounds(), { padding: [50, 50] });
                    } catch(e) { console.error("GeoJSON error:", e); }
                }

                // Draw Route Waypoints (Origin, Stops, Destination)
                let parsedWaypoints = this.waypointsJson;
                if (typeof parsedWaypoints === 'string') {
                    try { parsedWaypoints = JSON.parse(parsedWaypoints); } catch(e) { parsedWaypoints = null; }
                }

                if (parsedWaypoints && Array.isArray(parsedWaypoints)) {
                    parsedWaypoints.forEach((wp, index) => {
                        let color = index === 0 ? '#10b981' : '#3b82f6'; // Green for origin, blue for stops
                        L.circleMarker([wp[1], wp[0]], {
                            color: color,
                            fillColor: '#ffffff',
                            fillOpacity: 1,
                            weight: 4,
                            radius: 8
                        }).addTo(this.map);
                    });
                }

                // Draw Package Destinations
                if (this.orders && Array.isArray(this.orders)) {
                    this.orders.forEach(order => {
                        if (order.lat && order.lng) {
                            L.circleMarker([order.lat, order.lng], {
                                color: '#ef4444',
                                fillColor: '#ef4444',
                                fillOpacity: 1,
                                radius: 6,
                                weight: 2
                            }).addTo(this.map);
                        }
                    });
                }

                setTimeout(() => {
                    this.map.invalidateSize();
                    if (parsedRoute) {
                         try {
                             const routeLayer = L.geoJSON(parsedRoute);
                             this.map.fitBounds(routeLayer.getBounds(), { padding: [50, 50] });
                         } catch(e) {}
                    }
                }, 400);

                // Try to get actual location
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            
                            this.map.setView([lat, lng], 15);
                            
                            const truckIcon = L.divIcon({
                                html: `<div class="relative flex items-center justify-center w-full h-full">
                                         <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center shadow-[0_4px_10px_rgba(37,99,235,0.5)] border-2 border-white relative z-10 text-white">
                                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M5 17a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M15 17a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" /><path d="M3 9l4 0" /></svg>
                                         </div>
                                         <div class="absolute inset-0 bg-blue-400 rounded-full animate-ping opacity-50 z-0 m-auto" style="width: 40px; height: 40px;"></div>
                                       </div>`,
                                className: 'bg-transparent border-none',
                                iconSize: [80, 80],
                                iconAnchor: [40, 40]
                            });

                            this.marker = L.marker([lat, lng], { icon: truckIcon }).addTo(this.map);

                            // If on process, start pinging
                            if (this.status === 'On Process' || this.status === 'Arrived at Hub') {
                               // this.startPinging(); // Dikomentari sementara untuk tes simulator
                            }
                        },
                        (error) => {
                            console.warn("GPS Access Denied or Unavailable.", error);
                            // Fallback to route origin if available (mocked for now)
                        },
                        { enableHighAccuracy: true }
                    );
                }
            },

            recenterMap() {
                this.isTracking = true;
                if (this.marker) {
                    this.map.flyTo(this.marker.getLatLng(), 16, { animate: true, duration: 1 });
                }
            },

            startPinging() {
                let lastPingTime = 0;
                
                navigator.geolocation.watchPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        
                        // Update local marker
                        if(this.marker) {
                            this.marker.setLatLng([lat, lng]);
                            if (this.isTracking) {
                                this.map.panTo([lat, lng]);
                            }
                        }

                        const now = Date.now();
                        if (now - lastPingTime > 10000) {
                            lastPingTime = now;
                            
                            // Send ping to server
                            fetch('/api/driver/location/ping', {
                                method: 'POST',
                                credentials: 'include',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                                },
                                body: JSON.stringify({
                                    shipment_id: this.shipmentId,
                                    lat: lat,
                                    lng: lng
                                })
                            }).catch(err => console.warn("Background ping failed:", err));
                            
                        }
                    },
                    (error) => {
                        if (error.code !== 3) {
                            console.warn("GPS tracking error:", error);
                        }
                    },
                    { enableHighAccuracy: true, maximumAge: 0 }
                );
            }
        }));
    });
</script>
@endpush
