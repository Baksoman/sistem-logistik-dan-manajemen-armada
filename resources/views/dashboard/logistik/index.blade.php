@extends('layouts.logistik')

@section('title', 'Logistics & Fleet Command Center')

@section('content')
    <x-topbar />

    <div class="mb-8">
        <p class="text-gray-500 text-lg font-medium">Manage shipments, assign drivers, and optimize routing here.</p>
    </div>

    <!-- Script for Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Order Stats -->
        <div class="relative bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <div class="absolute top-4 right-4 group cursor-pointer">
                <svg class="w-4 h-4 text-gray-400 hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute right-0 top-6 w-48 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                    Jumlah total keseluruhan pesanan (Draft dan Confirmed).
                </div>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] flex items-center justify-center text-blue-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <h3 class="font-bold text-gray-700 text-lg">Total Orders</h3>
            </div>
            <p class="text-3xl font-black text-gray-800">{{ number_format($orderStats['total']) }}</p>
            <p class="text-sm text-gray-500 mt-2">
                <span class="text-orange-500 font-bold">{{ $orderStats['draft'] }}</span> Draft | 
                <span class="text-blue-500 font-bold">{{ $orderStats['confirmed'] }}</span> Confirmed
            </p>
        </div>

        <!-- Pending Shipments -->
        <div class="relative bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <div class="absolute top-4 right-4 group cursor-pointer">
                <svg class="w-4 h-4 text-gray-400 hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute right-0 top-6 w-48 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                    Jumlah pengiriman yang masih menunggu untuk diberangkatkan.
                </div>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] flex items-center justify-center text-orange-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="font-bold text-gray-700 text-lg">Pending Shipments</h3>
            </div>
            <p class="text-3xl font-black text-gray-800">{{ number_format($shipmentStats['pending']) }}</p>
            <p class="text-sm text-gray-500 mt-2">Awaiting dispatch</p>
        </div>

        <!-- On Process Shipments -->
        <div class="relative bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <div class="absolute top-4 right-4 group cursor-pointer">
                <svg class="w-4 h-4 text-gray-400 hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute right-0 top-6 w-48 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                    Jumlah pengiriman yang saat ini sedang dalam perjalanan.
                </div>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] flex items-center justify-center text-indigo-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h3 class="font-bold text-gray-700 text-lg">On Process</h3>
            </div>
            <p class="text-3xl font-black text-gray-800">{{ number_format($shipmentStats['on_process']) }}</p>
            <p class="text-sm text-gray-500 mt-2">Currently on the road</p>
        </div>

        <!-- Delivered Shipments -->
        <div class="relative bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <div class="absolute top-4 right-4 group cursor-pointer">
                <svg class="w-4 h-4 text-gray-400 hover:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute right-0 top-6 w-48 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                    Jumlah pengiriman yang telah diselesaikan dengan sukses.
                </div>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] flex items-center justify-center text-green-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="font-bold text-gray-700 text-lg">Delivered</h3>
            </div>
            <p class="text-3xl font-black text-gray-800">{{ number_format($shipmentStats['delivered']) }}</p>
            <p class="text-sm text-gray-500 mt-2">Successfully completed</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Chart Section -->
        <div class="lg:col-span-2 bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <h3 class="font-bold text-gray-700 text-lg mb-6">Shipment Trends (Last 7 Days)</h3>
            <div class="w-full h-72">
                <canvas id="shipmentChart"></canvas>
            </div>
        </div>

        <!-- Recent Shipments -->
        <div class="bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-gray-700 text-lg">Recent Pending</h3>
                <a href="{{ route('shipments.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors">View All</a>
            </div>
            
            @if($recentShipments->count() > 0)
                <div class="space-y-4">
                    @foreach($recentShipments as $shipment)
                        <div class="p-4 rounded-2xl shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff]">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-xs font-bold px-2 py-1 bg-gray-200 text-gray-700 rounded-lg shadow-[2px_2px_4px_#d1d5db,-2px_-2px_4px_#ffffff]">
                                    {{ $shipment->tracking_number }}
                                </span>
                                <span class="text-xs font-medium text-gray-500">{{ $shipment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm font-bold text-gray-800 line-clamp-1 mb-1">
                                Driver: {{ $shipment->driver ? $shipment->driver->user->name : 'Not Assigned' }}
                            </p>
                            <p class="text-xs text-gray-500 line-clamp-1">
                                Route: {{ $shipment->routeVersion ? $shipment->routeVersion->route->name : 'Ad-hoc Route' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="h-48 flex items-center justify-center flex-col text-gray-400">
                    <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    <p class="text-sm font-medium">No pending shipments</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Analytics Section -->
    <h3 class="font-bold text-gray-700 text-lg mb-6 mt-10 border-b border-gray-200 pb-2">Operational Analytics</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        <!-- Cost per KM -->
        <div class="relative bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <div class="absolute top-4 right-4 group cursor-pointer">
                <svg class="w-4 h-4 text-gray-400 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute right-0 top-6 w-48 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                    Rata-rata biaya operasional yang dikeluarkan untuk setiap jarak 1 Kilometer.
                </div>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] flex items-center justify-center text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <h3 class="font-bold text-gray-700 text-lg">Cost per KM</h3>
            </div>
            <p class="text-3xl font-black text-gray-800">Rp {{ number_format($analytics['cost_per_km'], 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500 mt-2">
                Based on <span class="font-bold">{{ number_format($analytics['total_distance'], 1) }}</span> Total KM
            </p>
        </div>

        <!-- SLA Performance -->
        <div class="relative bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <div class="absolute top-4 right-4 group cursor-pointer">
                <svg class="w-4 h-4 text-gray-400 hover:text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute right-0 top-6 w-48 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                    Persentase pengiriman yang berhasil tiba tepat waktu sesuai Service Level Agreement.
                </div>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] flex items-center justify-center text-yellow-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="font-bold text-gray-700 text-lg">SLA Performance</h3>
            </div>
            <p class="text-3xl font-black {{ $analytics['sla_achievement'] >= 90 ? 'text-green-600' : 'text-orange-600' }}">
                {{ number_format($analytics['sla_achievement'], 1) }}%
            </p>
            <p class="text-sm text-gray-500 mt-2">
                <span class="font-bold">{{ $analytics['on_time_deliveries'] }}</span> / {{ $analytics['total_with_sla'] }} On-Time Deliveries
            </p>
            
            <!-- Mini progress bar -->
            <div class="w-full bg-gray-300 rounded-full h-2.5 mt-3 shadow-[inset_1px_1px_2px_#d1d5db,inset_-1px_-1px_2px_#ffffff]">
                <div class="h-2.5 rounded-full {{ $analytics['sla_achievement'] >= 90 ? 'bg-green-500' : 'bg-orange-500' }}" style="width: {{ $analytics['sla_achievement'] }}%"></div>
            </div>
        </div>

        <!-- Profitability -->
        <div class="relative bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <div class="absolute top-4 right-4 group cursor-pointer">
                <svg class="w-4 h-4 text-gray-400 hover:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute right-0 top-6 w-48 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                    Persentase margin keuntungan (Net Profit) dari pendapatan dikurangi pengeluaran operasional.
                </div>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] flex items-center justify-center text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="font-bold text-gray-700 text-lg">Net Profit Margin</h3>
            </div>
            <p class="text-3xl font-black {{ $analytics['profit_margin'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ number_format($analytics['profit_margin'], 1) }}%
            </p>
            <div class="flex justify-between text-xs mt-3">
                <div class="text-gray-500">
                    Rev: <span class="font-bold text-gray-800">Rp {{ number_format($analytics['total_revenue'] / 1000000, 1) }}M</span>
                </div>
                <div class="text-gray-500">
                    Exp: <span class="font-bold text-gray-800">Rp {{ number_format($analytics['total_expense'] / 1000000, 1) }}M</span>
                </div>
            </div>
        </div>
        
    </div>

    <!-- Advanced Delivery & Cost Analytics Section -->
    <h3 class="font-bold text-gray-700 text-lg mb-6 mt-10 border-b border-gray-200 pb-2">Delivery Performance & Fleet</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- OTIF -->
        <div class="relative bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <div class="absolute top-4 right-4 group cursor-pointer">
                <svg class="w-4 h-4 text-gray-400 hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute right-0 top-6 w-48 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                    Persentase pengiriman yang utuh dan tepat waktu (On-Time In-Full).
                </div>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] flex items-center justify-center text-blue-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                </div>
                <h3 class="font-bold text-gray-700 text-lg">OTIF Rate</h3>
            </div>
            <p class="text-3xl font-black {{ $advancedAnalytics['otif_achievement'] >= 90 ? 'text-green-600' : 'text-yellow-600' }}">{{ number_format($advancedAnalytics['otif_achievement'], 1) }}%</p>
            <p class="text-xs text-gray-500 mt-2 font-bold uppercase tracking-wide">On-Time In-Full</p>
        </div>

        <!-- Avg Transit Time -->
        <div class="relative bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <div class="absolute top-4 right-4 group cursor-pointer">
                <svg class="w-4 h-4 text-gray-400 hover:text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute right-0 top-6 w-48 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                    Rata-rata durasi perjalanan dari saat berangkat hingga tiba di tujuan.
                </div>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] flex items-center justify-center text-purple-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="font-bold text-gray-700 text-lg">Avg Transit Time</h3>
            </div>
            <p class="text-3xl font-black text-gray-800">{{ number_format($advancedAnalytics['avg_transit_time'], 1) }} <span class="text-lg text-gray-500 font-bold">hrs</span></p>
            <p class="text-xs text-gray-500 mt-2 font-bold uppercase tracking-wide">Start to Finish</p>
        </div>

        <!-- Freight Cost per Shipment -->
        <div class="relative bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <div class="absolute top-4 right-4 group cursor-pointer">
                <svg class="w-4 h-4 text-gray-400 hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute right-0 top-6 w-48 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                    Rata-rata biaya operasional per satu kali siklus pengiriman sukses.
                </div>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] flex items-center justify-center text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="font-bold text-gray-700 text-lg">Avg Cost / Unit</h3>
            </div>
            <p class="text-2xl font-black text-gray-800">Rp {{ number_format($advancedAnalytics['freight_cost_per_shipment'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-2 font-bold uppercase tracking-wide">Per Delivered Shipment</p>
        </div>

        <!-- Fleet Utilization -->
        <div class="relative bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <div class="absolute top-4 right-4 group cursor-pointer">
                <svg class="w-4 h-4 text-gray-400 hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute right-0 top-6 w-48 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                    Persentase kendaraan yang sedang aktif dari total unit kendaraan terdaftar.
                </div>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] flex items-center justify-center text-emerald-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                </div>
                <h3 class="font-bold text-gray-700 text-lg">Fleet Utilization</h3>
            </div>
            <p class="text-3xl font-black text-gray-800">{{ number_format($advancedAnalytics['fleet_utilization'], 0) }}%</p>
            <p class="text-xs text-gray-500 mt-2 font-bold uppercase tracking-wide">Active / Total Vehicles</p>
        </div>
        
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Fuel Efficiency -->
        <div class="relative bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <div class="absolute top-4 right-4 group cursor-pointer">
                <svg class="w-4 h-4 text-gray-400 hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute right-0 top-6 w-48 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                    Rata-rata rupiah yang dihabiskan untuk bahan bakar per satu kilometer perjalanan.
                </div>
            </div>
            <h3 class="font-bold text-gray-700 text-lg mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Fuel Efficiency (Rp / KM)
            </h3>
            <div class="flex flex-col h-32 justify-center items-center">
                <p class="text-4xl font-black text-gray-800">Rp {{ number_format($advancedAnalytics['fuel_efficiency'], 0, ',', '.') }}</p>
                <p class="text-sm text-gray-500 mt-2">Fuel Cost per Distance Traveled</p>
            </div>
        </div>

        <!-- Tracking Analysis Doughnut -->
        <div class="relative bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <div class="absolute top-4 right-4 group cursor-pointer">
                <svg class="w-4 h-4 text-gray-400 hover:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute right-0 top-6 w-48 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                    Distribusi status pengiriman secara proporsional.
                </div>
            </div>
            <h3 class="font-bold text-gray-700 text-lg mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Delivery Status Distribution
            </h3>
            <div class="w-full h-48 relative">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('shipmentChart').getContext('2d');
            
            // Neon/Neumorphism style chart
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.5)'); // Blue-500 semi-transparent
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Shipments Created',
                        data: {!! json_encode($chartData) !!},
                        borderColor: '#3b82f6', // Tailwind blue-500
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#3b82f6',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4 // Smooth curves
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#1f2937',
                            bodyColor: '#4b5563',
                            borderColor: '#e5e7eb',
                            borderWidth: 1,
                            padding: 12,
                            boxPadding: 4,
                            usePointStyle: true,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                color: '#6b7280',
                                font: {
                                    family: "'Poppins', sans-serif",
                                    size: 11
                                }
                            },
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)', // Very faint gray
                                drawBorder: false,
                            }
                        },
                        x: {
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    family: "'Poppins', sans-serif",
                                    size: 11
                                }
                            },
                            grid: {
                                display: false,
                                drawBorder: false,
                            }
                        }
                    }
                }
            });

            // Status Doughnut Chart
            const statusCtx = document.getElementById('statusChart');
            if (statusCtx) {
                new Chart(statusCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Pending', 'On Process', 'Delivered'],
                        datasets: [{
                            data: [
                                {{ $advancedAnalytics['delivery_status']['pending'] }},
                                {{ $advancedAnalytics['delivery_status']['on_process'] }},
                                {{ $advancedAnalytics['delivery_status']['delivered'] }}
                            ],
                            backgroundColor: [
                                '#f97316', // Orange-500
                                '#6366f1', // Indigo-500
                                '#22c55e'  // Green-500
                            ],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: {
                                        family: "'Poppins', sans-serif",
                                        size: 12
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
