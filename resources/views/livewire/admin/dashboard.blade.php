<div>
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-xl font-medium text-gray-100">Admin Dashboard</h1>
        <p class="text-xs text-gray-500 mt-1">System-wide performance and volume overview</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Requesters --}}
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-5">
            <div class="flex items-center justify-between mb-3 text-gray-500">
                <span class="text-[10px] uppercase font-bold tracking-wider">Requesters</span>
                <span class="text-lg">👥</span>
            </div>
            <div class="text-2xl font-mono text-gray-100">{{ number_format($stats['requesters']) }}</div>
            <div class="text-[10px] text-gray-600 mt-1 font-mono uppercase tracking-widest italic">Registered Customers
            </div>
        </div>

        {{-- Agents --}}
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-5">
            <div class="flex items-center justify-between mb-3 text-gray-500">
                <span class="text-[10px] uppercase font-bold tracking-wider">Staff</span>
                <span class="text-lg">🛡️</span>
            </div>
            <div class="text-2xl font-mono text-gray-100">{{ number_format($stats['agents']) }}</div>
            <div class="text-[10px] text-gray-600 mt-1 font-mono uppercase tracking-widest italic">Agents & Admins</div>
        </div>

        {{-- Active Tickets --}}
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-5">
            <div class="flex items-center justify-between mb-3 text-gray-500">
                <span class="text-[10px] uppercase font-bold tracking-wider">Active</span>
                <span class="text-lg">🔥</span>
            </div>
            <div class="text-2xl font-mono text-orange-400">{{ number_format($stats['activeTickets']) }}</div>
            <div class="text-[10px] text-gray-600 mt-1 font-mono uppercase tracking-widest italic">New or In-Progress
            </div>
        </div>

        {{-- Resolved Tickets --}}
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-5">
            <div class="flex items-center justify-between mb-3 text-gray-500">
                <span class="text-[10px] uppercase font-bold tracking-wider">Resolved</span>
                <span class="text-lg">✅</span>
            </div>
            <div class="text-2xl font-mono text-green-400">{{ number_format($stats['resolvedTickets']) }}</div>
            <div class="text-[10px] text-gray-600 mt-1 font-mono uppercase tracking-widest italic">Lifetime Solved</div>
        </div>
    </div>

    {{-- Quick Links / Recent Activity Placeholder --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-gray-950 border border-gray-900 rounded-lg p-6">
            <h2 class="text-sm font-medium text-gray-300 mb-4 font-mono uppercase tracking-wider">Quick Actions</h2>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.categories') }}"
                    class="flex flex-col gap-1 p-3 bg-gray-900 border border-gray-800 rounded hover:border-indigo-800 transition-colors group">
                    <span class="text-xs text-gray-200 group-hover:text-indigo-400">Manage Categories</span>
                    <span class="text-[10px] text-gray-600">Edit triage routing</span>
                </a>
                <a href="{{ route('admin.sla') }}"
                    class="flex flex-col gap-1 p-3 bg-gray-900 border border-gray-800 rounded hover:border-indigo-800 transition-colors group">
                    <span class="text-xs text-gray-200 group-hover:text-indigo-400">SLA Config</span>
                    <span class="text-[10px] text-gray-600">Update response targets</span>
                </a>
                <a href="{{ route('admin.macros') }}"
                    class="flex flex-col gap-1 p-3 bg-gray-900 border border-gray-800 rounded hover:border-indigo-800 transition-colors group">
                    <span class="text-xs text-gray-200 group-hover:text-indigo-400">Macros</span>
                    <span class="text-[10px] text-gray-600">Standardize responses</span>
                </a>
                <a href="{{ route('admin.audit-log') }}"
                    class="flex flex-col gap-1 p-3 bg-gray-900 border border-gray-800 rounded hover:border-indigo-800 transition-colors group">
                    <span class="text-xs text-gray-200 group-hover:text-indigo-400">Audit Logs</span>
                    <span class="text-[10px] text-gray-600">Track all changes</span>
                </a>
            </div>
        </div>

        <div class="bg-gray-950 border border-gray-900 rounded-lg p-6">
            <h2 class="text-sm font-medium text-gray-300 mb-4 font-mono uppercase tracking-wider">System State</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-gray-900 pb-2">
                    <span class="text-xs text-gray-500">AI Engine</span>
                    <span class="text-xs text-green-400 font-mono">Running (Groq)</span>
                </div>
                <div class="flex items-center justify-between border-b border-gray-900 pb-2">
                    <span class="text-xs text-gray-500">Queue Worker</span>
                    <span class="text-xs text-indigo-400 font-mono">Active (Redis)</span>
                </div>
                <div class="flex items-center justify-between border-b border-gray-900 pb-2">
                    <span class="text-xs text-gray-500">SLA Monitor</span>
                    <span class="text-xs text-amber-400 font-mono">Operational</span>
                </div>
            </div>
        </div>
    </div>
</div>