@extends('layouts.warehouse')

@section('title', 'Racks')

@section('content')
    <x-topbar />

    <div x-data="{ 
            slideOverOpen: false, 
            editSlideOverOpen: false,
            editData: { id: '', zone_id: '', name: '', description: '' }
         }" 
         @keydown.escape.window="slideOverOpen = false; editSlideOverOpen = false"
         @open-edit.window="editData = $event.detail; editSlideOverOpen = true">
        
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8">
            <div>
                <p class="text-gray-500 text-lg font-medium">Manage warehouse racks.</p>
            </div>
            <button @click="slideOverOpen = true" class="flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-bold text-gray-800 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-indigo-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                Add Rack
            </button>
        </div>

        <x-card class="mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Rack List</h3>
            <div class="overflow-x-auto pb-4">
                <table class="w-full text-left border-collapse min-w-max whitespace-nowrap">
                    <thead>
                        <tr class="border-b border-gray-300 text-gray-500 text-sm tracking-widest uppercase">
                            <th class="py-4 px-4 font-bold">Warehouse</th>
                            <th class="py-4 px-4 font-bold">Zone</th>
                            <th class="py-4 px-4 font-bold">Rack Name</th>
                            <th class="py-4 px-4 font-bold">Description</th>
                            <th class="py-4 px-4 font-bold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 font-medium">
                        @forelse($racks as $rack)
                            <tr class="border-b border-gray-200/50 hover:bg-gray-200/30 transition">
                                <td class="py-4 px-4">{{ $rack->zone->warehouse->name ?? '-' }}</td>
                                <td class="py-4 px-4 font-bold text-gray-700">{{ $rack->zone->name ?? '-' }}</td>
                                <td class="py-4 px-4 font-bold text-gray-800">{{ $rack->name }}</td>
                                <td class="py-4 px-4 text-sm text-gray-500">{{ $rack->description ?? '-' }}</td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center justify-center gap-3">
                                        <button type="button" @click="$dispatch('open-edit', { id: '{{ $rack->id }}', zone_id: '{{ $rack->zone_id }}', name: '{{ $rack->name }}', description: '{{ $rack->description }}' })" class="w-10 h-10 rounded-full flex items-center justify-center text-blue-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <form id="delete-form-{{ $rack->id }}" action="{{ route('warehouse.racks.destroy', $rack->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete('delete-form-{{ $rack->id }}')" class="w-10 h-10 rounded-full flex items-center justify-center text-red-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400">No racks found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $racks->links() }}
            </div>
        </x-card>

        <!-- Create Slide-Over -->
        <x-slide-over title="Add New Rack">
            <form action="{{ route('warehouse.racks.store') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Zone</label>
                    <select name="zone_id" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none appearance-none select2-enable">
                        <option value="">Select Zone</option>
                        @foreach($zones as $z)
                            <option value="{{ $z->id }}">{{ $z->warehouse->name ?? '' }} - {{ $z->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Rack Name</label>
                    <x-input type="text" name="name" required placeholder="e.g. A-01" />
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                    <textarea name="description" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" rows="3"></textarea>
                </div>
                <div class="pt-6 mt-6 border-t border-gray-300">
                    <button type="submit" class="w-full py-4 rounded-2xl font-bold text-gray-100 bg-indigo-600 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#3730a3,inset_-2px_-2px_4px_#312e81] transition-all uppercase tracking-widest hover:bg-indigo-700">
                        Save Rack
                    </button>
                </div>
            </form>
        </x-slide-over>

        <!-- Edit Slide-Over -->
        <template x-if="editSlideOverOpen">
            <div class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="editSlideOverOpen = false"></div>
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                        <div class="pointer-events-auto w-screen max-w-md transform transition-transform duration-500 ease-in-out bg-gray-100 shadow-2xl flex flex-col"
                             x-show="editSlideOverOpen"
                             x-transition:enter="translate-x-full"
                             x-transition:enter-end="translate-x-0"
                             x-transition:leave="translate-x-0"
                             x-transition:leave-end="translate-x-full">
                            
                            <div class="flex items-center justify-between px-6 py-6 border-b border-gray-300">
                                <h2 class="text-xl font-bold text-gray-800 tracking-wide" id="slide-over-title">Edit Rack</h2>
                                <button type="button" @click="editSlideOverOpen = false" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-red-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <div class="relative flex-1 px-6 py-6 overflow-y-auto">
                                <form :action="`{{ route('warehouse.racks.index') }}/${editData.id}`" method="POST" class="space-y-6">
                                    @csrf @method('PUT')
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Zone</label>
                                        <select name="zone_id" x-model="editData.zone_id" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none appearance-none">
                                            @foreach($zones as $z)
                                                <option value="{{ $z->id }}">{{ $z->warehouse->name ?? '' }} - {{ $z->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Rack Name</label>
                                        <input type="text" name="name" x-model="editData.name" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                                        <textarea name="description" x-model="editData.description" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" rows="3"></textarea>
                                    </div>
                                    <div class="pt-6 mt-6 border-t border-gray-300">
                                        <button type="submit" class="w-full py-4 rounded-2xl font-bold text-gray-100 bg-blue-600 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#1e3a8a,inset_-2px_-2px_4px_#1e40af] transition-all uppercase tracking-widest hover:bg-blue-700">
                                            Update Rack
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2-enable').each(function() {
            $(this).select2({
                width: '100%'
            });
        });
    });
</script>
@endpush
