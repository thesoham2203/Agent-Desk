<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-base font-medium text-gray-100">Audit Logs</h1>
            <p class="text-xs text-gray-500 mt-0.5">Historical trace of system operations</p>
        </div>
        <a href="{{ route('admin.export.tickets') }}" class="bg-gray-800 hover:bg-gray-700 text-gray-300 text-xs
                  px-3 py-1.5 rounded-md transition-colors border border-gray-700">
            Export CSV
        </a>
    </div>

    {{-- Filters --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search action (e.g. ticket.assigned)"
            class="bg-gray-900 border border-gray-800 text-gray-100 text-sm
                      rounded-md px-3 py-2 placeholder-gray-600
                      focus:outline-none focus:ring-1 focus:ring-indigo-500">

        <select wire:model.live="actionFilter" class="bg-gray-900 border border-gray-800 text-gray-100 text-sm
                       rounded-md px-3 py-2 focus:outline-none focus:ring-1
                       focus:ring-indigo-500">
            <option value="">All Actions</option>
            <option value="ticket.created">Ticket Created</option>
            <option value="ticket.assigned">Ticket Assigned</option>
            <option value="ticket.unassigned">Ticket Unassigned</option>
            <option value="ticket.closed">Ticket Closed</option>
            <option value="ticket.reopened">Ticket Reopened</option>
            <option value="sla_config.updated">SLA Config Updated</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-lg border border-gray-800 bg-gray-950">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-900 border-b border-gray-800">
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider w-40">Timestamp</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider w-48">Action</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider w-24 text-center">Ticket</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider w-32">Changes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-900">
                @forelse($logList as $log)
                    <tr class="hover:bg-gray-900/50 transition-colors" wire:key="log-{{ $log->id }}">
                        <td class="px-4 py-3 font-mono text-[10px] text-gray-500">
                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-xs font-medium text-gray-200">
                                {{ $log->user->name ?? 'System' }}
                            </div>
                            <div class="text-[10px] text-gray-600">
                                {{ $log->user->email ?? '' }}
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-[10px] bg-gray-800
                                             text-gray-400 px-1.5 py-0.5 rounded border
                                             border-gray-700/50">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($log->ticket_id)
                                <span class="font-mono text-[10px] text-gray-500">
                                    #{{ $log->ticket_id }}
                                </span>
                            @else
                                <span class="text-gray-800">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($log->old_values || $log->new_values)
                                <div class="flex gap-2 font-mono text-[10px]">
                                    @if($log->old_values)
                                        <span class="text-red-900/80">
                                            -{{ count($log->old_values) }}
                                        </span>
                                    @endif
                                    @if($log->new_values)
                                        <span class="text-green-900/80">
                                            +{{ count($log->new_values) }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-[10px] text-gray-800 italic">none</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-16 text-center text-sm text-gray-600">
                            No matching audit logs found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($logList->hasPages())
        <div class="mt-4">{{ $logList->links() }}</div>
    @endif
</div>