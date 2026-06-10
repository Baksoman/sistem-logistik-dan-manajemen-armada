@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900">
    <div class="w-full max-w-md px-8 py-10 mx-4 bg-white/10 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/20">
        {{-- Logo / Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-400 shadow-lg shadow-blue-500/30 mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Sistem Logistik</h1>
            <p class="text-sm text-blue-200/70 mt-1">Manajemen Armada & Pengiriman</p>
        </div>

        {{-- Error Messages --}}
        @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-500/20 border border-red-400/30 backdrop-blur">
            @foreach ($errors->all() as $error)
                <p class="text-sm text-red-200">{{ $error }}</p>
            @endforeach
        </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-blue-100 mb-1.5">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-blue-200/50 focus:outline-none focus:ring-2 focus:ring-blue-400/60 focus:border-transparent transition"
                    placeholder="nama@email.com"
                >
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-blue-100 mb-1.5">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-blue-200/50 focus:outline-none focus:ring-2 focus:ring-blue-400/60 focus:border-transparent transition"
                    placeholder="Masukkan password"
                >
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-blue-200/80 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-white/30 bg-white/10 text-blue-500 focus:ring-blue-400/50">
                    Ingat saya
                </label>
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                class="w-full py-3 px-4 bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 hover:from-blue-600 hover:to-cyan-600 focus:outline-none focus:ring-2 focus:ring-blue-400/60 transition-all duration-200 transform hover:scale-[1.02]"
            >
                Masuk
            </button>
        </form>

        {{-- Footer --}}
        <p class="text-center text-xs text-blue-200/40 mt-8">
            &copy; {{ date('Y') }} Sistem Logistik dan Manajemen Armada
        </p>
    </div>
</div>
@endsection
