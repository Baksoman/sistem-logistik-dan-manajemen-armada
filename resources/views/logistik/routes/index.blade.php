@extends('layouts.logistik')

@section('title', 'Route Management')

@section('content')
    <x-topbar />

    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8">
        <p class="text-gray-500 text-lg font-medium">Manage and optimize land and sea delivery routes.</p>
        <a href="{{ route('routes.create') }}" class="flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-bold text-gray-800 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-600 shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Create New Route
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
        @forelse($routes as $route)
            @php
                $badgeClass = 'text-gray-700 bg-gray-100';
                if ($route->route_type === 'land') $badgeClass = 'text-amber-700 bg-amber-100';
                if ($route->route_type === 'sea') $badgeClass = 'text-blue-700 bg-blue-100';
                if ($route->route_type === 'auto' || $route->route_type === 'combined') $badgeClass = 'text-purple-700 bg-purple-100';
            @endphp
            <div class="p-6 rounded-[2rem] bg-gray-100 shadow-[8px_8px_16px_#d1d5db,-8px_-8px_16px_#ffffff] border border-gray-200/50 flex flex-col hover:shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] transition-all duration-300">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 tracking-widest uppercase">Route Code</span>
                        <h3 class="text-xl font-black text-gray-800">{{ $route->route_code }}</h3>
                    </div>
                    <div class="px-4 py-1.5 rounded-full text-xs font-bold shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] {{ $badgeClass }} uppercase tracking-widest">
                        {{ $route->route_type === 'auto' ? 'Combined' : $route->route_type }}
                    </div>
                </div>
                
                <div class="flex flex-col gap-4 relative mb-6">
                    <!-- Connecting Line -->
                    <div class="absolute left-[11px] top-4 bottom-4 w-0.5 bg-gray-300/50 rounded-full border border-dashed border-gray-400"></div>
                    
                    <div class="flex gap-4 items-center z-10">
                        <div class="w-6 h-6 shrink-0 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shadow-[inset_1px_1px_2px_rgba(0,0,0,0.1)]">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Origin</p>
                            <p class="text-sm font-bold text-gray-700 truncate w-48" title="{{ $route->origin_name }}">{{ $route->origin_name }}</p>
                        </div>
                    </div>

                    <div class="flex gap-4 items-center z-10">
                        <div class="w-6 h-6 shrink-0 rounded-full bg-red-100 text-red-600 flex items-center justify-center shadow-[inset_1px_1px_2px_rgba(0,0,0,0.1)]">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Destination</p>
                            <p class="text-sm font-bold text-gray-700 truncate w-48" title="{{ $route->destination_name }}">{{ $route->destination_name }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-auto">
                    <div class="flex items-center justify-between py-4 mb-4 border-t border-b border-gray-200/50">
                        <div class="flex items-center gap-2 text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            <span class="text-sm font-black">
                                @if($route->routeVersions->isNotEmpty())
                                    {{ number_format($route->routeVersions->first()->distance_km, 1) }} <span class="text-[10px] text-gray-400 font-bold uppercase">KM</span>
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                            {{ $route->routeVersions->count() }} Version(s)
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('routes.show', $route->id) }}" class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl text-sm font-bold text-blue-600 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            View Details
                        </a>
                        <form id="delete-form-{{ $route->id }}" action="{{ route('routes.destroy', $route->id) }}" method="POST" class="shrink-0 inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete('delete-form-{{ $route->id }}', 'Hapus rute beserta riwayat versinya?')" class="w-[44px] h-[44px] rounded-xl flex items-center justify-center text-red-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] hover:text-red-600 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 flex flex-col items-center justify-center text-gray-400 bg-gray-100 rounded-[2rem] shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff]">
                <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                <p class="text-lg font-bold text-gray-500">No routes found.</p>
                <p class="text-sm">Click "Create New Route" to add one.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $routes->links() }}
    </div>
@endsection
