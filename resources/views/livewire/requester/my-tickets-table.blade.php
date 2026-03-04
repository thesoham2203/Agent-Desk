<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Tickets</h1>
            <p class="mt-2 text-sm text-gray-700">A list of all tickets you have submitted.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('requester.tickets.create') }}"
                class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto">
                Create Ticket
            </a>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="mb-6 bg-white p-4 shadow sm:rounded-lg flex flex-col sm:flex-row gap-4">
        <!-- Search Input (Debounced) -->
        <div class="flex-1">
            <label for="search" class="sr-only">Search tickets</label>
            <div class="relative rounded-md shadow-sm">
                <!-- Search icon -->
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" id="search"
                    class="block w-full rounded-md border-gray-300 pl-10 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    placeholder="Search title or body...">
            </div>
        </div>

        <!-- Status Filter Dropdown -->
        <div class="w-full sm:w-48">
            <label for="statusFilter" class="sr-only">Filter by Status</label>
            <select wire:model.live="statusFilter" id="statusFilter"
                class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm">
                <option value="">All Statuses</option>
                <option value="new">New</option>
                <option value="triaged">Triaged</option>
                <option value="in_progress">In Progress</option>
                <option value="waiting">Waiting</option>
                <option value="resolved">Resolved</option>
            </select>
        </div>
    </div>

    <!-- Tickets Table Section -->
    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 w-16">
                        ID</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Title</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Category</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Priority</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Created</th>
                    <th scope="col"
                        class="relative py-3.5 pl-3 pr-4 sm:pr-6 whitespace-nowrap text-right text-sm font-medium">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($this->tickets as $ticket)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        #{{ $ticket->id }}
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-900">
                                        <a href="{{ route('requester.tickets.show', $ticket) }}"
                                            class="font-medium text-blue-600 hover:text-blue-900 hover:underline">
                                            {{ Str::limit($ticket->title, 50) }}
                                        </a>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $ticket->category?->name ?? 'Uncategorized' }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-{{ match ($ticket->status) {
                        \App\Enums\TicketStatus::New => 'blue',
                        \App\Enums\TicketStatus::Triaged => 'indigo',
                        \App\Enums\TicketStatus::InProgress => 'yellow',
                        \App\Enums\TicketStatus::Waiting => 'orange',
                        \App\Enums\TicketStatus::Resolved => 'green',
                        default => 'gray'
                    } }}-100 text-{{ match ($ticket->status) {
                        \App\Enums\TicketStatus::New => 'blue',
                        \App\Enums\TicketStatus::Triaged => 'indigo',
                        \App\Enums\TicketStatus::InProgress => 'yellow',
                        \App\Enums\TicketStatus::Waiting => 'orange',
                        \App\Enums\TicketStatus::Resolved => 'green',
                        default => 'gray'
                    } }}-800">
                                            {{ $ticket->status->label() }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $ticket->priority->label() }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $ticket->created_at->format('M j, Y') }}
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <a href="{{ route('requester.tickets.show', $ticket) }}"
                                            class="text-blue-600 hover:text-blue-900">
                                            View <span class="sr-only">, ticket #{{ $ticket->id }}</span>
                                        </a>
                                    </td>
                                </tr>
                @empty
                    <!-- Empty State -->
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                            No tickets found matching your criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $this->tickets->links() }}
    </div>
</div>