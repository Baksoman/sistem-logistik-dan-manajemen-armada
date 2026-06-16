@extends('layouts.driver-pwa')

@section('title', 'Operational Costs')

@section('content')
<div class="pt-6 pb-20" x-data="{ isModalOpen: false, fileName: '' }">
    <!-- Header -->
    <div class="flex items-center mb-8">
        <a href="{{ route('driver.workspace.show', $shipment->id) }}" class="w-12 h-12 rounded-full neu-flat flex items-center justify-center text-gray-600 mr-4 active:neu-pressed">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-800">Journey Costs</h1>
            <p class="text-sm text-gray-500 font-bold tracking-widest uppercase">{{ $shipment->shipment_number }}</p>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="bg-gray-100 p-6 rounded-3xl neu-pressed mb-8 flex justify-between items-center">
        <div>
            <span class="text-xs font-bold text-gray-400 tracking-widest uppercase block mb-1">Total Expenses</span>
            <span class="text-2xl font-black text-blue-600">Rp {{ number_format($costs->sum('amount'), 0, ',', '.') }}</span>
        </div>
        <div class="w-12 h-12 rounded-2xl neu-flat flex items-center justify-center text-blue-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path></svg>
        </div>
    </div>

    <!-- Add Cost Button -->
    <div class="mb-8">
        @if(in_array($shipment->status, ['Pending', 'On Process']))
        <button type="button" @click="isModalOpen = true" class="w-full neu-btn bg-emerald-500 text-white font-black py-4 rounded-2xl neu-flat transition-all uppercase tracking-widest flex items-center justify-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Record New Expense
        </button>
        @else
        <div class="text-center p-4 bg-yellow-100 text-yellow-700 rounded-2xl font-bold text-sm">
            Journey is completed. You can no longer add expenses.
        </div>
        @endif
    </div>

    <!-- Expenses List -->
    <h2 class="text-lg font-black text-gray-800 mb-6">Expense History</h2>
    
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

    <!-- Add Cost Modal -->
    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-end justify-center sm:items-center bg-gray-900/50 backdrop-blur-sm" x-transition.opacity>
        <div class="bg-gray-100 w-full sm:w-96 rounded-t-3xl sm:rounded-3xl p-6 neu-flat max-h-[90vh] overflow-y-auto" @click.away="isModalOpen = false" x-show="isModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">
            
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-black text-gray-800">Add Expense</h2>
                <button @click="isModalOpen = false" class="w-8 h-8 rounded-full neu-pressed flex items-center justify-center text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('driver.workspace.costs.store', $shipment->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 tracking-widest uppercase mb-2">Category</label>
                    <select name="category_id" required class="w-full bg-gray-100 neu-pressed rounded-xl px-4 py-3 text-gray-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all font-bold">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 tracking-widest uppercase mb-2">Amount (Rp)</label>
                    <input type="number" name="amount" min="0" required class="w-full bg-gray-100 neu-pressed rounded-xl px-4 py-3 text-gray-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all font-black text-lg" placeholder="0">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 tracking-widest uppercase mb-2">Description / Notes</label>
                    <textarea name="description" rows="2" class="w-full bg-gray-100 neu-pressed rounded-xl px-4 py-3 text-gray-700 outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="e.g. Tol Cipali..."></textarea>
                </div>

                <div class="mb-8">
                    <label class="block text-xs font-bold text-gray-500 tracking-widest uppercase mb-2">Receipt Photo</label>
                    <div class="relative w-full h-32 neu-pressed rounded-xl flex flex-col items-center justify-center text-gray-400 overflow-hidden group">
                        <svg class="w-8 h-8 mb-2 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="text-xs font-bold" x-text="fileName ? fileName : 'Tap to take a photo'"></span>
                        <input type="file" name="receipt" accept="image/*" capture="environment" @change="fileName = $event.target.files[0].name" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                    </div>
                </div>

                <button type="submit" class="w-full neu-btn bg-emerald-500 text-white font-black py-4 rounded-xl neu-flat transition-all uppercase tracking-widest">
                    Submit Expense
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
