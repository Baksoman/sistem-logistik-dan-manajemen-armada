@extends('layouts.warehouse')

@section('title', 'Inventory Management')

@section('content')
    <x-topbar />

    <div x-data="dataTable({
            endpoint: '/api/search/inventory',
            initialData: {{ Js::from($initialData['data'] ?? []) }},
            initialMeta: {{ Js::from($initialData['meta'] ?? []) }}
        })" class="w-full">
        
        <div x-data="{ 
                slideOverOpen: {{ $errors->any() && !old('inventory_id') ? 'true' : 'false' }}, 
                editSlideOverOpen: {{ $errors->any() && old('inventory_id') ? 'true' : 'false' }}, 
                editData: { 
                    id: '{{ old('inventory_id') }}', 
                    warehouse_id: '{{ old('warehouse_id') }}', 
                    category_id: '{{ old('category_id') }}', 
                    unit_type_id: '{{ old('unit_type_id') }}', 
                    sku: '{{ old('sku') }}', 
                    upc: '{{ old('upc') }}', 
                    brand: '{{ old('brand') }}',
                    name: '{{ old('name') }}', 
                    quantity: '{{ old('quantity') }}', 
                    min_quantity: '{{ old('min_quantity') }}', 
                    weight_kg: '{{ old('weight_kg') }}', 
                    volume_cbm: '{{ old('volume_cbm') }}', 
                    zone_id: '{{ old('zone_id') }}', 
                    rack_id: '{{ old('rack_id') }}' 
                },
                scannerActive: false,
                scannerTarget: 'create',
                startScanner(target) {
                    this.scannerTarget = target;
                    this.scannerActive = true;
                    this.$nextTick(() => {
                        const scanner = new Html5Qrcode('barcode-reader');
                        window.__barcodeScanner = scanner;
                        scanner.start(
                            { facingMode: 'environment' },
                            { fps: 10, qrbox: { width: 250, height: 150 } },
                            (decodedText) => {
                                scanner.stop().then(() => {
                                    this.scannerActive = false;
                                    if (target === 'create') {
                                        document.querySelector('[name=upc]').value = decodedText;
                                        document.querySelector('[name=upc]').dispatchEvent(new Event('input'));
                                    } else {
                                        this.editData.upc = decodedText;
                                    }
                                    Toastify({ text: 'Barcode detected: ' + decodedText, duration: 3000, gravity: 'top', position: 'right', style: { background: '#10b981', borderRadius: '12px', fontWeight: 'bold' } }).showToast();
                                });
                            },
                            (err) => {}
                        ).catch((err) => {
                            this.scannerActive = false;
                            Swal.fire('Camera Error', 'Unable to access camera. Please ensure camera permissions are granted and you are using HTTPS or localhost.', 'error');
                        });
                    });
                },
                stopScanner() {
                    if (window.__barcodeScanner) {
                        window.__barcodeScanner.stop().then(() => { this.scannerActive = false; }).catch(() => { this.scannerActive = false; });
                    } else {
                        this.scannerActive = false;
                    }
                }
             }" 
             @open-edit.window="editData = $event.detail; editSlideOverOpen = true;"
             @keydown.escape.window="slideOverOpen = false; editSlideOverOpen = false; stopScanner();">
            
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8">
                <p class="text-gray-500 text-lg font-medium">Manage stock items, quantities, and their storage locations.</p>
                <div class="flex flex-col lg:flex-row w-full lg:w-auto gap-3 shrink-0">
                    <a href="{{ route('warehouse.inventory.export.excel') }}" class="flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-emerald-700 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db] transition-all hover:bg-emerald-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Excel
                    </a>
                    <a href="{{ route('warehouse.inventory.export.pdf') }}" class="flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-red-600 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db] transition-all hover:bg-red-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                    <button @click="slideOverOpen = true" class="flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-bold text-gray-800 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Stock Item
                    </button>
                </div>
            </div>

            <x-search-filter-bar placeholder="Search inventory by SKU, name, or warehouse..." />

            <x-filter-modal title="Filter Inventory">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Warehouse</label>
                    <select x-model="filters.warehouse_id" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Category</label>
                    <select x-model="filters.category_id" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Low Stock Only</label>
                    <select x-model="filters.is_low_stock" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
                        <option value="">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
            </x-filter-modal>

            <x-card class="mb-8 relative min-h-[400px]">
                <div x-show="loading" class="absolute inset-0 z-10 flex items-center justify-center bg-gray-100/80 backdrop-blur-sm rounded-[2rem]">
                    <div class="w-12 h-12 rounded-full border-4 border-gray-300 border-t-blue-500 animate-spin shadow-[0_0_15px_rgba(59,130,246,0.5)]"></div>
                </div>

                <h3 class="text-xl font-bold text-gray-800 mb-6">Inventory List</h3>
                <div class="overflow-x-auto pb-4">
                <table class="w-full text-left border-collapse min-w-max whitespace-nowrap">
                    <thead>
                        <tr class="border-b border-gray-300 text-gray-500 text-sm tracking-widest uppercase">
                            <th class="py-4 px-4 font-bold">SKU</th>
                            <th class="py-4 px-4 font-bold">UPC</th>
                            <th class="py-4 px-4 font-bold">Brand</th>
                            <th class="py-4 px-4 font-bold">Name</th>
                            <th class="py-4 px-4 font-bold">Warehouse</th>
                            <th class="py-4 px-4 font-bold">Category</th>
                            <th class="py-4 px-4 font-bold">Qty (Fisik|Alokasi|Tersedia)</th>
                            <th class="py-4 px-4 font-bold">Location</th>
                            <th class="py-4 px-4 font-bold text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 font-medium">
                        <template x-for="item in data" :key="item.id">
                            <tr class="border-b border-gray-200/50 hover:bg-gray-200/30 transition">
                                <td class="py-4 px-4 font-bold text-gray-800 tracking-wider" x-text="item.sku"></td>
                                <td class="py-4 px-4 text-sm text-gray-500 font-mono" x-text="item.upc || '-'"></td>
                                <td class="py-4 px-4" x-text="item.brand || '-'"></td>
                                <td class="py-4 px-4 font-bold" x-text="item.name"></td>
                                <td class="py-4 px-4" x-text="item.warehouse?.name || '-'"></td>
                                <td class="py-4 px-4" x-text="item.category?.name || '-'"></td>
                                <td class="py-4 px-4">
                                    <div class="flex flex-col text-sm">
                                        <span class="text-gray-500" x-text="`Fisik: ${new Intl.NumberFormat().format(item.quantity)} ${item.unit_type?.name || ''}`"></span>
                                        <span class="font-bold" :class="item.is_low_stock ? 'text-red-600' : 'text-emerald-600'" x-text="`Tersedia: ${new Intl.NumberFormat().format(item.quantity)}`"></span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-sm text-gray-500" x-text="`Zone: ${item.zone?.name || '-'} | Rack: ${item.rack?.name || '-'}`"></td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center justify-center gap-3">
                                        <button type="button" @click="$dispatch('open-edit', { id: item.id, warehouse_id: item.warehouse?.id, category_id: item.category?.id, unit_type_id: item.unit_type?.id, sku: item.sku, upc: item.upc, brand: item.brand, name: item.name, quantity: item.quantity, min_quantity: item.min_quantity, weight_kg: item.weight_kg, volume_cbm: item.volume_cbm, zone_id: item.zone?.id, rack_id: item.rack?.id })" class="w-10 h-10 rounded-full flex items-center justify-center text-blue-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form :id="'delete-form-' + item.id" :action="'/warehouse-panel/inventory/' + item.id" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" @click="confirmDelete('delete-form-' + item.id)" class="w-10 h-10 rounded-full flex items-center justify-center text-red-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] hover:text-red-600 transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="data.length === 0" x-cloak>
                            <td colspan="9" class="py-8 text-center text-gray-400">No stock items found.</td>
                        </tr>
                    </tbody>
                </table>
                </div>
                <x-pagination />
            </x-card>

        <!-- Barcode Scanner Modal -->
        <template x-if="scannerActive">
            <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/70 backdrop-blur-sm" @click.self="stopScanner()">
                <div class="bg-gray-100 rounded-3xl shadow-2xl p-6 w-full max-w-md mx-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">📷 Scan Barcode / QR Code</h3>
                        <button @click="stopScanner()" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:text-red-500 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div id="barcode-reader" class="rounded-2xl overflow-hidden shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff]"></div>
                    <p class="text-sm text-gray-500 mt-4 text-center font-medium">Point your camera at a barcode or QR code.</p>
                </div>
            </div>
        </template>

        <!-- Create Form Slide-Over -->
        <x-slide-over title="Create New Stock Item">
            <form action="{{ route('warehouse.inventory.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- UPC with Scanner -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">UPC <span class="text-gray-400 font-normal">(Universal Product Code)</span></label>
                    <div class="flex gap-2">
                        <x-input type="text" name="upc" placeholder="Scan or type UPC" class="flex-1" />
                        <button type="button" @click="startScanner('create')" class="shrink-0 w-14 h-14 rounded-2xl flex items-center justify-center text-indigo-600 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Brand</label>
                        <x-input type="text" name="brand" placeholder="e.g. Samsung" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Item Name</label>
                        <x-input type="text" name="name" required />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Warehouse</label>
                    <x-select name="warehouse_id" required class="select2-enable" id="create_warehouse">
                        <option value="">Select Warehouse</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </x-select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Category</label>
                        <select name="category_id" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none appearance-none select2-enable">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Unit Type</label>
                        <select name="unit_type_id" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none appearance-none select2-enable">
                            <option value="">Select Unit</option>
                            @foreach($unitTypes as $unitType)
                                <option value="{{ $unitType->id }}">{{ $unitType->name }}</option>
                            @endforeach
                        </select>
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
                        <select name="zone_id" id="create_zone_id" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none appearance-none select2-enable">
                            <option value="">Select Zone</option>
                            @foreach($zones as $z)
                                <option value="{{ $z->id }}" data-warehouse="{{ $z->warehouse_id }}">{{ $z->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Rack Location</label>
                        <select name="rack_id" id="create_rack_id" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none appearance-none select2-enable">
                            <option value="">Select Rack</option>
                            @foreach($racks as $r)
                                <option value="{{ $r->id }}" data-zone="{{ $r->zone_id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
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

                <!-- UPC with Scanner -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">UPC <span class="text-gray-400 font-normal">(Universal Product Code)</span></label>
                    <div class="flex gap-2">
                        <input type="text" name="upc" x-model="editData.upc" placeholder="Scan or type UPC" class="flex-1 w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" />
                        <button type="button" @click="startScanner('edit')" class="shrink-0 w-14 h-14 rounded-2xl flex items-center justify-center text-indigo-600 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        </button>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Brand</label>
                        <input type="text" name="brand" x-model="editData.brand" placeholder="e.g. Samsung" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" />
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
                        <select name="zone_id" x-model="editData.zone_id" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none appearance-none">
                            <option value="">Select Zone</option>
                            @foreach($zones as $z)
                                <option value="{{ $z->id }}">{{ $z->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Rack Location</label>
                        <select name="rack_id" x-model="editData.rack_id" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none appearance-none">
                            <option value="">Select Rack</option>
                            @foreach($racks as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
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

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2-enable').each(function() {
            $(this).select2({
                width: '100%'
            });
        });

        // Filter zones by warehouse
        $('#create_warehouse').on('change', function() {
            let warehouseId = $(this).val();
            $('#create_zone_id option').each(function() {
                if ($(this).val() == '') return;
                if ($(this).data('warehouse') == warehouseId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            $('#create_zone_id').val('').trigger('change');
        });

        // Filter racks by zone
        $('#create_zone_id').on('change', function() {
            let zoneId = $(this).val();
            $('#create_rack_id option').each(function() {
                if ($(this).val() == '') return;
                if ($(this).data('zone') == zoneId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            $('#create_rack_id').val('').trigger('change');
        });
    });
</script>
@endpush
