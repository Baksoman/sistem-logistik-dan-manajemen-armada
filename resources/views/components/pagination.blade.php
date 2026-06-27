<div x-show="meta.last_page > 1" class="flex items-center justify-between mt-6 border-t border-gray-200/50 pt-4" x-cloak>
    <div class="flex flex-1 justify-between sm:hidden">
        <button 
            type="button" 
            @click="changePage(meta.links.find(l => l.label === '&laquo; Previous')?.url)"
            :disabled="!meta.links.find(l => l.label === '&laquo; Previous')?.url"
            class="relative inline-flex items-center rounded-xl px-4 py-2 text-sm font-bold text-gray-700 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[6px_6px_12px_#c2c6cc,-6px_-6px_12px_#ffffff] disabled:opacity-50 disabled:shadow-none"
        >
            Previous
        </button>
        <button 
            type="button" 
            @click="changePage(meta.links.find(l => l.label === 'Next &raquo;')?.url)"
            :disabled="!meta.links.find(l => l.label === 'Next &raquo;')?.url"
            class="relative inline-flex items-center rounded-xl px-4 py-2 text-sm font-bold text-gray-700 bg-gray-100 shadow-[4px_4px_8px_#d1d5db,-4px_-4px_8px_#ffffff] hover:shadow-[6px_6px_12px_#c2c6cc,-6px_-6px_12px_#ffffff] disabled:opacity-50 disabled:shadow-none"
        >
            Next
        </button>
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-gray-600 font-medium">
                Showing
                <span class="font-bold text-gray-900" x-text="meta.from || 0"></span>
                to
                <span class="font-bold text-gray-900" x-text="meta.to || 0"></span>
                of
                <span class="font-bold text-gray-900" x-text="meta.total || 0"></span>
                results
            </p>
        </div>
        <div>
            <nav class="isolate inline-flex -space-x-px rounded-xl bg-gray-100 shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff] p-1" aria-label="Pagination">
                <template x-for="(link, index) in meta.links" :key="index">
                    <button 
                        type="button"
                        @click="changePage(link.url)"
                        :disabled="!link.url"
                        x-html="link.label"
                        :class="{
                            'z-10 bg-blue-500 text-white shadow-[inset_2px_2px_4px_#1e3a8a,inset_-2px_-2px_4px_#3b82f6] font-bold': link.active,
                            'text-gray-600 hover:bg-gray-200 hover:text-gray-900 font-medium': !link.active,
                            'opacity-50 cursor-not-allowed': !link.url
                        }"
                        class="relative inline-flex items-center px-4 py-2 text-sm rounded-lg transition-all"
                    >
                    </button>
                </template>
            </nav>
        </div>
    </div>
</div>
