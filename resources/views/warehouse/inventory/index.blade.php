@extends('layouts.app')

@section('title', 'Inventory Management')

@section('content')
    <x-topbar />

    <div x-data="{ 
            slideOverOpen: {{ $errors->any() && !old('inventory_id') ? 'true' : 'false' }}, 
            editSlideOverOpen: {{ $errors->any() && old('inventory_id') ? 'true' : 'false' }}, 
            editData: { 
                id: '{{ old('inventory_id') }}', 
                warehouse_id: '{{ old('warehouse_id') }}', 
                category_id: '{{ old('category_id') }}', 
                unit_type_id: '{{ old('unit_type_id') }}', 
                sku: '{{ old('sku') }}', 
                name: '{{ old('name') }}', 
                quantity: '{{ old('quantity') }}', 
                min_quantity: '{{ old('min_quantity') }}', 
                weight_kg: '{{ old('weight_kg') }}', 
                volume_cbm: '{{ old('volume_cbm') }}', 
                zone: '{{ old('zone') }}', 
                bin_location: '{{ old('bin_location') }}' 
            } 
         }" 
         @open-edit.window="editData = $event.detail; editSlideOverOpen = true;"
         @keydown.escape.window="slideOverOpen = false; editSlideOverOpen = false">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <p class="text-gray-500 text-lg font-medium">Manage stock items, quantities, and their storage locations.</p>
            <button @click="slideOverOpen = true" class="flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-bold text-gray-800 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Stock Item
            </button>
        </div>

        <x-card class="mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Inventory List</h3>
            <div class="overflow-x-auto pb-4">
            <table class="w-full text-left border-collapse min-w-max whitespace-nowrap">
                <thead>
                    <tr class="border-b border-gray-300 text-gray-500 text-sm tracking-widest uppercase">
                        <th class="py-4 px-4 font-bold">SKU</th>
                        <th class="py-4 px-4 font-bold">Name</th>
                        <th class="py-4 px-4 font-bold">Warehouse</th>
                        <th class="py-4 px-4 font-bold">Category</th>
                        <th class="py-4 px-4 font-bold">Quantity</th>
                        <th class="py-4 px-4 font-bold">Location</th>
                        <th class="py-4 px-4 font-bold text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 font-medium">
                    @forelse($inventory as $item)
                        <tr class="border-b border-gray-200/50 hover:bg-gray-200/30 transition">
                            <td class="py-4 px-4 font-bold text-gray-800 tracking-wider">{{ $item->sku }}</td>
                            <td class="py-4 px-4 font-bold">{{ $item->name }}</td>
                            <td class="py-4 px-4">{{ $item->warehouse->name ?? '-' }}</td>
                            <td class="py-4 px-4">{{ $item->category->name ?? '-' }}</td>
                            <td class="py-4 px-4">
                                <span class="font-bold {{ $item->quantity <= $item->min_quantity ? 'text-red-600' : 'text-emerald-600' }}">
                                    {{ number_format($item->quantity, 0) }} {{ $item->unitType->name ?? '' }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-500">
                                Zone: {{ $item->zone ?? '-' }} | Bin: {{ $item->bin_location ?? '-' }}
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center justify-center gap-3">
                                    <button type="button" @click="$dispatch('open-edit', { id: '{{ $item->id }}', warehouse_id: '{{ $item->warehouse_id }}', category_id: '{{ $item->category_id }}', unit_type_id: '{{ $item->unit_type_id }}', sku: '{{ $item->sku }}', name: '{{ $item->name }}', quantity: '{{ $item->quantity }}', min_quantity: '{{ $item->min_quantity }}', weight_kg: '{{ $item->weight_kg }}', volume_cbm: '{{ $item->volume_cbm }}', zone: '{{ $item->zone }}', bin_location: '{{ $item->bin_location }}' })" class="w-10 h-10 rounded-full flex items-center justify-center text-blue-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form id="delete-form-{{ $item->id }}" action="{{ route('warehouse.inventory.destroy', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('delete-form-{{ $item->id }}')" class="w-10 h-10 rounded-full flex items-center justify-center text-red-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] hover:text-red-600 transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400">No stock items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            <div class="mt-4">
                {{ $inventory->links() }}
            </div>
        </x-card>

        <!-- Create Form Slide-Over -->
        <x-slide-over title="Create New Stock Item">
            <form action="{{ route('warehouse.inventory.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">SKU</label>
                        <x-input type="text" name="sku" required />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Item Name</label>
                        <x-input type="text" name="name" required />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Warehouse</label>
                    <x-select name="warehouse_id" required>
                        <option value="">Select Warehouse</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </x-select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Category</label>
                        <x-select name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </x-select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Unit Type</label>
                        <x-select name="unit_type_id" required>
                            <option value="">Select Unit</option>
                            @foreach($unitTypes as $unitType)
                                <option value="{{ $unitType->id }}">{{ $unitType->name }}</option>
                            @endforeach
                        </x-select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Quantity</label>
                        <x-input type="number" step="0.01" name="quantity" required />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Min. Quantity</label>
                        <x-input type="number" step="0.01" name="min_quantity" required />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Weight (KG)</label>
                        <x-input type="number" step="0.01" name="weight_kg" required />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Volume (CBM)</label>
                        <x-input type="number" step="0.01" name="volume_cbm" required />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Zone</label>
                        <x-input type="text" name="zone" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Bin Location</label>
                        <x-input type="text" name="bin_location" />
                    </div>
                </div>
                
                <div class="pt-6 mt-6 border-t border-gray-300">
                    <button type="submit" class="w-full py-4 rounded-2xl font-bold text-gray-100 bg-gray-800 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#4b5563,inset_-2px_-2px_4px_#1f2937] transition-all uppercase tracking-widest">
                        Save Stock Item
                    </button>
                </div>
            </form>
        </x-slide-over>

        <!-- Edit Form Slide-Over -->
        <x-slide-over title="Edit Stock Item" model="editSlideOverOpen">
            <form :action="'{{ route('warehouse.inventory.index') }}/' + editData.id" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="inventory_id" x-model="editData.id">
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">SKU</label>
                        <input type="text" name="sku" x-model="editData.sku" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Item Name</label>
                        <input type="text" name="name" x-model="editData.name" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Warehouse</label>
                    <select name="warehouse_id" x-model="editData.warehouse_id" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none appearance-none">
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Category</label>
                        <select name="category_id" x-model="editData.category_id" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none appearance-none">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Unit Type</label>
                        <select name="unit_type_id" x-model="editData.unit_type_id" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none appearance-none">
                            @foreach($unitTypes as $unitType)
                                <option value="{{ $unitType->id }}">{{ $unitType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Quantity</label>
                        <input type="number" step="0.01" name="quantity" x-model="editData.quantity" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Min. Quantity</label>
                        <input type="number" step="0.01" name="min_quantity" x-model="editData.min_quantity" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Weight (KG)</label>
                        <input type="number" step="0.01" name="weight_kg" x-model="editData.weight_kg" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Volume (CBM)</label>
                        <input type="number" step="0.01" name="volume_cbm" x-model="editData.volume_cbm" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Zone</label>
                        <input type="text" name="zone" x-model="editData.zone" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Bin Location</label>
                        <input type="text" name="bin_location" x-model="editData.bin_location" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" />
                    </div>
                </div>

                <div class="pt-6 mt-6 border-t border-gray-300">
                    <button type="submit" class="w-full py-4 rounded-2xl font-bold text-gray-100 bg-gray-800 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#4b5563,inset_-2px_-2px_4px_#1f2937] transition-all uppercase tracking-widest">
                        Update Stock Item
                    </button>
                </div>
            </form>
        </x-slide-over>

    </div>
@endsection
