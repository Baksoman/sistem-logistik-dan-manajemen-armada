@extends('layouts.logistik')

@section('title', 'Edit Tariff')

@section('content')
    <x-topbar />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('tariffs.index') }}" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <p class="text-gray-500 text-lg font-medium">Tariff Management</p>
                <h1 class="text-2xl font-black text-gray-800 tracking-wider">Edit Tariff</h1>
            </div>
        </div>
    </div>



    <form action="{{ route('tariffs.update', $tariff->id) }}" method="POST" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @csrf
        @method('PUT')
        
        <x-card class="space-y-6">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3">Scope Configuration</h3>
            
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Target Master Route (Optional)</label>
                <select name="route_id" class="w-full bg-gray-100 rounded-xl px-4 py-3 font-bold text-gray-700 border-none focus:ring-2 focus:ring-blue-500 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] appearance-none">
                    <option value="">-- Apply to All Direct Deliveries --</option>
                    @foreach($routes as $route)
                        <option value="{{ $route->id }}" {{ old('route_id', $tariff->route_id) == $route->id ? 'selected' : '' }}>
                            {{ $route->route_code }} : {{ $route->origin_name }} -> {{ $route->destination_name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-2">If left blank, this tariff acts as the generic formula for all Direct Deliveries.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Vehicle Type (Optional)</label>
                <select name="vehicle_type_id" class="w-full bg-gray-100 rounded-xl px-4 py-3 font-bold text-gray-700 border-none focus:ring-2 focus:ring-blue-500 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] appearance-none">
                    <option value="">-- Apply to All Vehicles --</option>
                    @foreach($vehicleTypes as $type)
                        <option value="{{ $type->id }}" {{ old('vehicle_type_id', $tariff->vehicle_type_id) == $type->id ? 'selected' : '' }}>
                            {{ $type->name }} (Cap: {{ $type->max_weight_kg }}kg)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $tariff->is_active) ? 'checked' : '' }} class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500 border-gray-300 shadow-sm">
                <label for="is_active" class="font-bold text-gray-700">Set as Active</label>
            </div>
        </x-card>

        <x-card class="space-y-6">
            <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3">Pricing Formula</h3>
            
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Fixed Base Price (Rp)</label>
                <input type="number" step="0.01" name="fixed_price" value="{{ old('fixed_price', $tariff->fixed_price) }}" required class="w-full bg-gray-100 rounded-xl px-4 py-3 font-mono font-bold text-gray-700 border-none focus:ring-2 focus:ring-blue-500 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff]">
                <p class="text-xs text-gray-400 mt-2">Flat rate applied before any per-km or per-kg calculation.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Price per Kilometer (Rp / KM)</label>
                <input type="number" step="0.01" name="price_per_km" value="{{ old('price_per_km', $tariff->price_per_km) }}" required class="w-full bg-gray-100 rounded-xl px-4 py-3 font-mono font-bold text-gray-700 border-none focus:ring-2 focus:ring-blue-500 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff]">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Price per KG (Rp / KG)</label>
                    <input type="number" step="0.01" name="price_per_kg" value="{{ old('price_per_kg', $tariff->price_per_kg) }}" required class="w-full bg-gray-100 rounded-xl px-4 py-3 font-mono font-bold text-gray-700 border-none focus:ring-2 focus:ring-blue-500 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Price per CBM (Rp)</label>
                    <input type="number" step="0.01" name="price_per_cbm" value="{{ old('price_per_cbm', $tariff->price_per_cbm) }}" required class="w-full bg-gray-100 rounded-xl px-4 py-3 font-mono font-bold text-gray-700 border-none focus:ring-2 focus:ring-blue-500 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff]">
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full py-4 bg-blue-600 text-white font-black rounded-2xl shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:bg-blue-700 transition-all uppercase tracking-widest">
                    Update Tariff Configuration
                </button>
            </div>
        </x-card>

    </form>
@endsection
