@props(['priority'])
@php
    $classes = match ($priority) {
        'urgent' => 'bg-red-950 text-red-300',
        'high' => 'bg-orange-950 text-orange-300',
        'medium' => 'bg-blue-950 text-blue-300',
        'low' => 'bg-gray-800 text-gray-400',
        default => 'bg-gray-800 text-gray-400',
    };
    $dot = match ($priority) {
        'urgent', 'high' => '●',
        default => '○',
    };
@endphp
<span class="font-mono text-[11px] px-2 py-0.5 rounded inline-flex items-center gap-1 {{ $classes }}">
    {{ $dot }} {{ ucfirst($priority) }}
</span>