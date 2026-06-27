@props(['placeholder' => 'Search...'])

<div class="flex flex-col lg:flex-row items-center gap-4 mb-6 w-full">
    <!-- Search Input -->
    <div class="relative w-full">
        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <input 
            type="text" 
            x-model.debounce.500ms="query" 
            placeholder="{{ $placeholder }}" 
            class="w-full bg-gray-100 rounded-2xl pl-12 pr-12 py-3 font-medium text-gray-600 shadow-[inset_6px_6px_12px_#d1d5db,inset_-6px_-6px_12px_#ffffff] border-none focus:ring-0 focus:outline-none transition-all duration-200"
        />
        <!-- Loading Spinner -->
        <div x-show="isLoading" class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none" x-cloak>
            <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>

    <!-- Filter Button -->
    <button 
        type="button" 
        @click="filterModalOpen = true" 
        class="shrink-0 flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 rounded-2xl font-bold text-gray-700 tracking-wide transition-all duration-300 ease-out shadow-[6px_6px_12px_#d1d5db,-6px_-6px_12px_#ffffff] hover:shadow-[10px_10px_20px_#c2c6cc,-10px_-10px_20px_#ffffff] hover:-translate-y-1 hover:text-blue-600 active:shadow-[inset_5px_5px_10px_#d1d5db,inset_-5px_-5px_10px_#ffffff] active:translate-y-0 active:scale-95 focus:outline-none w-full lg:w-auto"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
        Filters
        
        <!-- Filter count badge -->
        <span x-show="Object.values(filters).filter(v => v !== '' && v !== null).length > 0" 
              x-text="Object.values(filters).filter(v => v !== '' && v !== null).length"
              class="inline-flex items-center justify-center w-5 h-5 ml-2 text-xs font-bold text-white bg-blue-500 rounded-full" x-cloak>
        </span>
    </button>
</div>
