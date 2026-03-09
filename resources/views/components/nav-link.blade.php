@props(['active'])

@php
    $classes = ($active ?? false)
        ? 'text-xs px-3 py-1.5 rounded-md bg-gray-800 text-gray-100 transition-colors'
        : 'text-xs px-3 py-1.5 rounded-md text-gray-400 hover:text-gray-200 hover:bg-gray-800 transition-colors';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>