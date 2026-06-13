@extends('layouts.app')

@section('title', 'Dashboard Overview')

@section('content')
    <x-topbar />
    
    <div class="mb-8">
        <p class="text-gray-500 text-lg font-medium">Welcome back to the logistics control center.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
        <!-- Stat Card 1 -->
        <x-card class="flex items-center justify-between transition-transform duration-300 hover:-translate-y-1">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Total Shipments</p>
                <p class="text-4xl font-bold text-gray-800">1,284</p>
            </div>
            <div class="w-16 h-16 rounded-2xl shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] flex items-center justify-center text-gray-700 bg-gray-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
        </x-card>

        <!-- Stat Card 2 -->
        <x-card class="flex items-center justify-between transition-transform duration-300 hover:-translate-y-1">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Active Fleet</p>
                <p class="text-4xl font-bold text-gray-800">42</p>
            </div>
            <div class="w-16 h-16 rounded-2xl shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] flex items-center justify-center text-gray-700 bg-gray-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            </div>
        </x-card>

        <!-- Stat Card 3 -->
        <x-card class="flex items-center justify-between transition-transform duration-300 hover:-translate-y-1">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Pending Orders</p>
                <p class="text-4xl font-bold text-gray-800">15</p>
            </div>
            <div class="w-16 h-16 rounded-2xl shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] flex items-center justify-center text-gray-700 bg-gray-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
        </x-card>

        <!-- Stat Card 4 -->
        <x-card class="flex items-center justify-between transition-transform duration-300 hover:-translate-y-1">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Revenue</p>
                <p class="text-3xl font-bold text-gray-800">Rp 128M</p>
            </div>
            <div class="w-16 h-16 rounded-2xl shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] flex items-center justify-center text-gray-700 bg-gray-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </x-card>
    </div>

    <!-- Layout Split -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <div class="xl:col-span-2">
            <x-card class="h-full min-h-[450px] flex flex-col">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold text-gray-800">Shipment Analytics</h3>
                    <div class="flex gap-2">
                        <button class="px-4 py-2 text-xs font-semibold rounded-xl bg-gray-100 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] text-gray-800">Weekly</button>
                        <button class="px-4 py-2 text-xs font-semibold rounded-xl bg-gray-100 shadow-[2px_2px_4px_#d1d5db,-2px_-2px_4px_#ffffff] text-gray-500 hover:text-gray-800">Monthly</button>
                    </div>
                </div>
                <div class="flex-1 w-full rounded-3xl shadow-[inset_6px_6px_12px_#d1d5db,inset_-6px_-6px_12px_#ffffff] flex items-center justify-center text-gray-400 bg-gray-100">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                        <span class="font-medium tracking-wide">[ Interactive Chart Area ]</span>
                    </div>
                </div>
            </x-card>
        </div>
        <div>
            <x-card class="h-full">
                <h3 class="text-xl font-bold text-gray-800 mb-8">Recent Activities</h3>
                <div class="space-y-8">
                    <!-- Activity Item -->
                    <div class="flex gap-5">
                        <div class="w-12 h-12 rounded-full shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] flex-shrink-0 flex items-center justify-center text-gray-600 bg-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <p class="text-base font-bold text-gray-800">Shipment #SHP-1029 Delivered</p>
                            <p class="text-sm font-medium text-gray-500 mt-1">2 minutes ago</p>
                        </div>
                    </div>
                    <!-- Activity Item -->
                    <div class="flex gap-5">
                        <div class="w-12 h-12 rounded-full shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] flex-shrink-0 flex items-center justify-center text-gray-600 bg-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <p class="text-base font-bold text-gray-800">Maintenance Alert: L 8001 AA</p>
                            <p class="text-sm font-medium text-gray-500 mt-1">1 hour ago</p>
                        </div>
                    </div>
                    <!-- Activity Item -->
                    <div class="flex gap-5">
                        <div class="w-12 h-12 rounded-full shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] flex-shrink-0 flex items-center justify-center text-gray-600 bg-gray-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <p class="text-base font-bold text-gray-800">New Driver Onboarded</p>
                            <p class="text-sm font-medium text-gray-500 mt-1">Yesterday</p>
                        </div>
                    </div>
                </div>
                <div class="mt-8 pt-6 border-t border-gray-300">
                    <x-button class="w-full text-sm">View All Activities</x-button>
                </div>
            </x-card>
        </div>
    </div>
@endsection
