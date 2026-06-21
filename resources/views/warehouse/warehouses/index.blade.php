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
        .geocode-status {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 6px;
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .geocode-status.idle { display: none; }
        .geocode-status.searching {
            color: #6366f1;
            background: #eef2ff;
            box-shadow: inset 1px 1px 3px #c7d2fe, inset -1px -1px 3px #ffffff;
        }
        .geocode-status.found {
            color: #059669;
            background: #ecfdf5;
            box-shadow: inset 1px 1px 3px #a7f3d0, inset -1px -1px 3px #ffffff;
        }
        .geocode-status.not-found {
            color: #d97706;
            background: #fffbeb;
            box-shadow: inset 1px 1px 3px #fde68a, inset -1px -1px 3px #ffffff;
        }
        .geocode-spinner {
            width: 12px; height: 12px;
            border: 2px solid #c7d2fe;
            border-top-color: #6366f1;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
@endpush

@section('title', 'Warehouse Management')

@section('content')
    <x-topbar />

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

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <p class="text-gray-500 text-lg font-medium">Manage warehouse locations and staff assignments.</p>
            <div class="flex items-center gap-3 shrink-0">
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

        <x-card class="mb-8">
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
                    @forelse($warehouses as $warehouse)
                        <tr class="border-b border-gray-200/50 hover:bg-gray-200/30 transition">
                            <td class="py-4 px-4 font-bold text-gray-800">{{ $warehouse->code }}</td>
                            <td class="py-4 px-4 font-bold">{{ $warehouse->name }}</td>
                            <td class="py-4 px-4 whitespace-normal min-w-[200px]">{{ $warehouse->address }}</td>
                            <td class="py-4 px-4">
                                @if($warehouse->users->count() > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($warehouse->users as $user)
                                            <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-blue-50 text-blue-700 shadow-[inset_1px_1px_2px_#d1d5db,inset_-1px_-1px_2px_#ffffff]">{{ $user->name }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">No staff</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-3 py-1 text-xs font-bold rounded-full shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] {{ $warehouse->is_active ? 'text-emerald-600' : 'text-red-500' }}">
                                    {{ $warehouse->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center justify-center gap-3">
                                    <button type="button" @click="$dispatch('open-edit', { id: '{{ $warehouse->id }}', code: '{{ $warehouse->code }}', name: '{{ $warehouse->name }}', address: '{{ str_replace(["\r", "\n"], ["", " "], $warehouse->address) }}', latitude: '{{ $warehouse->latitude }}', longitude: '{{ $warehouse->longitude }}', is_active: '{{ $warehouse->is_active }}', user_ids: {{ json_encode($warehouse->users->pluck('id')->toArray()) }} })" class="w-10 h-10 rounded-full flex items-center justify-center text-blue-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form id="delete-form-{{ $warehouse->id }}" action="{{ route('warehouse.warehouses.destroy', $warehouse->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('delete-form-{{ $warehouse->id }}')" class="w-10 h-10 rounded-full flex items-center justify-center text-red-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] hover:text-red-600 transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">No warehouses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            <div class="mt-4">
                {{ $warehouses->links() }}
            </div>
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
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Address</label>
                    <textarea id="create_address" name="address" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" rows="3" placeholder="Ketik alamat lengkap, peta akan otomatis mencari..."></textarea>
                    <div id="create-geocode-status" class="geocode-status idle"></div>
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
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Address</label>
                    <textarea id="edit_address" name="address" x-model="editData.address" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" rows="3" placeholder="Ketik alamat lengkap, peta akan otomatis mencari..."></textarea>
                    <div id="edit-geocode-status" class="geocode-status idle"></div>
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

        // ─── Nominatim Geocoding ──────────────────────────────────────────────────
        function geocodeAddress(address, mapInstance, placeMarkerFn, statusElId) {
            const statusEl = document.getElementById(statusElId);
            if (!address || address.trim().length < 5) {
                if (statusEl) { statusEl.className = 'geocode-status idle'; statusEl.innerHTML = ''; }
                return;
            }

            // Show searching state
            if (statusEl) {
                statusEl.className = 'geocode-status searching';
                statusEl.innerHTML = '<div class="geocode-spinner"></div> Mencari lokasi...';
            }

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&countrycodes=id&limit=1`, {
                headers: { 'Accept-Language': 'id' }
            })
            .then(res => res.json())
            .then(data => {
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);
                    const displayName = data[0].display_name;

                    placeMarkerFn(L.latLng(lat, lng));
                    mapInstance.setView([lat, lng], 15, { animate: true });

                    if (statusEl) {
                        statusEl.className = 'geocode-status found';
                        const shortName = displayName.length > 60 ? displayName.substring(0, 60) + '…' : displayName;
                        statusEl.innerHTML = `<svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Ditemukan: ${shortName}`;
                    }
                } else {
                    if (statusEl) {
                        statusEl.className = 'geocode-status not-found';
                        statusEl.innerHTML = '<svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Lokasi tidak ditemukan, klik peta secara manual';
                    }
                }
            })
            .catch(() => {
                if (statusEl) {
                    statusEl.className = 'geocode-status not-found';
                    statusEl.innerHTML = '<svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Gagal mencari, klik peta secara manual';
                }
            });
        }

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
                    const debouncedGeocode = debounce(function () {
                        geocodeAddress(addressEl.value, _createMapObj.map, _createMapObj.placeMarker, 'create-geocode-status');
                    }, 800);
                    addressEl.addEventListener('input', debouncedGeocode);
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
                    const debouncedGeocode = debounce(function () {
                        geocodeAddress(addressEl.value, _editMapObj.map, _editMapObj.placeMarker, 'edit-geocode-status');
                    }, 800);
                    addressEl.addEventListener('input', debouncedGeocode);
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
