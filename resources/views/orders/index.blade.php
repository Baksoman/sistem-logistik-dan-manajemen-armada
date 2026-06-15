@extends('layouts.logistik')

@section('title', 'Order Management')

@section('content')
    <x-topbar />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <p class="text-gray-500 text-lg font-medium">Manage customer orders and tracking status.</p>
        <a href="{{ route('orders.create') }}" class="flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-bold text-gray-800 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-600 shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Create New Order
        </a>
    </div>



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
                    @forelse($orders as $order)
                        <tr class="border-b border-gray-200/50 hover:bg-gray-200/30 transition">
                            <td class="py-4 px-4 font-bold text-gray-800 tracking-wider">{{ $order->order_number }}</td>
                            <td class="py-4 px-4">{{ $order->customer->company_name ?? '-' }}</td>
                            <td class="py-4 px-4 text-sm">{{ $order->originWarehouse->name ?? '-' }}</td>
                            <td class="py-4 px-4">
                                <div class="flex flex-col text-sm">
                                    <span class="text-gray-800 truncate max-w-xs" title="{{ $order->destination_address }}">{{ $order->destination_address }}</span>
                                    <span class="text-xs text-blue-600 font-bold mt-1">
                                        @if($order->currentWarehouse)
                                            📍 Hub: {{ $order->currentWarehouse->name }}
                                        @else
                                            🚚 In Transit
                                        @endif
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-black text-emerald-600">
                                    {{ $order->quoted_price ? 'Rp ' . number_format($order->quoted_price, 0, ',', '.') : '-' }}
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                @php
                                    $badgeClass = 'text-gray-700 bg-gray-100';
                                    if ($order->status === 'Pending Approval') $badgeClass = 'text-amber-700 bg-amber-100';
                                    if ($order->status === 'Confirmed') $badgeClass = 'text-blue-700 bg-blue-100';
                                    if ($order->status === 'Assigned') $badgeClass = 'text-purple-700 bg-purple-100';
                                    if ($order->status === 'Completed') $badgeClass = 'text-emerald-700 bg-emerald-100';
                                @endphp
                                <span class="px-3 py-1 text-xs font-bold rounded-full shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] {{ $badgeClass }} uppercase">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('orders.show', $order->id) }}" class="w-10 h-10 rounded-full flex items-center justify-center text-blue-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">Belum ada order.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </x-card>
@endsection
