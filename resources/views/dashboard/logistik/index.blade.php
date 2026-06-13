@extends('layouts.app')

@section('title', 'Logistics & Fleet Command Center')

@section('content')
    <x-topbar />

    <div class="mb-8">
        <p class="text-gray-500 text-lg font-medium">Manage shipments, assign drivers, and optimize routing here.</p>
    </div>

    <!-- Neumorphic grid structure specific to Logistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <h3 class="font-bold text-gray-700 text-lg mb-4">Pending Shipments</h3>
            <div class="flex items-center justify-center h-32 rounded-2xl shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff]">
                <p class="text-gray-400">Loading order data...</p>
            </div>
        </div>
        <div class="bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <h3 class="font-bold text-gray-700 text-lg mb-4">Active Fleet</h3>
            <div class="flex items-center justify-center h-32 rounded-2xl shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff]">
                <p class="text-gray-400">Loading map route...</p>
            </div>
        </div>
    </div>
@endsection
