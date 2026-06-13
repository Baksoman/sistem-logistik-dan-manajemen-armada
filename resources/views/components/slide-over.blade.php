@props(['title'])

<div x-show="slideOverOpen" class="fixed inset-0 z-50 overflow-hidden" x-cloak>
    <!-- Background overlay -->
    <div x-show="slideOverOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm"
         @click="slideOverOpen = false"></div>

    <!-- Slide-over panel -->
    <div class="fixed inset-y-0 right-0 max-w-md w-full flex">
        <div x-show="slideOverOpen"
             x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="w-full h-full bg-gray-100 shadow-[-12px_0_24px_rgba(0,0,0,0.1),-4px_0_8px_rgba(255,255,255,0.5)] flex flex-col">
            
            <!-- Header -->
            <div class="flex items-center justify-between px-8 py-6 shrink-0 shadow-[0_4px_6px_-1px_#d1d5db,0_2px_4px_-1px_#ffffff] z-10 bg-gray-100">
                <h2 class="text-xl font-bold text-gray-800 tracking-tight">{{ $title }}</h2>
                <button type="button" @click="slideOverOpen = false" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 hover:text-red-500 bg-gray-100 shadow-[3px_3px_6px_#d1d5db,-3px_-3px_6px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Content (Form) -->
            <div class="flex-1 overflow-y-auto px-8 py-8 z-0">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
