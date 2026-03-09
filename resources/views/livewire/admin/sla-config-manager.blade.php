<div class="max-w-2xl">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-base font-medium text-gray-100">SLA Configuration</h1>
        <p class="text-xs text-gray-500 mt-0.5">Define target response and resolution times</p>
    </div>

    {{-- Form card --}}
    <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 space-y-6">

        {{-- Messages --}}
        @if (session()->has('success'))
            <div class="p-3 bg-green-950/30 border border-green-900/50 rounded-md
                            text-xs text-green-400">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit.prevent="update" class="space-y-6">
            {{-- First Response --}}
            <div class="space-y-2">
                <label class="block text-[10px] text-gray-600 uppercase tracking-wider">
                    First Response Target
                </label>
                <div class="flex items-center gap-4">
                    <div class="relative w-28">
                        <input type="number" wire:model="firstResponseHours"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-100
                                      text-sm rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <span class="absolute right-3 top-2 text-[10px] text-gray-600 font-mono">h</span>
                    </div>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Target time to provide the first human response after ticket creation.
                    </p>
                </div>
                @error('firstResponseHours')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Resolution --}}
            <div class="space-y-2 pt-4 border-t border-gray-800">
                <label class="block text-[10px] text-gray-600 uppercase tracking-wider">
                    Resolution Target
                </label>
                <div class="flex items-center gap-4">
                    <div class="relative w-28">
                        <input type="number" wire:model="resolutionHours"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-100
                                      text-sm rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <span class="absolute right-3 top-2 text-[10px] text-gray-600 font-mono">h</span>
                    </div>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Target time to fully resolve the ticket (Set to Resolved status).
                    </p>
                </div>
                @error('resolutionHours')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="pt-4 flex justify-end">
                <button type="submit" wire:loading.attr="disabled" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs
                               px-4 py-2 rounded-md transition-colors">
                    <span wire:loading.remove>Save Configuration</span>
                    <span wire:loading>Saving...</span>
                </button>
            </div>
        </form>
    </div>

    {{-- System impact notice --}}
    <div class="mt-6 p-4 bg-blue-950/20 border border-blue-900/30 rounded-lg">
        <div class="flex gap-3">
            <span class="text-blue-400 text-sm">ℹ</span>
            <div>
                <p class="text-[10px] text-blue-300 font-bold uppercase tracking-wider mb-1">
                    System Impact
                </p>
                <p class="text-xs text-blue-300/70 leading-relaxed">
                    Updating these values will affect overdue status calculations for all active tickets. Changes are
                    logged to the System Audit Log.
                </p>
            </div>
        </div>
    </div>
</div>