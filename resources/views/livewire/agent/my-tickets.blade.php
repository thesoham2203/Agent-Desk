<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <h1 class="text-base font-medium text-gray-100">My Tickets</h1>
            @if($this->tickets->total() > 0)
                <span class="font-mono text-xs bg-indigo-950 text-indigo-300
                             px-2 py-0.5 rounded">
                    {{ $this->tickets->total() }} assigned
                </span>
            @endif
        </div>
    </div>

    {{-- Filters --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-[10px] text-gray-600 uppercase tracking-wider mb-1.5">
                Search Title
            </label>
            <input type="text" wire:model.live.debounce.300ms="search"
                   placeholder="Type to search..."
                   class="w-full bg-gray-900 border border-gray-800 text-gray-100 text-sm
                          rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-[10px] text-gray-600 uppercase tracking-wider mb-1.5">
                Priority
            </label>
            <select wire:model.live="priorityFilter" class="w-full bg-gray-900 border border-gray-800 text-gray-100 text-sm
                           rounded-md px-3 py-2 focus:outline-none focus:ring-1
                           focus:ring-indigo-500">
                <option value="">All Priorities</option>
                @foreach (\App\Enums\TicketPriority::cases() as $priority)
                    <option value="{{ $priority->value }}">{{ $priority->label() }}</option>
                @endforeach
            </select>
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
                               text-gray-500 uppercase tracking-wider w-32">Status</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider w-24">Age</th>
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
                        <td class="px-4 py-3">
                            @include('partials.status-badge',
                                ['status' => $ticket->status->value])
                        </td>
                        <td class="px-4 py-3 font-mono text-xs {{ $ageColor }}">
                            {{ $ticket->created_at->diffForHumans(short: true) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-16 text-center">
                            <div class="text-2xl mb-2">✓</div>
                            <p class="text-sm text-gray-400">No tickets assigned</p>
                            <p class="text-xs text-gray-600 mt-1">
                                You don't have any tickets currently assigned to you.
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
