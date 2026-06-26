<header class="mb-8 pt-2">
    <div class="flex items-center justify-between shrink-0">
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = true" class="lg:hidden w-10 h-10 rounded-full flex items-center justify-center text-gray-600 bg-gray-100 shadow-[3px_3px_6px_#d1d5db,-3px_-3px_6px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <!-- Page Title (Desktop) -->
            <h2 class="text-3xl font-black text-gray-800 tracking-tight hidden sm:block">
                @yield('title', 'Overview')
            </h2>
        </div>

    <!-- Profile Dropdown -->
    <div class="flex items-center gap-4">
        <!-- Bell Icon / Notifications -->
        <div class="relative" x-data="{ notifOpen: false }">
            <button @click="notifOpen = !notifOpen" @click.away="notifOpen = false" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 hover:text-gray-800 bg-gray-100 shadow-[3px_3px_6px_#d1d5db,-3px_-3px_6px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-gray-100"></span>
            </button>
            
            <!-- Notifications Dropdown Menu -->
            <div x-show="notifOpen" x-transition.origin.top.right x-cloak class="absolute right-0 mt-3 w-80 bg-gray-100 rounded-2xl shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff] p-2 z-50 border border-white/50">
                <div class="px-4 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-bold text-gray-800">Notifications</h3>
                </div>
                <div class="max-h-64 overflow-y-auto">
                    <!-- Dummy Notification Item -->
                    <div class="p-4 hover:bg-gray-200/50 transition border-b border-gray-200 last:border-b-0 cursor-pointer">
                        <p class="text-xs font-bold text-gray-800">System Update</p>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">Versi aplikasi terbaru v2.1.0 sudah dirilis. Jelajahi fitur logistik baru sekarang.</p>
                        <p class="text-[10px] text-gray-400 mt-2 font-medium">10 menit yang lalu</p>
                    </div>
                    <!-- Dummy Notification Item -->
                    <div class="p-4 hover:bg-gray-200/50 transition border-b border-gray-200 last:border-b-0 cursor-pointer">
                        <p class="text-xs font-bold text-gray-800">Shipment Delivered</p>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">Shipment SHP-0012 telah berhasil dikirim ke tujuan.</p>
                        <p class="text-[10px] text-gray-400 mt-2 font-medium">1 jam yang lalu</p>
                    </div>
                    <!-- Dummy Notification Item -->
                    <div class="p-4 hover:bg-gray-200/50 transition border-b border-gray-200 last:border-b-0 cursor-pointer">
                        <p class="text-xs font-bold text-gray-800">New Order Assigned</p>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">Terdapat 3 pesanan baru yang harus diproses di gudang Jakarta.</p>
                        <p class="text-[10px] text-gray-400 mt-2 font-medium">Kemarin</p>
                    </div>
                </div>
                <div class="px-4 py-2 border-t border-gray-200 text-center">
                    <a href="#" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition">Mark all as read</a>
                </div>
            </div>
        </div>
        
        <div class="relative" x-data="{ open: false }">
            <div @click="open = !open" @click.away="open = false" class="flex items-center gap-3 px-2 py-1.5 rounded-full shadow-[3px_3px_6px_#d1d5db,-3px_-3px_6px_#ffffff] pr-4 cursor-pointer hover:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all">
                <div class="w-8 h-8 rounded-full shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] flex items-center justify-center font-bold text-xs text-gray-700 bg-gray-100">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
                <div class="flex flex-col hidden sm:flex">
                    <span class="text-xs font-bold text-gray-800 leading-tight">{{ Auth::user()->name ?? 'Admin' }}</span>
                    <span class="text-[10px] font-semibold text-gray-500 leading-tight uppercase">{{ Auth::user() ? (Auth::user()->roles->first()->name ?? 'User') : 'User' }}</span>
                </div>
                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>

            <!-- Dropdown Menu -->
            <div x-show="open" x-transition.origin.top.right x-cloak class="absolute right-0 mt-3 w-48 bg-gray-100 rounded-2xl shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff] p-2 z-50 border border-white/50">
                <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-200/50 rounded-xl transition">My Profile</a>
                <div class="border-t border-gray-300 my-1 mx-2"></div>
                <form method="POST" action="{{ route('logout') ?? '#' }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm font-bold text-red-500 hover:text-red-700 hover:bg-red-50 rounded-xl transition">Log Out</button>
                </form>
            </div>
        </div>
    </div> <!-- Close Profile Section -->
    </div> <!-- Close flex justify-between -->
    <!-- Page Title (Mobile) -->
    <div class="mt-6 sm:hidden">
        <h2 class="text-3xl font-black text-gray-800 tracking-tight">
            @yield('title', 'Overview')
        </h2>
    </div>
</header>
