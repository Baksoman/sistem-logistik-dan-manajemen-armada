<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Logistic | @yield('title', 'Dashboard')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|outfit:600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Toastify & SweetAlert2 -->
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Tailwind CDN & Alpine -->
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
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('dataTable', (config) => ({
                    data: config.initialData || [],
                    meta: config.initialMeta || {},
                    endpoint: config.endpoint || '',
                    query: config.initialQuery || '',
                    filters: config.initialFilters || {},
                    page: config.initialMeta?.current_page || 1,
                    isLoading: false,
                    filterModalOpen: false,

                    init() {
                        this.$watch('query', () => { 
                            this.page = 1; 
                            this.fetchData(); 
                        });
                        
                        this.$watch('filters', () => { 
                            this.page = 1; 
                            this.fetchData(); 
                        }, { deep: true });
                    },

                    async fetchData() {
                        if (!this.endpoint) return;
                        this.isLoading = true;
                        try {
                            const url = new URL(this.endpoint, window.location.origin);
                            if (this.query) url.searchParams.append('search', this.query);
                            if (this.page > 1) url.searchParams.append('page', this.page);
                            
                            for (const [key, value] of Object.entries(this.filters)) {
                                if (value !== '' && value !== null && value !== undefined) {
                                    url.searchParams.append(key, value);
                                }
                            }

                            const stateUrl = new URL(window.location.href);
                            stateUrl.search = url.search;
                            window.history.replaceState({}, '', stateUrl);

                            const response = await fetch(url, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            if (!response.ok) throw new Error('Network response was not ok');
                            const result = await response.json();
                            this.data = result.data;
                            this.meta = result.meta;
                        } catch (error) {
                            console.error('Error fetching data:', error);
                        } finally {
                            this.isLoading = false;
                        }
                    },
                    
                    changePage(urlStr) {
                        if (!urlStr) return;
                        const url = new URL(urlStr);
                        const pageStr = url.searchParams.get('page');
                        if (pageStr) {
                            this.page = parseInt(pageStr);
                            this.fetchData();
                        }
                    },

                    resetFilters() {
                        this.filters = {};
                        this.filterModalOpen = false;
                    },

                    applyFilters() {
                        this.filterModalOpen = false;
                        this.page = 1;
                        this.fetchData();
                    }
                }));
            });
        </script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Poppins', sans-serif; }
            h1, h2, h3, h4, h5, h6, .is-title { font-family: 'Outfit', sans-serif; }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-100 min-h-screen text-gray-800 flex overflow-hidden" x-data="{ sidebarOpen: false }">
        
        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-gray-900/50 lg:hidden" @click="sidebarOpen = false" x-cloak></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-72 bg-gray-100 shadow-[8px_0_24px_rgba(0,0,0,0.15)] lg:m-6 lg:h-[calc(100vh-3rem)] lg:rounded-[2rem] lg:shadow-[12px_12px_24px_#c2c6cc,-12px_-12px_24px_#ffffff] transition-all duration-300 lg:translate-x-0 lg:static lg:flex lg:flex-col">
            <x-sidebar-logo panel="Logistik" />
            <nav class="flex-1 px-6 py-8 space-y-4 overflow-y-auto">
                @php
                    $navItems = [
                        ['name' => 'Dashboard', 'route' => 'dashboard.logistik.index', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                        ['name' => 'Orders', 'route' => 'orders.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                        ['name' => 'Shipments', 'route' => 'shipments.index', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                        ['name' => 'Route Opt.', 'route' => 'routes.index', 'icon' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7l6-2 5.447 2.724A1 1 0 0121 8.618v10.764a1 1 0 01-1.447.894L15 17l-6 2z'],
                        ['name' => 'Tariffs', 'route' => 'tariffs.index', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['name' => 'Op. Costs', 'route' => 'operational-costs.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        [
                            'name' => 'Fleet & Drivers',
                            'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
                            'items' => [
                                ['name' => 'Vehicles List', 'route' => 'fleet.index'],
                                ['name' => 'Drivers List', 'route' => 'drivers.index'],
                                ['name' => 'Maintenance', 'route' => 'fleet.maintenances.index']
                            ]
                        ]
                    ];
                @endphp

                @foreach($navItems as $item)
                    @if(isset($item['items']))
                        @php
                            $isActiveGroup = false;
                            if (isset($item['items'])) {
                                foreach($item['items'] as $subItem) {
                                    if (request()->routeIs($subItem['route'] ?? '')) {
                                        $isActiveGroup = true;
                                        break;
                                    }
                                }
                            }
                        @endphp
                        <div x-data="{ open: {{ $isActiveGroup ? 'true' : 'false' }} }" class="space-y-1">
                            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-600 rounded-2xl transition-all duration-200 {{ $isActiveGroup ? 'shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff] text-gray-900 font-bold bg-gray-200/50' : 'hover:shadow-[3px_3px_6px_#d1d5db,-3px_-3px_6px_#ffffff] hover:text-gray-900' }}">
                                <div class="flex items-center gap-4">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path></svg>
                                    <span class="text-sm font-semibold">{{ $item['name'] }}</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" x-collapse class="pl-12 pr-4 space-y-1 py-1">
                                @foreach($item['items'] as $subItem)
                                    <a href="{{ isset($subItem['route']) ? route($subItem['route']) : '#' }}" class="block px-4 py-2 text-sm text-gray-600 rounded-xl transition-all duration-200 {{ request()->routeIs($subItem['route'] ?? '') ? 'shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] text-blue-600 font-bold bg-gray-200/30' : 'hover:shadow-[2px_2px_4px_#d1d5db,-2px_-2px_4px_#ffffff] hover:text-gray-900' }}">
                                        {{ $subItem['name'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ isset($item['route']) ? route($item['route']) : '#' }}" class="flex items-center gap-4 px-4 py-3 text-gray-600 rounded-2xl transition-all duration-200 {{ request()->routeIs($item['route'] ?? '') ? 'shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff] text-gray-900 font-bold bg-gray-200/50' : 'hover:shadow-[3px_3px_6px_#d1d5db,-3px_-3px_6px_#ffffff] hover:text-gray-900' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path></svg>
                            <span class="text-sm font-semibold">{{ $item['name'] }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>

            <div class="p-6 pt-0 space-y-3">
                @if(auth()->user()->hasRole('Super Admin'))
                    <a href="{{ route('dashboard') }}" class="w-full flex items-center justify-center gap-3 px-4 py-3 rounded-2xl text-gray-100 font-bold bg-gray-800 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:bg-gray-700 transition-all text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Back to App
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') ?? '#' }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-3 px-4 py-3 rounded-2xl text-gray-600 font-bold hover:text-red-500 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Log Out
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 overflow-hidden p-4 lg:p-6 lg:pl-0 lg:pb-6 h-screen">
            <!-- Unified Parent -->
            <main class="h-full flex flex-col bg-gray-100 overflow-hidden">
                <!-- Page Content Area -->
                <div class="flex-1 overflow-x-hidden overflow-y-auto px-6 lg:px-8 pb-8 pt-4">
                    @yield('content')
                </div>
            </main>
        </div>

        <script>
            function confirmDelete(formId, message = 'Apakah Anda yakin ingin menghapus data ini?') {
                Swal.fire({
                    title: 'Konfirmasi',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    background: '#f3f4f6',
                    backdrop: 'rgba(243, 244, 246, 0.85)', // Blend Neumorphism shadow with light backdrop
                    color: '#374151',
                    customClass: {
                        popup: 'rounded-[2rem] shadow-[12px_12px_24px_#d1d5db,-12px_-12px_24px_#ffffff] border border-white/40',
                        confirmButton: 'rounded-2xl font-bold text-gray-100 bg-red-500 shadow-[4px_4px_8px_#d1d5db] active:shadow-[inset_2px_2px_4px_#991b1b] px-6 py-3 ml-4 border-none hover:bg-red-600 transition',
                        cancelButton: 'rounded-2xl font-bold text-gray-700 bg-gray-100 shadow-[4px_4px_8px_#d1d5db] active:shadow-[inset_2px_2px_4px_#d1d5db] px-6 py-3 border-none hover:text-blue-600 transition'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(formId).submit();
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                @if(session('success'))
                    Toastify({
                        text: "{{ session('success') }}",
                        duration: 4000,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "#dcfce7",
                            color: "#166534",
                            boxShadow: "4px 4px 8px #d1d5db, -4px -4px 8px #ffffff",
                            borderRadius: "1rem",
                            padding: "16px 24px",
                            fontWeight: "bold",
                            border: "2px solid #bbf7d0"
                        }
                    }).showToast();
                @endif

                @if(session('error'))
                    Toastify({
                        text: "{{ session('error') }}",
                        duration: 4000,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "#fee2e2",
                            color: "#991b1b",
                            boxShadow: "4px 4px 8px #d1d5db, -4px -4px 8px #ffffff",
                            borderRadius: "1rem",
                            padding: "16px 24px",
                            fontWeight: "bold",
                            border: "2px solid #fecaca"
                        }
                    }).showToast();
                @endif

                @if($errors->any())
                    @foreach($errors->all() as $error)
                        Toastify({
                            text: "{{ str_replace('"', '\"', $error) }}",
                            duration: 5000,
                            gravity: "top",
                            position: "right",
                            style: {
                                background: "#fee2e2",
                                color: "#991b1b",
                                boxShadow: "4px 4px 8px #d1d5db, -4px -4px 8px #ffffff",
                                borderRadius: "1rem",
                                padding: "16px 24px",
                                fontWeight: "bold",
                                border: "2px solid #fecaca"
                            }
                        }).showToast();
                    @endforeach
                @endif
            });
        </script>
    </body>
</html>
