@extends('layouts.app')

@section('title', 'User Management')

@section('content')
    <x-topbar />

    <!-- Error/Success Alerts -->


    <div x-data="{ 
            slideOverOpen: {{ $errors->any() && !old('user_id') ? 'true' : 'false' }}, 
            editSlideOverOpen: {{ $errors->any() && old('user_id') ? 'true' : 'false' }}, 
            editData: { 
                id: '{{ old('user_id') }}', 
                name: '{{ old('name') }}', 
                email: '{{ old('email') }}', 
                role: '{{ old('role') }}' 
            } 
         }" 
         @open-edit.window="editData = $event.detail; editSlideOverOpen = true;"
         @keydown.escape.window="slideOverOpen = false; editSlideOverOpen = false">
        
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-8">
            <p class="text-gray-500 text-lg font-medium">Manage system users, roles, and access permissions.</p>
            <div class="flex flex-col lg:flex-row w-full lg:w-auto gap-3 shrink-0">
                <a href="{{ route('users.export.excel') }}" class="flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-emerald-700 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db] transition-all hover:bg-emerald-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Excel
                </a>
                <a href="{{ route('users.export.pdf') }}" class="flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-bold text-red-600 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db] transition-all hover:bg-red-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    PDF
                </a>
                <button @click="slideOverOpen = true" class="flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-bold text-gray-800 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all hover:text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add User
                </button>
            </div>
        </div>

        <div x-data="dataTable({
                endpoint: '/api/search/users',
                initialData: {{ Js::from($initialData['data'] ?? []) }},
                initialMeta: {{ Js::from($initialData['meta'] ?? []) }}
            })">

            <x-search-filter-bar placeholder="Search users by name or email..." />

            <x-filter-modal title="Filter Users">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Role</label>
                    <select x-model="filters.role" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
                        <option value="">All Roles</option>
                        <option value="Super Admin">Super Admin</option>
                        <option value="Admin Logistik">Admin Logistik</option>
                        <option value="Staff Warehouse">Warehouse</option>
                        <option value="Driver">Driver</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                    <select x-model="filters.is_active" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </x-filter-modal>

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
                    <template x-for="user in data" :key="user.id">
                        <tr class="border-b border-gray-200/50 hover:bg-gray-200/30 transition">
                            <td class="py-4 px-4 font-bold text-gray-800" x-text="user.name"></td>
                            <td class="py-4 px-4">
                                <template x-for="role in user.roles" :key="role">
                                    <span class="inline-block px-3 py-1 mr-1 text-xs font-bold rounded-full text-blue-700 bg-blue-100/50 shadow-[inset_1px_1px_2px_rgba(0,0,0,0.1),inset_-1px_-1px_2px_rgba(255,255,255,0.7)]" x-text="role"></span>
                                </template>
                            </td>
                            <td class="py-4 px-4" x-text="user.email"></td>
                            <td class="py-4 px-4">
                                <span class="px-3 py-1 text-xs font-bold rounded-full shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff]"
                                      :class="user.is_active ? 'text-green-600' : 'text-red-500'"
                                      x-text="user.is_active ? 'Active' : 'Inactive'">
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center justify-center gap-3">
                                    <button type="button" @click="$dispatch('open-edit', { id: user.id, name: user.name, email: user.email, role: user.roles[0] || '' })" class="w-10 h-10 rounded-full flex items-center justify-center text-blue-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <form :id="'delete-form-' + user.id" :action="'{{ route('users.index') }}/' + user.id" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" @click="confirmDelete('delete-form-' + user.id)" class="w-10 h-10 rounded-full flex items-center justify-center text-red-500 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] hover:text-red-600 transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="data.length === 0" x-cloak>
                        <td colspan="5" class="py-8 text-center text-gray-400">No users found.</td>
                    </tr>
                </tbody>
            </table>
            </div>
            
            <x-pagination />
            
        </x-card>
        </div>

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
                            <option value="Staff Warehouse">Warehouse</option>
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
        <x-slide-over title="Edit User" model="editSlideOverOpen">
            <form :action="'{{ route('users.index') }}/' + editData.id" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="user_id" x-model="editData.id">
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
                        <option value="Staff Warehouse">Warehouse</option>
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
        </x-slide-over>

    </div>
@endsection
