@push('styles')
<style>
.why-card { background: #e8ecef; border-radius: 1.5rem; padding: 2rem; box-shadow: 8px 8px 16px #c2c6cc, -8px -8px 16px #ffffff; display: flex; gap: 1.5rem; align-items: flex-start; transition: all 0.35s; }
.why-card:hover { box-shadow: 10px 10px 20px #b8bcc2, -10px -10px 20px #ffffff; transform: translateY(-3px); }
@media (max-width: 768px) { .why-card { flex-direction: column; } }
</style>
@endpush

<section id="why-section" class="section-fade-in relative w-full py-24 px-6 bg-gray-100 overflow-hidden">
    <div class="dot-grid opacity-25"></div>
    <div class="max-w-6xl mx-auto relative z-10">
        <div class="text-center mb-16">
            <h2 class="mt-3 text-4xl md:text-5xl font-black text-gray-800 tracking-tight" style="font-family:'Inter',sans-serif;">Mengapa Memilih Kami?</h2>
            <p class="mt-4 text-gray-500 font-medium max-w-xl mx-auto">Teknologi modern bertemu keandalan operasional untuk pengiriman yang tak pernah mengecewakan.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach([
                ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'title' => 'Real-time GPS Tracking', 'desc' => 'Pantau pergerakan armada setiap detik dengan teknologi GPS tracking. Transparansi penuh untuk setiap pengiriman Anda.'],
                ['icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'title' => 'Multi-modal Transport', 'desc' => 'Fleksibilitas pengiriman darat dan laut untuk jangkauan seluruh nusantara dengan biaya optimal.'],
                ['icon' => 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z', 'title' => 'Proof of Delivery', 'desc' => 'Bukti penerimaan digital dengan foto, tanda tangan, lokasi GPS, dan timestamp untuk keamanan maksimal.'],
                ['icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'title' => 'Cost Transparency', 'desc' => 'Sistem tarif berbasis zona dan berat. Perhitungan biaya operasional real-time untuk pricing yang fair.'],
                ['icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'title' => 'Fleet Management', 'desc' => 'Maintenance scheduling dan monitoring armada untuk keandalan dan keamanan pengiriman setiap saat.'],
                ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'SLA Guarantee', 'desc' => 'Komitmen pengiriman tepat waktu dengan tracking SLA achievement di atas 90% secara konsisten.'],
            ] as $i => $why)
            <div class="why-card">
                <div class="w-14 h-14 flex-shrink-0 rounded-full bg-gray-100 shadow-[inset_4px_4px_8px_#c2c6cc,inset_-4px_-4px_8px_#ffffff] flex items-center justify-center text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @foreach(explode(' M', $why['icon']) as $j => $part)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $j === 0 ? $part : 'M' . $part }}"></path>
                        @endforeach
                    </svg>
                </div>
                <div>
                    <h4 class="font-black text-gray-800 uppercase tracking-wide text-sm mb-2">{{ $why['title'] }}</h4>
                    <p class="text-gray-500 text-sm font-medium leading-relaxed">{{ $why['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>


