<div>
    {{-- Page header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-base font-medium text-gray-100">My Tickets</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                Your submitted support requests
            </p>
        </div>
        <a href="{{ route('requester.tickets.create') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs
                  px-3 py-1.5 rounded-md transition-colors">
            + New Ticket
        </a>
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
                               text-gray-500 uppercase tracking-wider w-32">Status</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider w-32">Category</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider w-28">Created</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse($this->tickets as $ticket)
                    <tr class="hover:bg-gray-900 transition-colors cursor-pointer"
                        wire:click="$dispatch('navigate', { url: '{{ route('requester.tickets.show', $ticket->id) }}' })">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">
                            #{{ $ticket->id }}
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('requester.tickets.show', $ticket->id) }}"
                                class="text-sm text-gray-200 hover:text-white transition-colors">
                                {{ $ticket->title }}
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            @include('partials.status-badge', ['status' => $ticket->status->value])
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-400">
                            {{ $ticket->category?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">
                            {{ $ticket->created_at->diffForHumans() }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-16 text-center">
                            <p class="text-sm text-gray-500">No tickets yet.</p>
                            <p class="text-xs text-gray-600 mt-1">
                                When you raise a support request it will appear here.
                            </p>
                            <a href="{{ route('requester.tickets.create') }}" class="inline-block mt-4 text-xs text-indigo-400
                                          hover:text-indigo-300 transition-colors">
                                Raise your first ticket →
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>