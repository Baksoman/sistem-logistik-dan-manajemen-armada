@extends('layouts.driver-pwa')

@section('title', 'All Journey Costs')

@section('content')
<div class="pt-6 pb-20">
    <!-- Header -->
    <div class="flex items-center mb-8">
        <a href="{{ route('driver.workspace.index') }}" class="w-12 h-12 rounded-full neu-flat flex items-center justify-center text-gray-600 mr-4 active:neu-pressed">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-800">All Expenses</h1>
            <p class="text-sm text-gray-500 font-bold tracking-widest uppercase">Global History</p>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="bg-gray-100 p-6 rounded-3xl neu-pressed mb-8 flex justify-between items-center">
        <div>
            <span class="text-xs font-bold text-gray-400 tracking-widest uppercase block mb-1">Total All Time</span>
            <span class="text-2xl font-black text-blue-600">Rp {{ number_format($costs->sum('amount'), 0, ',', '.') }}</span>
        </div>
        <div class="w-12 h-12 rounded-2xl neu-flat flex items-center justify-center text-blue-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path></svg>
        </div>
    </div>

    <!-- Expenses List -->
    @if($costs->isEmpty())
        <div class="text-center p-6 bg-gray-100 neu-flat rounded-3xl">
            <p class="text-gray-500 font-medium text-sm">No expenses recorded yet.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($costs as $cost)
            <div class="bg-gray-100 p-5 rounded-3xl neu-flat">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl neu-pressed flex items-center justify-center text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <span class="text-sm font-black text-gray-800 block">{{ $cost->category->name }}</span>
                            <span class="text-[10px] font-bold text-gray-400 tracking-widest uppercase">{{ \Carbon\Carbon::parse($cost->recorded_at)->format('H:i, d M Y') }}</span>
                        </div>
                    </div>
                    <span class="font-black text-blue-600">Rp {{ number_format($cost->amount, 0, ',', '.') }}</span>
                </div>

                <div class="mb-3">
                    <p class="text-[10px] font-bold text-gray-400 tracking-widest uppercase">Shipment</p>
                    <a href="{{ route('driver.workspace.show', $cost->shipment->id) }}" class="text-sm font-bold text-blue-500 underline">{{ $cost->shipment->shipment_number }}</a>
                </div>
                
                @if($cost->description)
                <div class="bg-white p-3 rounded-xl shadow-inner mb-3">
                    <p class="text-xs font-medium text-gray-600 italic">"{{ $cost->description }}"</p>
                </div>
                @endif
                
                @if($cost->receipt_path)
                <div class="w-full h-32 rounded-xl overflow-hidden shadow-sm neu-pressed">
                    <img src="{{ Storage::url($cost->receipt_path) }}" alt="Receipt" class="w-full h-full object-cover">
                </div>
                @endif
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
