@php
    $layout = auth()->user()->can('manage_orders') ? 'layouts.logistik' : 'layouts.warehouse';
@endphp
@extends($layout)

@section('title', 'Order Management')

@section('content')
    <x-topbar />

    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8">
        <p class="text-gray-500 text-lg font-medium">Manage customer orders and tracking status.</p>
        <div class="flex flex-col lg:flex-row w-full lg:w-auto gap-3 shrink-0">
            @can('manage_orders')
                <a href="{{ route('orders.export.excel') }}" class="flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-emerald-700 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db] transition-all hover:bg-emerald-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Excel
                </a>
                <a href="{{ route('orders.export.pdf') }}" class="flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-red-600 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db] transition-all hover:bg-red-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    PDF
                </a>
            @endcan

            @can('create_order')
                <a href="{{ route('orders.create') }}" class="flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-bold text-gray-800 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Create New Order
                </a>
            @endcan
        </div>
    </div>

    <div x-data="dataTable({
            endpoint: '/api/search/orders',
            initialData: {{ Js::from($initialData['data'] ?? []) }},
            initialMeta: {{ Js::from($initialData['meta'] ?? []) }}
        })">

        <x-search-filter-bar placeholder="Search orders by number, location, or customer..." />

        <x-filter-modal title="Filter Orders">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                <select x-model="filters.status" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
                    <option value="">All Statuses</option>
                    <option value="Draft">Draft</option>
                    <option value="Pending Approval">Pending Approval</option>
                    <option value="Confirmed">Confirmed</option>
                    <option value="Assigned">Assigned</option>
                    <option value="Arrived at Hub">Arrived at Hub</option>
                    <option value="Completed">Completed</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>
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
                <label class="block text-sm font-bold text-gray-700 mb-2">Customer</label>
                <select x-model="filters.customer_id" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
                    <option value="">All Customers</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">From Date</label>
                    <input type="date" x-model="filters.created_from" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">To Date</label>
                    <input type="date" x-model="filters.created_to" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
                </div>
            </div>
        </x-filter-modal>

    <x-card class="mb-8">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Daftar Order</h3>
        <div class="overflow-x-auto pb-4">
            <table class="w-full text-left border-collapse min-w-max whitespace-nowrap">
                <thead>
                    <tr class="border-b border-gray-300 text-gray-500 text-sm tracking-widest uppercase">
                        <th class="py-4 px-4 font-bold">Order No.</th>
                        <th class="py-4 px-4 font-bold">Customer</th>
                        <th class="py-4 px-4 font-bold">Origin</th>
                        <th class="py-4 px-4 font-bold">Dest. / Location</th>
                        <th class="py-4 px-4 font-bold">Quotation</th>
                        <th class="py-4 px-4 font-bold">Status</th>
                        <th class="py-4 px-4 font-bold text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 font-medium">
                    <template x-for="order in data" :key="order.id">
                        <tr class="border-b border-gray-200/50 hover:bg-gray-200/30 transition">
                            <td class="py-4 px-4 font-bold text-gray-800 tracking-wider" x-text="order.order_number"></td>
                            <td class="py-4 px-4" x-text="order.customer?.company_name || '-'"></td>
                            <td class="py-4 px-4 text-sm" x-text="order.origin_warehouse?.name || '-'"></td>
                            <td class="py-4 px-4">
                                <div class="flex flex-col text-sm">
                                    <span class="text-gray-800 truncate max-w-xs" :title="order.destination_address" x-text="order.destination_address"></span>
                                    <span class="text-xs text-blue-600 font-bold mt-1">
                                        <template x-if="order.current_warehouse">
                                            <span x-text="'📍 Hub: ' + order.current_warehouse.name"></span>
                                        </template>
                                        <template x-if="!order.current_warehouse">
                                            <span>🚚 In Transit</span>
                                        </template>
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-black text-emerald-600">
                                    <span x-text="order.quoted_price ? 'Rp ' + Number(order.quoted_price).toLocaleString('id-ID') : '-'"></span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-3 py-1 text-xs font-bold rounded-full shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] uppercase"
                                      :class="{
                                          'text-amber-700 bg-amber-100': order.status === 'Pending Approval',
                                          'text-blue-700 bg-blue-100': order.status === 'Confirmed',
                                          'text-purple-700 bg-purple-100': order.status === 'Assigned',
                                          'text-emerald-700 bg-emerald-100': order.status === 'Completed' || order.status === 'Delivered',
                                          'text-gray-700 bg-gray-100': !['Pending Approval', 'Confirmed', 'Assigned', 'Completed', 'Delivered'].includes(order.status)
                                      }"
                                      x-text="order.status">
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center justify-center gap-3">
                                    <a :href="'{{ route('orders.index') }}/' + order.id" class="w-10 h-10 rounded-full flex items-center justify-center text-blue-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="data.length === 0" x-cloak>
                        <td colspan="7" class="py-8 text-center text-gray-400">Belum ada order.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <x-pagination />
    </x-card>
    </div>
@endsection
