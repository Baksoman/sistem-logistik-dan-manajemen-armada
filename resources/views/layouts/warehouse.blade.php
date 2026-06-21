<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Warehouse | @yield('title', 'Dashboard')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|outfit:600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Toastify & SweetAlert2 -->
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Barcode / QR Scanner -->
        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

        <!-- jQuery and Select2 -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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

            /* Select2 Neomorphism Styling */
            .select2-container--default .select2-selection--single {
                background-color: #f3f4f6 !important;
                border: none !important;
                border-radius: 1rem !important;
                height: 3.5rem !important;
                padding: 0.875rem 1.25rem !important;
                box-shadow: inset 4px 4px 8px #d1d5db, inset -4px -4px 8px #ffffff !important;
                display: flex !important;
                align-items: center !important;
            }
            .select2-container--default.select2-container--focus .select2-selection--single {
                outline: none !important;
                box-shadow: inset 6px 6px 12px #d1d5db, inset -6px -6px 12px #ffffff !important;
            }
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: #4b5563 !important;
                font-weight: 500 !important;
                padding-left: 0 !important;
                line-height: normal !important;
            }
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 100% !important;
                right: 1.25rem !important;
            }
            .select2-dropdown {
                background-color: #f3f4f6 !important;
                border: none !important;
                border-radius: 1rem !important;
                box-shadow: 8px 8px 16px #d1d5db, -8px -8px 16px #ffffff !important;
                overflow: hidden !important;
                margin-top: 0.5rem !important;
                z-index: 999999 !important;
            }
            .select2-search--dropdown {
                padding: 1rem !important;
            }
            .select2-search--dropdown .select2-search__field {
                background-color: #f3f4f6 !important;
                border: none !important;
                border-radius: 0.75rem !important;
                padding: 0.75rem 1rem !important;
                box-shadow: inset 3px 3px 6px #d1d5db, inset -3px -3px 6px #ffffff !important;
                outline: none !important;
                color: #4b5563 !important;
            }
            .select2-results__option {
                padding: 0.75rem 1.25rem !important;
                color: #4b5563 !important;
                font-weight: 500 !important;
                transition: all 0.2s !important;
            }
            .select2-results__option--highlighted.select2-results__option--selectable {
                background-color: #e5e7eb !important;
                color: #1f2937 !important;
            }
            .select2-results__option--selected {
                background-color: #d1d5db !important;
                color: #1f2937 !important;
            }
        </style>
        @stack('head')
    </head>
    <body class="font-sans antialiased bg-gray-100 min-h-screen text-gray-800 flex overflow-hidden" x-data="{ sidebarOpen: false }">
        
        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-gray-900/50 lg:hidden" @click="sidebarOpen = false" x-cloak></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-72 bg-gray-100 shadow-[8px_0_16px_#d1d5db] transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0 lg:flex lg:flex-col">
            <div class="flex items-center justify-center h-24 shadow-[0_4px_6px_-1px_#d1d5db]">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] flex items-center justify-center font-bold text-2xl text-amber-700">W</div>
                    <span class="text-2xl font-bold tracking-widest text-gray-800 uppercase">Warehouse</span>
                </div>
            </div>

            <nav class="flex-1 px-6 py-8 space-y-4 overflow-y-auto">
                @php
                    $warehouseNav = [
                        ['name' => 'Dashboard', 'route' => 'warehouse.dashboard', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                        ['name' => 'Warehouses', 'route' => 'warehouse.warehouses.index', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                        ['name' => 'Categories', 'route' => 'warehouse.categories.index', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
                        ['name' => 'Zones', 'route' => 'warehouse.zones.index', 'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z'],
                        ['name' => 'Racks', 'route' => 'warehouse.racks.index', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                        ['name' => 'Stock Inventory', 'route' => 'warehouse.inventory.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                        ['name' => 'Inbound (Putaway)', 'route' => 'warehouse.inbound.index', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                        ['name' => 'Outbound (Pick & Pack)', 'route' => 'warehouse.outbound.index', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                    ];
                @endphp

                @foreach($warehouseNav as $item)
                    <a href="{{ isset($item['route']) ? route($item['route']) : '#' }}" class="flex items-center gap-4 px-4 py-3 text-gray-600 rounded-2xl transition-all duration-200 {{ request()->routeIs($item['route'] ?? '') ? 'shadow-[inset_3px_3px_6px_#d1d5db,inset_-3px_-3px_6px_#ffffff] text-gray-900 font-bold bg-gray-200/50' : 'hover:shadow-[3px_3px_6px_#d1d5db,-3px_-3px_6px_#ffffff] hover:text-gray-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path></svg>
                        <span class="text-sm font-semibold">{{ $item['name'] }}</span>
                    </a>
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
                    backdrop: 'rgba(243, 244, 246, 0.85)',
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
                            text: "{{ str_replace('"', '\\"', $error) }}",
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
        @stack('scripts')
    </body>
</html>
