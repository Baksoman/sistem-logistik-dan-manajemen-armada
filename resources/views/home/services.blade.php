@push('styles')
<style>
.service-card { background: #e8ecef; border-radius: 2.5rem; padding: 2.5rem 2rem; box-shadow: 10px 10px 20px #c2c6cc, -10px -10px 20px #ffffff; transition: all 0.4s cubic-bezier(.23,1.5,.32,1); }
.service-card:hover { box-shadow: 14px 14px 28px #b8bcc2, -14px -14px 28px #ffffff; transform: translateY(-6px) scale(1.01); }
.vehicle-card { background: #e8ecef; border-radius: 2rem; padding: 2rem 1.5rem; box-shadow: 10px 10px 20px #c2c6cc, -10px -10px 20px #ffffff; text-align: center; transition: all 0.4s cubic-bezier(.23,1.5,.32,1); opacity: 0; transform: translateY(30px); }
.vehicle-card.fade-in { animation: fadeInUp 0.8s ease-out forwards; }
.vehicle-card:hover { box-shadow: 14px 14px 28px #b8bcc2, -14px -14px 28px #ffffff; transform: translateY(-6px) !important; opacity: 1 !important; }
.vehicle-card img { width: 100%; height: auto; object-fit: contain; max-height: 120px; }

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.mode-btn { padding: 0.5rem 1.5rem; border-radius: 1rem; font-size: 0.875rem; font-weight: 700; color: #6b7280; background: #e8ecef; box-shadow: 6px 6px 12px #d1d5db, -6px -6px 12px #ffffff; transition: all 0.3s; cursor: pointer; text-transform: uppercase; letter-spacing: 0.05em; }
.mode-btn:hover { color: #374151; }
.mode-btn.active { box-shadow: inset 4px 4px 8px #d1d5db, inset -4px -4px 8px #ffffff; color: #1f2937; }

@media (max-width: 768px) { .service-card { padding: 1.75rem 1.5rem; } }
</style>
@endpush

<section id="services-section" class="relative w-full py-24 px-6 bg-gray-100">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="mt-3 text-4xl md:text-5xl font-black text-gray-800 tracking-tight" style="font-family:'Inter',sans-serif;">Layanan Logistik Terpadu</h2>
            <p class="mt-4 text-gray-500 font-medium max-w-xl mx-auto">Solusi pengiriman darat, laut, hingga intermodal untuk memenuhi setiap kebutuhan distribusi Anda.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            @foreach([
                ['icon' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0', 'badge' => 'Darat', 'title' => 'Pengiriman Darat', 'desc' => 'Jangkauan luas ke seluruh kota dengan armada truk berbagai kapasitas dari pickup hingga tronton.', 'tags' => ['Same Day','Next Day','Regular'], 'anim' => 'reveal-left'],
                ['icon' => 'M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2z', 'badge' => 'Laut', 'title' => 'Pengiriman Laut', 'desc' => 'Solusi pengiriman antar pulau dengan sea route optimization untuk efisiensi biaya maksimal.', 'tags' => ['FCL','LCL','Ro-Ro'], 'anim' => 'reveal'],
                ['icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'badge' => 'Intermodal', 'title' => 'Kombinasi Darat-Laut', 'desc' => 'Intermodal transportation untuk efisiensi maksimal menjangkau seluruh pelosok nusantara.', 'tags' => ['Door to Port','Door to Door'], 'anim' => 'reveal-right']
            ] as $service)
            <div class="service-card">
                <div class="w-20 h-20 rounded-2xl bg-gray-100 shadow-[inset_6px_6px_12px_#c2c6cc,inset_-6px_-6px_12px_#ffffff] flex items-center justify-center mb-6 text-gray-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @foreach(explode(' M', $service['icon']) as $j => $part)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $j === 0 ? $part : 'M' . $part }}"></path>
                        @endforeach
                    </svg>
                </div>
                <div class="neo-badge mb-4 inline-block">{{ $service['badge'] }}</div>
                <h3 class="font-black text-lg text-gray-800 uppercase tracking-wider mb-3">{{ $service['title'] }}</h3>
                <p class="text-gray-500 text-sm font-medium leading-relaxed">{{ $service['desc'] }}</p>
                <div class="mt-6 pt-5 border-t border-gray-200/60 flex gap-3 flex-wrap">
                    @foreach($service['tags'] as $tag)
                    <span class="text-xs font-bold text-gray-500 bg-gray-100 rounded-full px-3 py-1 shadow-[inset_2px_2px_4px_#c2c6cc,inset_-2px_-2px_4px_#ffffff]">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4" x-data="{ activeMode: 'darat' }">
            <h3 class="text-center text-sm font-black text-gray-500 uppercase tracking-widest mb-6">Tipe Armada Tersedia</h3>
            
            <!-- Mode Toggle -->
            <div class="flex justify-center gap-3 mb-10">
                <button @click="activeMode = 'darat'" :class="activeMode === 'darat' ? 'active' : ''" class="mode-btn">Darat</button>
                <button @click="activeMode = 'laut'" :class="activeMode === 'laut' ? 'active' : ''" class="mode-btn">Laut</button>
            </div>

            <!-- Armada Darat -->
            <div x-show="activeMode === 'darat'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach([
                    ['name' => 'PICKUP', 'cap' => '500 KG', 'img' => 'pickup.png', 'delay' => '0s'],
                    ['name' => 'ENGKEL', 'cap' => '2 TON', 'img' => 'engkel.png', 'delay' => '0.1s'],
                    ['name' => 'FUSO', 'cap' => '5 TON', 'img' => 'fuso.png', 'delay' => '0.2s'],
                    ['name' => 'TRONTON', 'cap' => '10+ TON', 'img' => 'tronton.png', 'delay' => '0.3s']
                ] as $vehicle)
                <div class="vehicle-card" style="animation-delay: {{ $vehicle['delay'] }};" x-intersect.once="$el.classList.add('fade-in')">
                    <div class="mb-4 px-2">
                        <img src="{{ asset('assets/home/truk/' . $vehicle['img']) }}" alt="{{ $vehicle['name'] }}" class="w-full h-auto mx-auto">
                    </div>
                    <h4 class="font-black text-gray-800 text-sm uppercase tracking-wider mb-2">{{ $vehicle['name'] }}</h4>
                    <div class="inline-block neo-badge text-[0.65rem] px-3 py-1">{{ $vehicle['cap'] }}</div>
                </div>
                @endforeach
            </div>

            <!-- Armada Laut -->
            <div x-show="activeMode === 'laut'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="flex flex-wrap justify-center gap-6">
                @foreach([
                    ['name' => 'KAPAL KARGO', 'cap' => '100+ TON', 'img' => 'kargo.png', 'delay' => '0s'],
                    ['name' => 'KAPAL KONTAINER', 'cap' => '500+ TON', 'img' => 'kontainer.png', 'delay' => '0.1s'],
                    ['name' => 'KAPAL RO-RO', 'cap' => '50+ UNIT', 'img' => 'roro.png', 'delay' => '0.2s']
                ] as $vehicle)
                <div class="vehicle-card w-[calc(50%-0.75rem)] md:w-[calc(25%-1.125rem)]" style="animation-delay: {{ $vehicle['delay'] }};" x-intersect.once="$el.classList.add('fade-in')">
                    <div class="mb-4 px-2">
                        <img src="{{ asset('assets/home/kapal/' . $vehicle['img']) }}" alt="{{ $vehicle['name'] }}" class="w-full h-auto mx-auto">
                    </div>
                    <h4 class="font-black text-gray-800 text-sm uppercase tracking-wider mb-2 leading-tight">{{ $vehicle['name'] }}</h4>
                    <div class="inline-block neo-badge text-[0.65rem] px-3 py-1">{{ $vehicle['cap'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>


