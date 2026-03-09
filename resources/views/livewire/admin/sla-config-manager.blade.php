{{--
/**
* ============================================================
* FILE: sla-config-manager.blade.php
* LAYER: View
* ============================================================
*
* WHAT IS THIS?
* The settings screen for configuring SLA targets.
*
* WHY DOES IT EXIST?
* To allow administrators to define the organization's service
* delivery standards (response and resolution times).
*
* HOW IT FITS IN THE APP:
* Rendered by App\Livewire\Admin\SlaConfigManager.
*
* LARAVEL CONCEPT EXPLAINED:
* Flash messages (session('success')) provide temporary feedback to the user
* after a successful server-side operation like saving configuration.
* ============================================================
*/
--}}

<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">SLA Configuration</h1>
        <p class="text-sm text-gray-500 mt-1">
            Define target response and resolution times for all tickets.
        </p>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
        <form wire:submit.prevent="update" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    First Response Target (Hours)
                </label>
                <div class="mt-1 flex items-center gap-4">
                    <input type="number" wire:model="firstResponseHours"
                        class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        min="1" max="168">
                    <span class="text-sm text-gray-500">
                        Target time to provide the first human response after ticket creation.
                    </span>
                </div>
                @error('firstResponseHours') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Resolution Target (Hours)
                </label>
                <div class="mt-1 flex items-center gap-4">
                    <input type="number" wire:model="resolutionHours"
                        class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        min="1" max="720">
                    <span class="text-sm text-gray-500">
                        Target time to fully resolve the ticket (Set to Resolved status).
                    </span>
                </div>
                @error('resolutionHours') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end">
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition disabled:opacity-50"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>Save Configuration</span>
                    <span wire:loading>Saving...</span>
                </button>
            </div>
        </form>
    </div>

    <div class="mt-8 p-4 bg-blue-50 rounded-lg text-blue-800 text-sm">
        <h3 class="font-bold flex items-center gap-2 mb-1">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clip-rule="evenodd"></path>
            </svg>
            System Impact
        </h3>
        Updating these values will affect the overdue status calculations for all active tickets. Changes are logged to
        the System Audit Log.
    </div>
</div>

{{--
* ============================================================
* WHAT TO READ NEXT:
* ============================================================
* → resources/views/livewire/admin/kb-article-manager.blade.php
* WHY: Continuing with administrative content management.
* ============================================================
--}}