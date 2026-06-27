@extends('layouts.warehouse')

@section('title', 'Inbound (Putaway)')

@section('content')
    <x-topbar />

    <div x-data="Object.assign({}, dataTable({
            endpoint: '/api/search/inventory-movements?force_type=inbound',
            initialData: {{ Js::from($initialData['data'] ?? []) }},
            initialMeta: {{ Js::from($initialData['meta'] ?? []) }}
        }), { 
            slideOverOpen: false,
            selectedWarehouse: '',
            filteredItems: @js($stockItems->groupBy('warehouse_id')->map(fn($items) => $items->map(fn($item) => ['id' => $item->id, 'name' => $item->sku . ' - ' . $item->name, 'upc' => $item->upc ?? '', 'unit' => $item->unitType->name ?? '']))->toArray()),
            allItems: @js($stockItems->map(fn($item) => ['id' => $item->id, 'upc' => $item->upc ?? '', 'sku' => $item->sku, 'name' => $item->name, 'warehouse_id' => $item->warehouse_id])->toArray()),
            scannerActive: false,
            startScanner() {
                this.scannerActive = true;
                this.$nextTick(() => {
                    const scanner = new Html5Qrcode('inbound-barcode-reader');
                    window.__inboundScanner = scanner;
                    scanner.start(
                        { facingMode: 'environment' },
                        { fps: 10, qrbox: { width: 250, height: 150 } },
                        (decodedText) => {
                            scanner.stop().then(() => {
                                this.scannerActive = false;
                                this.handleScannedCode(decodedText);
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
                if (window.__inboundScanner) {
                    window.__inboundScanner.stop().then(() => { this.scannerActive = false; }).catch(() => { this.scannerActive = false; });
                } else {
                    this.scannerActive = false;
                }
            },
            handleScannedCode(code) {
                const found = this.allItems.find(i => i.upc === code);
                if (found) {
                    this.selectedWarehouse = found.warehouse_id;
                    this.$nextTick(() => {
                        const selectEl = document.querySelector('select[name=stock_item_id]');
                        if (selectEl) { selectEl.value = found.id; selectEl.dispatchEvent(new Event('change')); }
                    });
                    Toastify({ text: 'Item found: ' + found.sku + ' - ' + found.name, duration: 3000, gravity: 'top', position: 'right', style: { background: '#10b981', borderRadius: '12px', fontWeight: 'bold' } }).showToast();
                    this.slideOverOpen = true;
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Item Not Registered',
                        html: '<b>Code:</b> ' + code + '<br><br>Item\'s code is not registered in stock inventory.<br>Please register it in <b>Stock Inventory</b> first.',
                        confirmButtonText: 'Go to Stock Inventory',
                        showCancelButton: true,
                        cancelButtonText: 'Close',
                        confirmButtonColor: '#4f46e5'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route('warehouse.inventory.index') }}';
                        }
                    });
                }
            }
         })" 
         @keydown.escape.window="slideOverOpen = false; stopScanner();" class="w-full">
        
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8">
            <div>
                <p class="text-gray-500 text-lg font-medium">Penerimaan barang masuk (Putaway) ke gudang.</p>
            </div>
            <div class="flex flex-col lg:flex-row w-full lg:w-auto gap-3">
                <a href="{{ route('warehouse.inbound.export.excel') }}" class="flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-emerald-700 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db] transition-all hover:bg-emerald-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Excel
                </a>
                <a href="{{ route('warehouse.inbound.export.pdf') }}" class="flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-red-600 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db] transition-all hover:bg-red-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    PDF
                </a>
                <button @click="startScanner()" class="flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-indigo-700 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-indigo-500 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    Scan Item
                </button>
                <button @click="slideOverOpen = true" class="flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-bold text-gray-800 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-emerald-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    Record Inbound
                </button>
            </div>
        </div>

        <x-search-filter-bar placeholder="Search by reference, SKU, or item name..." />

        <x-filter-modal title="Filter Inbound Records">
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
                <label class="block text-sm font-bold text-gray-700 mb-2">Date From</label>
                <input type="date" x-model="filters.date_from" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Date To</label>
                <input type="date" x-model="filters.date_to" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
            </div>
        </x-filter-modal>

        <x-card class="mb-8 relative min-h-[400px]">
            <div x-show="loading" class="absolute inset-0 z-10 flex items-center justify-center bg-gray-100/80 backdrop-blur-sm rounded-[2rem]">
                <div class="w-12 h-12 rounded-full border-4 border-gray-300 border-t-blue-500 animate-spin shadow-[0_0_15px_rgba(59,130,246,0.5)]"></div>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mb-6">Inbound History</h3>
            <div class="overflow-x-auto pb-4">
            <table class="w-full text-left border-collapse min-w-max whitespace-nowrap">
                <thead>
                    <tr class="border-b border-gray-300 text-gray-500 text-sm tracking-widest uppercase">
                        <th class="py-4 px-4 font-bold">Date</th>
                        <th class="py-4 px-4 font-bold">Reference</th>
                        <th class="py-4 px-4 font-bold">Item</th>
                        <th class="py-4 px-4 font-bold">Warehouse</th>
                        <th class="py-4 px-4 font-bold">Qty</th>
                        <th class="py-4 px-4 font-bold">By</th>
                        <th class="py-4 px-4 font-bold">Notes</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 font-medium">
                    <template x-for="movement in data" :key="movement.id">
                        <tr class="border-b border-gray-200/50 hover:bg-gray-200/30 transition">
                            <td class="py-4 px-4 text-sm text-gray-500" x-text="new Date(movement.created_at).toLocaleString('en-GB', {day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit'})"></td>
                            <td class="py-4 px-4 font-bold text-gray-800 tracking-wider" x-text="movement.reference_number || '-'"></td>
                            <td class="py-4 px-4">
                                <div>
                                    <span class="font-bold" x-text="movement.stock_item?.name || '-'"></span>
                                    <span class="block text-xs text-gray-400" x-text="movement.stock_item?.sku || ''"></span>
                                </div>
                            </td>
                            <td class="py-4 px-4" x-text="movement.stock_item?.warehouse?.name || '-'"></td>
                            <td class="py-4 px-4">
                                <span class="px-3 py-1 text-xs font-bold rounded-full shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] text-emerald-700 bg-emerald-50" x-text="`+${new Intl.NumberFormat().format(movement.quantity)}`">
                                </span>
                            </td>
                            <td class="py-4 px-4 text-sm" x-text="movement.creator?.name || '-'"></td>
                            <td class="py-4 px-4 text-sm text-gray-500 whitespace-normal max-w-[200px]" x-text="movement.notes || '-'"></td>
                        </tr>
                    </template>
                    <tr x-show="data.length === 0" x-cloak>
                        <td colspan="7" class="py-8 text-center text-gray-400">No inbound records found.</td>
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
                        <h3 class="text-lg font-bold text-gray-800">📷 Scan Item Barcode</h3>
                        <button @click="stopScanner()" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:text-red-500 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div id="inbound-barcode-reader" class="rounded-2xl overflow-hidden shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff]"></div>
                    <p class="text-sm text-gray-500 mt-4 text-center font-medium">Scan the item's barcode to auto-fill the form.</p>
                </div>
            </div>
        </template>

        <!-- Record Inbound Slide-Over -->
        <x-slide-over title="Record Inbound (Putaway)">
            <form action="{{ route('warehouse.inbound.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Warehouse</label>
                    <select x-model="selectedWarehouse" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none appearance-none">
                        <option value="">Select Warehouse</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Stock Item</label>
                    <select name="stock_item_id" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none appearance-none">
                        <option value="">Select Item</option>
                        <template x-if="selectedWarehouse && filteredItems[selectedWarehouse]">
                            <template x-for="item in filteredItems[selectedWarehouse]" :key="item.id">
                                <option :value="item.id" x-text="item.name + ' (' + item.unit + ')'"></option>
                            </template>
                        </template>
                        @if(!$stockItems->isEmpty())
                            @foreach($stockItems as $item)
                                <option value="{{ $item->id }}" x-show="!selectedWarehouse">{{ $item->sku }} - {{ $item->name }} ({{ $item->unitType->name ?? '' }})</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Quantity</label>
                    <x-input type="number" name="quantity" min="1" placeholder="Enter quantity" required />
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Reference Number <span class="text-gray-400 font-normal">(PO / Surat Jalan)</span></label>
                    <x-input type="text" name="reference_number" placeholder="PO-2026-001" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" rows="3" placeholder="Catatan tambahan..."></textarea>
                </div>

                <div class="pt-6 mt-6 border-t border-gray-300">
                    <button type="submit" class="w-full py-4 rounded-2xl font-bold text-gray-100 bg-emerald-600 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#065f46,inset_-2px_-2px_4px_#047857] transition-all uppercase tracking-widest hover:bg-emerald-700">
                        Confirm Inbound
                    </button>
                </div>
            </form>
        </x-slide-over>

    </div>
@endsection
