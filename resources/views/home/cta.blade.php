@push('styles')
<style>
.cta-input { width: 100%; background: #e8ecef; border-radius: 1rem; padding: 0.875rem 1.25rem; font-weight: 600; color: #374151; box-shadow: inset 5px 5px 10px #c2c6cc, inset -5px -5px 10px #ffffff; border: none; outline: none; transition: box-shadow 0.3s; }
.cta-input:focus { box-shadow: inset 6px 6px 12px #b8bcc2, inset -6px -6px 12px #ffffff; }
.cta-input::placeholder { color: #9ca3af; }
.toast-notification { position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 1rem 1.5rem; border-radius: 1rem; box-shadow: 10px 10px 20px rgba(0,0,0,0.15), -5px -5px 15px rgba(255,255,255,0.7); font-weight: 600; display: flex; align-items: center; gap: 0.75rem; transform: translateX(400px); opacity: 0; transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55); }
.toast-notification.show { transform: translateX(0); opacity: 1; }
.toast-success { background: #e8ecef; color: #059669; }
.toast-error { background: #e8ecef; color: #dc2626; }
</style>
@endpush

<section id="cta-section" class="relative w-full py-24 px-6 bg-gray-100 overflow-hidden">
    <div class="dot-grid opacity-20"></div>
    <div class="max-w-6xl mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div>
                <h2 class="mt-4 text-4xl md:text-5xl font-black text-gray-800 tracking-tight leading-tight" style="font-family:'Inter',sans-serif;">Solusi Logistik<br>untuk Bisnis Anda</h2>
                <p class="mt-5 text-gray-500 font-medium leading-relaxed">Bergabunglah dengan ratusan perusahaan yang telah mempercayai kami untuk kebutuhan distribusi dan supply chain mereka.</p>
                <div class="mt-8 space-y-4">
                    @foreach(['Dedicated account manager', 'Volume discount untuk pengiriman reguler', 'API integration untuk sistem otomasi', 'Customized reporting & analytics', 'Flexible payment terms 14–30 hari'] as $b)
                    <div class="flex items-center gap-4">
                        <div class="w-7 h-7 rounded-full bg-gray-100 shadow-[inset_3px_3px_6px_#c2c6cc,inset_-3px_-3px_6px_#ffffff] flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="text-gray-700 font-semibold text-sm">{{ $b }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div>
                <div class="bg-gray-100 rounded-[2.5rem] p-8 md:p-10 shadow-[16px_16px_32px_#c2c6cc,-16px_-16px_32px_#ffffff]">
                    <h3 class="text-xl font-black text-gray-800 uppercase tracking-wider mb-8">Request Penawaran</h3>
                    <form id="quoteForm" class="space-y-5">
                        @csrf
                        <div>
                            <label class="text-xs font-black text-gray-500 uppercase tracking-widest block mb-2">Nama Perusahaan</label>
                            <input type="text" name="company" placeholder="PT. Contoh Maju" class="cta-input" required />
                        </div>
                        <div>
                            <label class="text-xs font-black text-gray-500 uppercase tracking-widest block mb-2">Email Bisnis</label>
                            <input type="email" name="email" placeholder="hrd@perusahaan.co.id" class="cta-input" required />
                        </div>
                        <div>
                            <label class="text-xs font-black text-gray-500 uppercase tracking-widest block mb-2">No. Telepon</label>
                            <input type="tel" name="phone" placeholder="+62 812 3456 7890" class="cta-input" required />
                        </div>
                        <div>
                            <label class="text-xs font-black text-gray-500 uppercase tracking-widest block mb-2">Kebutuhan Logistik</label>
                            <textarea rows="3" name="needs" placeholder="Ceritakan kebutuhan distribusi Anda..." class="cta-input resize-none" required></textarea>
                        </div>
                        <div class="flex gap-4 pt-2">
                            <button type="submit" class="flex-1 bg-gray-100 text-gray-700 font-black uppercase tracking-widest text-sm py-4 rounded-2xl shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff] active:shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] hover:text-gray-900 transition-all">Kirim Request</button>
                            <a href="https://wa.me/6281234567890" target="_blank" class="w-14 flex items-center justify-center bg-gray-100 text-green-500 rounded-2xl shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff] active:shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] transition-all">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    
    const icon = type === 'success' 
        ? '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        : '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
    
    toast.innerHTML = `${icon}<span>${message}</span>`;
    document.body.appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 100);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
    }, 3500);
}

const form = document.getElementById('quoteForm');
form.addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Mengirim...';

    const formData = new FormData(form);
    const data = Object.fromEntries(formData);

    try {
        const response = await fetch('{{ route("quote.request") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showToast('Request berhasil dikirim! Kami akan menghubungi Anda segera.', 'success');
            form.reset();
        } else {
            showToast('Gagal mengirim request. Silakan coba lagi.', 'error');
        }
    } catch (error) {
        showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = originalText;
    }
});
</script>
@endpush
