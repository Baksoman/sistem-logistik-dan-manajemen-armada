@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <x-topbar />

    <div class="max-w-4xl mx-auto mt-8">
        <x-card class="p-8">
            <div class="flex flex-col md:flex-row gap-12 items-center md:items-start">
                
                <!-- Avatar Section -->
                <div class="flex flex-col items-center gap-6">
                    <div class="relative w-40 h-40 rounded-full bg-gray-100 shadow-[8px_8px_16px_#d1d5db,-8px_-8px_16px_#ffffff] flex items-center justify-center border-4 border-gray-100">
                        <span class="text-6xl font-black text-gray-400">{{ substr($user->name ?? 'A', 0, 1) }}</span>
                    </div>
                    <div class="text-center">
                        <h2 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h2>
                        <p class="text-sm font-bold text-indigo-500 mt-1 uppercase tracking-widest">{{ $user->roles->first()->name ?? 'User' }}</p>
                    </div>
                </div>

                <!-- Info Section -->
                <div class="flex-1 w-full space-y-6 mt-4 md:mt-0">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="flex items-center justify-between border-b border-gray-200 pb-2 mb-6">
                            <h3 class="text-lg font-bold text-gray-800">Personal Information</h3>
                            <button type="submit" class="px-5 py-2 bg-indigo-500 text-white text-xs font-bold rounded-xl shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_rgba(0,0,0,0.1)] transition-all">
                                Save Changes
                            </button>
                        </div>
                        
                        @if (session('success'))
                            <div class="mb-4 p-4 text-sm font-bold text-emerald-700 bg-emerald-50 rounded-2xl shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] border-l-4 border-emerald-500">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Full Name</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none transition-shadow">
                                @error('name')
                                    <span class="text-xs text-red-500 mt-2 block font-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Email Address (Read Only)</label>
                                <div class="w-full bg-gray-100/50 rounded-2xl px-5 py-4 font-medium text-gray-400 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff]">
                                    {{ $user->email }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Phone Number</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none transition-shadow" placeholder="e.g. 08123456789">
                                @error('phone')
                                    <span class="text-xs text-red-500 mt-2 block font-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Account Created</label>
                                <div class="w-full bg-gray-100/50 rounded-2xl px-5 py-4 font-medium text-gray-400 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff]">
                                    {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </x-card>
    </div>
@endsection
