<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>LogiX | @yield('title', ' ')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CDN & Alpine (For immediate frontend shell rendering) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Mapbox GL JS -->
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.css" rel="stylesheet">
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.js"></script>

    <!-- GSAP -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollToPlugin.min.js"></script>

    <!-- QR Scanner -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Scripts (Vite for Laravel) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *{
            scroll-behavior: smooth !important;
        }

        body {
            font-family: 'Poppins', sans-serif;
            /* scroll-padding-top: 120px; */
        }

        [x-cloak] {
            display: none !important;
        }

        /* Active nav link */
        .nav-link.active {
            background: #e8ecef;
            box-shadow: inset 3px 3px 6px #d1d5db, inset -3px -3px 6px #ffffff;
            color: #111827;
        }
    </style>
    @stack('styles')
</head>

<body
    class="font-sans text-gray-900 antialiased bg-gray-100 min-h-screen flex flex-col selection:bg-gray-300 selection:text-gray-900">

    <!-- Animated Navbar Container -->
    <div x-data="{ scrolled: false, mobileMenuOpen: false }" @scroll.window="scrolled = (window.pageYOffset > 40)"
        class="fixed top-0 inset-x-0 z-50 flex justify-center transition-all duration-500 ease-in-out"
        :class="scrolled ? 'pt-4' : 'pt-0'">

        <nav class="bg-gray-100 flex justify-between items-center transition-all duration-500 ease-in-out relative"
            :class="scrolled ? 'w-[95%] md:w-[85%] lg:w-[75%] xl:w-[65%] py-3 px-6 md:px-8 rounded-full shadow-[8px_8px_16px_#d1d5db,-8px_-8px_16px_#ffffff]' : 'w-full py-5 px-6 md:px-12 rounded-b-3xl shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff]'">

            <!-- Logo -->
            <div class="flex items-center gap-3 transition-transform duration-500"
                :class="scrolled ? 'scale-90' : 'scale-100'">
                <div class="w-10 h-10 rounded-2xl bg-gray-100 flex items-center justify-center shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff] relative overflow-hidden group p-1">
                    <img src="{{ asset('assets/logo/logix-logo-only.jpg') }}" alt="LogiX Logo" class="w-full h-full object-contain mix-blend-multiply">
                </div>
                <span class="text-2xl font-black tracking-tight text-gray-800" style="font-family: 'Inter', sans-serif;">
                    Logi<span class="text-blue-600">X</span>
                </span>
            </div>

            <!-- Desktop Links -->
            <div class="hidden lg:flex items-center gap-2 transition-all duration-500">
                <a href="{{ url('/') }}#hero"
                    class="nav-link text-sm font-semibold text-gray-600 hover:text-gray-900 transition-all duration-300 px-3 py-2 rounded-xl hover:shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff]">Home</a>
                <a href="{{ url('/') }}#stats-section"
                    class="nav-link text-sm font-semibold text-gray-600 hover:text-gray-900 transition-all duration-300 px-3 py-2 rounded-xl hover:shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff]">Statistik</a>
                <a href="{{ url('/') }}#services-section"
                    class="nav-link text-sm font-semibold text-gray-600 hover:text-gray-900 transition-all duration-300 px-3 py-2 rounded-xl hover:shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff]">Layanan</a>
                <a href="{{ url('/') }}#why-section"
                    class="nav-link text-sm font-semibold text-gray-600 hover:text-gray-900 transition-all duration-300 px-3 py-2 rounded-xl hover:shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff]">Keunggulan</a>
                <a href="{{ url('/') }}#faq-section"
                    class="nav-link text-sm font-semibold text-gray-600 hover:text-gray-900 transition-all duration-300 px-3 py-2 rounded-xl hover:shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff]">FAQ</a>
                <a href="{{ url('/') }}#cta-section"
                    class="nav-link text-sm font-semibold text-gray-600 hover:text-gray-900 transition-all duration-300 px-3 py-2 rounded-xl hover:shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff]">Kontak</a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="text-sm font-semibold text-gray-700 hover:text-gray-900 transition-all duration-300 px-5 py-2 rounded-xl shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[6px_6px_12px_#c2c6cc,-6px_-6px_12px_#ffffff] hover:-translate-y-0.5 active:shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff] active:translate-y-0 ml-2">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-sm font-semibold text-gray-700 hover:text-gray-900 transition-all duration-300 px-5 py-2 rounded-xl shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[6px_6px_12px_#c2c6cc,-6px_-6px_12px_#ffffff] hover:-translate-y-0.5 active:shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff] active:translate-y-0 ml-2">Login</a>
                    @endauth
                @endif
            </div>

            <!-- Mobile Hamburger Button -->
            <div class="lg:hidden flex items-center">
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="w-10 h-10 rounded-full flex items-center justify-center text-gray-700 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all focus:outline-none">
                    <svg x-show="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-5 h-5" x-cloak fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu Dropdown -->
            <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 -translate-y-4" @click.away="mobileMenuOpen = false" x-cloak
                class="absolute top-full left-0 right-0 mt-4 mx-auto w-[90%] lg:hidden bg-gray-100 rounded-[2rem] shadow-[12px_12px_24px_#c2c6cc,-12px_-12px_24px_#ffffff] p-6 flex flex-col gap-3 border border-white/40">

                <a href="{{ url('/') }}#hero" @click="mobileMenuOpen = false"
                    class="text-center font-bold text-gray-700 hover:text-gray-900 px-4 py-3 rounded-2xl shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] transition-all bg-gray-100">Home</a>
                <a href="{{ url('/') }}#stats-section" @click="mobileMenuOpen = false"
                    class="text-center font-bold text-gray-600 hover:text-gray-900 px-4 py-3 rounded-2xl shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] transition-all bg-gray-100">Statistik</a>
                <a href="{{ url('/') }}#services-section" @click="mobileMenuOpen = false"
                    class="text-center font-bold text-gray-600 hover:text-gray-900 px-4 py-3 rounded-2xl shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] transition-all bg-gray-100">Layanan</a>
                <a href="{{ url('/') }}#why-section" @click="mobileMenuOpen = false"
                    class="text-center font-bold text-gray-600 hover:text-gray-900 px-4 py-3 rounded-2xl shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] transition-all bg-gray-100">Keunggulan</a>
                <a href="{{ url('/') }}#faq-section" @click="mobileMenuOpen = false"
                    class="text-center font-bold text-gray-600 hover:text-gray-900 px-4 py-3 rounded-2xl shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] transition-all bg-gray-100">FAQ</a>
                <a href="{{ url('/') }}#cta-section" @click="mobileMenuOpen = false"
                    class="text-center font-bold text-gray-600 hover:text-gray-900 px-4 py-3 rounded-2xl shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] transition-all bg-gray-100">Bisnis</a>

                <div class="h-px w-full bg-gray-300 shadow-[0_1px_0_#ffffff] my-1"></div>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="text-center font-extrabold text-gray-800 px-4 py-3 rounded-2xl shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff] active:shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] transition-all bg-gray-100">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-center font-extrabold text-gray-800 px-4 py-3 rounded-2xl shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff] active:shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] transition-all bg-gray-100">Login</a>
                    @endauth
                @endif
            </div>
        </nav>
    </div>

    <div class="flex-grow flex flex-col items-center">
        @yield('content')
    </div>
    
    @stack('scripts')

    <script>
    // Active nav link based on scroll position - ONLY on homepage
    document.addEventListener('DOMContentLoaded', function() {
        // Check if we're on the homepage
        const isHomepage = window.location.pathname === '/' || window.location.pathname === '/home';
        
        if (!isHomepage) {
            // If not on homepage, remove all active states
            return;
        }
        
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.nav-link');
        
        function setActiveLink() {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (pageYOffset >= (sectionTop - 150)) {
                    current = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').includes(current)) {
                    link.classList.add('active');
                }
            });
        }
        
        window.addEventListener('scroll', setActiveLink);
        setActiveLink();
    });
    </script>
</body>

</html>