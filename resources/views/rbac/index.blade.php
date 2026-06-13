@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h2 class="text-3xl font-black text-gray-800 tracking-tight mb-2">Access Control (RBAC)</h2>
        <p class="text-gray-500 text-lg font-medium">Manage permissions assigned to each system role.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Roles List Card -->
        <div class="lg:col-span-1" x-data="{ slideOverOpen: {{ $errors->any() && old('form_type') === 'role' ? 'true' : 'false' }} }" @keydown.escape.window="slideOverOpen = false">
            <x-card class="h-full">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Roles</h3>
                    <button type="button" @click="slideOverOpen = true" class="px-4 py-2 rounded-xl text-sm font-bold text-blue-600 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                        + New
                    </button>
                </div>
                <div class="space-y-4">
                    @foreach($roles as $role)
                        @php
                            $isActive = $selectedRole && $selectedRole->id === $role->id;
                        @endphp
                        <a href="{{ route('rbac.index', ['role' => $role->id]) }}" 
                           class="block w-full text-left px-6 py-4 rounded-2xl font-bold transition-all duration-200 
                           {{ $isActive ? 'bg-blue-100/50 text-blue-700 shadow-[inset_4px_4px_8px_rgba(37,99,235,0.15),inset_-4px_-4px_8px_rgba(255,255,255,0.8)]' : 'bg-gray-100 text-gray-600 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff]' }}">
                            {{ $role->name }}
                        </a>
                    @endforeach
                </div>
            </x-card>

            <!-- Create Role Slide-Over -->
            <x-slide-over title="Create New Role">
                <form action="{{ route('rbac.roles.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="form_type" value="role">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Role Name</label>
                        <x-input type="text" name="name" value="{{ old('form_type') === 'role' ? old('name') : '' }}" required placeholder="e.g. Manager" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Description (Optional)</label>
                        <x-input type="text" name="description" value="{{ old('form_type') === 'role' ? old('description') : '' }}" placeholder="Describe this role..." />
                    </div>
                    <div class="pt-6 mt-6 border-t border-gray-300">
                        <button type="submit" class="w-full py-4 rounded-2xl font-bold text-gray-100 bg-gray-800 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#4b5563] transition-all uppercase tracking-widest">
                            Create Role
                        </button>
                    </div>
                </form>
            </x-slide-over>
        </div>

        <!-- Permissions Editor Card -->
        <div class="lg:col-span-2" x-data="{ slideOverOpen: {{ $errors->any() && old('form_type') === 'permission' ? 'true' : 'false' }} }" @keydown.escape.window="slideOverOpen = false">
            <x-card class="h-full min-h-[500px]">
                @if($selectedRole)
                    <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-300">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">Permissions for <span class="text-blue-600">"{{ $selectedRole->name }}"</span></h3>
                            <p class="text-gray-500 text-sm mt-1">Select the capabilities this role should have.</p>
                        </div>
                        <button type="button" @click="slideOverOpen = true" class="px-4 py-2 rounded-xl text-sm font-bold text-blue-600 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                            + New Permission
                        </button>
                    </div>

                    <form action="{{ route('rbac.update', $selectedRole->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
                            @foreach($permissions as $permission)
                                @php
                                    $isChecked = $selectedRole->permissions->contains('id', $permission->id);
                                @endphp
                                <label class="flex items-start gap-4 p-5 rounded-2xl bg-gray-100 cursor-pointer transition-all duration-200 
                                              {{ $isChecked ? 'shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff]' : 'shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff]' }}">
                                    <div class="relative flex items-center justify-center mt-1">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                               class="peer appearance-none w-6 h-6 rounded-lg bg-gray-200 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] checked:bg-blue-500 checked:shadow-[inset_2px_2px_4px_#1d4ed8] focus:ring-0 cursor-pointer transition"
                                               {{ $isChecked ? 'checked' : '' }}>
                                        <svg class="absolute w-4 h-4 text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-700 tracking-wide">{{ ucwords(str_replace('_', ' ', $permission->name)) }}</div>
                                        <div class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $permission->description ?? 'Allows the user to perform this specific action.' }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="flex justify-end pt-6 border-t border-gray-300">
                            <button type="submit" class="px-8 py-4 rounded-2xl font-bold text-gray-100 bg-blue-600 shadow-[4px_4px_8px_#d1d5db] active:shadow-[inset_2px_2px_4px_#1e40af] transition-all uppercase tracking-widest hover:bg-blue-700">
                                Save Permissions
                            </button>
                        </div>
                    </form>
                @else
                    <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-300">
                        <h3 class="text-2xl font-bold text-gray-800">Permissions</h3>
                        <button type="button" @click="slideOverOpen = true" class="px-4 py-2 rounded-xl text-sm font-bold text-blue-600 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                            + New Permission
                        </button>
                    </div>
                    <div class="flex flex-col items-center justify-center h-full text-center text-gray-500 space-y-4">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center bg-gray-100 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff]">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-700">No Role Selected</h4>
                        <p>Select a role from the left menu to view and manage its permissions.</p>
                    </div>
                @endif
            </x-card>

            <!-- Create Permission Slide-Over -->
            <x-slide-over title="Create New Permission">
                <form action="{{ route('rbac.permissions.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="form_type" value="permission">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Permission Key</label>
                        <x-input type="text" name="name" value="{{ old('form_type') === 'permission' ? old('name') : '' }}" required placeholder="e.g. manage_users, view_reports" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                        <x-input type="text" name="description" value="{{ old('form_type') === 'permission' ? old('description') : '' }}" placeholder="What does this permission allow?" />
                    </div>
                    <div class="pt-6 mt-6 border-t border-gray-300">
                        <button type="submit" class="w-full py-4 rounded-2xl font-bold text-gray-100 bg-gray-800 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#4b5563] transition-all uppercase tracking-widest">
                            Create Permission
                        </button>
                    </div>
                </form>
            </x-slide-over>
        </div>
    </div>
@endsection
