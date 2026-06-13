@extends('layouts.app')

@section('title', 'User Management')

@section('content')
    <x-topbar />

    <!-- Error/Success Alerts -->


    <div x-data="{ slideOverOpen: {{ $errors->any() && !old('_method') ? 'true' : 'false' }}, editSlideOverOpen: false, editData: { id: '', name: '', email: '', role: '' } }" 
         @open-edit.window="editData = $event.detail; editSlideOverOpen = true; $refs.editForm.action = '/users/' + editData.id"
         @keydown.escape.window="slideOverOpen = false; editSlideOverOpen = false">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <p class="text-gray-500 text-lg font-medium">Manage system users, roles, and access permissions.</p>
            <button @click="slideOverOpen = true" class="flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-bold text-gray-800 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add User
            </button>
        </div>

        <x-card class="mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Active Users</h3>
            <div class="overflow-x-auto pb-4">
            <table class="w-full text-left border-collapse min-w-max whitespace-nowrap">
                <thead>
                    <tr class="border-b border-gray-300 text-gray-500 text-sm tracking-widest uppercase">
                        <th class="py-4 px-4 font-bold">Name</th>
                        <th class="py-4 px-4 font-bold">Roles</th>
                        <th class="py-4 px-4 font-bold">Email</th>
                        <th class="py-4 px-4 font-bold">Status</th>
                        <th class="py-4 px-4 font-bold text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 font-medium">
                    @forelse($users as $user)
                        <tr class="border-b border-gray-200/50 hover:bg-gray-200/30 transition">
                            <td class="py-4 px-4 font-bold text-gray-800">{{ $user->name }}</td>
                            <td class="py-4 px-4">
                                @foreach($user->roles as $role)
                                    <span class="inline-block px-3 py-1 mr-1 text-xs font-bold rounded-full text-blue-700 bg-blue-100/50 shadow-[inset_1px_1px_2px_rgba(0,0,0,0.1),inset_-1px_-1px_2px_rgba(255,255,255,0.7)]">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td class="py-4 px-4">{{ $user->email }}</td>
                            <td class="py-4 px-4">
                                <span class="px-3 py-1 text-xs font-bold rounded-full shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] {{ $user->is_active ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center justify-center gap-3">
                                    <button type="button" @click="$dispatch('open-edit', { id: '{{ $user->id }}', name: '{{ $user->name }}', email: '{{ $user->email }}', role: '{{ $user->roles->first()?->name ?? '' }}' })" class="w-10 h-10 rounded-full flex items-center justify-center text-blue-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('delete-form-{{ $user->id }}')" class="w-10 h-10 rounded-full flex items-center justify-center text-red-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] hover:text-red-600 transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </x-card>

        <!-- Create Form Slide-Over -->
        <x-slide-over title="Create New User">
            <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Full Name</label>
                    <x-input type="text" name="name" placeholder="John Doe" required />
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                    <x-input type="email" name="email" placeholder="john@example.com" required />
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Role</label>
                    <div class="relative">
                        <select name="role" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none appearance-none">
                            <option value="Super Admin">Super Admin</option>
                            <option value="Admin Logistik">Admin Logistik</option>
                            <option value="Warehouse">Warehouse</option>
                            <option value="Driver">Driver</option>
                        </select>
                        <svg class="w-5 h-5 absolute right-4 top-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Password</label>
                    <x-input type="password" name="password" placeholder="••••••••" required />
                </div>
                
                <div class="pt-6 mt-6 border-t border-gray-300">
                    <button type="submit" class="w-full py-4 rounded-2xl font-bold text-gray-100 bg-gray-800 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#4b5563,inset_-2px_-2px_4px_#1f2937] transition-all uppercase tracking-widest">
                        Save User
                    </button>
                </div>
            </form>
        </x-slide-over>

        <!-- Edit Form Slide-Over -->
        <div x-show="editSlideOverOpen" class="fixed inset-0 z-50 overflow-hidden" x-cloak>
            <div x-show="editSlideOverOpen" x-transition.opacity class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" @click="editSlideOverOpen = false"></div>
            <div class="fixed inset-y-0 right-0 max-w-md w-full flex">
                <div x-show="editSlideOverOpen" x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="w-full h-full bg-gray-100 flex flex-col shadow-[-12px_0_24px_rgba(0,0,0,0.1)]">
                    <div class="flex items-center justify-between px-8 py-6 shrink-0 shadow-[0_4px_6px_-1px_#d1d5db,0_2px_4px_-1px_#ffffff] z-10 bg-gray-100">
                        <h2 class="text-xl font-bold text-gray-800 tracking-tight">Edit User</h2>
                        <button type="button" @click="editSlideOverOpen = false" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 hover:text-red-500 bg-gray-100 shadow-[3px_3px_6px_#d1d5db,-3px_-3px_6px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db] focus:outline-none"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                    </div>
                    <div class="flex-1 overflow-y-auto px-8 py-8 z-0">
                        <form x-ref="editForm" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Full Name</label>
                                <input type="text" name="name" x-model="editData.name" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" />
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                                <input type="email" name="email" x-model="editData.email" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" />
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Role</label>
                                <select name="role" x-model="editData.role" required class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
                                    <option value="Super Admin">Super Admin</option>
                                    <option value="Admin Logistik">Admin Logistik</option>
                                    <option value="Warehouse">Warehouse</option>
                                    <option value="Driver">Driver</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Password (Leave blank to keep)</label>
                                <input type="password" name="password" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" />
                            </div>
                            <div class="pt-6 mt-6 border-t border-gray-300">
                                <button type="submit" class="w-full py-4 rounded-2xl font-bold text-gray-100 bg-gray-800 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#4b5563,inset_-2px_-2px_4px_#1f2937] transition-all uppercase tracking-widest">
                                    Update User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
