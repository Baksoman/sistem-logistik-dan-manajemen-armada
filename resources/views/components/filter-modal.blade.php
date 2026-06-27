@props(['title' => 'Filters'])

<div x-show="filterModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="filterModalOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 transition-opacity bg-gray-900/40 backdrop-blur-sm"
             @click="filterModalOpen = false" aria-hidden="true"></div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div x-show="filterModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-gray-100 rounded-3xl text-left overflow-hidden shadow-[12px_12px_24px_rgba(0,0,0,0.1),-12px_-12px_24px_rgba(255,255,255,0.8)] transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200/50">
            
            <div class="bg-gray-100 px-8 py-6 flex items-center justify-between border-b border-gray-300/50 shadow-[0_4px_6px_-1px_rgba(0,0,0,0.05)]">
                <h3 class="text-xl font-black text-gray-800 tracking-tight">
                    {{ $title }}
                </h3>
                <button type="button" @click="filterModalOpen = false" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-500 hover:text-red-500 bg-gray-100 shadow-[3px_3px_6px_#d1d5db,-3px_-3px_6px_#ffffff] active:shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] transition-all focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="px-8 py-6 bg-gray-100 max-h-[60vh] overflow-y-auto">
                <div class="space-y-6">
                    {{ $slot }}
                </div>
            </div>
            
            <div class="bg-gray-100 px-8 py-6 flex items-center justify-end gap-4 border-t border-gray-300/50 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                <button type="button" @click="resetFilters()" class="px-6 py-3 bg-gray-100 rounded-2xl font-bold text-gray-500 transition-all duration-300 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[6px_6px_12px_#c2c6cc,-6px_-6px_12px_#ffffff] hover:-translate-y-0.5 hover:text-red-500 active:shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] active:translate-y-0 focus:outline-none">
                    Reset
                </button>
                <button type="button" @click="applyFilters()" class="px-8 py-3 bg-blue-600 rounded-2xl font-bold text-white transition-all duration-300 shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff] hover:bg-blue-700 hover:shadow-[8px_8px_16px_#c2c6cc,-8px_-8px_16px_#ffffff] hover:-translate-y-1 active:shadow-[inset_4px_4px_8px_#1e3a8a] active:translate-y-0 focus:outline-none">
                    Apply Filters
                </button>
            </div>
        </div>
    </div>
</div>
