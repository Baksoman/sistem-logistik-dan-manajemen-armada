@props(['panel' => 'Admin'])

<div class="flex items-center justify-center h-32 border-b-2 border-gray-200/50 pb-4 pt-6">
    <div class="flex flex-col items-center">
        <!-- Main Logo Area -->
        <div class="flex items-center gap-3">
            <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center shadow-[4px_4px_10px_#d1d5db,-4px_-4px_10px_#ffffff] relative overflow-hidden group p-1">
                <img src="{{ asset('images/logix-logo-only.jpg') }}" alt="LogiX Logo" class="w-full h-full object-contain mix-blend-multiply">
            </div>
            
            <div class="flex flex-col justify-center">
                <span class="text-3xl font-black tracking-tight text-gray-800" style="font-family: 'Inter', sans-serif;">
                    Logi<span class="text-blue-600">X</span>
                </span>
            </div>
        </div>

        <!-- Panel Badge -->
        <div class="mt-4 flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-gray-600 text-[9px] font-black tracking-[0.2em] uppercase shadow-[inset_2px_2px_4px_#d1d5db,inset_-2px_-2px_4px_#ffffff]">
            @if(strtolower($panel) === 'superadmin')
                <svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            @elseif(strtolower($panel) === 'logistik')
                <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
            @elseif(strtolower($panel) === 'warehouse')
                <svg class="w-3 h-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            @endif
            <span>{{ $panel }} Panel</span>
        </div>
    </div>
</div>
