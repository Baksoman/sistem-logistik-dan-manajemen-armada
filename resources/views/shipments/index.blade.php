@extends('layouts.logistik')

@section('title', 'Shipment Console')

@section('content')
    <x-topbar />

    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8">
        <p class="text-gray-500 text-lg font-medium">Manage and consolidate orders into fleet shipments.</p>
        <div class="flex flex-col lg:flex-row w-full lg:w-auto gap-3 shrink-0">
            <a href="{{ route('shipments.export.excel') }}" class="flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-emerald-700 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db] transition-all hover:bg-emerald-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Excel
            </a>
            <a href="{{ route('shipments.export.pdf') }}" class="flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-red-600 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db] transition-all hover:bg-red-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                PDF
            </a>
            <a href="{{ route('shipments.create') }}" class="flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-bold text-gray-800 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create Shipment
            </a>
        </div>
    </div>



    <x-card class="mb-8">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Daftar Shipment</h3>
        <div class="overflow-x-auto pb-4">
            <table class="w-full text-left border-collapse min-w-max whitespace-nowrap">
                <thead>
                    <tr class="border-b border-gray-300 text-gray-500 text-sm tracking-widest uppercase">
                        <th class="py-4 px-4 font-bold">Shipment No.</th>
                        <th class="py-4 px-4 font-bold">Vehicle</th>
                        <th class="py-4 px-4 font-bold">Driver</th>
                        <th class="py-4 px-4 font-bold text-gray-500 uppercase tracking-widest border-b-2 border-gray-200">Orders</th>
                        <th class="py-4 px-4 font-bold text-gray-500 uppercase tracking-widest border-b-2 border-gray-200">Status</th>
                        <th class="py-4 px-4 font-bold text-gray-500 uppercase tracking-widest border-b-2 border-gray-200">SLA</th>
                        <th class="py-4 px-4 font-bold text-gray-500 uppercase tracking-widest text-center border-b-2 border-gray-200">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 font-medium">
                    @forelse($shipments as $shipment)
                        <tr class="border-b border-gray-200/50 hover:bg-gray-200/30 transition">
                            <td class="py-4 px-4 font-bold text-gray-800 tracking-wider">{{ $shipment->shipment_number }}</td>
                            <td class="py-4 px-4">
                                {{ $shipment->vehicle->plate_number ?? '-' }}
                                <div class="text-xs text-gray-500">{{ $shipment->vehicle->brand ?? '' }} {{ $shipment->vehicle->model ?? '' }}</div>
                            </td>
                            <td class="py-4 px-4">{{ $shipment->driver->user->name ?? '-' }}</td>
                            <td class="py-4 px-4">
                                <span class="font-bold text-blue-600">{{ $shipment->orders->count() }} Orders</span>
                            </td>
                            <td class="py-4 px-4">
                                @php
                                    $badgeClass = 'text-gray-700 bg-gray-100';
                                    if ($shipment->status === 'Pending') $badgeClass = 'text-amber-700 bg-amber-100';
                                    if ($shipment->status === 'On Process') $badgeClass = 'text-blue-700 bg-blue-100';
                                    if ($shipment->status === 'Delivered') $badgeClass = 'text-emerald-700 bg-emerald-100';
                                    if ($shipment->status === 'Failed') $badgeClass = 'text-red-700 bg-red-100';
                                @endphp
                                <span class="px-3 py-1 text-xs font-bold rounded-full shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] {{ $badgeClass }} uppercase">
                                    {{ $shipment->status }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                @php
                                    $slaClass = 'text-gray-700 bg-gray-100';
                                    if ($shipment->sla_status === 'On Time') $slaClass = 'text-emerald-700 bg-emerald-100';
                                    if ($shipment->sla_status === 'Late' || $shipment->sla_status === 'Late (Ongoing)') $slaClass = 'text-red-700 bg-red-100';
                                    if ($shipment->sla_status === 'At Risk') $slaClass = 'text-orange-700 bg-orange-100';
                                    if ($shipment->sla_status === 'On Track') $slaClass = 'text-blue-700 bg-blue-100';
                                @endphp
                                <span class="px-3 py-1 text-xs font-bold rounded-full shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] {{ $slaClass }} uppercase">
                                    {{ $shipment->sla_status }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('shipments.show', $shipment->id) }}" class="w-10 h-10 rounded-full flex items-center justify-center text-blue-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">Belum ada shipment.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $shipments->links() }}
        </div>
    </x-card>
@endsection
