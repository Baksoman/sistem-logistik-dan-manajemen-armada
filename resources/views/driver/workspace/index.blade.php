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
            <a href="{{ route('driver.workspace.show', $shipment->id) }}" class="block neu-flat rounded-3xl p-5 relative overflow-hidden bg-gray-100 active:scale-95 transition-transform duration-200">
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
                <div class="neu-pressed rounded-2xl p-4 mb-2">
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-xs font-bold text-gray-500">Shipment Code</span>
                        <span class="text-sm font-black text-gray-800">{{ $shipment->shipment_number }}</span>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-xs font-bold text-gray-500">Total Packages</span>
                        <span class="text-sm font-black text-gray-800">{{ $shipment->orders->count() }} Boxes</span>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <span class="text-xs font-bold text-blue-500 tracking-widest uppercase">Tap to view map details &rarr;</span>
                </div>
            </a>
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
