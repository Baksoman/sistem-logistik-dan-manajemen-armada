@push('styles')
<style>
.faq-item { background: #e8ecef; border-radius: 1.5rem; box-shadow: 8px 8px 16px #c2c6cc, -8px -8px 16px #ffffff; overflow: hidden; transition: box-shadow 0.3s; }
.faq-item.open { box-shadow: inset 6px 6px 12px #c2c6cc, inset -6px -6px 12px #ffffff; }
.faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.5s cubic-bezier(0.4,0,0.2,1), padding 0.3s; padding: 0 2rem; }
.faq-item.open .faq-answer { max-height: 500px; padding: 0 2rem 1.5rem; }
.faq-chevron { transition: transform 0.4s, box-shadow 0.3s; }
.faq-item.open .faq-chevron { transform: rotate(180deg); box-shadow: inset 3px 3px 6px #c2c6cc, inset -3px -3px 6px #ffffff; }
</style>
@endpush

<section id="faq-section" class="relative w-full py-24 px-6 bg-gray-100">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="mt-3 text-4xl md:text-5xl font-black text-gray-800 tracking-tight" style="font-family:'Inter',sans-serif;">Pertanyaan yang Sering Diajukan</h2>
        </div>
        <div class="space-y-4">
            @foreach([
                ['q' => 'Bagaimana cara melacak paket saya?', 'a' => 'Masukkan nomor resi (order number atau shipment number) pada kolom pencarian di halaman utama. Anda dapat melihat status real-time, lokasi armada di peta, dan riwayat perjalanan lengkap.'],
                ['q' => 'Berapa lama estimasi waktu pengiriman?', 'a' => 'Estimasi waktu bervariasi tergantung jarak dan jenis layanan. Pengiriman dalam kota: 1–2 hari. Antar kota: 3–5 hari. Antar pulau: 5–10 hari. Setiap order memiliki SLA target yang dapat dipantau.'],
                ['q' => 'Apakah ada batasan berat dan dimensi barang?', 'a' => 'Kami melayani pengiriman mulai dari paket kecil hingga muatan kontainer. Kapasitas armada berkisar 500 kg (pickup) hingga 10+ ton (truk tronton). Hubungi tim kami untuk kebutuhan khusus.'],
                ['q' => 'Bagaimana cara menghitung biaya pengiriman?', 'a' => 'Biaya dihitung berdasarkan zona pengiriman, berat aktual/volumetrik, dan jenis layanan. Gunakan sistem tarif berbasis zona kami atau hubungi customer service untuk quotation detail.'],
                ['q' => 'Apa yang harus dilakukan jika barang rusak atau hilang?', 'a' => 'Setiap pengiriman tercatat dengan Proof of Delivery (POD) termasuk foto dan tanda tangan penerima. Untuk klaim, hubungi customer service maksimal 2×24 jam setelah delivery dengan menyertakan nomor resi dan bukti kerusakan.'],
                ['q' => 'Apakah pengiriman diasuransikan?', 'a' => 'Kami menerapkan SOP ketat untuk keamanan barang. Untuk nilai barang tinggi, tersedia opsi asuransi pengiriman. Informasi lebih lanjut hubungi tim kami.'],
                ['q' => 'Bagaimana sistem pembayaran yang tersedia?', 'a' => 'Kami menerima transfer bank, e-wallet, dan COD untuk customer retail. Untuk corporate client tersedia payment term 14–30 hari dengan approval.'],
                ['q' => 'Apakah bisa pickup barang dari lokasi saya?', 'a' => 'Ya, kami menyediakan layanan pickup untuk area tertentu. Hubungi customer service untuk request penjemputan barang.'],
            ] as $i => $faq)
            <div class="faq-item" data-faq="{{ $i }}">
                <div class="faq-question px-8 py-6 cursor-pointer flex justify-between items-center select-none" onclick="toggleFaq({{ $i }})">
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
function toggleFaq(idx) {
    const item = document.querySelector(`[data-faq="${idx}"]`);
    const isOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));
    if (!isOpen) item.classList.add('open');
}
</script>
@endpush
