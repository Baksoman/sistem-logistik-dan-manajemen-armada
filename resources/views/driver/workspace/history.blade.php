@extends('layouts.driver-pwa')

@section('title', 'Shipment History')

@section('content')
<div class="pb-10">

    @if($completedShipments->isEmpty())
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center mt-20 text-center">
            <div class="w-32 h-32 rounded-full neu-flat flex items-center justify-center mb-6">
                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h2 class="text-xl font-bold text-gray-700">No History Yet</h2>
            <p class="text-gray-500 text-sm mt-2">You haven't completed any shipments.</p>
        </div>
    @else
        <!-- List of Past Shipments -->
        <div class="space-y-6">
            @foreach($completedShipments as $completedShipment)
            <a href="{{ route('driver.workspace.history.show', $completedShipment->id) }}" class="block neu-flat rounded-3xl p-5 relative overflow-hidden bg-gray-100 active:scale-95 transition-transform duration-200">
                <!-- Status Badge -->
                <div class="absolute top-0 right-0 {{ in_array($completedShipment->status, ['Delivered', 'Completed']) ? 'bg-emerald-500' : 'bg-gray-500' }} text-white text-[10px] font-black px-4 py-1 rounded-bl-xl uppercase tracking-widest shadow-md">
                    {{ $completedShipment->status }}
                </div>
                
                <div class="mb-4">
                    <p class="text-[10px] font-bold text-gray-400 tracking-widest uppercase mb-1">Shipment Ref.</p>
                    <h3 class="text-lg font-black text-gray-800 leading-tight">{{ $completedShipment->shipment_number }}</h3>
                    <p class="text-xs font-bold text-blue-500 mt-1">{{ $completedShipment->routeVersion->route->name ?? 'Ad-hoc Route' }}</p>
                </div>
                
                <div class="flex items-center gap-4 text-xs font-medium text-gray-600">
                    <div class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>{{ \Carbon\Carbon::parse($completedShipment->completed_at ?? $completedShipment->updated_at)->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ \Carbon\Carbon::parse($completedShipment->completed_at ?? $completedShipment->updated_at)->format('H:i') }}</span>
                    </div>
                </div>

                <!-- Info Banner -->
                <div class="mt-4 pt-4 border-t-2 border-gray-200 flex justify-between items-center text-xs">
                    <div class="flex flex-col">
                        <span class="font-bold text-gray-400 uppercase tracking-widest text-[9px]">Vehicle</span>
                        <span class="font-black text-gray-800">{{ $completedShipment->vehicle->plate_number ?? '-' }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
