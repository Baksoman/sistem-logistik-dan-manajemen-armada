@push('styles')
<style>
:root {
    --nm-bg: #E8ECEF;
    --nm-dark: #c2c6cc;
    --nm-light: #ffffff;
    --nm-dark2: #d1d5db;
    --nm-light2: #f9fafb;
}

/* OUT — timbul (tombol, ikon, badge, social) */
.nm-out    { box-shadow: 5px 5px 10px var(--nm-dark), -5px -5px 10px var(--nm-light); }
.nm-out-sm { box-shadow: 3px 3px 7px  var(--nm-dark), -3px -3px 7px  var(--nm-light); }
.nm-out-lg { box-shadow: 8px 8px 16px var(--nm-dark), -8px -8px 16px var(--nm-light); }

/* IN — tertekan (teks/label heading) */
.nm-in     { box-shadow: inset 4px 4px 8px var(--nm-dark), inset -4px -4px 8px var(--nm-light); }
.nm-in-sm  { box-shadow: inset 3px 3px 6px var(--nm-dark2), inset -3px -3px 6px var(--nm-light2); }

/* Social buttons */
.social-btn {
    width: 2.75rem; height: 2.75rem;
    border-radius: 0.5rem;
    background: var(--nm-bg);
    box-shadow: 5px 5px 10px var(--nm-dark), -5px -5px 10px var(--nm-light);
    display: flex; align-items: center; justify-content: center;
    color: #6b7280;
    transition: box-shadow 0.2s ease, transform 0.2s ease, color 0.2s;
}
.social-btn:hover {
    box-shadow: 7px 7px 14px var(--nm-dark), -7px -7px 14px var(--nm-light);
    transform: translateY(-3px);
    color: #374151;
}
.social-btn:active {
    box-shadow: inset 4px 4px 8px var(--nm-dark), inset -4px -4px 8px var(--nm-light);
    transform: translateY(0);
}

/* Nav links — OUT */
.footer-link {
    color: #6b7280;
    font-weight: 600;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.875rem;
    border-radius: 0.625rem;
    background: var(--nm-bg);
    box-shadow: 4px 4px 8px var(--nm-dark), -4px -4px 8px var(--nm-light);
    transition: box-shadow 0.2s ease, color 0.2s, transform 0.15s;
}
.footer-link:hover {
    color: #374151;
    box-shadow: inset 3px 3px 7px var(--nm-dark), inset -3px -3px 7px var(--nm-light);
    transform: none;
}

