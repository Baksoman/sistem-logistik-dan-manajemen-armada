@extends('layouts.guest')

@section('title', 'Home')

@section('content')
    <div class="w-full max-w-5xl px-6 flex flex-col justify-center items-center h-full my-auto py-16 relative z-10">
        <!-- Hero Title -->
        <h1 class="text-4xl md:text-6xl font-black text-gray-800 text-center mb-6 leading-tight tracking-tight drop-shadow-[2px_4px_6px_rgba(209,213,219,0.5)]" style="font-family: 'Inter', sans-serif;">
            Sistem Logistik, <br> 
            <span class="text-indigo-600">Aman & Terpercaya</span>
        </h1>
        <p class="text-lg md:text-xl text-gray-500 text-center mb-16 max-w-2xl mx-auto font-medium">
            Lacak pengiriman secara real-time dan kelola armada dengan efisiensi maksimal melalui platform manajemen terpusat.
        </p>

        <!-- Tracking Card (Convex) -->
        <div class="w-full max-w-3xl mx-auto bg-gray-100 rounded-3xl p-8 shadow-[12px_12px_24px_#c2c6cc,-12px_-12px_24px_#ffffff]" x-data="qrScanner()">
            <h2 class="text-2xl font-bold text-gray-800 mb-8 text-center tracking-tight">Cek Resi Pengiriman</h2>
            
            <form action="{{ route('track.search') }}" method="GET" class="flex flex-col sm:flex-row gap-6" id="track-form">
                <!-- Input Field (Concave) -->
                <div class="flex-1">
                    <label for="tracking_id" class="sr-only">Nomor Resi</label>
                    <input id="tracking_id" name="tracking_id" value="{{ request('tracking_id') }}" x-model="trackingId" type="text" placeholder="Contoh: ORD-12345678" class="w-full bg-gray-100 rounded-2xl px-6 py-4 text-lg font-semibold text-gray-800 shadow-[inset_6px_6px_12px_#d1d5db,inset_-6px_-6px_12px_#ffffff] border-none focus:ring-0 focus:outline-none transition-shadow placeholder-gray-400 tracking-wide" required />
                </div>
                
                <div class="flex gap-4">
                    <!-- QR Button (Tactile) -->
                    <button type="button" @click="openScanner" class="flex-none w-16 h-[60px] flex items-center justify-center text-gray-500 hover:text-indigo-600 bg-gray-100 rounded-2xl shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff] active:shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] transition-all focus:outline-none">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    </button>
                    <!-- Submit Button (Tactile Convex to Concave) -->
                    <button type="submit" class="flex-1 sm:w-32 bg-gray-100 text-indigo-600 text-lg font-bold tracking-wide rounded-2xl py-4 px-8 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff] active:shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] hover:text-indigo-700 transition-all focus:outline-none">
                        Lacak
                    </button>
                </div>
            </form>

            <!-- Tracking Results Area -->
            @if(isset($error))
                <div class="mt-10 w-full p-6 bg-gray-100 rounded-2xl shadow-[inset_6px_6px_12px_#d1d5db,inset_-6px_-6px_12px_#ffffff] border-l-4 border-red-500 flex items-center gap-4 text-red-600 font-bold">
                    <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p>{{ $error }}</p>
                </div>
            @elseif(isset($type) && isset($data))
                <div class="mt-12 pt-8 border-t-2 border-gray-200/50">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-gray-200/50 pb-6 mb-8 gap-4">
                        <div>
                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1 shadow-sm">Status {{ $type === 'order' ? 'Pesanan' : 'Pengiriman' }}</p>
                            <h2 class="text-2xl font-black text-gray-800 uppercase tracking-widest">{{ $type === 'order' ? $data->order_number : $data->shipment_number }}</h2>
                        </div>
                        <div class="px-6 py-2 bg-gray-100 rounded-full shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff]">
                            <span class="text-sm font-black text-indigo-600 uppercase tracking-widest">{{ $data->status }}</span>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                        @if($type === 'order')
                            <div class="bg-gray-100 p-5 rounded-2xl shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff]">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tujuan Pengiriman</p>
                                <p class="font-bold text-gray-700 leading-relaxed">{{ $data->destination_address }}</p>
                            </div>
                            <div class="bg-gray-100 p-5 rounded-2xl shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff]">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Gudang Asal</p>
                                <p class="font-bold text-gray-700">{{ $data->originWarehouse->name ?? 'N/A' }}</p>
                            </div>
                        @else
                            <div class="bg-gray-100 p-5 rounded-2xl shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff]">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Kendaraan</p>
                                <p class="font-bold text-gray-700">{{ $data->vehicle->license_plate ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-100 p-5 rounded-2xl shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff]">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Driver</p>
                                <p class="font-bold text-gray-700">{{ $data->driver->user->name ?? 'N/A' }}</p>
                            </div>
                        @endif
                    </div>

                    @php
                        $latestGps = null;
                        if ($type === 'shipment') {
                            $latestGps = $data->gpsHistory->first();
                        } else {
                            $activeShipment = $data->shipments->first();
                            if ($activeShipment) {
                                $latestGps = $activeShipment->gpsHistory->first();
                            }
                        }
                    @endphp

                    @if($latestGps)
                        <!-- Live Tracking Map -->
                        <div class="mb-10 w-full bg-gray-100 rounded-3xl p-6 shadow-[inset_6px_6px_12px_#d1d5db,inset_-6px_-6px_12px_#ffffff]">
                            <h3 class="text-sm font-black text-gray-700 uppercase tracking-widest mb-4">Lokasi Armada Terkini</h3>
                            <div class="p-2 bg-gray-100 rounded-3xl shadow-[8px_8px_16px_#d1d5db,-8px_-8px_16px_#ffffff]">
                                <div id="tracking-map" class="w-full h-80 rounded-2xl z-10" style="border-radius: 1rem;"></div>
                            </div>
                            <div class="mt-6 flex flex-col sm:flex-row justify-between items-center bg-gray-100 p-4 rounded-xl shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff]">
                                <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Update Terakhir: <span class="font-black text-indigo-600">{{ $latestGps->recorded_at->diffForHumans() }}</span></p>
                                <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-2 sm:mt-0">Kecepatan: <span class="font-black text-indigo-600">{{ number_format($latestGps->speed, 1) }} km/j</span></p>
                            </div>
                        </div>
                    @endif

                    <!-- Timeline -->
                    <h3 class="text-sm font-black text-gray-700 uppercase tracking-widest mb-8 text-center">Riwayat Perjalanan</h3>
                    <div class="space-y-8 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-1 before:bg-gray-200 before:shadow-[inset_1px_1px_2px_#d1d5db]">
                        @php
                            $timelineItems = $type === 'order' ? $data->histories : $data->checkpoints;
                        @endphp

                        @forelse($timelineItems as $index => $item)
                            <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                                <!-- Marker Concave -->
                                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] text-indigo-600 shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10">
                                    @if($index === 0)
                                        <div class="w-6 h-6 rounded-full bg-indigo-500 shadow-[2px_2px_4px_rgba(0,0,0,0.2)] animate-pulse"></div>
                                    @else
                                        <div class="w-4 h-4 rounded-full bg-gray-400 shadow-[2px_2px_4px_#c2c6cc]"></div>
                                    @endif
                                </div>
                                <!-- Content Convex -->
                                <div class="w-[calc(100%-4rem)] md:w-[calc(50%-3rem)] p-6 bg-gray-100 rounded-3xl shadow-[8px_8px_16px_#d1d5db,-8px_-8px_16px_#ffffff]">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-2">
                                            {{ $type === 'order' ? $item->created_at->format('d M Y, H:i') : $item->recorded_at->format('d M Y, H:i') }}
                                        </span>
                                        <h4 class="font-black text-gray-800 text-sm uppercase tracking-wide">{{ $type === 'order' ? $item->status : $item->checkpoint_type }}</h4>
                                        <p class="text-xs font-medium text-gray-500 mt-2 leading-relaxed">
                                            {{ $item->description ?? '-' }}
                                            @if($type === 'order' && $item->location)
                                                <br><span class="font-bold text-gray-700">📍 {{ $item->location }}</span>
                                            @elseif($type === 'shipment' && $item->location_name)
                                                <br><span class="font-bold text-gray-700">📍 {{ $item->location_name }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="w-full text-center py-8 bg-gray-100 rounded-3xl shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff]">
                                <p class="text-sm font-bold text-gray-500 uppercase tracking-widest">Belum ada riwayat tercatat.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- QR Scanner Modal -->
            <div x-show="isScanning" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-md">
                <div @click.away="closeScanner" class="bg-gray-100 rounded-[2.5rem] p-8 w-full max-w-lg shadow-[16px_16px_32px_#000000,-16px_-16px_32px_#ffffff]">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-black text-gray-700 uppercase tracking-widest">Scan QR Code</h3>
                        <button @click="closeScanner" type="button" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] text-gray-600 hover:text-red-500 active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="p-4 bg-gray-100 rounded-3xl shadow-[inset_6px_6px_12px_#d1d5db,inset_-6px_-6px_12px_#ffffff]">
                        <div id="reader" class="w-full bg-black rounded-2xl overflow-hidden min-h-[300px]"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Decoration / Features (Neumorphism Style) -->
        <div class="mt-28 grid grid-cols-1 md:grid-cols-3 gap-12 w-full">
            <div class="bg-gray-100 rounded-[2.5rem] p-10 flex flex-col items-center text-center shadow-[12px_12px_24px_#c2c6cc,-12px_-12px_24px_#ffffff]">
                <div class="w-24 h-24 rounded-full mb-8 bg-gray-100 shadow-[inset_6px_6px_12px_#d1d5db,inset_-6px_-6px_12px_#ffffff] flex items-center justify-center text-indigo-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </div>
                <h3 class="font-black text-lg uppercase tracking-widest text-gray-800 mb-4">Fast Delivery</h3>
                <p class="text-gray-500 font-medium text-sm leading-relaxed">Pengiriman secepat kilat ke seluruh pelosok Indonesia dengan armada terbaik.</p>
            </div>
            
            <div class="bg-gray-100 rounded-[2.5rem] p-10 flex flex-col items-center text-center shadow-[12px_12px_24px_#c2c6cc,-12px_-12px_24px_#ffffff]">
                <div class="w-24 h-24 rounded-full mb-8 bg-gray-100 shadow-[inset_6px_6px_12px_#d1d5db,inset_-6px_-6px_12px_#ffffff] flex items-center justify-center text-indigo-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <h3 class="font-black text-lg uppercase tracking-widest text-gray-800 mb-4">Secure Cargo</h3>
                <p class="text-gray-500 font-medium text-sm leading-relaxed">Jaminan keamanan penuh untuk setiap barang Anda melalui asuransi dan SOP ketat.</p>
            </div>
            
            <div class="bg-gray-100 rounded-[2.5rem] p-10 flex flex-col items-center text-center shadow-[12px_12px_24px_#c2c6cc,-12px_-12px_24px_#ffffff]">
                <div class="w-24 h-24 rounded-full mb-8 bg-gray-100 shadow-[inset_6px_6px_12px_#d1d5db,inset_-6px_-6px_12px_#ffffff] flex items-center justify-center text-indigo-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h3 class="font-black text-lg uppercase tracking-widest text-gray-800 mb-4">Live Tracking</h3>
                <p class="text-gray-500 font-medium text-sm leading-relaxed">Pantau pergerakan armada dan status pengiriman barang Anda kapan saja 24/7.</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@if(isset($latestGps) && $latestGps)
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('tracking-map', { zoomControl: false }).setView([{{ $latestGps->latitude }}, {{ $latestGps->longitude }}], 15);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO'
        }).addTo(map);

        const icon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div class="w-10 h-10 bg-indigo-500 rounded-full border-4 border-white shadow-[0_4px_10px_rgba(0,0,0,0.3)] flex items-center justify-center">
                     <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                   </div>`,
            iconSize: [40, 40],
            iconAnchor: [20, 20]
        });

        L.marker([{{ $latestGps->latitude }}, {{ $latestGps->longitude }}], {icon: icon})
            .addTo(map).bindPopup("<div class='text-center'><b class='text-indigo-600'>Kurir sedang di perjalanan!</b></div>");
    });
</script>
@endif

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('qrScanner', () => ({
            trackingId: '',
            isScanning: false,
            html5QrcodeScanner: null,
            
            openScanner() {
                this.isScanning = true;
                this.$nextTick(() => {
                    if (!this.html5QrcodeScanner) {
                        this.html5QrcodeScanner = new Html5Qrcode("reader");
                    }
                    
                    const config = { fps: 10, qrbox: { width: 250, height: 250 } };
                    
                    this.html5QrcodeScanner.start(
                        { facingMode: "environment" },
                        config,
                        (decodedText, decodedResult) => {
                            // On Success
                            this.trackingId = decodedText;
                            this.closeScanner();
                            
                            // Auto submit form
                            setTimeout(() => {
                                document.getElementById('track-form').submit();
                            }, 300);
                        },
                        (errorMessage) => {
                            // parse error, ignore it.
                        }
                    ).catch((err) => {
                        console.error("Camera error:", err);
                        alert("Gagal mengakses kamera. Pastikan Anda telah memberikan izin kamera (HTTPS required).");
                        this.isScanning = false;
                    });
                });
            },
            
            closeScanner() {
                this.isScanning = false;
                if (this.html5QrcodeScanner && this.html5QrcodeScanner.isScanning) {
                    this.html5QrcodeScanner.stop().catch(err => console.error(err));
                }
            }
        }));
    });
</script>
@endpush
