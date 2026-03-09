@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-[10px] font-medium text-gray-500 uppercase tracking-wider mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>