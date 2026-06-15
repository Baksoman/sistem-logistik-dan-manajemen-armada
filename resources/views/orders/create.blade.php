@extends('layouts.logistik')

@section('title', 'Create Order')

@section('content')
    <x-topbar />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('orders.index') }}" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <p class="text-gray-500 text-lg font-medium">Create New Order</p>
        </div>
    </div>



    @if ($errors->any())
        <div class="mb-8 p-4 bg-red-100 text-red-800 rounded-xl shadow-sm border border-red-200">
            <ul class="list-disc list-inside font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" x-data="orderForm()">
        <!-- Order Form Section -->
        <div>
            <x-card>
                <form action="{{ route('orders.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Customer</label>
                        <select name="customer_id" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
                            <option value="">-- Pilih Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->company_name }} ({{ $customer->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Origin Warehouse</label>
                        <select name="origin_warehouse_id" x-model="warehouseId" @change="fetchWarehouseItems()" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
                            <option value="">-- Pilih Gudang Asal --</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Destination (Omni Search)</label>
                        <div @dest-selected.window="setDestination($event.detail)">
                            <x-omni-search name="destination_omni" placeholder="Search Customer Address..." event-name="dest-selected" />
                        </div>
                        
                        <!-- Hidden inputs for form submission -->
                        <input type="hidden" name="destination_address" x-model="destinationAddress">
                        <input type="hidden" name="destination_latitude" x-model="destinationLat">
                        <input type="hidden" name="destination_longitude" x-model="destinationLng">
                        
                        <div x-show="destinationAddress" class="mt-2 text-xs font-medium text-emerald-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Destination selected.
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-300">
                        <div class="flex justify-between items-center mb-4">
                            <p class="text-sm font-bold text-gray-700">Order Items</p>
                            <button type="button" @click="addItem()" class="text-blue-600 text-xs font-bold flex items-center gap-1 hover:text-blue-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Add Item
                            </button>
                        </div>
                        
                        <div x-show="loadingItems" class="text-xs text-gray-500 font-medium animate-pulse mb-4">
                            Memuat daftar barang...
                        </div>

                        <div x-show="!warehouseId" class="text-xs text-gray-400 font-bold mb-4">
                            Silakan pilih Origin Warehouse terlebih dahulu.
                        </div>

                        <div x-show="warehouseId && !loadingItems" class="space-y-4">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="flex items-center gap-2 p-3 rounded-xl bg-gray-100 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff]">
                                    <div class="flex-1">
                                        <select x-model="item.stock_item_id" :name="`items[${index}][stock_item_id]`" @change="updateItemDetails(index)" required class="w-full text-xs bg-transparent border-b border-gray-300 focus:border-blue-500 focus:ring-0 p-1 font-medium text-gray-700">
                                            <option value="">-- Pilih Barang --</option>
                                            <template x-for="stock in availableStockItems" :key="stock.id">
                                                <option :value="stock.id" x-text="`${stock.sku} - ${stock.name} (Avail: ${stock.available_qty})`"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="w-24">
                                        <input type="number" x-model="item.quantity" :name="`items[${index}][quantity]`" min="1" :max="item.max_qty" required class="w-full text-xs bg-transparent border-b border-gray-300 focus:border-blue-500 focus:ring-0 p-1 text-center font-bold" placeholder="Qty">
                                    </div>
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-red-500 hover:text-red-700 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="pt-6 mt-6">
                        <button type="submit" class="w-full py-4 rounded-2xl font-bold text-gray-100 bg-blue-600 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#1e3a8a] transition-all uppercase tracking-widest text-sm">
                            Create Order
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
        
        <!-- Right Column (Empty for now or could display a summary / map) -->
        <div class="hidden lg:block">
            <x-card class="h-full flex items-center justify-center bg-gray-50">
                <div class="text-center">
                    <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <p class="text-gray-400 font-bold tracking-widest uppercase">Order Summary Panel</p>
                </div>
            </x-card>
        </div>
    </div>

    <script>
        function orderForm() {
            return {
                warehouseId: '',
                destinationAddress: '',
                destinationLat: '',
                destinationLng: '',
                availableStockItems: [],
                loadingItems: false,
                items: [
                    { stock_item_id: '', quantity: 1, max_qty: null, unit: 'pcs' }
                ],

                setDestination(detail) {
                    this.destinationLat = detail.lat;
                    this.destinationLng = detail.lng;
                    this.destinationAddress = detail.name || detail.address || `${detail.lat}, ${detail.lng}`;
                },

                async fetchWarehouseItems() {
                    this.availableStockItems = [];
                    this.items = [{ stock_item_id: '', quantity: 1, max_qty: null, unit: 'pcs' }];
                    
                    if (!this.warehouseId) return;

                    this.loadingItems = true;
                    try {
                        const response = await fetch(`/orders/warehouse-items/${this.warehouseId}`);
                        const data = await response.json();
                        this.availableStockItems = data;
                    } catch (error) {
                        console.error("Error fetching items:", error);
                        Toastify({text: "Gagal mengambil data stok dari gudang.", duration: 3000, style:{background:"#fee2e2", color:"#991b1b"}}).showToast();
                    } finally {
                        this.loadingItems = false;
                    }
                },

                addItem() {
                    this.items.push({ stock_item_id: '', quantity: 1, max_qty: null, unit: 'pcs' });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },

                updateItemDetails(index) {
                    const selectedId = this.items[index].stock_item_id;
                    const stockItem = this.availableStockItems.find(s => s.id === selectedId);
                    if (stockItem) {
                        this.items[index].max_qty = stockItem.available_qty;
                        this.items[index].unit = stockItem.unit;
                        if (this.items[index].quantity > stockItem.available_qty) {
                            this.items[index].quantity = stockItem.available_qty;
                        }
                    } else {
                        this.items[index].max_qty = null;
                        this.items[index].unit = 'pcs';
                    }
                }
            }
        }
    </script>
@endsection
