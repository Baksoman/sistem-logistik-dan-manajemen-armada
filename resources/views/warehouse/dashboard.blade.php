@extends('layouts.warehouse')

@section('title', 'Dashboard')

@section('content')
    <x-topbar />

    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Warehouse Dashboard</h2>
        <p class="text-gray-500 mt-2 font-medium">Welcome back, {{ auth()->user()->name }}! Here is what's happening in your warehouses today.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-card class="relative transform hover:-translate-y-1 transition-transform duration-300">
            <div class="absolute top-2 right-2 group cursor-pointer">
                <svg class="w-4 h-4 text-gray-300 hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute right-0 top-5 w-40 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                    Jumlah gudang yang Anda miliki akses untuk dikelola.
                </div>
            </div>
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

        <x-card class="relative transform hover:-translate-y-1 transition-transform duration-300">
            <div class="absolute top-2 right-2 group cursor-pointer">
                <svg class="w-4 h-4 text-gray-300 hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute right-0 top-5 w-40 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                    Total kuantitas barang di seluruh gudang yang Anda kelola.
                </div>
            </div>
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

        <x-card class="relative transform hover:-translate-y-1 transition-transform duration-300">
            <div class="absolute top-2 right-2 group cursor-pointer">
                <svg class="w-4 h-4 text-gray-300 hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute right-0 top-5 w-40 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                    Total kuantitas barang yang masuk (inbound) hari ini.
                </div>
            </div>
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

        <x-card class="relative transform hover:-translate-y-1 transition-transform duration-300">
            <div class="absolute top-2 right-2 group cursor-pointer">
                <svg class="w-4 h-4 text-gray-300 hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute right-0 top-5 w-40 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                    Total kuantitas barang yang keluar (outbound) hari ini.
                </div>
            </div>
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

    <!-- Charts and Secondary Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        
        <!-- Trend Chart -->
        <x-card class="lg:col-span-2">
            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                Inbound vs Outbound Trend (Last 7 Days)
            </h3>
            <div class="relative h-72 w-full">
                <canvas id="trendChart"></canvas>
            </div>
        </x-card>

        <!-- Low Stock Alerts -->
        <x-card>
            <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Low Stock Alerts
            </h3>
            
            <div class="space-y-4">
                @forelse($lowStockItems as $item)
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-gray-100 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff]">
                        <div>
                            <p class="font-bold text-gray-800 text-sm">{{ $item->name }}</p>
                            <p class="text-xs text-gray-500">{{ $item->sku }} • {{ $item->warehouse->name ?? '-' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-2 py-1 text-xs font-bold rounded-full text-red-700 bg-red-100">
                                {{ $item->quantity }} left
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-40 text-gray-400">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <p class="text-sm font-medium">All items are sufficiently stocked.</p>
                    </div>
                @endforelse
            </div>
            
            @if(count($lowStockItems) > 0)
            <div class="mt-4 pt-4 border-t border-gray-200 text-center">
                <a href="{{ route('warehouse.inventory.index') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors">Manage Inventory &rarr;</a>
            </div>
            @endif
        </x-card>
    </div>
    
    <!-- Advanced Analytics Section -->
    <h3 class="font-bold text-gray-700 text-lg mb-6 mt-10 border-b border-gray-200 pb-2">Advanced Analytics</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        <!-- Inventory Turnover -->
        <div class="relative bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <div class="absolute top-4 right-4 group cursor-pointer">
                <svg class="w-4 h-4 text-gray-400 hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute right-0 top-6 w-48 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                    Rasio perputaran stok. Menunjukkan berapa kali stok berganti dalam 30 hari terakhir.
                </div>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] flex items-center justify-center text-blue-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </div>
                <h3 class="font-bold text-gray-700 text-lg">Inventory Turnover</h3>
            </div>
            <p class="text-3xl font-black text-gray-800">{{ number_format($warehouseAnalytics['inventory_turnover'], 3) }} <span class="text-lg text-gray-500 font-bold">x</span></p>
            <p class="text-sm text-gray-500 mt-2">
                Ratio over last 30 days
            </p>
        </div>

        <!-- Space Utilization -->
        <div class="relative bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <div class="absolute top-4 right-4 group cursor-pointer">
                <svg class="w-4 h-4 text-gray-400 hover:text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute right-0 top-6 w-48 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                    Persentase rak yang terisi oleh barang dibandingkan dengan total rak yang tersedia.
                </div>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] flex items-center justify-center text-purple-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h3 class="font-bold text-gray-700 text-lg">Capacity Utilization</h3>
            </div>
            <p class="text-3xl font-black {{ $warehouseAnalytics['space_utilization'] >= 80 ? 'text-red-600' : 'text-green-600' }}">
                {{ number_format($warehouseAnalytics['space_utilization'], 1) }}%
            </p>
            <p class="text-sm text-gray-500 mt-2">
                Occupied Racks
            </p>
            <!-- Mini progress bar -->
            <div class="w-full bg-gray-300 rounded-full h-2.5 mt-3 shadow-[inset_1px_1px_2px_#d1d5db,inset_-1px_-1px_2px_#ffffff]">
                <div class="h-2.5 rounded-full {{ $warehouseAnalytics['space_utilization'] >= 80 ? 'bg-red-500' : 'bg-green-500' }}" style="width: {{ $warehouseAnalytics['space_utilization'] }}%"></div>
            </div>
        </div>

        <!-- Order Cycle Time -->
        <div class="relative bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <div class="absolute top-4 right-4 group cursor-pointer">
                <svg class="w-4 h-4 text-gray-400 hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div class="absolute right-0 top-6 w-48 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                    Rata-rata waktu yang dibutuhkan dari sejak pesanan dibuat hingga pengiriman dimulai.
                </div>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] flex items-center justify-center text-orange-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="font-bold text-gray-700 text-lg">Order Cycle Time</h3>
            </div>
            <p class="text-3xl font-black text-gray-800">
                {{ number_format($warehouseAnalytics['order_cycle_time'], 1) }} <span class="text-lg text-gray-500 font-bold">hrs</span>
            </p>
            <p class="text-sm text-gray-500 mt-2">
                Avg time from order to shipment start
            </p>
        </div>
        
    </div>

    <!-- Recent Activities -->
    <x-card>
        <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Recent Activities
        </h3>
        <div class="overflow-x-auto pb-4">
            <table class="w-full text-left border-collapse min-w-max whitespace-nowrap">
                <thead>
                    <tr class="border-b border-gray-300 text-gray-500 text-xs tracking-widest uppercase">
                        <th class="py-3 px-4 font-bold">Time</th>
                        <th class="py-3 px-4 font-bold">Type</th>
                        <th class="py-3 px-4 font-bold">Item</th>
                        <th class="py-3 px-4 font-bold">Qty</th>
                        <th class="py-3 px-4 font-bold">By</th>
                        <th class="py-3 px-4 font-bold">Ref</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 font-medium text-sm">
                    @forelse($recentActivities as $activity)
                        <tr class="border-b border-gray-200/50 hover:bg-gray-200/30 transition">
                            <td class="py-3 px-4 text-gray-500">{{ $activity->created_at->diffForHumans() }}</td>
                            <td class="py-3 px-4">
                                @if($activity->type === 'inbound')
                                    <span class="px-2 py-1 text-xs font-bold rounded-md shadow-[inset_1px_1px_2px_#d1d5db,inset_-1px_-1px_2px_#ffffff] text-emerald-700 bg-emerald-50">Inbound</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-bold rounded-md shadow-[inset_1px_1px_2px_#d1d5db,inset_-1px_-1px_2px_#ffffff] text-red-700 bg-red-50">Outbound</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-gray-800">{{ $activity->stockItem->name ?? '-' }}</span>
                                <span class="text-xs text-gray-400 block">{{ $activity->stockItem->sku ?? '' }}</span>
                            </td>
                            <td class="py-3 px-4 font-bold {{ $activity->type === 'inbound' ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $activity->type === 'inbound' ? '+' : '-' }}{{ number_format($activity->quantity) }}
                            </td>
                            <td class="py-3 px-4">{{ $activity->creator->name ?? '-' }}</td>
                            <td class="py-3 px-4 text-gray-500">{{ $activity->reference_number ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">No recent activities found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const trendData = @json($trendData);
            
            const ctx = document.getElementById('trendChart').getContext('2d');
            
            // Gradient for Inbound
            const inboundGradient = ctx.createLinearGradient(0, 0, 0, 300);
            inboundGradient.addColorStop(0, 'rgba(16, 185, 129, 0.5)'); // Emerald 500
            inboundGradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');
            
            // Gradient for Outbound
            const outboundGradient = ctx.createLinearGradient(0, 0, 0, 300);
            outboundGradient.addColorStop(0, 'rgba(239, 68, 68, 0.5)'); // Red 500
            outboundGradient.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

            Chart.defaults.font.family = "'Inter', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif";
            Chart.defaults.color = '#6b7280'; // Gray 500

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: trendData.labels,
                    datasets: [
                        {
                            label: 'Inbound (Qty)',
                            data: trendData.inbound,
                            borderColor: '#10b981', // Emerald 500
                            backgroundColor: inboundGradient,
                            borderWidth: 3,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Outbound (Qty)',
                            data: trendData.outbound,
                            borderColor: '#ef4444', // Red 500
                            backgroundColor: outboundGradient,
                            borderWidth: 3,
                            pointBackgroundColor: '#ef4444',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8,
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)', // Gray 900
                            titleFont: { size: 13, family: 'Inter', weight: 'bold' },
                            bodyFont: { size: 13, family: 'Inter' },
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: true,
                            boxPadding: 4
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)', // Gray 400 with 0.1 opacity
                                drawBorder: false,
                            },
                            ticks: {
                                font: { weight: '500' },
                                padding: 8
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false,
                            },
                            ticks: {
                                font: { weight: '500' },
                                padding: 8
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
