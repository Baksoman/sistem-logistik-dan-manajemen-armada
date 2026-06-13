@extends('layouts.guest')

@section('content')
    <div class="w-full max-w-5xl px-6 flex flex-col justify-center items-center h-full my-auto py-16">
        <h1 class="text-4xl md:text-6xl font-bold text-gray-800 text-center mb-6 leading-tight tracking-tight">
            Logistik Cepat, <br> <span class="text-gray-500">Aman & Terpercaya</span>
        </h1>
        <p class="text-lg md:text-xl text-gray-600 text-center mb-16 max-w-3xl">
            Lacak pengiriman Anda secara real-time dan kelola armada dengan efisiensi tinggi melalui platform terintegrasi kami.
        </p>

        <x-card class="w-full max-w-3xl mx-auto">
            <h2 class="text-2xl font-semibold text-gray-800 mb-8 text-center">Cek Resi / Fast Tracking</h2>
            <form action="#" method="GET" class="flex flex-col sm:flex-row gap-6">
                <div class="flex-1">
                    <label for="tracking_id" class="sr-only">Nomor Resi</label>
                    <x-input id="tracking_id" type="text" placeholder="Masukkan nomor resi pengiriman Anda..." class="h-full text-lg py-4" />
                </div>
                <x-button type="submit" class="sm:w-auto w-full text-lg py-4 px-8">
                    Lacak Sekarang
                </x-button>
            </form>
        </x-card>
        
        <!-- Decoration / Features -->
        <div class="mt-24 grid grid-cols-1 md:grid-cols-3 gap-10 w-full">
            <div class="bg-gray-100 rounded-3xl p-8 flex flex-col items-center text-center shadow-[inset_6px_6px_12px_#d1d5db,inset_-6px_-6px_12px_#ffffff]">
                <div class="w-20 h-20 rounded-full mb-6 shadow-[8px_8px_16px_#d1d5db,-8px_-8px_16px_#ffffff] flex items-center justify-center text-gray-600">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </div>
                <h3 class="font-bold text-xl text-gray-800 mb-3">Fast Delivery</h3>
                <p class="text-gray-500">Pengiriman secepat kilat ke seluruh pelosok Indonesia dengan armada terbaik.</p>
            </div>
            <div class="bg-gray-100 rounded-3xl p-8 flex flex-col items-center text-center shadow-[inset_6px_6px_12px_#d1d5db,inset_-6px_-6px_12px_#ffffff]">
                <div class="w-20 h-20 rounded-full mb-6 shadow-[8px_8px_16px_#d1d5db,-8px_-8px_16px_#ffffff] flex items-center justify-center text-gray-600">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <h3 class="font-bold text-xl text-gray-800 mb-3">Secure Cargo</h3>
                <p class="text-gray-500">Jaminan keamanan penuh untuk setiap barang Anda melalui asuransi dan SOP ketat.</p>
            </div>
            <div class="bg-gray-100 rounded-3xl p-8 flex flex-col items-center text-center shadow-[inset_6px_6px_12px_#d1d5db,inset_-6px_-6px_12px_#ffffff]">
                <div class="w-20 h-20 rounded-full mb-6 shadow-[8px_8px_16px_#d1d5db,-8px_-8px_16px_#ffffff] flex items-center justify-center text-gray-600">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h3 class="font-bold text-xl text-gray-800 mb-3">Realtime Tracking</h3>
                <p class="text-gray-500">Pantau pergerakan armada dan status pengiriman barang Anda kapan saja 24/7.</p>
            </div>
        </div>
    </div>
@endsection
