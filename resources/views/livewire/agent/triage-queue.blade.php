<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <h1 class="text-base font-medium text-gray-100">Triage Queue</h1>
            @if($this->tickets->total() > 0)
                <span class="font-mono text-xs bg-indigo-950 text-indigo-300
                             px-2 py-0.5 rounded">
                    {{ $this->tickets->total() }} unassigned
                </span>
            @endif
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-lg border border-gray-800">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-900 border-b border-gray-800">
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider w-16">ID</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider w-32">Requester</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider w-28">Priority</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider w-24">Age</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider w-28">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse($this->tickets as $ticket)
                    @php
                        $ageHours = $ticket->created_at->diffInHours(now());
                        $ageColor = match(true) {
                            $ageHours >= 4 => 'text-red-400',
                            $ageHours >= 2 => 'text-amber-400',
                            default        => 'text-gray-500',
                        };
                    @endphp
                    <tr class="hover:bg-gray-900 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">
                            #{{ $ticket->id }}
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('agent.tickets.show', $ticket->id) }}"
                               class="text-sm text-gray-200 hover:text-white transition-colors">
                                {{ $ticket->title }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-400">
                            {{ $ticket->requester->name }}
                        </td>
                        <td class="px-4 py-3">
                            @include('partials.priority-badge',
                                ['priority' => $ticket->priority->value])
                        </td>
                        <td class="px-4 py-3 font-mono text-xs {{ $ageColor }}">
                            {{ $ticket->created_at->diffForHumans(short: true) }}
                        </td>
                        <td class="px-4 py-3">
                            <button wire:click="assignToSelf({{ $ticket->id }})"
                                    wire:confirm="Assign this ticket to yourself?"
                                    class="text-xs text-indigo-400 hover:text-indigo-300
                                           transition-colors">
                                Assign to me
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-16 text-center">
                            <div class="text-2xl mb-2">✓</div>
                            <p class="text-sm text-gray-400">Queue is clear</p>
                            <p class="text-xs text-gray-600 mt-1">
                                All tickets have been assigned. Nice work.
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($this->tickets->hasPages())
        <div class="mt-4">{{ $this->tickets->links() }}</div>
    @endif
</div>