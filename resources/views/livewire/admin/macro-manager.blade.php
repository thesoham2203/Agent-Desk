{{--
/**
* ============================================================
* FILE: macro-manager.blade.php
* LAYER: View
* ============================================================
*
* WHAT IS THIS?
* The UI for managing agent macros (canned responses).
*
* WHY DOES IT EXIST?
* To allow admins to curate a library of efficient responses
* that agents can use to speed up ticket resolution.
*
* HOW IT FITS IN THE APP:
* Rendered by App\Livewire\Admin\MacroManager.
*
* LARAVEL CONCEPT EXPLAINED:
* wire:model synchronizes input values with the PHP component
* properties in real-time, allowing for immediate feedback and validation.
* ============================================================
*/
--}}

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manage Macros</h1>
        <button wire:click="$set('showForm', true)"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
            Add Macro
        </button>
    </div>

    @if ($showForm)
        <div class="mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <h2 class="text-lg font-medium mb-4">{{ $editingId ? 'Edit' : 'Add' }} Macro</h2>
            <form wire:submit.prevent="{{ $editingId ? 'update' : 'create' }}" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" wire:model="title"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="e.g. Password Reset Instructions">
                    @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Body</label>
                    <textarea wire:model="body" rows="5"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Type the macro content here..."></textarea>
                    @error('body') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="resetForm"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition disabled:opacity-50"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ $editingId ? 'Update' : 'Save' }}</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-400 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden border border-gray-200 rounded-lg shadow-sm bg-white">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Body
                        Preview</th>
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($macroList as $macro)
                    <tr wire:key="macro-{{ $macro->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $macro->title }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ Str::limit($macro->body, 100) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="edit({{ $macro->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                Edit
                            </button>
                            <button wire:click="delete({{ $macro->id }})" wire:confirm="Delete this macro?"
                                class="text-red-600 hover:text-red-900">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">
                            No macros found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{--
* ============================================================
* WHAT TO READ NEXT:
* ============================================================
* → resources/views/livewire/admin/sla-config-manager.blade.php
* WHY: Moving from content management to system configuration.
* ============================================================
--}}