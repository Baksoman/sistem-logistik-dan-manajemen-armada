<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Logistik') }} - Dashboard</title>

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
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-72 bg-gray-100 shadow-[8px_0_16px_#d1d5db] transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0 lg:flex lg:flex-col">
            <div class="flex items-center justify-center h-24 shadow-[0_4px_6px_-1px_#d1d5db]">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] flex items-center justify-center font-bold text-2xl text-gray-800">L</div>
                    <span class="text-2xl font-bold tracking-widest text-gray-800 uppercase">Logistik</span>
                </div>
            </div>

            <nav class="flex-1 px-6 py-8 space-y-4 overflow-y-auto">
                @php
                    $user = auth()->user();
                    
                    // Default menu for all
                    $navItems = [
                        ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ];

                    // Check if role method exists to prevent crashes
                    $hasRole = function($role) use ($user) {
                        return $user && method_exists($user, 'hasRole') && $user->hasRole($role);
                    };

                    if ($hasRole('Super Admin')) {
                        $navItems[] = ['name' => 'User Management', 'route' => 'users.index', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'];
                        $navItems[] = ['name' => 'Access Control', 'route' => 'rbac.index', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'];

                        $navItems[] = ['name' => 'Drivers List', 'route' => 'drivers.index', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'];
                        $navItems[] = ['name' => 'Route Opt.', 'route' => 'routes.index', 'icon' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7l6-2 5.447 2.724A1 1 0 0121 8.618v10.764a1 1 0 01-1.447.894L15 17l-6 2z'];
                        $navItems[] = [
                            'name' => 'Fleet Management',
                            'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
                            'items' => [
                                ['name' => 'Vehicles List', 'route' => 'fleet.index'],
                                ['name' => 'Maintenance', 'route' => 'fleet.maintenances.index']
                            ]
                        ];
                    } elseif ($hasRole('Admin Logistik')) {
                        $navItems[] = ['name' => 'Orders', 'route' => null, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'];
                        $navItems[] = ['name' => 'Shipments', 'route' => null, 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'];
                        $navItems[] = ['name' => 'Route Opt.', 'route' => 'routes.index', 'icon' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7l6-2 5.447 2.724A1 1 0 0121 8.618v10.764a1 1 0 01-1.447.894L15 17l-6 2z'];
                        $navItems[] = ['name' => 'Vehicles & Drivers', 'route' => 'fleet.index', 'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'];

                    } elseif ($hasRole('Driver')) {
                        $navItems[] = ['name' => 'My Schedule', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'];
                        $navItems[] = ['name' => 'Navigation', 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z'];
                        $navItems[] = ['name' => 'Upload POD', 'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12'];
                    }
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
                @if($hasRole('Super Admin') || $hasRole('Staff Warehouse'))
                    <a href="{{ route('warehouse.dashboard') }}" class="w-full flex items-center justify-center gap-3 px-4 py-3 rounded-2xl text-gray-100 font-bold bg-gray-800 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:bg-gray-700 transition-all text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Menuju Panel Warehouse
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
