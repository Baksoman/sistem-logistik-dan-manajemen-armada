@props(['name', 'required' => false])

@php
    $hasError = $name && $errors->has($name);
    $errorClass = $hasError ? 'border border-red-400 shadow-[inset_4px_4px_8px_#fecaca,inset_-4px_-4px_8px_#ffffff]' : 'border-none shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff]';
@endphp

<div class="relative">
    <select 
        name="{{ $name }}" 
        @if($required) required @endif
        {!! $attributes->merge(['class' => 'w-full bg-gray-100 rounded-2xl px-5 py-4 pr-12 font-medium text-gray-600 focus:ring-0 focus:outline-none appearance-none cursor-pointer ' . $errorClass]) !!}
    >
        {{ $slot }}
    </select>
    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-5 text-gray-500 drop-shadow-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
    </div>
</div>
