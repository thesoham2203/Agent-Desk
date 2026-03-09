@props(['active'])

@php
    $classes = ($active ?? false)
        ? 'block w-full ps-3 pe-4 py-2 border-l-2 border-indigo-400 text-start text-xs font-medium text-indigo-400 bg-gray-900 transition-colors'
        : 'block w-full ps-3 pe-4 py-2 border-l-2 border-transparent text-start text-xs font-medium text-gray-500 hover:text-gray-300 hover:bg-gray-900 transition-colors';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>