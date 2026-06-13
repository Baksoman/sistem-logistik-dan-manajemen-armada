@props(['disabled' => false])

@php
    $name = $attributes->get('name');
    $hasError = $name && $errors->has($name);
    $errorClass = $hasError ? 'border border-red-400 shadow-[inset_4px_4px_8px_#fecaca,inset_-4px_-4px_8px_#ffffff]' : 'border-none shadow-[inset_6px_6px_12px_#d1d5db,inset_-6px_-6px_12px_#ffffff]';
@endphp

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'w-full bg-gray-100 rounded-2xl px-5 py-3 text-gray-700 focus:outline-none focus:ring-0 focus:shadow-[inset_4px_4px_8px_#d1d5db,inset_-4px_-4px_8px_#ffffff] transition-all duration-200 ' . $errorClass]) !!}>
