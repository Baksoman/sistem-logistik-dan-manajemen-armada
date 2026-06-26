@push('styles')
<style>
.stat-card { background: #e8ecef; border-radius: 2rem; padding: 2.5rem 2rem; box-shadow: 12px 12px 24px #c2c6cc, -12px -12px 24px #ffffff; transition: all 0.3s; }
.stat-card:hover { box-shadow: 16px 16px 32px #b8bcc2, -16px -16px 32px #ffffff; transform: translateY(-4px); }
@media (max-width: 768px) { .stat-card { padding: 1.75rem 1.25rem; } }
</style>
@endpush

<section id="stats-section" class="relative w-full py-24 px-6 bg-gray-100 overflow-hidden">
    <div class="dot-grid"></div>
    <div class="relative z-10 max-w-6xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="mt-3 text-4xl md:text-5xl font-black text-gray-800 tracking-tight" style="font-family:'Inter',sans-serif;">Dipercaya oleh Ribuan Pelanggan</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
            @foreach([
                ['icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4', 'count' => 48291, 'label' => 'Paket Terkirima'],
                ['icon' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0', 'count' => 143, 'label' => 'Armada Aktif'],
                ['icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'count' => 27, 'label' => 'Gudang Nasional'],
                ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'count' => 96, 'label' => 'Tepat Waktu (SLA)', 'percent' => true]
            ] as $stat)
            <div class="stat-card text-center">
                <div class="w-16 h-16 mx-auto mb-5 rounded-full bg-gray-100 shadow-[inset_4px_4px_8px_#c2c6cc,inset_-4px_-4px_8px_#ffffff] flex items-center justify-center text-gray-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @foreach(explode(' M', $stat['icon']) as $j => $part)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $j === 0 ? $part : 'M' . $part }}"></path>
                        @endforeach
                    </svg>
                </div>
                <div class="flex items-baseline justify-center gap-0.5">
                    <div class="text-4xl md:text-5xl font-black text-gray-800">{{ number_format($stat['count'], 0, ',', '.') }}</div>
                    @if($stat['percent'] ?? false)<div class="text-2xl font-black text-gray-700">%</div>@endif
                </div>
                <div class="text-xs font-black text-gray-500 uppercase tracking-widest mt-2">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>
