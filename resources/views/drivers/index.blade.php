@extends('layouts.logistik')

@section('title', 'Driver Management')

@section('content')
    <x-topbar />



    <div x-data="{ 
            slideOverOpen: {{ $errors->any() && !old('driver_id') ? 'true' : 'false' }}, 
            editSlideOverOpen: {{ $errors->any() && old('driver_id') ? 'true' : 'false' }}, 
            editData: { 
                id: '{{ old('driver_id') }}', 
                user_id: '{{ old('user_id') }}', 
                nik: '{{ old('nik') }}', 
                phone: '{{ old('phone') }}', 
                address: '{{ addslashes(old('address')) }}', 
                license_number: '{{ old('license_number') }}', 
                license_type: '{{ old('license_type') }}', 
                license_expired_at: '{{ old('license_expired_at') }}', 
                status: '{{ old('status') }}', 
                joined_at: '{{ old('joined_at') }}' 
            } 
         }" 
         @open-edit.window="editData = $event.detail; editSlideOverOpen = true;"
         @keydown.escape.window="slideOverOpen = false; editSlideOverOpen = false">
         
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <p class="text-gray-500 text-lg font-medium">Manage driver records, licenses, and operations.</p>
            <div class="flex flex-col sm:flex-row w-full sm:w-auto gap-3 shrink-0">
                <a href="{{ route('drivers.export.excel') }}" class="flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-emerald-700 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db] transition-all hover:bg-emerald-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Excel
                </a>
                <a href="{{ route('drivers.export.pdf') }}" class="flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-red-600 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db] transition-all hover:bg-red-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    PDF
                </a>
                <button @click="slideOverOpen = true" class="flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-bold text-gray-800 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Driver
                </button>
            </div>
        </div>

        <x-card class="mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Driver Roster</h3>
            <div class="overflow-x-auto pb-4">
            <table class="w-full text-left border-collapse min-w-max whitespace-nowrap">
                <thead>
                    <tr class="border-b border-gray-300 text-gray-500 text-sm tracking-widest uppercase">
                        <th class="py-4 px-4 font-bold">User/Name</th>
                        <th class="py-4 px-4 font-bold">NIK</th>
                        <th class="py-4 px-4 font-bold">Phone</th>
                        <th class="py-4 px-4 font-bold">License (SIM)</th>
                        <th class="py-4 px-4 font-bold">SIM Expiry</th>
                        <th class="py-4 px-4 font-bold">Status</th>
                        <th class="py-4 px-4 font-bold text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 font-medium">
                    @forelse($drivers as $driver)
                        <tr class="border-b border-gray-200/50 hover:bg-gray-200/30 transition">
                            <td class="py-4 px-4">{{ $driver->user->name ?? 'N/A' }}</td>
                            <td class="py-4 px-4">{{ $driver->nik }}</td>
                            <td class="py-4 px-4">{{ $driver->phone }}</td>
                            <td class="py-4 px-4">
                                <span class="font-bold text-gray-800 mr-2">{{ $driver->license_type }}</span>
                                {{ $driver->license_number }}
                            </td>
                            @php
                                $simDate = \Carbon\Carbon::parse($driver->license_expired_at);
                                $simDiff = now()->diffInDays($simDate, false);
                                if ($simDiff < 0) {
                                    $simCls = 'text-red-600 bg-red-100/50 shadow-[inset_1px_1px_2px_rgba(220,38,38,0.2),inset_-1px_-1px_2px_rgba(255,255,255,0.7)]';
                                    $simIcn = 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
                                } elseif ($simDiff < 30) {
                                    $simCls = 'text-orange-600 bg-orange-100/50 shadow-[inset_1px_1px_2px_rgba(234,88,12,0.2),inset_-1px_-1px_2px_rgba(255,255,255,0.7)]';
                                    $simIcn = 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z';
                                } else {
                                    $simCls = 'text-emerald-600 bg-emerald-100/50 shadow-[inset_1px_1px_2px_rgba(5,150,105,0.2),inset_-1px_-1px_2px_rgba(255,255,255,0.7)]';
                                    $simIcn = 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
                                }

                                $stsCls = $driver->status === 'available' ? 'text-emerald-600 bg-emerald-100/50 shadow-[inset_2px_2px_4px_rgba(5,150,105,0.2),inset_-2px_-2px_4px_rgba(255,255,255,0.7)]' : 
                                         ($driver->status === 'on_trip' ? 'text-blue-600 bg-blue-100/50 shadow-[inset_2px_2px_4px_rgba(37,99,235,0.2),inset_-2px_-2px_4px_rgba(255,255,255,0.7)]' : 
                                         'text-gray-500 bg-gray-200/50 shadow-[inset_2px_2px_4px_rgba(156,163,175,0.2),inset_-2px_-2px_4px_rgba(255,255,255,0.7)]');
                                
                                $stsIcn = $driver->status === 'available' ? 'M5 13l4 4L19 7' : 
                                         ($driver->status === 'on_trip' ? 'M13 5l7 7-7 7M5 5l7 7-7 7' : 
                                         'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636');
                            @endphp
                            <td class="py-4 px-4">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl font-bold text-xs tracking-wider {{ $simCls }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $simIcn }}"></path></svg>
                                    {{ $simDate->format('d M Y') }}
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full uppercase tracking-widest text-xs font-bold {{ $stsCls }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stsIcn }}"></path></svg>
                                    {{ str_replace('_', ' ', $driver->status) }}
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center justify-center gap-3">
                                    <button type="button" @click="$dispatch('open-edit', { id: '{{ $driver->id }}', user_id: '{{ $driver->user_id }}', nik: '{{ $driver->nik }}', phone: '{{ $driver->phone }}', address: '{{ addslashes($driver->address) }}', license_number: '{{ $driver->license_number }}', license_type: '{{ $driver->license_type }}', license_expired_at: '{{ $driver->license_expired_at ? \Carbon\Carbon::parse($driver->license_expired_at)->format('Y-m-d') : '' }}', status: '{{ $driver->status }}', joined_at: '{{ $driver->joined_at ? \Carbon\Carbon::parse($driver->joined_at)->format('Y-m-d') : '' }}' })" class="w-10 h-10 rounded-full flex items-center justify-center text-blue-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form id="delete-form-{{ $driver->id }}" action="{{ route('drivers.destroy', $driver->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('delete-form-{{ $driver->id }}', 'Hapus profil driver ini?')" class="w-10 h-10 rounded-full flex items-center justify-center text-red-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] hover:text-red-600 transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400">No driver profiles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            <div class="mt-4">
                {{ $drivers->links() }}
            </div>
        </x-card>

        <!-- Create Slide-Over -->
        <x-slide-over title="Register Driver">
            <form action="{{ route('drivers.store') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Linked User Account</label>
                    <x-select name="user_id" required>
                        <option value="">-- Assign a User --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </x-select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-2">NIK</label><x-input type="text" name="nik" value="{{ old('nik') }}" required /></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-2">Phone Number</label><x-input type="text" name="phone" value="{{ old('phone') }}" required /></div>
                </div>
                <div><label class="block text-sm font-bold text-gray-700 mb-2">Address</label><x-input type="text" name="address" value="{{ old('address') }}" required /></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-bold text-gray-700 mb-2">License No</label><x-input type="text" name="license_number" value="{{ old('license_number') }}" required /></div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Type</label>
                        <x-select name="license_type" required>
                            <option value="A" {{ old('license_type') == 'A' ? 'selected' : '' }}>SIM A</option>
                            <option value="B1" {{ old('license_type') == 'B1' ? 'selected' : '' }}>SIM B1</option>
                            <option value="B2" {{ old('license_type') == 'B2' ? 'selected' : '' }}>SIM B2</option>
                        </x-select>
                    </div>
                </div>
                <div><label class="block text-sm font-bold text-gray-700 mb-2">License Expired At</label><x-input type="date" name="license_expired_at" value="{{ old('license_expired_at') }}" required /></div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                        <x-select name="status" required>
                            <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="on_trip" {{ old('status') == 'on_trip' ? 'selected' : '' }}>On Trip</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </x-select>
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-2">Joined At</label><x-input type="date" name="joined_at" value="{{ old('joined_at') }}" required /></div>
                </div>
                <div class="pt-6 mt-6 border-t border-gray-300">
                    <button type="submit" class="w-full py-4 rounded-2xl font-bold text-gray-100 bg-gray-800 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#4b5563] transition-all uppercase tracking-widest">Register Driver</button>
                </div>
            </form>
        </x-slide-over>

        <!-- Edit Driver Slide-Over -->
        <div x-show="editSlideOverOpen" class="fixed inset-0 z-50 overflow-hidden" x-cloak>
            <div x-show="editSlideOverOpen" x-transition.opacity class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="editSlideOverOpen = false"></div>
            <div class="fixed inset-y-0 right-0 max-w-md w-full flex">
                <div x-show="editSlideOverOpen" x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="w-full h-full bg-gray-100 flex flex-col shadow-[-12px_0_24px_rgba(0,0,0,0.1)]">
                    <div class="flex items-center justify-between px-8 py-6 shrink-0 shadow-[0_4px_6px_-1px_#d1d5db,0_2px_4px_-1px_#ffffff] z-10 bg-gray-100">
                        <h2 class="text-xl font-bold text-gray-800 tracking-tight">Edit Driver</h2>
                        <button type="button" @click="editSlideOverOpen = false" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 hover:text-red-500 bg-gray-100 shadow-[3px_3px_6px_#d1d5db,-3px_-3px_6px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db] focus:outline-none"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                    </div>
                    <div class="flex-1 overflow-y-auto px-8 py-8 z-0">
                        <form :action="'{{ route('drivers.index') }}/' + editData.id" method="POST" class="space-y-6">
                            @csrf @method('PUT')
                            <input type="hidden" name="driver_id" x-model="editData.id">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Linked User Account</label>
                                <!-- Cannot edit user_id safely easily without breaking relations, but let's allow it as requested -->
                                <x-select name="user_id" x-model="editData.user_id" required>
                                    <option value="">-- Assign a User --</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                    @endforeach
                                    <option :value="editData.user_id" selected x-text="'(Current User ID: ' + editData.user_id.substring(0,8) + '...)'"></option>
                                </x-select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="block text-sm font-bold text-gray-700 mb-2">NIK</label><input type="text" name="nik" x-model="editData.nik" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" /></div>
                                <div><label class="block text-sm font-bold text-gray-700 mb-2">Phone</label><input type="text" name="phone" x-model="editData.phone" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" /></div>
                            </div>
                            <div><label class="block text-sm font-bold text-gray-700 mb-2">Address</label><input type="text" name="address" x-model="editData.address" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" /></div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="block text-sm font-bold text-gray-700 mb-2">License No</label><input type="text" name="license_number" x-model="editData.license_number" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" /></div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Type</label>
                                    <x-select name="license_type" x-model="editData.license_type" required>
                                        <option value="A">SIM A</option><option value="B1">SIM B1</option><option value="B2">SIM B2</option>
                                    </x-select>
                                </div>
                            </div>
                            <div><label class="block text-sm font-bold text-gray-700 mb-2">License Expired</label><input type="date" name="license_expired_at" x-model="editData.license_expired_at" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" /></div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                                    <x-select name="status" x-model="editData.status" required>
                                        <option value="available">Available</option><option value="on_trip">On Trip</option><option value="inactive">Inactive</option>
                                    </x-select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Joined At</label>
                                    <input type="date" name="joined_at" x-model="editData.joined_at" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" />
                                </div>
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
