<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-base font-medium text-gray-100">AI Execution Logs</h1>
            <p class="text-xs text-gray-500 mt-0.5">History of automated triage and drafting operations</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-[10px] text-gray-600 uppercase tracking-wider mb-1.5">
                Status
            </label>
            <select wire:model.live="statusFilter" class="w-full bg-gray-900 border border-gray-800 text-gray-100 text-sm
                           rounded-md px-3 py-2 focus:outline-none focus:ring-1
                           focus:ring-indigo-500">
                <option value="">All Statuses</option>
                @foreach (\App\Enums\AiRunStatus::cases() as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] text-gray-600 uppercase tracking-wider mb-1.5">
                Type
            </label>
            <select wire:model.live="typeFilter" class="w-full bg-gray-900 border border-gray-800 text-gray-100 text-sm
                           rounded-md px-3 py-2 focus:outline-none focus:ring-1
                           focus:ring-indigo-500">
                <option value="">All Types</option>
                @foreach (\App\Enums\AiRunType::cases() as $type)
                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-lg border border-gray-800 bg-gray-950">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-900 border-b border-gray-800">
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider w-16">ID</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider w-24">Ticket</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider w-32">Agent</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider w-32">Status</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider w-32">Model</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider w-28 text-right">Age</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-900">
                @forelse($runList as $run)
                    <tr class="hover:bg-gray-900/50 transition-colors" wire:key="airun-{{ $run->id }}">
                        <td class="px-4 py-3 font-mono text-[10px] text-gray-600">
                            #{{ $run->id }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-mono text-[10px] text-indigo-400">
                                #{{ $run->ticket_id }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-300">
                            {{ $run->run_type->label() }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-400">
                            {{ $run->initiatedBy?->name ?? 'System' }}
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $statusStyle = match ($run->status) {
                                    \App\Enums\AiRunStatus::Queued => 'bg-gray-800 text-gray-500',
                                    \App\Enums\AiRunStatus::Running => 'bg-blue-950 text-blue-400',
                                    \App\Enums\AiRunStatus::Succeeded => 'bg-green-950 text-green-400',
                                    \App\Enums\AiRunStatus::Failed => 'bg-red-950 text-red-400',
                                };
                            @endphp
                            <span class="font-mono text-[10px] px-2 py-0.5 rounded {{ $statusStyle }}">
                                {{ $run->status->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-mono text-[10px] text-gray-600">
                            {{ $run->model ?: 'default' }}
                        </td>
                        <td class="px-4 py-3 text-right font-mono text-[10px] text-gray-500">
                            {{ $run->created_at->diffForHumans(short: true) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-16 text-center text-sm text-gray-600">
                            No AI execution records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($runList->hasPages())
        <div class="mt-4">{{ $runList->links() }}</div>
    @endif
</div>