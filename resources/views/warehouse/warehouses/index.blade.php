@extends('layouts.app')

@section('title', 'Warehouse Management')

@section('content')
    <x-topbar />

    <div x-data="{ 
            slideOverOpen: {{ $errors->any() && !old('warehouse_id') ? 'true' : 'false' }}, 
            editSlideOverOpen: {{ $errors->any() && old('warehouse_id') ? 'true' : 'false' }}, 
            editData: { 
                id: '{{ old('warehouse_id') }}', 
                code: '{{ old('code') }}', 
                name: '{{ old('name') }}', 
                address: '{{ old('address') }}', 
                latitude: '{{ old('latitude') }}', 
                longitude: '{{ old('longitude') }}', 
                is_active: '{{ old('is_active', 1) }}' 
            } 
         }" 
         @open-edit.window="editData = $event.detail; editSlideOverOpen = true;"
         @keydown.escape.window="slideOverOpen = false; editSlideOverOpen = false">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <p class="text-gray-500 text-lg font-medium">Manage warehouse locations and details.</p>
            <button @click="slideOverOpen = true" class="flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-bold text-gray-800 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Warehouse
            </button>
        </div>

        <x-card class="mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Warehouses</h3>
            <div class="overflow-x-auto pb-4">
            <table class="w-full text-left border-collapse min-w-max whitespace-nowrap">
                <thead>
                    <tr class="border-b border-gray-300 text-gray-500 text-sm tracking-widest uppercase">
                        <th class="py-4 px-4 font-bold">Code</th>
                        <th class="py-4 px-4 font-bold">Name</th>
                        <th class="py-4 px-4 font-bold">Address</th>
                        <th class="py-4 px-4 font-bold">Status</th>
                        <th class="py-4 px-4 font-bold text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 font-medium">
                    @forelse($warehouses as $warehouse)
                        <tr class="border-b border-gray-200/50 hover:bg-gray-200/30 transition">
                            <td class="py-4 px-4 font-bold text-gray-800">{{ $warehouse->code }}</td>
                            <td class="py-4 px-4 font-bold">{{ $warehouse->name }}</td>
                            <td class="py-4 px-4 whitespace-normal min-w-[200px]">{{ $warehouse->address }}</td>
                            <td class="py-4 px-4">
                                <span class="px-3 py-1 text-xs font-bold rounded-full shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] {{ $warehouse->is_active ? 'text-emerald-600' : 'text-red-500' }}">
                                    {{ $warehouse->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center justify-center gap-3">
                                    <button type="button" @click="$dispatch('open-edit', { id: '{{ $warehouse->id }}', code: '{{ $warehouse->code }}', name: '{{ $warehouse->name }}', address: '{{ str_replace(["\r", "\n"], ["", " "], $warehouse->address) }}', latitude: '{{ $warehouse->latitude }}', longitude: '{{ $warehouse->longitude }}', is_active: '{{ $warehouse->is_active }}' })" class="w-10 h-10 rounded-full flex items-center justify-center text-blue-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form id="delete-form-{{ $warehouse->id }}" action="{{ route('warehouse.warehouses.destroy', $warehouse->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('delete-form-{{ $warehouse->id }}')" class="w-10 h-10 rounded-full flex items-center justify-center text-red-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] hover:text-red-600 transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">No warehouses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            <div class="mt-4">
                {{ $warehouses->links() }}
            </div>
        </x-card>

        <!-- Create Form Slide-Over -->
        <x-slide-over title="Create New Warehouse">
            <form action="{{ route('warehouse.warehouses.store') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Code</label>
                    <x-input type="text" name="code" placeholder="WH-01" required />
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Name</label>
                    <x-input type="text" name="name" placeholder="Warehouse Jakarta" required />
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Address</label>
                    <textarea name="address" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" rows="3"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Latitude</label>
                        <x-input type="text" name="latitude" placeholder="-6.200000" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Longitude</label>
                        <x-input type="text" name="longitude" placeholder="106.816666" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                    <x-select name="is_active" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </x-select>
                </div>
                
                <div class="pt-6 mt-6 border-t border-gray-300">
                    <button type="submit" class="w-full py-4 rounded-2xl font-bold text-gray-100 bg-gray-800 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#4b5563,inset_-2px_-2px_4px_#1f2937] transition-all uppercase tracking-widest">
                        Save Warehouse
                    </button>
                </div>
            </form>
        </x-slide-over>

        <!-- Edit Form Slide-Over -->
        <x-slide-over title="Edit Warehouse" model="editSlideOverOpen">
            <form :action="'{{ route('warehouse.warehouses.index') }}/' + editData.id" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="warehouse_id" x-model="editData.id">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Code</label>
                    <input type="text" name="code" x-model="editData.code" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" />
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" x-model="editData.name" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" />
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Address</label>
                    <textarea name="address" x-model="editData.address" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" rows="3"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Latitude</label>
                        <input type="text" name="latitude" x-model="editData.latitude" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Longitude</label>
                        <input type="text" name="longitude" x-model="editData.longitude" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                    <select name="is_active" x-model="editData.is_active" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none appearance-none">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                
                <div class="pt-6 mt-6 border-t border-gray-300">
                    <button type="submit" class="w-full py-4 rounded-2xl font-bold text-gray-100 bg-gray-800 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#4b5563,inset_-2px_-2px_4px_#1f2937] transition-all uppercase tracking-widest">
                        Update Warehouse
                    </button>
                </div>
            </form>
        </x-slide-over>

    </div>
@endsection
