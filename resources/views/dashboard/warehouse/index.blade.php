@extends('layouts.app')

@section('title', 'Warehouse Management')

@section('content')
    <x-topbar />

    <div class="mb-8">
        <p class="text-gray-500 text-lg font-medium">Monitor stock levels, putaway, and daily picking/packing tasks.</p>
    </div>

    <!-- Neumorphic grid structure specific to Warehouse -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff] lg:col-span-2">
            <h3 class="font-bold text-gray-700 text-lg mb-4">Inventory Status</h3>
            <div class="flex items-center justify-center h-48 rounded-2xl shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff]">
                <p class="text-gray-400">Loading stock table...</p>
            </div>
        </div>
        <div class="bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <h3 class="font-bold text-gray-700 text-lg mb-4">Quick Tasks</h3>
            <div class="flex flex-col gap-4">
                <button class="w-full py-3 rounded-2xl font-bold text-gray-600 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">Scan Barcode</button>
                <button class="w-full py-3 rounded-2xl font-bold text-gray-600 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">Record Putaway</button>
            </div>
        </div>
    </div>
@endsection
