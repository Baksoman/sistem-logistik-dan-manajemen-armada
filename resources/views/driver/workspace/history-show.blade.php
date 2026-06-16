@extends('layouts.driver-pwa')

@section('title', 'Shipment Detail')

@section('content')
<div class="pt-6 pb-20">
    <!-- Header -->
    <div class="flex items-center mb-8">
        <a href="{{ route('driver.workspace.history') }}" class="w-12 h-12 rounded-full neu-flat flex items-center justify-center text-gray-600 mr-4 active:neu-pressed">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-800">History</h1>
            <p class="text-sm text-gray-500 font-bold tracking-widest uppercase">{{ $shipment->shipment_number }}</p>
        </div>
    </div>

    <!-- Overview Card -->
    <div class="bg-gray-100 p-6 rounded-3xl neu-flat mb-8">
        <div class="flex justify-between items-center mb-4">
            <span class="text-xs font-bold text-gray-400 tracking-widest uppercase">Status</span>
            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold uppercase tracking-wider">{{ $shipment->status }}</span>
        </div>
        <div class="mb-4">
            <span class="text-xs font-bold text-gray-400 tracking-widest uppercase block mb-1">Route</span>
            <span class="text-sm font-black text-gray-800">{{ $shipment->routeVersion->route->route_code ?? 'Ad-hoc Route' }}</span>
        </div>
        <div class="flex gap-4">
            <div class="flex-1 bg-white p-3 rounded-2xl shadow-inner">
                <span class="text-[10px] font-bold text-gray-400 tracking-widest uppercase block mb-1">Vehicle</span>
                <span class="text-sm font-black text-gray-800">{{ $shipment->vehicle->plate_number ?? '-' }}</span>
            </div>
            <div class="flex-1 bg-white p-3 rounded-2xl shadow-inner">
                <span class="text-[10px] font-bold text-gray-400 tracking-widest uppercase block mb-1">Finished</span>
                <span class="text-sm font-black text-gray-800">{{ $shipment->completed_at ? \Carbon\Carbon::parse($shipment->completed_at)->format('H:i, d M') : '-' }}</span>
            </div>
        </div>
    </div>

    <!-- Timeline Checkpoints -->
    <h2 class="text-lg font-black text-gray-800 mb-6 flex items-center gap-2">
        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        Journey Timeline
    </h2>
    
    <div class="mb-10 pl-4 border-l-2 border-gray-200 space-y-6 relative ml-2">
        @foreach($checkpoints as $index => $checkpoint)
        <div class="relative">
            <!-- Timeline Dot -->
            <div class="absolute -left-[21px] top-1 w-4 h-4 rounded-full border-4 border-gray-100 {{ $index === count($checkpoints)-1 ? 'bg-emerald-500' : 'bg-blue-500' }}"></div>
            
            <div class="bg-gray-100 p-4 rounded-2xl neu-pressed">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-sm font-black text-gray-800">{{ $checkpoint->checkpoint_type }}</span>
                    <span class="text-[10px] font-bold text-gray-500 bg-gray-200 px-2 py-1 rounded-full">{{ \Carbon\Carbon::parse($checkpoint->recorded_at)->format('H:i') }}</span>
                </div>
                <p class="text-xs font-medium text-gray-600">{{ $checkpoint->description }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Proof of Deliveries -->
    <h2 class="text-lg font-black text-gray-800 mb-6 flex items-center gap-2">
        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        Proof of Deliveries
    </h2>

    <div class="space-y-6">
        @foreach($shipment->orders as $order)
            @foreach($order->proofOfDeliveries as $pod)
            <div class="bg-gray-100 p-5 rounded-3xl neu-flat">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 tracking-widest uppercase block mb-1">Order Ref</span>
                        <span class="text-sm font-black text-gray-800">{{ $order->order_number }}</span>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-100 px-3 py-1 rounded-full flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Delivered
                    </span>
                </div>

                <div class="bg-white p-4 rounded-2xl shadow-inner mb-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 tracking-widest uppercase block mb-1">Received By</span>
                            <span class="text-sm font-black text-gray-800">{{ $pod->receiver_name }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 tracking-widest uppercase block mb-1">Time</span>
                            <span class="text-sm font-bold text-gray-700">{{ $pod->delivered_at->format('H:i, d M') }}</span>
                        </div>
                        @if($pod->notes)
                        <div class="col-span-2">
                            <span class="text-[10px] font-bold text-gray-400 tracking-widest uppercase block mb-1">Notes</span>
                            <span class="text-sm font-medium text-gray-600 italic">"{{ $pod->notes }}"</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Photos -->
                @if($pod->podPhotos->isNotEmpty())
                <div>
                    <span class="text-[10px] font-bold text-gray-400 tracking-widest uppercase block mb-2">Attached Proof</span>
                    <div class="flex gap-2 overflow-x-auto pb-2 snap-x">
                        @foreach($pod->podPhotos as $photo)
                        <div class="w-32 h-32 flex-shrink-0 rounded-xl overflow-hidden shadow-md snap-center">
                            <img src="{{ Storage::url($photo->photo_path) }}" alt="PoD Photo" class="w-full h-full object-cover">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        @endforeach
        
        @if($shipment->orders->flatMap->proofOfDeliveries->isEmpty())
            <div class="text-center p-6 bg-gray-100 neu-pressed rounded-2xl">
                <p class="text-gray-500 font-medium text-sm">No Proof of Delivery recorded for this shipment.</p>
            </div>
        @endif
    </div>

</div>
@endsection
