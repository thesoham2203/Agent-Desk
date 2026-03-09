{{--
/**
* ============================================================
* FILE: ai-runs-viewer.blade.php
* LAYER: View
* ============================================================
*
* WHAT IS THIS?
* A read-only screen for monitoring AI agent executions.
*
* WHY DOES IT EXIST?
* To allow administrators to monitor automated ticket triage
* and reply drafting tasks, ensuring the AI is operating correctly.
*
* HOW IT FITS IN THE APP:
* Rendered by App\Livewire\Admin\AiRunsViewer.
*
* LARAVEL CONCEPT EXPLAINED:
* Conditional styling in Blade (e.g., status-based badge colors)
* makes it easy to visualize different states of a model without
* writing complex JavaScript logic.
* ============================================================
*/
--}}

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">AI Execution Logs</h1>
        <p class="text-sm text-gray-500 mt-1">
            History of all AI operations, including triage and reply drafting.
        </p>
    </div>

    <!-- Filters -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-4 border border-gray-200 rounded-lg shadow-sm">
        <div>
            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Filter by Status</label>
            <select wire:model.live="statusFilter"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">All Statuses</option>
                @foreach (\App\Enums\AiRunStatus::cases() as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Filter by Type</label>
            <select wire:model.live="typeFilter"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">All Types</option>
                @foreach (\App\Enums\AiRunType::cases() as $type)
                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-hidden border border-gray-200 rounded-lg shadow-sm bg-white mb-4">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Triggered
                        By</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created
                        At</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($runList as $run)
                    <tr wire:key="airun-{{ $run->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-gray-900">#{{ $run->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-indigo-600">
                                #{{ $run->ticket_id }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $run->run_type->label() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $badgeColor = match ($run->status) {
                                    \App\Enums\AiRunStatus::Queued => 'bg-gray-100 text-gray-800 border-gray-200',
                                    \App\Enums\AiRunStatus::Running => 'bg-blue-100 text-blue-800 border-blue-200',
                                    \App\Enums\AiRunStatus::Succeeded => 'bg-green-100 text-green-800 border-green-200',
                                    \App\Enums\AiRunStatus::Failed => 'bg-red-100 text-red-800 border-red-200',
                                };
                            @endphp
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full border {{ $badgeColor }}">
                                {{ $run->status->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-gray-500">
                            {{ $run->model_name ?? 'default' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $run->initiatedBy->name ?? 'System' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                            {{ $run->created_at->diffForHumans() }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                            No AI execution records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $runList->links() }}
    </div>
</div>

{{--
* ============================================================
* WHAT TO READ NEXT:
* ============================================================
* → routes/web.php
* WHY: After building components and views, we wire them to URLs.
* ============================================================
--}}