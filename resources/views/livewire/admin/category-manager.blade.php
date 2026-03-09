{{--
/**
* ============================================================
* FILE: category-manager.blade.php
* LAYER: View
* ============================================================
*
* WHAT IS THIS?
* The user interface for the CategoryManager Livewire component.
*
* WHY DOES IT EXIST?
* To provide administrators with a way to create, read, update,
* and delete ticket categories via a clean web interface.
*
* HOW IT FITS IN THE APP:
* Rendered by App\Livewire\Admin\CategoryManager.
*
* LARAVEL CONCEPT EXPLAINED:
* Blade templates use directives like @@if and @@foreach to dynamically
* render HTML based on the state of the associated Livewire component.
* ============================================================
*/
--}}

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manage Categories</h1>
        <button wire:click="$set('showForm', true)"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
            Add Category
        </button>
    </div>

    @if ($showForm)
        <div class="mb-8 p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <h2 class="text-lg font-medium mb-4">{{ $editingId ? 'Edit' : 'Add' }} Category</h2>
            <form wire:submit.prevent="{{ $editingId ? 'update' : 'create' }}" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" wire:model="name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="e.g. Hardware Support">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea wire:model="description" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Briefly describe what this category covers..."></textarea>
                    @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-400 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-hidden border border-gray-200 rounded-lg shadow-sm bg-white">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Description</th>
                    <th scope="col"
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($categoryList as $category)
                    <tr wire:key="cat-{{ $category->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $category->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $category->description ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="edit({{ $category->id }})"
                                class="text-indigo-600 hover:text-indigo-900 mr-3">
                                Edit
                            </button>
                            <button wire:click="delete({{ $category->id }})"
                                wire:confirm="Are you sure you want to delete this category? This cannot be undone."
                                class="text-red-600 hover:text-red-900">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">
                            No categories found. Click "Add Category" to create your first one.
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
* → resources/views/livewire/admin/macro-manager.blade.php
* WHY: Both follow the same administrative CRUD pattern.
* ============================================================
--}}