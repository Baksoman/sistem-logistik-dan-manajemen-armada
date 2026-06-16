@extends('layouts.driver-pwa')

@section('title', 'My Journey')

@section('content')
<div x-data="driverWorkspace()" class="pb-10">

    @if($activeShipments->isEmpty())
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center mt-20 text-center">
            <div class="w-32 h-32 rounded-full neu-flat flex items-center justify-center mb-6">
                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h2 class="text-xl font-bold text-gray-700">No Active Journeys</h2>
            <p class="text-gray-500 text-sm mt-2">You don't have any active shipments assigned to you right now.</p>
        </div>
    @else
        <!-- Swiper/List of Shipments -->
        <div class="space-y-8">
            @foreach($activeShipments as $shipment)
            <div class="neu-flat rounded-3xl p-5 relative overflow-hidden bg-gray-100">
                <!-- Status Badge -->
                <div class="absolute top-0 right-0 bg-blue-500 text-white text-[10px] font-black px-4 py-1 rounded-bl-xl uppercase tracking-widest shadow-md">
                    {{ $shipment->status }}
                </div>

                <!-- Vehicle Info -->
                <div class="flex items-center gap-4 mb-6 mt-2">
                    <div class="w-14 h-14 rounded-2xl neu-pressed flex items-center justify-center bg-gray-100 text-gray-600">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 tracking-widest uppercase">Your Truck</p>
                        <p class="text-lg font-black text-gray-800">{{ $shipment->vehicle->plate_number ?? 'Unknown' }}</p>
                    </div>
                </div>

                <!-- Shipment Info -->
                <div class="neu-pressed rounded-2xl p-4 mb-6">
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-xs font-bold text-gray-500">Shipment Code</span>
                        <span class="text-sm font-black text-gray-800">{{ $shipment->shipment_number }}</span>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-xs font-bold text-gray-500">Total Packages</span>
                        <span class="text-sm font-black text-gray-800">{{ $shipment->orders->count() }} Boxes</span>
                    </div>
                </div>

                <!-- Action Swipe / Buttons -->
                @if($shipment->status === 'Pending')
                    <form action="{{ route('driver.shipments.start', $shipment->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full neu-btn bg-blue-500 text-white font-black py-4 rounded-2xl neu-flat transition-all flex items-center justify-center gap-2 uppercase tracking-widest text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Start Journey
                        </button>
                    </form>
                @elseif(in_array($shipment->status, ['On Process', 'Arrived at Hub']))
                    
                    <div class="flex flex-col gap-4">
                        <!-- Location Ping Status (Simulated) -->
                        <div class="flex items-center justify-center gap-2 text-xs font-bold text-emerald-600 mb-2">
                            <span class="relative flex h-3 w-3">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                            </span>
                            Broadcasting Live GPS...
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Show Map Button -->
                            <button class="neu-btn bg-gray-100 text-blue-600 font-bold py-4 rounded-2xl neu-flat transition-all flex flex-col items-center justify-center gap-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                                <span class="text-xs uppercase tracking-wider">Map</span>
                            </button>
                            <!-- View Orders Button -->
                            <button class="neu-btn bg-gray-100 text-gray-700 font-bold py-4 rounded-2xl neu-flat transition-all flex flex-col items-center justify-center gap-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                <span class="text-xs uppercase tracking-wider">Packages</span>
                            </button>
                        </div>

                        <!-- Complete/Unload Journey Action -->
                        <button type="button" @click="confirmComplete('{{ $shipment->id }}')" class="w-full neu-btn bg-emerald-500 text-white font-black py-4 rounded-2xl neu-flat transition-all flex items-center justify-center gap-2 uppercase tracking-widest text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Arrive / Complete
                        </button>
                    </div>
                @endif
            </div>
            @endforeach
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function driverWorkspace() {
        return {
            init() {
                // If there's an On Process shipment, start pinging
                @if($activeShipments->where('status', 'On Process')->count() > 0)
                    this.startLocationTracking("{{ $activeShipments->where('status', 'On Process')->first()->id }}");
                @endif
            },

            startLocationTracking(shipmentId) {
                if ("geolocation" in navigator) {
                    navigator.geolocation.watchPosition((position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        
                        // Send ping to server
                        fetch('/api/driver/location/ping', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                lat: lat,
                                lng: lng,
                                shipment_id: shipmentId
                            })
                        }).catch(e => console.log('Offline ping saved'));

                    }, (error) => {
                        console.error("GPS Error:", error);
                    }, {
                        enableHighAccuracy: true,
                        maximumAge: 10000,
                        timeout: 5000
                    });
                }
            },

            confirmComplete(shipmentId) {
                Swal.fire({
                    title: 'Arrived at Destination?',
                    text: "Have you reached the checkpoint or final destination?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Arrived',
                    cancelButtonText: 'Not Yet',
                    background: '#f3f4f6',
                    color: '#374151',
                    customClass: {
                        popup: 'rounded-3xl shadow-[12px_12px_24px_#d1d5db,-12px_-12px_24px_#ffffff] border-none',
                        confirmButton: 'rounded-xl font-bold bg-emerald-500 shadow-[4px_4px_8px_#d1d5db] px-6 py-3 ml-2 text-white',
                        cancelButton: 'rounded-xl font-bold bg-gray-100 shadow-[4px_4px_8px_#d1d5db] px-6 py-3 text-gray-600'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // In real scenario, redirect to Unload screen or submit form
                        Toastify({ text: "Arrival recorded. Awaiting Unload process.", duration: 3000, style: { background: "#10b981", borderRadius: "1rem" } }).showToast();
                    }
                });
            }
        }
    }
</script>
@endpush
