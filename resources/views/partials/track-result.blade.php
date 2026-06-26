<div class="mt-10 p-8 bg-gray-100 rounded-3xl shadow-[inset_8px_8px_16px_#d1d5db,inset_-8px_-8px_16px_#ffffff]">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-gray-200 pb-6 mb-8 gap-4">
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Status {{ $type === 'order' ? 'Pesanan' : 'Pengiriman' }}</p>
            <h2 class="text-2xl font-black text-gray-800 uppercase">{{ $type === 'order' ? $data->order_number : $data->shipment_number }}</h2>
        </div>
        <div class="px-6 py-2 bg-gray-100 rounded-full shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff]">
            <span class="text-sm font-bold text-indigo-600 uppercase">{{ $data->status }}</span>
        </div>
    </div>

    <!-- Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        @if($type === 'order')
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tujuan Pengiriman</p>
                <p class="font-medium text-gray-800 leading-relaxed">{{ $data->destination_address }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Gudang Asal</p>
                <p class="font-medium text-gray-800">{{ $data->originWarehouse->name ?? 'N/A' }}</p>
            </div>
        @else
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Kendaraan</p>
                <p class="font-medium text-gray-800">{{ $data->vehicle->license_plate ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Driver</p>
                <p class="font-medium text-gray-800">{{ $data->driver->user->name ?? 'N/A' }}</p>
            </div>
        @endif
    </div>

    @if($latestGps)
        <!-- Live Tracking Map -->
        <div class="mb-10 w-full bg-gray-100 rounded-3xl p-4 shadow-[inset_6px_6px_12px_#d1d5db,inset_-6px_-6px_12px_#ffffff]">
            <h3 class="text-lg font-bold text-gray-800 mb-4 px-2">Lokasi Kurir Terkini</h3>
            <div id="tracking-map" class="w-full h-80 rounded-2xl z-10" style="border-radius: 1rem; border: 4px solid #f3f4f6;"></div>
            <div class="mt-4 px-2 flex justify-between items-center">
                <p class="text-xs text-gray-500 font-medium">Update Terakhir: <span class="font-bold text-indigo-500">{{ $latestGps->recorded_at->diffForHumans() }}</span></p>
                <p class="text-xs text-gray-500 font-medium">Kecepatan: <span class="font-bold text-indigo-500">{{ number_format($latestGps->speed, 1) }} km/j</span></p>
            </div>
        </div>
    @endif

    <!-- Timeline -->
    <h3 class="text-lg font-bold text-gray-800 mb-6">Riwayat Perjalanan</h3>
    <div class="space-y-6 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-gray-300 before:to-transparent">
        @php
            $timelineItems = $type === 'order' ? $data->histories : $data->checkpoints;
        @endphp

        @forelse($timelineItems as $index => $item)
            <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                <!-- Icon -->
                <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-gray-100 bg-gray-100 shadow-[2px_2px_4px_#d1d5db,-2px_-2px_4px_#ffffff] text-indigo-500 shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10">
                    @if($index === 0)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    @else
                        <span class="w-2 h-2 bg-indigo-400 rounded-full"></span>
                    @endif
                </div>
                <!-- Card -->
                <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 bg-gray-100 rounded-2xl shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff]">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider mb-1">
                            {{ $type === 'order' ? $item->created_at->format('d M Y, H:i') : $item->recorded_at->format('d M Y, H:i') }}
                        </span>
                        <h4 class="font-bold text-gray-800 text-sm">{{ $type === 'order' ? $item->status : $item->checkpoint_type }}</h4>
                        <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                            {{ $item->description ?? '-' }}
                            @if($type === 'order' && $item->location)
                                <br><span class="font-medium text-gray-600">Lokasi: {{ $item->location }}</span>
                            @elseif($type === 'shipment' && $item->location_name)
                                <br><span class="font-medium text-gray-600">Lokasi: {{ $item->location_name }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-sm font-medium text-gray-500 italic py-8">Belum ada riwayat tercatat.</p>
        @endforelse
    </div>
</div>
