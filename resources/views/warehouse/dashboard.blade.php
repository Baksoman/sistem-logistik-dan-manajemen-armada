@extends('layouts.warehouse')

@section('title', 'Warehouse Dashboard')

@section('content')
    <x-topbar />

    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Warehouse Dashboard</h2>
        <p class="text-gray-500 mt-2 font-medium">Welcome back, {{ auth()->user()->name }}! Here is what's happening in your warehouses today.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-card class="transform hover:-translate-y-1 transition-transform duration-300">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff] flex items-center justify-center">
                    <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-bold tracking-widest uppercase">My Warehouses</p>
                    <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($totalWarehouses) }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="transform hover:-translate-y-1 transition-transform duration-300">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff] flex items-center justify-center">
                    <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-bold tracking-widest uppercase">Total Stock</p>
                    <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($totalItems) }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="transform hover:-translate-y-1 transition-transform duration-300">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff] flex items-center justify-center">
                    <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-bold tracking-widest uppercase">Inbound Today</p>
                    <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($todayInbound) }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="transform hover:-translate-y-1 transition-transform duration-300">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff] flex items-center justify-center">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l4 4m0 0l-4 4m4-4H3m5-4V6a3 3 0 013-3h7a3 3 0 013 3v12a3 3 0 01-3 3h-7a3 3 0 01-3-3v-1"></path></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 font-bold tracking-widest uppercase">Outbound Today</p>
                    <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($todayOutbound) }}</p>
                </div>
            </div>
        </x-card>
    </div>

@endsection
