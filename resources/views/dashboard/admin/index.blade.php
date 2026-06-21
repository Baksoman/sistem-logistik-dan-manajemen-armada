@extends('layouts.app')

@section('title', 'Dashboard Overview')

@section('content')
    <x-topbar />
    
    <div class="mb-8">
        <p class="text-gray-500 text-lg font-medium">Welcome back to the control center.</p>
    </div>

    <!-- Script for Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Executive Stats Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <!-- Financial Pillar -->
        <div class="space-y-4">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-200 pb-2">Financials</h3>
            <x-card class="relative flex flex-col gap-2 transition-transform duration-300 hover:-translate-y-1 bg-gradient-to-br from-green-50 to-emerald-100/50">
                <div class="absolute top-3 right-3 group cursor-pointer">
                    <svg class="w-4 h-4 text-emerald-500/50 hover:text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div class="absolute right-0 top-6 w-48 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                        Total pendapatan vs pengeluaran operasional perusahaan secara keseluruhan.
                    </div>
                </div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Revenue vs Expense</p>
                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-sm text-gray-500">Revenue</p>
                        <p class="text-xl font-black text-green-700">Rp {{ number_format($stats['revenue'] / 1000000, 1) }}M</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Expense</p>
                        <p class="text-xl font-black text-red-600">Rp {{ number_format($stats['expense'] / 1000000, 1) }}M</p>
                    </div>
                </div>
                <div class="mt-2 pt-2 border-t border-emerald-200/50 flex justify-between items-center">
                    <span class="text-sm font-bold text-gray-600">Net Profit</span>
                    <span class="text-lg font-black {{ $stats['net_profit'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                        Rp {{ number_format($stats['net_profit'] / 1000000, 1) }}M
                    </span>
                </div>
            </x-card>
        </div>

        <!-- Logistics Pillar -->
        <div class="space-y-4">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-200 pb-2">Logistics & Fleet</h3>
            <div class="grid grid-cols-2 gap-4 h-[148px]">
                <x-card class="relative flex flex-col justify-center items-center text-center transition-transform duration-300 hover:-translate-y-1 !p-4">
                    <div class="absolute top-2 right-2 group cursor-pointer">
                        <svg class="w-4 h-4 text-gray-300 hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute right-0 top-5 w-40 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                            Persentase pengiriman yang tiba tepat waktu sesuai target SLA.
                        </div>
                    </div>
                    <p class="text-3xl font-black {{ $stats['sla_achievement'] >= 90 ? 'text-green-600' : 'text-yellow-600' }}">{{ number_format($stats['sla_achievement'], 1) }}%</p>
                    <p class="text-[11px] font-bold text-gray-500 uppercase mt-1">SLA On-Time</p>
                </x-card>
                <x-card class="relative flex flex-col justify-center items-center text-center transition-transform duration-300 hover:-translate-y-1 !p-4">
                    <div class="absolute top-2 right-2 group cursor-pointer">
                        <svg class="w-4 h-4 text-gray-300 hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute right-0 top-5 w-40 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                            Persentase armada kendaraan yang sedang aktif beroperasi dari total kendaraan.
                        </div>
                    </div>
                    <p class="text-3xl font-black text-indigo-600">{{ number_format($stats['fleet_utilization'], 0) }}%</p>
                    <p class="text-[11px] font-bold text-gray-500 uppercase mt-1">Fleet Utilized</p>
                    <p class="text-[10px] text-gray-400 mt-1">{{ $stats['active_fleet'] }} / {{ $stats['total_vehicles'] }}</p>
                </x-card>
            </div>
        </div>

        <!-- Warehouse Pillar -->
        <div class="space-y-4">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-200 pb-2">Warehouse & Orders</h3>
            <div class="grid grid-cols-2 gap-4 h-[148px]">
                <x-card class="relative flex flex-col justify-center items-center text-center transition-transform duration-300 hover:-translate-y-1 !p-4">
                    <div class="absolute top-2 right-2 group cursor-pointer">
                        <svg class="w-4 h-4 text-gray-300 hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute right-0 top-5 w-40 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                            Total kuantitas barang fisik di seluruh jaringan gudang.
                        </div>
                    </div>
                    <p class="text-3xl font-black text-orange-500">{{ number_format($stats['total_inventory']) }}</p>
                    <p class="text-[11px] font-bold text-gray-500 uppercase mt-1">Total Items</p>
                    <p class="text-[10px] text-gray-400 mt-1">{{ $stats['active_warehouses'] }} Warehouses</p>
                </x-card>
                <x-card class="relative flex flex-col justify-center items-center text-center transition-transform duration-300 hover:-translate-y-1 !p-4">
                    <div class="absolute top-2 right-2 group cursor-pointer">
                        <svg class="w-4 h-4 text-gray-300 hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="absolute right-0 top-5 w-40 p-2 bg-gray-800 text-white text-xs rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 font-normal text-left">
                            Jumlah pesanan aktif yang masih dalam proses pemenuhan atau belum selesai.
                        </div>
                    </div>
                    <p class="text-3xl font-black text-rose-500">{{ number_format($stats['pending_fulfillment']) }}</p>
                    <p class="text-[11px] font-bold text-gray-500 uppercase mt-1">Pending Orders</p>
                </x-card>
            </div>
        </div>
    </div>

    <!-- Layout Split -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
        <div class="xl:col-span-2">
            <x-card class="h-full min-h-[450px] flex flex-col">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-bold text-gray-800">Financial Trends (Last 7 Days)</h3>
                    <div class="flex gap-2">
                        <span class="flex items-center text-xs text-gray-500 font-medium before:w-3 before:h-3 before:bg-emerald-500 before:rounded-sm before:mr-2">Revenue</span>
                        <span class="flex items-center text-xs text-gray-500 font-medium before:w-3 before:h-3 before:bg-red-500 before:rounded-sm before:mr-2 ml-2">Expense</span>
                    </div>
                </div>
                <div class="flex-1 w-full rounded-3xl p-4 shadow-[inset_6px_6px_12px_#d1d5db,inset_-6px_-6px_12px_#ffffff] flex items-center justify-center text-gray-400 bg-gray-100">
                    <div class="w-full h-72">
                        <canvas id="adminChart"></canvas>
                    </div>
                </div>
            </x-card>
        </div>
        <div>
            <x-card class="h-full">
                <h3 class="text-xl font-bold text-gray-800 mb-8">Recent Activities</h3>
                <div class="space-y-8">
                    @forelse($recentActivities as $activity)
                        <div class="flex gap-5">
                            <div class="w-12 h-12 rounded-full shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] flex-shrink-0 flex items-center justify-center text-gray-600 bg-gray-100">
                                @if(str_contains(strtolower($activity->checkpoint_type), 'depart'))
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                                @elseif(str_contains(strtolower($activity->checkpoint_type), 'arriv'))
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800 leading-tight">
                                    {{ $activity->shipment->shipment_number ?? 'Shipment' }}: {{ $activity->checkpoint_type }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $activity->description }}</p>
                                <p class="text-xs font-medium text-gray-400 mt-1">{{ \Carbon\Carbon::parse($activity->recorded_at)->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-400 py-8">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p class="text-sm font-medium">No recent activities</p>
                        </div>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('adminChart');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [
                        {
                            label: 'Revenue',
                            data: {!! json_encode($revenueData) !!},
                            backgroundColor: '#10b981', // Emerald-500
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        },
                        {
                            label: 'Expense',
                            data: {!! json_encode($expenseData) !!},
                            backgroundColor: '#ef4444', // Red-500
                            borderRadius: 4,
                            barPercentage: 0.6,
                            categoryPercentage: 0.8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#1f2937',
                            bodyColor: '#4b5563',
                            borderColor: '#e5e7eb',
                            borderWidth: 1,
                            padding: 12,
                            boxPadding: 4,
                            usePointStyle: true,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#6b7280',
                                font: { family: "'Poppins', sans-serif", size: 11 },
                                callback: function(value) {
                                    return 'Rp ' + (value / 1000000) + 'M';
                                }
                            },
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)',
                                drawBorder: false,
                            }
                        },
                        x: {
                            ticks: {
                                color: '#6b7280',
                                font: { family: "'Poppins', sans-serif", size: 11 }
                            },
                            grid: { display: false, drawBorder: false }
                        }
                    }
                }
            });
        });
    </script>
@endsection
