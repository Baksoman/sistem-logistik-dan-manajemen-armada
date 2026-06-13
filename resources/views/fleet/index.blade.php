@extends('layouts.app')

@section('title', 'Fleet Management')

@section('content')
    <x-topbar />



    <div x-data="{ slideOverOpen: false, editSlideOverOpen: false, editData: {} }" 
         @open-edit.window="editData = $event.detail; editSlideOverOpen = true; $refs.editForm.action = '/fleet/' + editData.id"
         @keydown.escape.window="slideOverOpen = false; editSlideOverOpen = false">
         
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <p class="text-gray-500 text-lg font-medium">Manage vehicles, maintenance schedules, and assignments.</p>
            <button @click="slideOverOpen = true" class="flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-bold text-gray-800 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Vehicle
            </button>
        </div>

        <x-card class="mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Fleet List</h3>
            <div class="overflow-x-auto pb-4">
            <table class="w-full text-left border-collapse min-w-max whitespace-nowrap">
                <thead>
                    <tr class="border-b border-gray-300 text-gray-500 text-sm tracking-widest uppercase">
                        <th class="py-4 px-4 font-bold">Plate Number</th>
                        <th class="py-4 px-4 font-bold">Brand & Model</th>
                        <th class="py-4 px-4 font-bold">Type</th>
                        <th class="py-4 px-4 font-bold">Cap. (KG / CBM)</th>
                        <th class="py-4 px-4 font-bold">Fuel</th>
                        <th class="py-4 px-4 font-bold">KIR Exp.</th>
                        <th class="py-4 px-4 font-bold">STNK Exp.</th>
                        <th class="py-4 px-4 font-bold">Status</th>
                        <th class="py-4 px-4 font-bold text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 font-medium">
                    @forelse($vehicles as $vehicle)
                        <tr class="border-b border-gray-200/50 hover:bg-gray-200/30 transition">
                            <td class="py-4 px-4 font-bold tracking-widest">{{ $vehicle->plate_number }}</td>
                            <td class="py-4 px-4">{{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->year }})</td>
                            <td class="py-4 px-4">{{ $vehicle->vehicleType->name ?? 'N/A' }}</td>
                            <td class="py-4 px-4">{{ number_format($vehicle->capacity_kg, 0) }} KG / {{ $vehicle->capacity_volume_cbm }} CBM</td>
                            <td class="py-4 px-4">{{ $vehicle->fuel_type }}</td>
                            @php
                                $kirDate = \Carbon\Carbon::parse($vehicle->kir_expired_at);
                                $kirDiff = now()->diffInDays($kirDate, false);
                                if ($kirDiff < 0) {
                                    $kirCls = 'text-red-600 bg-red-100/50 shadow-[inset_1px_1px_2px_rgba(220,38,38,0.2),inset_-1px_-1px_2px_rgba(255,255,255,0.7)]';
                                    $kirIcn = 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
                                } elseif ($kirDiff < 30) {
                                    $kirCls = 'text-orange-600 bg-orange-100/50 shadow-[inset_1px_1px_2px_rgba(234,88,12,0.2),inset_-1px_-1px_2px_rgba(255,255,255,0.7)]';
                                    $kirIcn = 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z';
                                } else {
                                    $kirCls = 'text-emerald-600 bg-emerald-100/50 shadow-[inset_1px_1px_2px_rgba(5,150,105,0.2),inset_-1px_-1px_2px_rgba(255,255,255,0.7)]';
                                    $kirIcn = 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
                                }

                                $stnkDate = \Carbon\Carbon::parse($vehicle->stnk_expired_at);
                                $stnkDiff = now()->diffInDays($stnkDate, false);
                                if ($stnkDiff < 0) {
                                    $stnkCls = 'text-red-600 bg-red-100/50 shadow-[inset_1px_1px_2px_rgba(220,38,38,0.2),inset_-1px_-1px_2px_rgba(255,255,255,0.7)]';
                                    $stnkIcn = 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
                                } elseif ($stnkDiff < 30) {
                                    $stnkCls = 'text-orange-600 bg-orange-100/50 shadow-[inset_1px_1px_2px_rgba(234,88,12,0.2),inset_-1px_-1px_2px_rgba(255,255,255,0.7)]';
                                    $stnkIcn = 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z';
                                } else {
                                    $stnkCls = 'text-emerald-600 bg-emerald-100/50 shadow-[inset_1px_1px_2px_rgba(5,150,105,0.2),inset_-1px_-1px_2px_rgba(255,255,255,0.7)]';
                                    $stnkIcn = 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
                                }

                                $isActive = in_array($vehicle->status, ['active', 'available']);
                                $stsCls = $isActive ? 'text-emerald-600 bg-emerald-100/50 shadow-[inset_2px_2px_4px_rgba(5,150,105,0.2),inset_-2px_-2px_4px_rgba(255,255,255,0.7)]' : 
                                         ($vehicle->status === 'maintenance' ? 'text-orange-600 bg-orange-100/50 shadow-[inset_2px_2px_4px_rgba(234,88,12,0.2),inset_-2px_-2px_4px_rgba(255,255,255,0.7)]' : 
                                         'text-gray-500 bg-gray-200/50 shadow-[inset_2px_2px_4px_rgba(156,163,175,0.2),inset_-2px_-2px_4px_rgba(255,255,255,0.7)]');
                                $stsIcn = $isActive ? 'M5 13l4 4L19 7' : 
                                         ($vehicle->status === 'maintenance' ? 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' : 
                                         'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636');
                            @endphp
                            <td class="py-4 px-4">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl font-bold text-xs tracking-wider {{ $kirCls }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kirIcn }}"></path></svg>
                                    {{ $kirDate->format('d M Y') }}
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl font-bold text-xs tracking-wider {{ $stnkCls }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stnkIcn }}"></path></svg>
                                    {{ $stnkDate->format('d M Y') }}
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full uppercase tracking-widest text-xs font-bold {{ $stsCls }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stsIcn }}"></path></svg>
                                    {{ str_replace('_', ' ', $vehicle->status) }}
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center justify-center gap-3">
                                    <button type="button" @click="$dispatch('open-edit', { id: '{{ $vehicle->id }}', vehicle_type_id: '{{ $vehicle->vehicle_type_id }}', plate_number: '{{ $vehicle->plate_number }}', brand: '{{ $vehicle->brand }}', model: '{{ $vehicle->model }}', year: '{{ $vehicle->year }}', capacity_kg: '{{ $vehicle->capacity_kg }}', capacity_volume_cbm: '{{ $vehicle->capacity_volume_cbm }}', fuel_type: '{{ $vehicle->fuel_type }}', status: '{{ $vehicle->status }}', kir_expired_at: '{{ $vehicle->kir_expired_at }}', stnk_expired_at: '{{ $vehicle->stnk_expired_at }}' })" class="w-10 h-10 rounded-full flex items-center justify-center text-blue-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form id="delete-form-{{ $vehicle->id }}" action="{{ route('fleet.destroy', $vehicle->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('delete-form-{{ $vehicle->id }}', 'Hapus kendaraan ini?')" class="w-10 h-10 rounded-full flex items-center justify-center text-red-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] hover:text-red-600 transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-400">No vehicles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            <div class="mt-4">
                {{ $vehicles->links() }}
            </div>
        </x-card>

        <!-- Create Slide-Over -->
        <x-slide-over title="Register Vehicle">
            <form action="{{ route('fleet.store') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Vehicle Type</label>
                    <x-select name="vehicle_type_id" required>
                        <option value="">Select a Type</option>
                        @foreach($vehicleTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div><label class="block text-sm font-bold text-gray-700 mb-2">License Plate</label><x-input type="text" name="plate_number" required /></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-2">Brand</label><x-input type="text" name="brand" required /></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-2">Model</label><x-input type="text" name="model" required /></div>
                </div>
                <div><label class="block text-sm font-bold text-gray-700 mb-2">Year</label><x-input type="number" name="year" required /></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-2">Cap (KG)</label><x-input type="number" step="0.01" name="capacity_kg" required /></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-2">Vol (CBM)</label><x-input type="number" step="0.01" name="capacity_volume_cbm" required /></div>
                </div>
                <div><label class="block text-sm font-bold text-gray-700 mb-2">Fuel Type</label><x-input type="text" name="fuel_type" required /></div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                    <x-select name="status" required>
                        <option value="available">Available</option>
                        <option value="on_trip">On Trip</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="inactive">Inactive</option>
                    </x-select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-2">KIR Exp</label><x-input type="date" name="kir_expired_at" required /></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-2">STNK Exp</label><x-input type="date" name="stnk_expired_at" required /></div>
                </div>
                <div class="pt-6 mt-6 border-t border-gray-300">
                    <button type="submit" class="w-full py-4 rounded-2xl font-bold text-gray-100 bg-gray-800 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#4b5563] transition-all uppercase tracking-widest">Save</button>
                </div>
            </form>
        </x-slide-over>

        <!-- Edit Slide-Over -->
        <div x-show="editSlideOverOpen" class="fixed inset-0 z-50 overflow-hidden" x-cloak>
            <div x-show="editSlideOverOpen" x-transition.opacity class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="editSlideOverOpen = false"></div>
            <div class="fixed inset-y-0 right-0 max-w-md w-full flex">
                <div x-show="editSlideOverOpen" x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="w-full h-full bg-gray-100 flex flex-col shadow-[-12px_0_24px_rgba(0,0,0,0.1)]">
                    <div class="flex items-center justify-between px-8 py-6 shrink-0 shadow-[0_4px_6px_-1px_#d1d5db,0_2px_4px_-1px_#ffffff] z-10 bg-gray-100">
                        <h2 class="text-xl font-bold text-gray-800 tracking-tight">Edit Vehicle</h2>
                        <button type="button" @click="editSlideOverOpen = false" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 hover:text-red-500 bg-gray-100 shadow-[3px_3px_6px_#d1d5db,-3px_-3px_6px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db] focus:outline-none"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                    </div>
                    <div class="flex-1 overflow-y-auto px-8 py-8 z-0">
                        <form x-ref="editForm" method="POST" class="space-y-6">
                            @csrf @method('PUT')
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Vehicle Type</label>
                                <x-select name="vehicle_type_id" x-model="editData.vehicle_type_id" required>
                                    @foreach($vehicleTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </x-select>
                            </div>
                            <div><label class="block text-sm font-bold text-gray-700 mb-2">License Plate</label><input type="text" name="plate_number" x-model="editData.plate_number" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" /></div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="block text-sm font-bold text-gray-700 mb-2">Brand</label><input type="text" name="brand" x-model="editData.brand" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" /></div>
                                <div><label class="block text-sm font-bold text-gray-700 mb-2">Model</label><input type="text" name="model" x-model="editData.model" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" /></div>
                            </div>
                            <div><label class="block text-sm font-bold text-gray-700 mb-2">Year</label><input type="number" name="year" x-model="editData.year" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" /></div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="block text-sm font-bold text-gray-700 mb-2">Cap (KG)</label><input type="number" step="0.01" name="capacity_kg" x-model="editData.capacity_kg" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" /></div>
                                <div><label class="block text-sm font-bold text-gray-700 mb-2">Vol (CBM)</label><input type="number" step="0.01" name="capacity_volume_cbm" x-model="editData.capacity_volume_cbm" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" /></div>
                            </div>
                            <div><label class="block text-sm font-bold text-gray-700 mb-2">Fuel Type</label><input type="text" name="fuel_type" x-model="editData.fuel_type" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" /></div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                                <x-select name="status" x-model="editData.status" required>
                                    <option value="available">Available</option>
                                    <option value="on_trip">On Trip</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="inactive">Inactive</option>
                                </x-select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="block text-sm font-bold text-gray-700 mb-2">KIR Exp</label><input type="date" name="kir_expired_at" x-model="editData.kir_expired_at" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" /></div>
                                <div><label class="block text-sm font-bold text-gray-700 mb-2">STNK Exp</label><input type="date" name="stnk_expired_at" x-model="editData.stnk_expired_at" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" /></div>
                            </div>
                            <div class="pt-6 mt-6 border-t border-gray-300">
                                <button type="submit" class="w-full py-4 rounded-2xl font-bold text-gray-100 bg-gray-800 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#4b5563] transition-all uppercase tracking-widest">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
