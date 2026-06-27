@extends('layouts.logistik')

@section('title', 'Operational Costs Management')

@section('content')
    <x-topbar />

    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8">
        <p class="text-gray-500 text-lg font-medium">Monitor all operational expenses submitted by drivers during shipments.</p>
    </div>

    <div x-data="dataTable({
            endpoint: '/api/search/operational-costs',
            initialData: {{ Js::from($initialData['data'] ?? []) }},
            initialMeta: {{ Js::from($initialData['meta'] ?? []) }}
        })"
         x-init="
            window.showReceipt = function(path) {
                receiptModalOpen = true;
                currentReceipt = path;
            }
         ">

        <x-search-filter-bar placeholder="Search by shipment number, driver name, or category..." />

        <x-filter-modal title="Filter Operational Costs">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Cost Category</label>
                <select x-model="filters.category_id" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Driver</label>
                <select x-model="filters.driver_id" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
                    <option value="">All Drivers</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}">{{ $driver->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Shipment Status</label>
                <select x-model="filters.status" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
                    <option value="">All Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="On Process">On Process</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Failed">Failed</option>
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

            <h3 class="text-xl font-bold text-gray-800 mb-6">Daftar Operational Costs</h3>
            
            <div class="overflow-x-auto pb-4">
                <table class="w-full text-left border-collapse min-w-max whitespace-nowrap">
                    <thead>
                        <tr class="border-b border-gray-300 text-gray-500 text-sm tracking-widest uppercase">
                            <th class="py-4 px-4 font-bold">Shipment</th>
                            <th class="py-4 px-4 font-bold">Driver</th>
                            <th class="py-4 px-4 font-bold text-gray-500 uppercase tracking-widest border-b-2 border-gray-200">Category</th>
                            <th class="py-4 px-4 font-bold text-gray-500 uppercase tracking-widest border-b-2 border-gray-200">Amount</th>
                            <th class="py-4 px-4 font-bold text-gray-500 uppercase tracking-widest border-b-2 border-gray-200">Date</th>
                            <th class="py-4 px-4 font-bold text-gray-500 uppercase tracking-widest text-center border-b-2 border-gray-200">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 font-medium">
                        <template x-for="cost in data" :key="cost.id">
                            <tr class="border-b border-gray-200/50 hover:bg-gray-200/30 transition">
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <div class="font-bold text-gray-800 tracking-wider" x-text="cost.shipment.shipment_number || '-'"></div>
                                    <div class="text-[10px] uppercase font-bold text-gray-400 mt-1 tracking-wider" x-text="cost.shipment.status || '-'"></div>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs shadow-[inset_1px_1px_2px_rgba(0,0,0,0.1)]">
                                            <span x-text="cost.driver.name.substring(0, 2).toUpperCase()"></span>
                                        </div>
                                        <span class="font-bold text-gray-700" x-text="cost.driver.name"></span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap" x-text="cost.category.name"></td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span class="font-bold text-blue-600" x-text="'Rp ' + Number(cost.amount).toLocaleString('id-ID')"></span>
                                    <div class="text-[10px] text-gray-400 mt-1 italic w-40 truncate" :title="cost.description" x-text="cost.description || 'No description'"></div>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap" x-text="cost.recorded_at || cost.created_at.substring(0, 16)"></td>
                                <td class="py-4 px-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center">
                                        <template x-if="cost.receipt_path">
                                            <button @click="$dispatch('open-receipt', { path: cost.receipt_path })" class="w-10 h-10 rounded-full flex items-center justify-center text-blue-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </button>
                                        </template>
                                        <template x-if="!cost.receipt_path">
                                            <span class="text-xs text-gray-400 font-bold px-3 py-1">-</span>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="data.length === 0" x-cloak>
                            <td colspan="6" class="py-8 text-center text-gray-400">Belum ada operational costs.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <x-pagination />
        </x-card>
    </div>

    <!-- Receipt Modal -->
    <div x-data="{ open: false, path: '' }" 
         @open-receipt.window="open = true; path = $event.detail.path"
         x-show="open" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
         x-transition.opacity>
        
        <div @click.away="open = false" 
             class="relative w-full max-w-3xl bg-gray-100 rounded-[2rem] shadow-[16px_16px_32px_rgba(0,0,0,0.2),-16px_-16px_32px_rgba(255,255,255,0.1)] border border-gray-200 p-2 overflow-hidden flex flex-col"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100">
             
             <!-- Close Button -->
             <div class="absolute top-4 right-4 z-10">
                 <button @click="open = false" class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-100/80 backdrop-blur shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] text-gray-500 hover:text-red-500 transition-colors">
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                 </button>
             </div>

             <!-- Image Container -->
             <div class="w-full bg-gray-200/50 rounded-[1.5rem] shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] overflow-auto max-h-[80vh] flex items-center justify-center p-4">
                 <img :src="path" alt="Receipt" class="max-w-full h-auto object-contain rounded-xl shadow-md">
             </div>
        </div>
    </div>
@endsection
