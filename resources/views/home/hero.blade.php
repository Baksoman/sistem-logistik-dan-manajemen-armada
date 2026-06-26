@push('styles')
    <style>
        #hero {
            position: relative;
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            overflow: hidden;
            padding-top: 8rem;
            padding-bottom: 4rem;
        }

        #hero-map {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        /* Paksa canvas Mapbox ikut ukuran container */
        #hero-map .mapboxgl-canvas-container,
        #hero-map .mapboxgl-canvas {
            width: 100% !important;
            height: 100% !important;
        }

        #hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 70% 60% at 50% 50%, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 40%, rgba(255, 255, 255, 0.1) 100%);
            z-index: 1;
            pointer-events: none;
        }

        .mapboxgl-control-container {
            visibility: hidden !important;
        }

        .neo-badge {
            background: #e8ecef;
            border-radius: 999px;
            padding: 0.35rem 1rem;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            box-shadow: inset 3px 3px 6px #c2c6cc, inset -3px -3px 6px #ffffff;
            color: #6b7280;
        }

        /* Zoom effect for hero */
        #hero-content {
            transform-origin: center center;
            will-change: transform, opacity;
        }

        .section-fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }

        .section-fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
@endpush

<section id="hero" class="pt-32 pb-10">
    <div id="hero-map"></div>
    <div id="hero-content" class="relative z-10 w-full max-w-5xl px-6 flex flex-col items-center text-center">
        <div class="neo-badge" id="hero-eyebrow">Platform Logistik #1 Indonesia</div>
        <h1 id="hero-title"
            class="mt-6 text-5xl md:text-7xl font-black text-gray-100 leading-tight tracking-tight drop-shadow-[2px_4px_6px_rgba(209,213,219,0.6)]"
            style="font-family:'Inter',sans-serif;">
            Sistem Logistik,<br><span class="text-gray-100" id="hero-accent">Aman &amp; Terpercaya</span>
        </h1>
        <p id="hero-sub" class="mt-6 text-lg md:text-xl text-gray-100 max-w-2xl font-medium leading-relaxed">
            Lacak pengiriman secara real-time dan kelola armada dengan efisiensi maksimal melalui platform manajemen
            terpusat.
        </p>

        <div id="hero-card" class="mt-12 mb-10 w-full max-w-3xl bg-gray-100 rounded-3xl p-8" x-data="qrScanner()">
            <h2 class="text-xl font-bold text-gray-700 mb-6 tracking-tight">Cek Resi Pengiriman</h2>
            <form action="{{ route('track.search') }}" method="GET" class="flex flex-col sm:flex-row gap-4"
                id="track-form">
                <input id="tracking_id" name="tracking_id" value="{{ request('tracking_id') }}" x-model="trackingId"
                    type="text" placeholder="Contoh: ORD-12345678"
                    class="flex-1 bg-gray-100 rounded-2xl px-6 py-4 text-base font-semibold text-gray-800 shadow-[inset_6px_6px_12px_#d1d5db,inset_-6px_-6px_12px_#ffffff] border-none focus:ring-0 focus:outline-none placeholder-gray-400 tracking-wide"
                    required />
                <div class="flex gap-3">
                    <button type="button" @click="openScanner"
                        class="w-14 h-[56px] flex items-center justify-center text-gray-500 hover:text-gray-700 bg-gray-100 rounded-2xl shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff] active:shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                            </path>
                        </svg>
                    </button>
                    <button type="submit"
                        class="flex-1 sm:w-32 bg-gray-100 text-gray-700 text-base font-bold tracking-wide rounded-2xl py-4 px-6 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff] active:shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] hover:text-gray-900 transition-all">Lacak</button>
                </div>
            </form>
            @if(isset($error))
                <div
                    class="mt-8 p-5 bg-gray-100 rounded-2xl shadow-[inset_5px_5px_10px_#d1d5db,inset_-5px_-5px_10px_#ffffff] border-l-4 border-red-500 flex items-center gap-4 text-red-600 font-bold">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>{{ $error }}</p>
                </div>
            @elseif(isset($type) && isset($data))
                @include('partials.track-result', ['type' => $type, 'data' => $data, 'latestGps' => $latestGps ?? null])
            @endif

            <div x-show="isScanning" x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-md">
                <div @click.away="closeScanner"
                    class="bg-gray-100 rounded-[2.5rem] p-8 w-full max-w-lg shadow-[16px_16px_32px_#000000,-16px_-16px_32px_#ffffff]">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-black text-gray-700 uppercase tracking-widest">Scan QR Code</h3>
                        <button @click="closeScanner" type="button"
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] text-gray-600 hover:text-red-500 active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div
                        class="p-4 bg-gray-100 rounded-3xl shadow-[inset_6px_6px_12px_#d1d5db,inset_-6px_-6px_12px_#ffffff]">
                        <div id="reader" class="w-full bg-black rounded-2xl overflow-hidden min-h-[300px]"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
    <script>
        (function () {
            mapboxgl.accessToken = '{{ config('services.mapbox.token') }}';

            const map = new mapboxgl.Map({
                container: 'hero-map',
                center: [103.851959, 1.290270],
                zoom: 16.1,
                pitch: 62,
                bearing: -20,
                style: 'mapbox://styles/mapbox/standard',
                interactive: true,
                attributionControl: false,
                renderWorldCopies: false,
                maxZoom: 16.1,
                minZoom: 16.1
            });

            map.on('style.load', () => {
                map.setConfigProperty('basemap', 'lightPreset', 'dusk');

                // Sembunyikan semua label
                map.setConfigProperty('basemap', 'showPlaceLabels', false);
                map.setConfigProperty('basemap', 'showPointOfInterestLabels', false);
                map.setConfigProperty('basemap', 'showRoadLabels', false);
                map.setConfigProperty('basemap', 'showTransitLabels', false);
            });

            window.addEventListener('resize', () => map.resize());
        })();

        @if(isset($latestGps) && $latestGps)
            document.addEventListener('DOMContentLoaded', function () {
                const trackMap = L.map('tracking-map', { zoomControl: false }).setView([{{ $latestGps->latitude }}, {{ $latestGps->longitude }}], 15);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { attribution: '&copy; OpenStreetMap &copy; CARTO' }).addTo(trackMap);
                const icon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div class="w-10 h-10 bg-gray-500 rounded-full border-4 border-white flex items-center justify-center shadow-lg"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg></div>`,
                    iconSize: [40, 40],
                    iconAnchor: [20, 20]
                });
                L.marker([{{ $latestGps->latitude }}, {{ $latestGps->longitude }}], { icon }).addTo(trackMap).bindPopup("<b class='text-gray-700'>Kurir sedang di perjalanan!</b>");
            });
        @endif



        document.addEventListener('alpine:init', () => {
            Alpine.data('qrScanner', () => ({
                trackingId: '', isScanning: false, html5QrcodeScanner: null,
                openScanner() {
                    this.isScanning = true;
                    this.$nextTick(() => {
                        if (!this.html5QrcodeScanner) this.html5QrcodeScanner = new Html5Qrcode("reader");
                        this.html5QrcodeScanner.start(
                            { facingMode: "environment" },
                            { fps: 10, qrbox: { width: 250, height: 250 } },
                            (decoded) => { this.trackingId = decoded; this.closeScanner(); setTimeout(() => document.getElementById('track-form').submit(), 300); },
                            () => { }
                        ).catch(err => { console.error(err); alert("Gagal mengakses kamera."); this.isScanning = false; });
                    });
                },
                closeScanner() {
                    this.isScanning = false;
                    if (this.html5QrcodeScanner?.isScanning) this.html5QrcodeScanner.stop().catch(console.error);
                }
            }));
        });

        window.addEventListener('scroll', () => {
            document.getElementById('scroll-progress').style.width =
                (document.documentElement.scrollTop / (document.documentElement.scrollHeight - document.documentElement.clientHeight) * 100) + '%';
        });

        // Reset tracking on page refresh - clear query params
        window.addEventListener('load', () => {
            const url = new URL(window.location);
            if (url.searchParams.has('tracking_id')) {
                // Only clear on refresh, not on initial load with results
                const perfData = window.performance.getEntriesByType('navigation')[0];
                if (perfData && perfData.type === 'reload') {
                    window.location.href = '/';
                }
            }
        });

        // Section fade in on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.section-fade-in').forEach(section => {
            observer.observe(section);
        });
    </script>
@endpush