/* Section headings — IN (teks tercetak ke dalam) */
.nm-label {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.875rem;
    border-radius: 0.625rem;
    background: var(--nm-bg);
    box-shadow: inset 3px 3px 7px var(--nm-dark), inset -3px -3px 7px var(--nm-light);
    font-size: 0.7rem;
    font-weight: 900;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

/* Icon wrap — OUT */
.nm-icon {
    background: var(--nm-bg);
    box-shadow: 4px 4px 8px var(--nm-dark), -4px -4px 8px var(--nm-light);
    transition: box-shadow 0.2s, transform 0.2s;
}
.nm-icon:hover {
    box-shadow: 6px 6px 12px var(--nm-dark), -6px -6px 12px var(--nm-light);
    transform: scale(1.05);
}

/* Kontak row — teks IN */
.nm-contact-row {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.5rem 0.75rem;
    border-radius: 0.75rem;
    background: var(--nm-bg);
    box-shadow: inset 3px 3px 6px var(--nm-dark2), inset -3px -3px 6px var(--nm-light2);
}

/* Badge chips — OUT */
.nm-badge {
    background: var(--nm-bg);
    box-shadow: 5px 5px 10px var(--nm-dark), -5px -5px 10px var(--nm-light);
    border-radius: 0.875rem;
    padding: 0.5rem 0.875rem;
    font-size: 0.7rem;
    font-weight: 900;
    color: #4b5563;
    letter-spacing: 0.03em;
    transition: box-shadow 0.2s, transform 0.2s;
}
.nm-badge:hover {
    box-shadow: 7px 7px 14px var(--nm-dark), -7px -7px 14px var(--nm-light);
    transform: translateY(-2px);
}

/* Status online panel — IN */
.nm-status-panel {
    border-radius: 1rem;
    background: var(--nm-bg);
    box-shadow: inset 4px 4px 8px var(--nm-dark), inset -4px -4px 8px var(--nm-light);
    padding: 0.875rem 1rem;
}

/* Bottom pills — OUT */
.nm-pill {
    background: var(--nm-bg);
    box-shadow: 5px 5px 10px var(--nm-dark), -5px -5px 10px var(--nm-light);
    border-radius: 0.5rem;
    padding: 0.5rem 1.25rem;
}

.nm-text-link {
    font-size: 0.75rem; font-weight: 700; color: #9ca3af;
    padding: 0.4rem 0.875rem;
    border-radius: 0.5rem;
    background: var(--nm-bg);
    box-shadow: 4px 4px 8px var(--nm-dark), -4px -4px 8px var(--nm-light);
    transition: box-shadow 0.2s, color 0.2s;
}
.nm-text-link:hover {
    color: #374151;
    box-shadow: inset 3px 3px 7px var(--nm-dark), inset -3px -3px 7px var(--nm-light);
}

/* Divider */
.nm-divider {
    height: 2px;
    border-radius: 0.25rem;
    box-shadow: inset 1px 1px 2px var(--nm-dark), inset -1px -1px 2px var(--nm-light);
    background: transparent;
}

.dot-grid {
    position: absolute; inset: 0;
    background-image: radial-gradient(circle, #a8adb5 1px, transparent 1px);
    background-size: 24px 24px;
    pointer-events: none;
}
</style>
@endpush

<footer class="relative w-full bg-[#E8ECEF] pt-20 pb-8 px-6 overflow-hidden">
    <div class="dot-grid opacity-20"></div>

    <div class="relative z-10 max-w-6xl mx-auto">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">

            {{-- Col 1 : Brand --}}
            <div class="reveal-left flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-lg nm-icon flex items-center justify-center overflow-hidden p-1 flex-shrink-0">
                        <img src="{{ asset('assets/logo/logix-logo-only.jpg') }}" alt="LogiX" class="w-full h-full object-contain mix-blend-multiply">
                    </div>
                    <span class="text-2xl font-black tracking-tight text-gray-800" style="font-family:'Inter',sans-serif;">
                        Logi<span class="text-blue-600">X</span>
                    </span>
                </div>

                <p class="text-gray-500 text-sm font-medium leading-relaxed">
                    Platform manajemen logistik modern dengan tracking real-time, route optimization, dan warehouse management terpadu.
                </p>

                <div class="flex gap-3">
                    @foreach([
                        'facebook'  => 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z',
                        'instagram' => 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z',
                        'linkedin'  => 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z',
                        'twitter'   => 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z',
                    ] as $s => $path)
                    <a href="#" class="social-btn" aria-label="{{ $s }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="{{ $path }}"/></svg>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Col 2 : Navigasi --}}
            <div class="reveal flex flex-col gap-5">
                <div class="nm-label w-fit">
                    <svg class="w-3 h-3 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                    Navigasi
                </div>
                <nav class="flex flex-col gap-2">
                    @foreach([
                        ['url' => url('/').'#hero',             'label' => 'Home'],
                        ['url' => url('/').'#stats-section',    'label' => 'Statistik'],
                        ['url' => url('/').'#services-section', 'label' => 'Layanan'],
                        ['url' => url('/').'#why-section',      'label' => 'Keunggulan'],
                        ['url' => url('/').'#faq-section',      'label' => 'FAQ'],
                        ['url' => url('/').'#cta-section',      'label' => 'Bisnis'],
                        ['url' => route('login'),               'label' => 'Login'],
                    ] as $nav)
                    <a href="{{ $nav['url'] }}" class="footer-link">
                        <svg class="w-3 h-3 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                        {{ $nav['label'] }}
                    </a>
                    @endforeach
                </nav>
            </div>

            {{-- Col 3 : Kontak --}}
            <div class="reveal flex flex-col gap-5">
                <div class="nm-label w-fit">
                    <svg class="w-3 h-3 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Kontak Kami
                </div>
                <div class="flex flex-col gap-2">
                    @foreach([
                        ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'text' => 'Jl. Logistik Raya No. 123, Jakarta Selatan 12345'],
                        ['icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'text' => '+62 21 1234 5678'],
                        ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'text' => 'cs@logix.co.id'],
                        ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'Senin - Sabtu 08.00 - 17.00'],
                    ] as $c)
                    <div class="nm-contact-row">
                        <div class="w-8 h-8 rounded-lg nm-icon flex items-center justify-center flex-shrink-0 text-gray-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @foreach(explode(' M', $c['icon']) as $j => $part)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $j === 0 ? $part : 'M'.$part }}"></path>
                                @endforeach
                            </svg>
                        </div>
                        <span class="text-gray-500 text-sm font-medium leading-relaxed">{{ $c['text'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Col 4 : Trust badges --}}
            <div class="reveal-right flex flex-col gap-5">
                <div class="nm-label w-fit">
                    <svg class="w-3 h-3 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Dipercaya Oleh
                </div>
                <div class="flex gap-3 flex-wrap">
                    @foreach(['ISO 9001','Trusted Shipper','IATA Member'] as $badge)
                    <div class="nm-badge">{{ $badge }}</div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="nm-divider mb-8"></div>

        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="nm-pill">
                <p class="text-xs font-bold text-gray-400">© {{ date('Y') }} LogiX - Sistem Logistik dan Manajemen Armada. All rights reserved.</p>
            </div>
            <div class="flex gap-3">
                <a href="#" class="nm-text-link">Syarat &amp; Ketentuan</a>
                <a href="#" class="nm-text-link">Kebijakan Privasi</a>
            </div>
        </div>
    </div>
</footer>