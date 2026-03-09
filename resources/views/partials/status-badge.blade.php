@props(['status'])
@php
    $classes = match ($status) {
        'new' => 'bg-blue-950 text-blue-300',
        'triaged' => 'bg-purple-950 text-purple-300',
        'in_progress' => 'bg-amber-950 text-amber-300',
        'waiting' => 'bg-orange-950 text-orange-300',
        'resolved' => 'bg-green-950 text-green-300',
        default => 'bg-gray-800 text-gray-400',
    };
    $label = match ($status) {
        'new' => 'New',
        'triaged' => 'Triaged',
        'in_progress' => 'In Progress',
        'waiting' => 'Waiting',
        'resolved' => 'Resolved',
        default => ucfirst($status),
    };
@endphp
<span class="font-mono text-[11px] px-2 py-0.5 rounded {{ $classes }}">
    {{ $label }}
</span>