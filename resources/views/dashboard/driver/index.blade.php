@extends('layouts.driver')

@section('title', 'Driver Workspace')

@section('content')
    <x-topbar />

    <div class="mb-6">
        <p class="text-gray-500 text-lg font-medium">Hello, stay safe on the road today.</p>
    </div>

    <!-- Mobile-first UI for Driver -->
    <div class="flex flex-col gap-8 max-w-lg mx-auto">
        <div class="bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <h3 class="font-bold text-gray-700 text-lg mb-4">Active Route</h3>
            <div class="flex items-center justify-center h-48 rounded-2xl shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff]">
                <p class="text-gray-400">Loading map navigation...</p>
            </div>
            
            <button class="w-full mt-6 py-4 rounded-full font-bold text-gray-100 bg-gray-800 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff] active:shadow-[inset_4px_4px_8px_#4b5563,inset_-4px_-4px_8px_#1f2937] transition-all tracking-widest uppercase">
                Start Trip
            </button>
        </div>
        
        <div class="bg-gray-100 rounded-3xl p-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]">
            <h3 class="font-bold text-gray-700 text-lg mb-4">Digital POD</h3>
            <button class="w-full flex items-center justify-center gap-3 py-4 rounded-2xl font-bold text-gray-600 bg-gray-100 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] hover:shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] transition-all border border-dashed border-gray-400">
                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Upload Photo Evidence
            </button>
        </div>
    </div>
@endsection
