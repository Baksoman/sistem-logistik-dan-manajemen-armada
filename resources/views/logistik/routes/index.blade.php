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

    <x-card class="mb-8">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Saved Routes</h3>
        <div class="overflow-x-auto pb-4">
        <table class="w-full text-left border-collapse min-w-max whitespace-nowrap">
            <thead>
                <tr class="border-b border-gray-300 text-gray-500 text-sm tracking-widest uppercase">
                    <th class="py-4 px-4 font-bold">Route Code</th>
                    <th class="py-4 px-4 font-bold">Type</th>
                    <th class="py-4 px-4 font-bold">Origin</th>
                    <th class="py-4 px-4 font-bold">Destination</th>
                    <th class="py-4 px-4 font-bold">Latest Distance</th>
                    <th class="py-4 px-4 font-bold text-center">Action</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 font-medium">
                @forelse($routes as $route)
                    <tr class="border-b border-gray-200/50 hover:bg-gray-200/30 transition">
                        <td class="py-4 px-4 font-bold text-gray-800 tracking-wider">{{ $route->route_code }}</td>
                        <td class="py-4 px-4">
                            @php
                                $badgeClass = 'text-gray-700 bg-gray-100';
                                if ($route->route_type === 'land') $badgeClass = 'text-amber-700 bg-amber-100';
                                if ($route->route_type === 'sea') $badgeClass = 'text-blue-700 bg-blue-100';
                                if ($route->route_type === 'auto' || $route->route_type === 'combined') $badgeClass = 'text-purple-700 bg-purple-100';
                            @endphp
                            <span class="px-3 py-1 text-xs font-bold rounded-full shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] {{ $badgeClass }} uppercase">
                                {{ $route->route_type === 'auto' ? 'Combined' : $route->route_type }}
                            </span>
                        </td>
                        <td class="py-4 px-4">{{ $route->origin_name }}</td>
                        <td class="py-4 px-4">{{ $route->destination_name }}</td>
                        <td class="py-4 px-4">
                            @if($route->routeVersions->isNotEmpty())
                                {{ number_format($route->routeVersions->first()->distance_km, 2) }} KM
                            @else
                                -
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center justify-center gap-3">
                                <a href="{{ route('routes.show', $route->id) }}" class="w-10 h-10 rounded-full flex items-center justify-center text-blue-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <form id="delete-form-{{ $route->id }}" action="{{ route('routes.destroy', $route->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('delete-form-{{ $route->id }}', 'Hapus rute beserta riwayat versinya?')" class="w-10 h-10 rounded-full flex items-center justify-center text-red-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] hover:text-red-600 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-400">No routes found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        <div class="mt-4">
            {{ $routes->links() }}
        </div>
    </x-card>
@endsection
