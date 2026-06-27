@extends('layouts.warehouse')

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        .inline-map {
            height: 280px;
            width: 100%;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: inset 4px 4px 8px #d1d5db, inset -4px -4px 8px #ffffff;
        }
        .map-coords-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background: #f3f4f6;
            border-radius: 12px;
            box-shadow: inset 2px 2px 5px #d1d5db, inset -2px -2px 5px #ffffff;
            font-size: 0.78rem;
            font-weight: 600;
            color: #6b7280;
            font-family: monospace;
            margin-top: 8px;
        }
        .map-coords-badge.has-location { color: #2563eb; }
        .leaflet-container { font-family: inherit; }
        .suggestions-dropdown {
            position: absolute;
            z-index: 9999;
            width: 100%;
            background: #f3f4f6;
            border-radius: 1rem;
            box-shadow: 8px 8px 16px #d1d5db, -8px -8px 16px #ffffff;
            margin-top: 0.5rem;
            max-height: 250px;
            overflow-y: auto;
            display: none;
            border: 1px solid #e5e7eb;
        }
        .suggestions-dropdown.active {
            display: block;
        }
        .suggestion-item {
            padding: 12px 20px;
            cursor: pointer;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: background 0.15s;
        }
        .suggestion-item:last-child {
            border-bottom: none;
        }
        .suggestion-item:hover {
            background: rgba(229, 231, 235, 0.5);
        }
        .suggestion-icon {
            padding: 8px;
            border-radius: 9999px;
            background: #ffffff;
            box-shadow: inset 1px 1px 2px #d1d5db, inset -1px -1px 2px #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .suggestion-text {
            font-size: 0.875rem;
            color: #374151;
            font-weight: 700;
        }
        .suggestion-type {
            font-size: 0.75rem;
            color: #9ca3af;
            font-weight: 500;
            text-transform: uppercase;
        }
    </style>
@endpush

@section('title', 'Warehouse Management')

@section('content')
    <x-topbar />

    <div x-data="dataTable({
            endpoint: '/api/search/warehouses',
            initialData: {{ Js::from($initialData['data'] ?? []) }},
            initialMeta: {{ Js::from($initialData['meta'] ?? []) }}
        })" class="w-full">
        
        <div x-data="{
                slideOverOpen: {{ $errors->any() && !old('warehouse_id') ? 'true' : 'false' }},
                editSlideOverOpen: {{ $errors->any() && old('warehouse_id') ? 'true' : 'false' }},
                editData: {
                    id: '{{ old('warehouse_id') }}',
                    code: '{{ old('code') }}',
                    name: '{{ old('name') }}',
                    address: '{{ old('address') }}',
                    latitude: '{{ old('latitude') }}',
                    longitude: '{{ old('longitude') }}',
                    is_active: '{{ old('is_active', 1) }}',
                    user_ids: []
                }
             }"
             @open-edit.window="editData = $event.detail; editSlideOverOpen = true; $nextTick(() => initEditMap(editData.latitude, editData.longitude))"
             @keydown.escape.window="slideOverOpen = false; editSlideOverOpen = false">

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8">
                <p class="text-gray-500 text-lg font-medium">Manage warehouse locations and staff assignments.</p>
                <div class="flex flex-col lg:flex-row w-full lg:w-auto gap-3 shrink-0">
                    <a href="{{ route('warehouse.warehouses.export.excel') }}" class="flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-emerald-700 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db] transition-all hover:bg-emerald-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Excel
                    </a>
                    <a href="{{ route('warehouse.warehouses.export.pdf') }}" class="flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-red-600 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db] transition-all hover:bg-red-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                    <button @click="slideOverOpen = true; $nextTick(() => initCreateMap())" class="flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-bold text-gray-800 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Warehouse
                    </button>
                </div>
            </div>

            <x-search-filter-bar placeholder="Search warehouses by code, name, or address..." />

            <x-filter-modal title="Filter Warehouses">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                    <select x-model="filters.is_active" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Created From</label>
                    <input type="date" x-model="filters.date_from" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Created To</label>
                    <input type="date" x-model="filters.date_to" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
                </div>
            </x-filter-modal>

            <x-card class="mb-8 relative min-h-[400px]">
                <div x-show="loading" class="absolute inset-0 z-10 flex items-center justify-center bg-gray-100/80 backdrop-blur-sm rounded-[2rem]">
                    <div class="w-12 h-12 rounded-full border-4 border-gray-300 border-t-blue-500 animate-spin shadow-[0_0_15px_rgba(59,130,246,0.5)]"></div>
                </div>

                <h3 class="text-xl font-bold text-gray-800 mb-6">Warehouses</h3>
                <div class="overflow-x-auto pb-4">
                <table class="w-full text-left border-collapse min-w-max whitespace-nowrap">
                    <thead>
                        <tr class="border-b border-gray-300 text-gray-500 text-sm tracking-widest uppercase">
                            <th class="py-4 px-4 font-bold">Code</th>
                            <th class="py-4 px-4 font-bold">Name</th>
                            <th class="py-4 px-4 font-bold">Address</th>
                            <th class="py-4 px-4 font-bold">Assigned Staff</th>
                            <th class="py-4 px-4 font-bold">Status</th>
                            <th class="py-4 px-4 font-bold text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 font-medium">
                        <template x-for="warehouse in data" :key="warehouse.id">
                            <tr class="border-b border-gray-200/50 hover:bg-gray-200/30 transition">
                                <td class="py-4 px-4 font-bold text-gray-800" x-text="warehouse.code"></td>
                                <td class="py-4 px-4 font-bold" x-text="warehouse.name"></td>
                                <td class="py-4 px-4 whitespace-normal min-w-[200px]" x-text="warehouse.address"></td>
                                <td class="py-4 px-4">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff]"
                                          :class="warehouse.is_active ? 'text-emerald-600' : 'text-red-500'"
                                          x-text="warehouse.is_active ? 'Active' : 'Inactive'">
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center justify-center gap-3">
                                        <button type="button" @click="$dispatch('open-edit', { id: warehouse.id, code: warehouse.code, name: warehouse.name, address: warehouse.address.replace(/[\r\n]+/g, ' '), latitude: warehouse.latitude, longitude: warehouse.longitude, is_active: warehouse.is_active, user_ids: warehouse.users ? warehouse.users.map(u => u.id) : [] })" class="w-10 h-10 rounded-full flex items-center justify-center text-blue-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form :id="'delete-form-' + warehouse.id" :action="'/warehouse-panel/warehouses/' + warehouse.id" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" @click="confirmDelete('delete-form-' + warehouse.id)" class="w-10 h-10 rounded-full flex items-center justify-center text-red-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] hover:text-red-600 transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="data.length === 0" x-cloak>
                            <td colspan="6" class="py-8 text-center text-gray-400">No warehouses found.</td>
                        </tr>
                    </tbody>
                </table>
                </div>
                <x-pagination />
            </x-card>

        {{-- ==================== CREATE FORM SLIDE-OVER ==================== --}}
        <x-slide-over title="Create New Warehouse">
            <form action="{{ route('warehouse.warehouses.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Hidden inputs that hold the actual lat/lng values for submission --}}
                <input type="hidden" id="create_latitude" name="latitude" value="{{ old('latitude') }}">
                <input type="hidden" id="create_longitude" name="longitude" value="{{ old('longitude') }}">

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Code</label>
                    <x-input type="text" name="code" placeholder="WH-01" required />
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Name</label>
                    <x-input type="text" name="name" placeholder="Warehouse Jakarta" required />
                </div>
                <div class="relative">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Address</label>
                    <textarea id="create_address" name="address" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" rows="3" placeholder="Ketik alamat lengkap untuk mencari..."></textarea>
                    <div id="create_address_suggestions" class="suggestions-dropdown"></div>
                </div>

                {{-- Inline Map --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <svg class="inline w-4 h-4 mb-0.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Location - <span class="text-gray-400 font-normal">Click on the map to set warehouse position</span>
                    </label>
                    <div id="create-map" class="inline-map"></div>
                    <div id="create-coords-badge" class="map-coords-badge">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        <span id="create-coords-text">No location selected — click the map to pin</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                    <x-select name="is_active" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </x-select>
                </div>

                {{-- User Mapping --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Assign Staff</label>
                    <div class="bg-gray-100 rounded-2xl p-4 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] max-h-48 overflow-y-auto space-y-2">
                        @forelse($assignableUsers as $user)
                            <label class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-200/50 cursor-pointer transition">
                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">{{ $user->name }}</span>
                                <span class="text-xs text-gray-400 ml-auto">{{ $user->email }}</span>
                            </label>
                        @empty
                            <p class="text-sm text-gray-400 text-center py-2">No Staff Warehouse users found.</p>
                        @endforelse
                    </div>
                </div>

                <div class="pt-6 mt-6 border-t border-gray-300">
                    <button type="submit" class="w-full py-4 rounded-2xl font-bold text-gray-100 bg-gray-800 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#4b5563,inset_-2px_-2px_4px_#1f2937] transition-all uppercase tracking-widest">
                        Save Warehouse
                    </button>
                </div>
            </form>
        </x-slide-over>

        {{-- ==================== EDIT FORM SLIDE-OVER ==================== --}}
        <x-slide-over title="Edit Warehouse" model="editSlideOverOpen">
            <form :action="'{{ route('warehouse.warehouses.index') }}/' + editData.id" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="warehouse_id" x-model="editData.id">

                {{-- Hidden inputs for lat/lng --}}
                <input type="hidden" id="edit_latitude" name="latitude" x-model="editData.latitude">
                <input type="hidden" id="edit_longitude" name="longitude" x-model="editData.longitude">

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Code</label>
                    <input type="text" name="code" x-model="editData.code" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" />
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" x-model="editData.name" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" />
                </div>
                <div class="relative">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Address</label>
                    <textarea id="edit_address" name="address" x-model="editData.address" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" rows="3" placeholder="Ketik alamat lengkap untuk mencari..."></textarea>
                    <div id="edit_address_suggestions" class="suggestions-dropdown"></div>
                </div>

                {{-- Inline Map --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <svg class="inline w-4 h-4 mb-0.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Location - <span class="text-gray-400 font-normal">Drag the pin or click to reposition</span>
                    </label>
                    <div id="edit-map" class="inline-map"></div>
                    <div id="edit-coords-badge" class="map-coords-badge">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        <span id="edit-coords-text">Loading location...</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                    <select name="is_active" x-model="editData.is_active" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none appearance-none">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                {{-- User Mapping --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Assign Staff</label>
                    <div class="bg-gray-100 rounded-2xl p-4 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] max-h-48 overflow-y-auto space-y-2">
                        @forelse($assignableUsers as $user)
                            <label class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-gray-200/50 cursor-pointer transition">
                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" :checked="editData.user_ids && editData.user_ids.includes('{{ $user->id }}')" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">{{ $user->name }}</span>
                                <span class="text-xs text-gray-400 ml-auto">{{ $user->email }}</span>
                            </label>
                        @empty
                            <p class="text-sm text-gray-400 text-center py-2">No Staff Warehouse users found.</p>
                        @endforelse
                    </div>
                </div>

                <div class="pt-6 mt-6 border-t border-gray-300">
                    <button type="submit" class="w-full py-4 rounded-2xl font-bold text-gray-100 bg-gray-800 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#4b5563,inset_-2px_-2px_4px_#1f2937] transition-all uppercase tracking-widest">
                        Update Warehouse
                    </button>
                </div>
            </form>
        </x-slide-over>

    </div>

    @push('scripts')
    <script>
        // ─── Shared helper ───────────────────────────────────────────────────────
        function buildMap(containerId, latHiddenId, lngHiddenId, coordsBadgeId, coordsTextId, initLat, initLng) {
            const hasLocation = initLat && initLng && !isNaN(parseFloat(initLat)) && !isNaN(parseFloat(initLng));
            const center = hasLocation ? [parseFloat(initLat), parseFloat(initLng)] : [-2.5, 118.0];
            const zoom   = hasLocation ? 14 : 5;

            const map = L.map(containerId, { center, zoom, zoomControl: true });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 19,
            }).addTo(map);

            let marker = null;

            function setCoords(lat, lng) {
                const latEl = document.getElementById(latHiddenId);
                const lngEl = document.getElementById(lngHiddenId);
                const badge = document.getElementById(coordsBadgeId);
                const text  = document.getElementById(coordsTextId);

                const latVal = parseFloat(lat).toFixed(7);
                const lngVal = parseFloat(lng).toFixed(7);

                if (latEl) { latEl.value = latVal; latEl.dispatchEvent(new Event('input')); }
                if (lngEl) { lngEl.value = lngVal; lngEl.dispatchEvent(new Event('input')); }
                if (text)  { text.textContent = `${parseFloat(latVal).toFixed(6)}, ${parseFloat(lngVal).toFixed(6)}`; }
                if (badge) { badge.classList.add('has-location'); }
            }

            function placeMarker(latlng) {
                if (marker) {
                    marker.setLatLng(latlng);
                } else {
                    marker = L.marker(latlng, { draggable: true }).addTo(map);
                    marker.on('dragend', function (e) {
                        const pos = e.target.getLatLng();
                        setCoords(pos.lat, pos.lng);
                    });
                }
                setCoords(latlng.lat, latlng.lng);
            }

            // Place existing pin
            if (hasLocation) {
                placeMarker(L.latLng(parseFloat(initLat), parseFloat(initLng)));
            }

            // Click to place / move pin
            map.on('click', function (e) {
                placeMarker(e.latlng);
            });

            // Invalidate so tiles render correctly inside slide-over
            setTimeout(() => map.invalidateSize(), 100);

            // Return both the map and placeMarker so geocoding can use it
            return { map, placeMarker };
        }

        // ─── Location Search (API) ──────────────────────────────────────────────────
        function searchLocationSuggestions(address, dropdownId, onSelectCallback) {
            const dropdownEl = document.getElementById(dropdownId);
            
            if (!address || address.trim().length < 3) {
                if (dropdownEl) { dropdownEl.classList.remove('active'); dropdownEl.innerHTML = ''; }
                return;
            }

            // Show searching state (Omni-search style loader)
            if (dropdownEl) {
                dropdownEl.innerHTML = `
                    <div class="px-5 py-4 text-sm text-gray-500 flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>`;
                dropdownEl.classList.add('active');
            }

            fetch(`/api/locations/search?q=${encodeURIComponent(address)}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.length > 0) {
                    dropdownEl.innerHTML = '';
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'suggestion-item';
                        
                        let iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>';
                        let iconClass = 'text-gray-500';

                        if (item.type === 'warehouse') {
                            iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>';
                            iconClass = 'text-blue-500';
                        } else if (item.type === 'customer') {
                            iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>';
                            iconClass = 'text-emerald-500';
                        }
                        
                        div.innerHTML = `
                            <div class="suggestion-icon">
                                <svg class="w-4 h-4 ${iconClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24">${iconSvg}</svg>
                            </div>
                            <div>
                                <div class="suggestion-text">${item.name}</div>
                                <div class="suggestion-type">${item.type}</div>
                            </div>
                        `;
                        div.onclick = () => {
                            dropdownEl.classList.remove('active');
                            onSelectCallback(item);
                        };
                        dropdownEl.appendChild(div);
                    });
                } else {
                    if (dropdownEl) {
                        dropdownEl.innerHTML = `
                            <div class="px-5 py-4 text-sm font-medium text-gray-500 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> 
                                Lokasi tidak ditemukan
                            </div>`;
                    }
                }
            })
            .catch(() => {
                if (dropdownEl) {
                    dropdownEl.innerHTML = `
                        <div class="px-5 py-4 text-sm font-medium text-red-500 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> 
                            Gagal mencari lokasi
                        </div>`;
                }
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const createDropdown = document.getElementById('create_address_suggestions');
            const editDropdown = document.getElementById('edit_address_suggestions');
            const createInput = document.getElementById('create_address');
            const editInput = document.getElementById('edit_address');
            
            if (createDropdown && createInput && !createInput.contains(e.target) && !createDropdown.contains(e.target)) {
                createDropdown.classList.remove('active');
            }
            if (editDropdown && editInput && !editInput.contains(e.target) && !editDropdown.contains(e.target)) {
                editDropdown.classList.remove('active');
            }
        });

        // ─── Debounce utility ─────────────────────────────────────────────────────
        function debounce(fn, delay) {
            let timer = null;
            return function (...args) {
                clearTimeout(timer);
                timer = setTimeout(() => fn.apply(this, args), delay);
            };
        }

        // ─── Create form map ─────────────────────────────────────────────────────
        let _createMapObj = null;
        function initCreateMap() {
            if (_createMapObj) { _createMapObj.map.remove(); _createMapObj = null; }
            const initLat = document.getElementById('create_latitude')?.value || '';
            const initLng = document.getElementById('create_longitude')?.value || '';
            setTimeout(() => {
                _createMapObj = buildMap('create-map', 'create_latitude', 'create_longitude',
                                         'create-coords-badge', 'create-coords-text',
                                         initLat, initLng);

                // Attach geocoding to address textarea
                const addressEl = document.getElementById('create_address');
                if (addressEl) {
                    const debouncedSearch = debounce(function () {
                        searchLocationSuggestions(addressEl.value, 'create_address_suggestions', (item) => {
                            addressEl.value = item.name;
                            const lat = parseFloat(item.lat);
                            const lng = parseFloat(item.lng);
                            _createMapObj.placeMarker(L.latLng(lat, lng));
                            _createMapObj.map.setView([lat, lng], 15, { animate: true });
                        });
                    }, 500);
                    addressEl.addEventListener('input', debouncedSearch);
                    addressEl.addEventListener('focus', debouncedSearch);
                }
            }, 80);
        }

        // ─── Edit form map ───────────────────────────────────────────────────────
        let _editMapObj = null;
        function initEditMap(lat, lng) {
            if (_editMapObj) { _editMapObj.map.remove(); _editMapObj = null; }
            setTimeout(() => {
                _editMapObj = buildMap('edit-map', 'edit_latitude', 'edit_longitude',
                                       'edit-coords-badge', 'edit-coords-text',
                                       lat, lng);

                // Attach geocoding to address textarea
                const addressEl = document.getElementById('edit_address');
                if (addressEl) {
                    const debouncedSearch = debounce(function () {
                        searchLocationSuggestions(addressEl.value, 'edit_address_suggestions', (item) => {
                            addressEl.value = item.name;
                            addressEl.dispatchEvent(new Event('input')); // for Alpine.js x-model update
                            const l = parseFloat(item.lat);
                            const lg = parseFloat(item.lng);
                            _editMapObj.placeMarker(L.latLng(l, lg));
                            _editMapObj.map.setView([l, lg], 15, { animate: true });
                        });
                    }, 500);
                    addressEl.addEventListener('input', debouncedSearch);
                    addressEl.addEventListener('focus', debouncedSearch);
                }
            }, 80);
        }

        // ─── Auto-init if slide-over already open on page load (validation errors) ─
        document.addEventListener('DOMContentLoaded', function () {
            @if($errors->any() && !old('warehouse_id'))
                initCreateMap();
            @endif
            @if($errors->any() && old('warehouse_id'))
                initEditMap('{{ old('latitude') }}', '{{ old('longitude') }}');
            @endif
        });
    </script>
    @endpush

@endsection
