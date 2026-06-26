@extends('layouts.logistik')

@section('title', 'Tariff Management')

@section('content')
    <x-topbar />

    <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
        <div>
            <p class="text-gray-500 mt-2">Manage pricing logic for Direct Deliveries and Master Routes</p>
        </div>
        <div class="flex flex-col sm:flex-row w-full sm:w-auto gap-3 shrink-0">
            <a href="{{ route('tariffs.export.excel') }}" class="flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-emerald-700 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db] transition-all hover:bg-emerald-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Excel
            </a>
            <a href="{{ route('tariffs.export.pdf') }}" class="flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-red-600 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db] transition-all hover:bg-red-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                PDF
            </a>
            <a href="{{ route('tariffs.create') }}" class="flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-bold text-gray-800 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add New Tariff
            </a>
        </div>
    </div>



    <x-card>
        <div class="overflow-x-auto pb-4">
            <table class="w-full text-left border-collapse min-w-max whitespace-nowrap">
                <thead>
                    <tr class="border-b border-gray-300 text-gray-500 text-sm tracking-widest uppercase">
                        <th class="py-4 px-4 font-bold">Scope</th>
                        <th class="py-4 px-4 font-bold">Base Price</th>
                        <th class="py-4 px-4 font-bold">Price / KM</th>
                        <th class="py-4 px-4 font-bold">Price / KG</th>
                        <th class="py-4 px-4 font-bold">Status</th>
                        <th class="py-4 px-4 font-bold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 font-medium">
                    @forelse($tariffs as $tariff)
                        <tr class="border-b border-gray-200/50 hover:bg-gray-200/30 transition">
                            <td class="py-4 px-4">
                                @if($tariff->route)
                                    <span class="inline-block px-3 py-1 text-xs font-bold rounded-full shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] text-purple-700 bg-purple-100 uppercase mb-2">Specific Route</span>
                                    <div class="font-bold text-gray-800 tracking-wider">{{ $tariff->route->route_code }}</div>
                                    <div class="text-xs text-gray-500">{{ $tariff->route->origin_name }} &rarr; {{ $tariff->route->destination_name }}</div>
                                @else
                                    <span class="inline-block px-3 py-1 text-xs font-bold rounded-full shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] text-blue-700 bg-blue-100 uppercase mb-2">Global Default</span>
                                    <div class="font-bold text-gray-800 tracking-wider">Direct Delivery Standard</div>
                                @endif
                                @if($tariff->vehicleType)
                                    <div class="mt-2 text-xs font-bold text-orange-600">Vehicle: {{ $tariff->vehicleType->name }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-4 font-mono font-bold text-gray-800 tracking-wider">Rp {{ number_format($tariff->fixed_price, 0, ',', '.') }}</td>
                            <td class="py-4 px-4 font-mono font-bold text-gray-800 tracking-wider">Rp {{ number_format($tariff->price_per_km, 0, ',', '.') }}</td>
                            <td class="py-4 px-4 font-mono font-bold text-gray-800 tracking-wider">Rp {{ number_format($tariff->price_per_kg, 0, ',', '.') }}</td>
                            <td class="py-4 px-4">
                                <span class="px-3 py-1 text-xs font-bold rounded-full shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] uppercase {{ $tariff->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $tariff->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('tariffs.edit', $tariff->id) }}" title="Edit" class="w-10 h-10 rounded-full flex items-center justify-center text-blue-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                    <form id="delete-form-{{ $tariff->id }}" action="{{ route('tariffs.destroy', $tariff->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('delete-form-{{ $tariff->id }}', 'Delete this tariff?')" title="Delete" class="w-10 h-10 rounded-full flex items-center justify-center text-red-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400 font-medium">No tariffs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-6">
            {{ $tariffs->links() }}
        </div>
    </x-card>
@endsection
