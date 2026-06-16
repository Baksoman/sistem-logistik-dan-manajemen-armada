<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Driver PWA')</title>

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#f3f4f6">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,600,700,900|outfit:600,800,900&display=swap" rel="stylesheet" />

    <!-- Tailwind & Alpine -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Toastify for Mobile Alerts -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <!-- Leaflet (Optional if there's map) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; -webkit-tap-highlight-color: transparent; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }
        
        /* Neumorphism Utilities Mobile */
        .neu-flat { box-shadow: 6px 6px 12px #d1d5db, -6px -6px 12px #ffffff; }
        .neu-pressed { box-shadow: inset 4px 4px 8px #d1d5db, inset -4px -4px 8px #ffffff; }
        .neu-btn:active { box-shadow: inset 2px 2px 4px #d1d5db, inset -2px -2px 4px #ffffff; transform: scale(0.98); }
    </style>
</head>
<body class="text-gray-800 pb-24 overflow-x-hidden selection:bg-blue-200">

    <!-- Top Header -->
    <header class="sticky top-0 z-40 bg-gray-100/90 backdrop-blur-md px-6 py-5 rounded-b-3xl neu-flat flex justify-between items-center mb-6">
        <div>
            <p class="text-xs font-bold text-gray-400 tracking-widest uppercase">Driver Workspace</p>
            <h1 class="text-2xl font-black text-gray-800">@yield('title', 'Dashboard')</h1>
        </div>
        <div class="w-12 h-12 rounded-full neu-flat flex items-center justify-center bg-gray-100">
            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>
    </header>

    <!-- Main Content -->
    <main class="px-6 space-y-6">
        @yield('content')
    </main>

    <!-- Bottom Navigation -->
    <nav class="fixed bottom-6 left-6 right-6 bg-gray-100 rounded-3xl px-6 py-4 flex justify-between items-center neu-flat z-50">
        <a href="#" class="flex flex-col items-center text-blue-600 gap-1 transition">
            <div class="w-10 h-10 rounded-2xl neu-pressed flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            </div>
            <span class="text-[10px] font-bold">Home</span>
        </a>

        <a href="#" class="flex flex-col items-center text-gray-400 hover:text-blue-500 gap-1 transition">
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
            </div>
            <span class="text-[10px] font-bold">Route</span>
        </a>

        <a href="#" class="flex flex-col items-center text-gray-400 hover:text-blue-500 gap-1 transition">
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path></svg>
            </div>
            <span class="text-[10px] font-bold">Costs</span>
        </a>
    </nav>

    <!-- Global Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Toastify({ text: "{{ session('success') }}", duration: 3000, gravity: "top", position: "center", style: { background: "#10b981", borderRadius: "1rem" } }).showToast();
            @endif
            @if(session('error'))
                Toastify({ text: "{{ session('error') }}", duration: 3000, gravity: "top", position: "center", style: { background: "#ef4444", borderRadius: "1rem" } }).showToast();
            @endif
        });
    </script>
    @stack('scripts')
</body>
</html>
