@extends('layouts.driver-pwa')

@section('title', 'Shipment Packages')

@section('content')
<div class="pt-6" x-data="podManager()">
    <!-- Header -->
    <div class="flex items-center mb-8">
        <a href="{{ route('driver.workspace.show', $shipment->id) }}" class="w-12 h-12 rounded-full neu-flat flex items-center justify-center text-gray-600 mr-4 active:neu-pressed">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-800">Packages</h1>
            <p class="text-sm text-gray-500 font-bold tracking-widest uppercase">{{ $shipment->shipment_number }}</p>
        </div>
    </div>

    <!-- Scan Barcode Button (Dummy) -->
    <div class="mb-8">
        <button type="button" onclick="alert('Camera scanner will open here!')" class="w-full neu-btn bg-blue-500 text-white font-bold py-4 rounded-2xl neu-flat transition-all flex items-center justify-center gap-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            SCAN PACKAGE QR
        </button>
    </div>

    <div class="space-y-6 pb-20">
        @foreach($shipment->orders as $order)
            @php
                $pivotStatus = $order->pivot->status;
                $isPending = in_array($pivotStatus, ['Pending', 'On Process', 'Loaded']);
                $dropoffHub = $order->pivot->dropoff_warehouse_id ? $warehouses->get($order->pivot->dropoff_warehouse_id) : null;
            @endphp
            <div class="bg-gray-100 p-5 rounded-3xl {{ $isPending ? 'neu-flat' : 'neu-pressed opacity-60' }}">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="text-xs font-bold text-gray-400 tracking-widest uppercase mb-1">Order No.</p>
                        <h3 class="text-lg font-black text-gray-800">{{ $order->order_number }}</h3>
                    </div>
                    @if($isPending)
                        <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">In Transit</span>
                    @else
                        <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Unloaded</span>
                    @endif
                </div>

                <div class="mb-4">
                    <p class="text-xs font-bold text-gray-400 tracking-widest uppercase mb-1">Dropoff Point</p>
                    @if($dropoffHub)
                        <p class="font-bold text-blue-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            Hub: {{ $dropoffHub->name }}
                        </p>
                    @else
                        <p class="font-bold text-purple-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Customer: {{ $order->customer->name ?? 'Direct' }}
                        </p>
                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $order->destination_address }}</p>
                    @endif
                </div>

                <div class="mb-4 bg-white p-3 rounded-xl shadow-inner text-sm">
                    <p class="text-xs font-bold text-gray-400 tracking-widest uppercase mb-2">Items</p>
                    <ul class="list-disc pl-4 text-gray-700 font-medium">
                        @foreach($order->items as $item)
                            <li>{{ $item->quantity }}x {{ $item->stockItem->name ?? 'Item' }}</li>
                        @endforeach
                    </ul>
                </div>

                @if($isPending)
                    <button type="button" @click="openModal('{{ $order->id }}', '{{ route('driver.workspace.unload', ['shipment' => $shipment->id, 'order' => $order->id]) }}')" class="w-full neu-btn bg-emerald-500 text-white font-black py-3 rounded-xl neu-flat transition-all uppercase tracking-widest text-sm flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Unload / Check-out
                    </button>
                @endif
            </div>
        @endforeach
    </div>

    <!-- PoD Modal -->
    <div x-show="isOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-end justify-center sm:items-center bg-gray-900/50 backdrop-blur-sm" x-transition.opacity>
        <div class="bg-gray-100 w-full sm:w-96 rounded-t-3xl sm:rounded-3xl p-6 neu-flat max-h-[90vh] overflow-y-auto" @click.away="isOpen = false" x-show="isOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">
            
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-black text-gray-800">Proof of Delivery</h2>
                <button @click="isOpen = false" class="w-8 h-8 rounded-full neu-pressed flex items-center justify-center text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="actionUrl" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- GPS Coordinates (Hidden) -->
                <input type="hidden" name="latitude" x-model="lat">
                <input type="hidden" name="longitude" x-model="lng">

                <!-- Receiver Name -->
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 tracking-widest uppercase mb-2">Receiver Name</label>
                    <input type="text" name="receiver_name" required class="w-full bg-gray-100 neu-pressed rounded-xl px-4 py-3 text-gray-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="e.g. Budi (Warehouse Staff)">
                </div>

                <!-- Receiver Phone (Optional) -->
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 tracking-widest uppercase mb-2">Phone Number</label>
                    <input type="text" name="receiver_phone" class="w-full bg-gray-100 neu-pressed rounded-xl px-4 py-3 text-gray-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="(Optional)">
                </div>

                <!-- Notes (Optional) -->
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 tracking-widest uppercase mb-2">Notes / Condition</label>
                    <textarea name="notes" rows="2" class="w-full bg-gray-100 neu-pressed rounded-xl px-4 py-3 text-gray-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="e.g. Package intact..."></textarea>
                </div>

                <!-- Photo Upload -->
                <div class="mb-8">
                    <label class="block text-xs font-bold text-gray-500 tracking-widest uppercase mb-2">Photo Proof</label>
                    <div class="relative w-full h-32 neu-pressed rounded-xl flex flex-col items-center justify-center text-gray-400 overflow-hidden group">
                        <svg class="w-8 h-8 mb-2 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="text-xs font-bold" x-text="fileName ? fileName : 'Tap to take a photo'"></span>
                        <input type="file" name="pod_photo" accept="image/*" capture="environment" @change="fileName = $event.target.files[0].name" required class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                    </div>
                </div>

                <button type="submit" class="w-full neu-btn bg-emerald-500 text-white font-black py-4 rounded-xl neu-flat transition-all uppercase tracking-widest flex items-center justify-center gap-2" :class="{ 'opacity-50 cursor-not-allowed': isLoading }" x-on:click="isLoading = true">
                    <span x-show="!isLoading">Submit Unload</span>
                    <span x-show="isLoading">Processing...</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function podManager() {
    return {
        isOpen: false,
        orderId: null,
        actionUrl: '',
        fileName: '',
        lat: null,
        lng: null,
        isLoading: false,
        openModal(id, url) {
            this.orderId = id;
            this.actionUrl = url;
            this.isOpen = true;
            this.fileName = '';
            this.isLoading = false;
            
            // Get Current GPS for PoD
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition((position) => {
                    this.lat = position.coords.latitude;
                    this.lng = position.coords.longitude;
                });
            }
        }
    }
}
</script>
@endpush
