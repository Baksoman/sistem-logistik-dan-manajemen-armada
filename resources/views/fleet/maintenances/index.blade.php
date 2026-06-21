@extends('layouts.logistik')

@section('title', 'Maintenance Management')

@section('content')
        <div x-data="{ 
            slideOverOpen: {{ $errors->any() && !old('maintenance_id') ? 'true' : 'false' }}, 
            editSlideOverOpen: {{ $errors->any() && old('maintenance_id') ? 'true' : 'false' }},
            detailModalOpen: false,
            editData: { 
                id: '{{ old('maintenance_id') }}', 
                vehicle_id: '{{ old('vehicle_id') }}', 
                maintenance_type: '{{ addslashes(old('maintenance_type')) }}', 
                status: '{{ old('status') }}', 
                cost: '{{ old('cost') }}', 
                scheduled_date: '{{ old('scheduled_date') }}', 
                completed_date: '{{ old('completed_date') }}', 
                next_maintenance_date: '{{ old('next_maintenance_date') }}', 
                description: '{{ str_replace(["\r", "\n"], ["\\r", "\\n"], addslashes(old('description'))) }}' 
            },
            detailData: { vehicle_plate: '', brand_model: '', maintenance_type: '', status: '', cost: '', scheduled_date: '', completed_date: '', next_maintenance_date: '', description: '' },
            openEdit(data) {
                this.editData = data;
                this.editSlideOverOpen = true;
            },
            openDetail(data) {
                this.detailData = data;
                this.detailModalOpen = true;
            }
        }" 
        @keydown.escape.window="slideOverOpen = false; editSlideOverOpen = false; detailModalOpen = false">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-black text-gray-800 tracking-tight mb-2">Vehicle Maintenance</h2>
                <p class="text-gray-500 text-lg font-medium">Manage and track fleet maintenance schedules and records.</p>
            </div>
            <button @click="slideOverOpen = true" class="flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-bold text-gray-800 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-600 shrink-0 uppercase tracking-widest">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Record Maintenance
            </button>
        </div>

        <x-card class="mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Maintenance Log</h3>
            <div class="overflow-x-auto pb-4">
            <table class="w-full text-left border-collapse min-w-max whitespace-nowrap">
                <thead>
                    <tr class="border-b border-gray-300 text-gray-500 text-sm tracking-widest uppercase">
                        <th class="py-4 px-4 font-bold">Vehicle Plate</th>
                        <th class="py-4 px-4 font-bold">Maintenance Type</th>
                        <th class="py-4 px-4 font-bold">Status</th>
                        <th class="py-4 px-4 font-bold">Cost (IDR)</th>
                        <th class="py-4 px-4 font-bold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse($maintenances as $maintenance)
                        <tr class="border-b border-gray-200/50 hover:bg-gray-200/30 transition-colors">
                            <td class="py-4 px-4 font-bold tracking-widest">{{ $maintenance->vehicle->plate_number }}</td>
                            <td class="py-4 px-4 font-medium">{{ $maintenance->maintenance_type }}</td>
                            <td class="py-4 px-4">
                                @if($maintenance->status === 'Completed')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold text-emerald-700 bg-emerald-100/50 rounded-xl shadow-[inset_1px_1px_2px_rgba(5,150,105,0.2),inset_-1px_-1px_2px_rgba(255,255,255,0.7)] uppercase tracking-wider">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        Completed
                                    </span>
                                @elseif($maintenance->status === 'In Progress')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold text-blue-700 bg-blue-100/50 rounded-xl shadow-[inset_1px_1px_2px_rgba(29,78,216,0.2),inset_-1px_-1px_2px_rgba(255,255,255,0.7)] uppercase tracking-wider">
                                        <svg class="w-3 h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        In Progress
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold text-orange-700 bg-orange-100/50 rounded-xl shadow-[inset_1px_1px_2px_rgba(194,65,12,0.2),inset_-1px_-1px_2px_rgba(255,255,255,0.7)] uppercase tracking-wider">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Scheduled
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 font-mono font-bold text-gray-800">{{ $maintenance->cost ? 'Rp ' . number_format($maintenance->cost, 0, ',', '.') : '-' }}</td>
                            <td class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <button type="button" @click="openDetail({
                                        vehicle_plate: '{{ $maintenance->vehicle->plate_number }}',
                                        brand_model: '{{ $maintenance->vehicle->brand }} {{ $maintenance->vehicle->model }} ({{ $maintenance->vehicle->year }})',
                                        maintenance_type: '{{ addslashes($maintenance->maintenance_type) }}',
                                        status: '{{ $maintenance->status }}',
                                        cost: '{{ $maintenance->cost ? 'Rp ' . number_format($maintenance->cost, 0, ',', '.') : '-' }}',
                                        scheduled_date: '{{ $maintenance->scheduled_date ? \Carbon\Carbon::parse($maintenance->scheduled_date)->format('d M Y') : '-' }}',
                                        completed_date: '{{ $maintenance->completed_date ? \Carbon\Carbon::parse($maintenance->completed_date)->format('d M Y') : '-' }}',
                                        next_maintenance_date: '{{ $maintenance->next_maintenance_date ? \Carbon\Carbon::parse($maintenance->next_maintenance_date)->format('d M Y') : '-' }}',
                                        description: '{{ str_replace(["\r", "\n"], ["", " "], addslashes($maintenance->description ?? 'No description provided.')) }}'
                                    })" class="w-10 h-10 rounded-full flex items-center justify-center text-teal-600 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] hover:text-teal-700 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>

                                    <button type="button" @click="openEdit({
                                        id: '{{ $maintenance->id }}',
                                        vehicle_id: '{{ $maintenance->vehicle_id }}',
                                        maintenance_type: '{{ addslashes($maintenance->maintenance_type) }}',
                                        status: '{{ $maintenance->status }}',
                                        cost: '{{ $maintenance->cost }}',
                                        scheduled_date: '{{ $maintenance->scheduled_date ? \Carbon\Carbon::parse($maintenance->scheduled_date)->format('Y-m-d') : '' }}',
                                        completed_date: '{{ $maintenance->completed_date ? \Carbon\Carbon::parse($maintenance->completed_date)->format('Y-m-d') : '' }}',
                                        next_maintenance_date: '{{ $maintenance->next_maintenance_date ? \Carbon\Carbon::parse($maintenance->next_maintenance_date)->format('Y-m-d') : '' }}',
                                        description: '{{ str_replace(["\r", "\n"], ["\\r", "\\n"], addslashes($maintenance->description)) }}'
                                    })" class="w-10 h-10 rounded-full flex items-center justify-center text-blue-600 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] hover:text-blue-700 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500 font-medium">
                                No maintenance records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            <div class="mt-4">
                {{ $maintenances->links() }}
            </div>
        </x-card>

        <!-- Detail Modal -->
        <x-modal title="Maintenance Details" model="detailModalOpen">
            <div class="space-y-6">
                <!-- Vehicle Info Card inside Modal -->
                <div class="p-6 bg-gray-100 rounded-3xl shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] flex flex-col items-center justify-center shrink-0">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold tracking-widest text-gray-500 uppercase">Vehicle</p>
                        <p class="text-xl font-black text-gray-800" x-text="detailData.vehicle_plate"></p>
                        <p class="text-sm font-medium text-gray-600" x-text="detailData.brand_model"></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-y-6 gap-x-4 px-2">
                    <div>
                        <p class="text-sm font-bold tracking-widest text-gray-500 uppercase mb-1">Maintenance Type</p>
                        <p class="text-gray-800 font-semibold" x-text="detailData.maintenance_type"></p>
                    </div>
                    <div>
                        <p class="text-sm font-bold tracking-widest text-gray-500 uppercase mb-1">Status</p>
                        <p class="font-bold text-blue-700 uppercase" x-text="detailData.status"></p>
                    </div>
                    <div>
                        <p class="text-sm font-bold tracking-widest text-gray-500 uppercase mb-1">Cost</p>
                        <p class="font-mono font-bold text-gray-800" x-text="detailData.cost"></p>
                    </div>
                    <div>
                        <p class="text-sm font-bold tracking-widest text-gray-500 uppercase mb-1">Scheduled Date</p>
                        <p class="text-gray-800 font-semibold" x-text="detailData.scheduled_date"></p>
                    </div>
                    <div>
                        <p class="text-sm font-bold tracking-widest text-gray-500 uppercase mb-1">Completed Date</p>
                        <p class="text-gray-800 font-semibold" x-text="detailData.completed_date"></p>
                    </div>
                    <div>
                        <p class="text-sm font-bold tracking-widest text-gray-500 uppercase mb-1">Next Maint. Date</p>
                        <p class="text-gray-800 font-semibold" x-text="detailData.next_maintenance_date"></p>
                    </div>
                </div>

                <div class="px-2 pt-2 border-t border-gray-300">
                    <p class="text-sm font-bold tracking-widest text-gray-500 uppercase mb-2">Description / Notes</p>
                    <div class="p-4 bg-gray-100 rounded-2xl shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff]">
                        <p class="text-gray-700 italic" x-text="detailData.description"></p>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 flex justify-end">
                <button @click="detailModalOpen = false" class="px-8 py-3 rounded-2xl font-bold text-gray-700 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all uppercase tracking-widest hover:text-red-500">
                    Close
                </button>
            </div>
        </x-modal>

        <!-- Create Slide-Over -->
        <x-slide-over title="Record New Maintenance" model="slideOverOpen">
            <form action="{{ route('fleet.maintenances.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Vehicle</label>
                    <x-select name="vehicle_id" required>
                        <option value="">Select a Vehicle...</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->plate_number }} ({{ $vehicle->brand }} {{ $vehicle->model }})
                            </option>
                        @endforeach
                    </x-select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Maintenance Type</label>
                    <x-input type="text" name="maintenance_type" value="{{ old('maintenance_type') }}" required placeholder="e.g. Ganti Oli, Turun Mesin" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                    <x-select name="status" required>
                        <option value="Scheduled" {{ old('status') == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="In Progress" {{ old('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Completed" {{ old('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    </x-select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Cost (IDR)</label>
                    <x-input type="number" name="cost" value="{{ old('cost') }}" placeholder="Leave blank if not yet known" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Scheduled Date</label>
                    <x-input type="date" name="scheduled_date" value="{{ old('scheduled_date', date('Y-m-d')) }}" required />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Completed Date</label>
                        <x-input type="date" name="completed_date" value="{{ old('completed_date') }}" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Next Maint. Date</label>
                        <x-input type="date" name="next_maintenance_date" value="{{ old('next_maintenance_date') }}" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Description / Notes</label>
                    <textarea name="description" rows="3" class="w-full bg-gray-100 border-none rounded-2xl shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] focus:ring-2 focus:ring-blue-400 focus:outline-none transition-all px-4 py-3 text-gray-700 {{ $errors->has('description') ? 'border-red-400 shadow-[inset_4px_4px_8px_#fca5a5,inset_-4px_-4px_8px_#fee2e2]' : '' }}">{{ old('description') }}</textarea>
                </div>

                <div class="pt-6 mt-6 border-t border-gray-300">
                    <button type="submit" class="w-full py-4 rounded-2xl font-bold text-gray-100 bg-gray-800 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#4b5563] transition-all uppercase tracking-widest">
                        Save Record
                    </button>
                </div>
            </form>
        </x-slide-over>

        <!-- Edit Slide-Over -->
        <x-slide-over title="Edit Maintenance Record" model="editSlideOverOpen">
            <form :action="'{{ route('fleet.maintenances.index') }}/' + editData.id" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <input type="hidden" name="maintenance_id" :value="editData.id">

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Vehicle</label>
                    <x-select name="vehicle_id" required ::value="editData.vehicle_id">
                        <option value="">Select a Vehicle...</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">
                                {{ $vehicle->plate_number }} ({{ $vehicle->brand }} {{ $vehicle->model }})
                            </option>
                        @endforeach
                    </x-select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Maintenance Type</label>
                    <x-input type="text" name="maintenance_type" ::value="editData.maintenance_type" required />
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                    <x-select name="status" required ::value="editData.status">
                        <option value="Scheduled">Scheduled</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                    </x-select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Cost (IDR)</label>
                    <x-input type="number" name="cost" ::value="editData.cost" />
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Scheduled Date</label>
                    <x-input type="date" name="scheduled_date" ::value="editData.scheduled_date" required />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Completed Date</label>
                        <x-input type="date" name="completed_date" ::value="editData.completed_date" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Next Maint. Date</label>
                        <x-input type="date" name="next_maintenance_date" ::value="editData.next_maintenance_date" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Description / Notes</label>
                    <textarea name="description" rows="3" x-model="editData.description" class="w-full bg-gray-100 border-none rounded-2xl shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] focus:ring-2 focus:ring-blue-400 focus:outline-none transition-all px-4 py-3 text-gray-700 {{ $errors->has('description') ? 'border-red-400 shadow-[inset_4px_4px_8px_#fca5a5,inset_-4px_-4px_8px_#fee2e2]' : '' }}"></textarea>
                </div>

                <div class="pt-6 mt-6 border-t border-gray-300">
                    <button type="submit" class="w-full py-4 rounded-2xl font-bold text-gray-100 bg-gray-800 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#4b5563] transition-all uppercase tracking-widest hover:bg-gray-700">
                        Update Record
                    </button>
                </div>
            </form>
        </x-slide-over>
    </div>
@endsection
