@extends('layouts.warehouse')

@section('title', 'Inbound (Putaway)')

@section('content')
    <x-topbar />

    <div x-data="{ 
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
         }" 
         @keydown.escape.window="slideOverOpen = false; stopScanner();">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <p class="text-gray-500 text-lg font-medium">Penerimaan barang masuk (Putaway) ke gudang.</p>
            </div>
            <div class="flex items-center gap-3">
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

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
            <x-card>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff] flex items-center justify-center">
                        <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Today's Inbound</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $movements->where('created_at', '>=', today())->count() }}</p>
                    </div>
                </div>
            </x-card>
            <x-card>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff] flex items-center justify-center">
                        <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Items Received</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($movements->sum('quantity')) }}</p>
                    </div>
                </div>
            </x-card>
            <x-card>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff] flex items-center justify-center">
                        <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Records</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $movements->total() }}</p>
                    </div>
                </div>
            </x-card>
        </div>

        <x-card class="mb-8">
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
                    @forelse($movements as $movement)
                        <tr class="border-b border-gray-200/50 hover:bg-gray-200/30 transition">
                            <td class="py-4 px-4 text-sm text-gray-500">{{ $movement->created_at->format('d M Y H:i') }}</td>
                            <td class="py-4 px-4 font-bold text-gray-800 tracking-wider">{{ $movement->reference_number ?? '-' }}</td>
                            <td class="py-4 px-4">
                                <div>
                                    <span class="font-bold">{{ $movement->stockItem->name ?? '-' }}</span>
                                    <span class="block text-xs text-gray-400">{{ $movement->stockItem->sku ?? '' }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4">{{ $movement->stockItem->warehouse->name ?? '-' }}</td>
                            <td class="py-4 px-4">
                                <span class="px-3 py-1 text-xs font-bold rounded-full shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] text-emerald-700 bg-emerald-50">
                                    +{{ number_format($movement->quantity) }} {{ $movement->stockItem->unitType->name ?? '' }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-sm">{{ $movement->creator->name ?? '-' }}</td>
                            <td class="py-4 px-4 text-sm text-gray-500 whitespace-normal max-w-[200px]">{{ $movement->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400">No inbound records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            <div class="mt-4">
                {{ $movements->links() }}
            </div>
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
