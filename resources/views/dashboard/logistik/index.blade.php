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
        <div class="bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
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
        <div class="bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
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
        <div class="bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
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
        <div class="bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
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
        });
    </script>
@endsection
