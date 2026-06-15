@props(['name' => '', 'placeholder' => 'Search location...', 'eventName' => 'location-selected', 'value' => ''])
<div x-data="{
    query: '{{ addslashes($value) }}',
    results: [],
    isOpen: false,
    isSearching: false,
    async search() {
        if (this.query.length < 3) { this.results = []; return; }
        this.isSearching = true;
        try {
            const response = await fetch('/api/locations/search?q=' + encodeURIComponent(this.query));
            this.results = await response.json();
        } catch(e) { console.error(e); } finally { this.isSearching = false; }
    },
    selectResult(result) {
        this.query = result.name;
        this.isOpen = false;
        this.$dispatch('{{ $eventName }}', result);
    }
}" class="relative w-full">
    <input type="text" name="{{ $name }}" x-model="query" @input.debounce.500ms="search()" @focus="isOpen = true" @click.away="isOpen = false" placeholder="{{ $placeholder }}" autocomplete="off" class="w-full bg-gray-100 rounded-2xl px-5 py-4 font-medium text-gray-600 shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] border-none focus:ring-0 focus:outline-none" required />
    
    <div x-show="isOpen && (results.length > 0 || isSearching)" class="absolute z-50 w-full mt-2 bg-gray-100 rounded-2xl shadow-[8px_8px_16px_#d1d5db,-8px_-8px_16px_#ffffff] border border-gray-200 overflow-hidden" x-transition x-cloak>
        <template x-for="result in results" :key="result.id || result.name">
            <div @click="selectResult(result)" class="px-5 py-3 hover:bg-gray-200/50 cursor-pointer transition-colors border-b last:border-b-0 border-gray-200 flex items-center gap-3">
                <div class="p-2 rounded-full bg-white shadow-[inset_1px_1px_2px_#d1d5db,inset_-1px_-1px_2px_#ffffff]">
                    <svg x-show="result.type === 'warehouse'" class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <svg x-show="result.type === 'customer'" class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <svg x-show="result.type === 'public'" class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-700" x-text="result.name"></p>
                    <p class="text-xs font-medium text-gray-400" x-text="result.type.toUpperCase()"></p>
                </div>
            </div>
        </template>
        <div x-show="isSearching" class="px-5 py-4 text-sm text-gray-500 flex items-center justify-center">
            <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>
</div>
