@push('styles')
<style>
.faq-item { background: #e8ecef; border-radius: 1.5rem; box-shadow: 8px 8px 16px #c2c6cc, -8px -8px 16px #ffffff; overflow: hidden; transition: all 0.4s cubic-bezier(.23,1.5,.32,1); opacity: 0; transform: translateY(30px); }
.faq-item.fade-in { animation: fadeInUp 0.8s ease-out forwards; }
.faq-item.open { box-shadow: inset 6px 6px 12px #c2c6cc, inset -6px -6px 12px #ffffff; }
.faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.5s cubic-bezier(0.4,0,0.2,1), padding 0.3s; padding: 0 2rem; }
.faq-item.open .faq-answer { max-height: 500px; padding: 0 2rem 1.5rem; }
.faq-chevron { transition: transform 0.4s, box-shadow 0.3s; }
.faq-item.open .faq-chevron { transform: rotate(180deg); box-shadow: inset 3px 3px 6px #c2c6cc, inset -3px -3px 6px #ffffff; }

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.category-btn { padding: 0.5rem 1.5rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 700; color: #6b7280; background: #e8ecef; box-shadow: 6px 6px 12px #d1d5db, -6px -6px 12px #ffffff; transition: all 0.3s; cursor: pointer; text-transform: uppercase; letter-spacing: 0.05em; }
.category-btn:hover { color: #374151; }
.category-btn.active { box-shadow: inset 4px 4px 8px #d1d5db, inset -4px -4px 8px #ffffff; color: #1f2937; }
</style>
@endpush

<section id="faq-section" class="relative w-full py-24 px-6 bg-gray-100" x-data="faqManager()">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="mt-3 text-4xl md:text-5xl font-black text-gray-800 tracking-tight" style="font-family:'Inter',sans-serif;">Pertanyaan yang Sering Diajukan</h2>
        </div>
        
        <div class="flex flex-wrap justify-center gap-3 mb-10">
            <button @click="activeCategory = 'semua'" :class="activeCategory === 'semua' ? 'active' : ''" class="category-btn">Semua</button>
            <button @click="activeCategory = 'pengiriman'" :class="activeCategory === 'pengiriman' ? 'active' : ''" class="category-btn">Pengiriman</button>
            <button @click="activeCategory = 'tarif'" :class="activeCategory === 'tarif' ? 'active' : ''" class="category-btn">Tarif</button>
            <button @click="activeCategory = 'layanan'" :class="activeCategory === 'layanan' ? 'active' : ''" class="category-btn">Layanan</button>
        </div>
        
        <!-- Pengiriman -->
        <div x-show="activeCategory === 'semua' || activeCategory === 'pengiriman'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="space-y-4">
            @foreach([
                ['q' => 'Bagaimana cara melacak paket saya?', 'a' => 'Masukkan nomor resi (order number atau shipment number) pada kolom pencarian di halaman utama. Anda dapat melihat status real-time, lokasi armada di peta, dan riwayat perjalanan lengkap.', 'idx' => 0],
                ['q' => 'Berapa lama estimasi waktu pengiriman?', 'a' => 'Estimasi waktu bervariasi tergantung jarak dan jenis layanan. Pengiriman dalam kota: 1–2 hari. Antar kota: 3–5 hari. Antar pulau: 5–10 hari.', 'idx' => 1],
            ] as $faq)
            <div class="faq-item" style="animation-delay: {{ $faq['idx'] * 0.1 }}s;" x-intersect.once="$el.classList.add('fade-in')">
                <div class="faq-question px-8 py-6 cursor-pointer flex justify-between items-center select-none" @click="toggleFaq({{ $faq['idx'] }})">
                    <span class="font-black text-gray-800 text-sm pr-4 uppercase tracking-wide">{{ $faq['q'] }}</span>
                    <div class="faq-chevron w-9 h-9 flex-shrink-0 rounded-full bg-gray-100 shadow-[4px_4px_8px_#c2c6cc,-4px_-4px_8px_#ffffff] flex items-center justify-center text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                <div class="faq-answer">
                    <p class="text-gray-500 font-medium text-sm leading-relaxed pb-2">{{ $faq['a'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Tarif -->
        <div x-show="activeCategory === 'semua' || activeCategory === 'tarif'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="space-y-4" :class="activeCategory === 'semua' ? 'mt-4' : ''">
            @foreach([
                ['q' => 'Bagaimana cara menghitung biaya pengiriman?', 'a' => 'Biaya dihitung berdasarkan zona pengiriman, berat aktual/volumetrik, dan jenis layanan. Gunakan sistem tarif berbasis zona kami atau hubungi customer service untuk quotation detail.', 'idx' => 2],
                ['q' => 'Bagaimana sistem pembayaran yang tersedia?', 'a' => 'Kami menerima transfer bank, e-wallet, dan COD untuk customer retail. Untuk corporate client tersedia payment term 14–30 hari dengan approval.', 'idx' => 3],
            ] as $faq)
            <div class="faq-item" style="animation-delay: {{ ($faq['idx'] - 2) * 0.1 }}s;" x-intersect.once="$el.classList.add('fade-in')">
                <div class="faq-question px-8 py-6 cursor-pointer flex justify-between items-center select-none" @click="toggleFaq({{ $faq['idx'] }})">
                    <span class="font-black text-gray-800 text-sm pr-4 uppercase tracking-wide">{{ $faq['q'] }}</span>
                    <div class="faq-chevron w-9 h-9 flex-shrink-0 rounded-full bg-gray-100 shadow-[4px_4px_8px_#c2c6cc,-4px_-4px_8px_#ffffff] flex items-center justify-center text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                <div class="faq-answer">
                    <p class="text-gray-500 font-medium text-sm leading-relaxed pb-2">{{ $faq['a'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Layanan -->
        <div x-show="activeCategory === 'semua' || activeCategory === 'layanan'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="space-y-4" :class="activeCategory === 'semua' ? 'mt-4' : ''">
            @foreach([
                ['q' => 'Apakah bisa pickup barang dari lokasi saya?', 'a' => 'Ya, kami menyediakan layanan pickup untuk area tertentu. Hubungi customer service untuk request penjemputan barang dengan jadwal yang fleksibel.', 'idx' => 4],
                ['q' => 'Apa yang harus dilakukan jika barang rusak?', 'a' => 'Setiap pengiriman tercatat dengan Proof of Delivery (POD) termasuk foto dan tanda tangan. Untuk klaim, hubungi customer service maksimal 2×24 jam setelah delivery.', 'idx' => 5],
            ] as $faq)
            <div class="faq-item" style="animation-delay: {{ ($faq['idx'] - 4) * 0.1 }}s;" x-intersect.once="$el.classList.add('fade-in')">
                <div class="faq-question px-8 py-6 cursor-pointer flex justify-between items-center select-none" @click="toggleFaq({{ $faq['idx'] }})">
                    <span class="font-black text-gray-800 text-sm pr-4 uppercase tracking-wide">{{ $faq['q'] }}</span>
                    <div class="faq-chevron w-9 h-9 flex-shrink-0 rounded-full bg-gray-100 shadow-[4px_4px_8px_#c2c6cc,-4px_-4px_8px_#ffffff] flex items-center justify-center text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                <div class="faq-answer">
                    <p class="text-gray-500 font-medium text-sm leading-relaxed pb-2">{{ $faq['a'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('faqManager', () => ({
        activeCategory: 'semua',
        openFaq: null,
        toggleFaq(idx) {
            const items = document.querySelectorAll('.faq-item');
            const item = items[idx];
            const isOpen = item.classList.contains('open');
            items.forEach(i => i.classList.remove('open'));
            if (!isOpen) {
                item.classList.add('open');
                this.openFaq = idx;
            } else {
                this.openFaq = null;
            }
        }
    }));
});
</script>
@endpush
