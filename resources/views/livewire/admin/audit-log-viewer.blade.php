{{--
/**
 * ============================================================
 * FILE: audit-log-viewer.blade.php
 * LAYER: View
 * ============================================================
 *
 * WHAT IS THIS?
 * A read-only screen for reviewing system audit logs.
 *
 * WHY DOES IT EXIST?
 * To allow administrators to trace back actions and changes
 * for security reviews or troubleshooting.
 *
 * HOW IT FITS IN THE APP:
 * Rendered by App\Livewire\Admin\AuditLogViewer.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Livewire provides seamless integration with Laravel's
 * pagination links ($logs->links()), enabling efficient navigation
 * of large datasets with zero full-page reloads.
 * ============================================================
 */
--}}

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">System Audit Log</h1>
            <p class="text-sm text-gray-500 mt-1">
                Historical trace of all sensitive operations performed in the system.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.export.tickets') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Export CSV
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4 bg-white p-4 border border-gray-200 rounded-lg shadow-sm">
        <div>
            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Search Action</label>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                placeholder="e.g. ticket.assigned"
            >
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Filter by Action</label>
            <select
                wire:model.live="actionFilter"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
                <option value="">All Actions</option>
                <option value="ticket.created">Ticket Created</option>
                <option value="ticket.assigned">Ticket Assigned</option>
                <option value="ticket.unassigned">Ticket Unassigned</option>
                <option value="ticket.closed">Ticket Closed</option>
                <option value="ticket.reopened">Ticket Reopened</option>
                <option value="sla_config.updated">SLA Config Updated</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-hidden border border-gray-200 rounded-lg shadow-sm bg-white mb-4">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider font-mono">Changes</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($logList as $log)
                    <tr wire:key="log-{{ $log->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $log->user->name ?? 'System' }}</div>
                            <div class="text-xs text-gray-500">{{ $log->user->email ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 border border-gray-200">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if ($log->ticket_id)
                                <span class="font-mono text-indigo-600">#{{ $log->ticket_id }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs font-mono text-gray-400">
                            @if ($log->old_values || $log->new_values)
                                <div class="max-w-xs overflow-hidden text-ellipsis" title="{{ json_encode(['old' => $log->old_values, 'new' => $log->new_values], JSON_PRETTY_PRINT) }}">
                                    @if ($log->old_values) <span class="text-red-400">-{{ count($log->old_values) }}</span> @endif
                                    @if ($log->new_values) <span class="text-green-400">+{{ count($log->new_values) }}</span> @endif
                                </div>
                            @else
                                (no payload)
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                            No audit logs matching the criteria were found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $logList->links() }}
    </div>
</div>

{{--
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → resources/views/livewire/admin/ai-runs-viewer.blade.php
 * WHY: Both files are read-only viewers for system activity traces.
 * ============================================================
 --}}
