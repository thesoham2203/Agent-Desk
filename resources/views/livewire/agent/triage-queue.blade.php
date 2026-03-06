<div>
    <!--
    ============================================================
    FILE: triage-queue.blade.php
    LAYER: View (Livewire)
    ============================================================
    WHAT IS THIS?
    The UI for the unassigned New tickets list for agents.
    ============================================================
    -->
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- HEADER -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-3">
                <h1 class="text-3xl font-bold text-gray-900">Triage Queue</h1>
                <span
                    class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    {{ $this->tickets->total() }} unassigned
                </span>
            </div>
        </div>

        <!-- FILTERS -->
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search tickets by title..."
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <!-- Priority Filter -->
            <div class="w-full sm:w-64">
                <select wire:model.live="priorityFilter"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">All Priorities</option>
                    @foreach (\App\Enums\TicketPriority::cases() as $priority)
                        <option value="{{ $priority->value }}">{{ $priority->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- TICKETS TABLE -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            @if ($this->tickets->isEmpty())
                <!-- EMPTY STATE -->
                <div class="p-12 text-center">
                    <p class="text-gray-500 text-lg">No tickets in triage queue. Great work! 🎉</p>
                </div>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Requester</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Priority</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($this->tickets as $ticket)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    #{{ $ticket->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $ticket->requester?->name ?? '—' }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                    <a href="{{ route('agent.tickets.show', $ticket) }}">{{ $ticket->title }}</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $ticket->category?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                        @if ($ticket->priority === \App\Enums\TicketPriority::Low) bg-gray-100 text-gray-800
                                                        @elseif ($ticket->priority === \App\Enums\TicketPriority::Medium) bg-blue-100 text-blue-800
                                                        @elseif ($ticket->priority === \App\Enums\TicketPriority::High) bg-orange-100 text-orange-800
                                                        @elseif ($ticket->priority === \App\Enums\TicketPriority::Urgent) bg-red-100 text-red-800
                                                        @endif
                                                    ">
                                        {{ $ticket->priority->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $ticket->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button wire:click="assignToSelf({{ $ticket->id }})"
                                        wire:confirm="Assign this ticket to yourself?"
                                        class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                        Assign to me
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- PAGINATION -->
        <div class="mt-4">
            {{ $this->tickets->links() }}
        </div>
    </div>
</div>
<!--
============================================================
WHAT TO READ NEXT:
============================================================
→ [app/Livewire/Agent/TicketDetail.php]
WHY: Once a ticket is assigned, the agent views its details to work on it.
============================================================
-->