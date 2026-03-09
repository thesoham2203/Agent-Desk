<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-base font-medium text-gray-100">Ticket Categories</h1>
            <p class="text-xs text-gray-500 mt-0.5">Define classifications for support requests</p>
        </div>
        <button wire:click="$set('showForm', true)" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs
                       px-3 py-1.5 rounded-md transition-colors">
            + Add Category
        </button>
    </div>

    {{-- Form Sidebar/Drawer style (visible when triggered) --}}
    @if($showForm)
        <div class="mb-8 p-6 bg-gray-900 border border-gray-800 rounded-lg space-y-5">
            <h2 class="text-sm font-medium text-gray-100">
                {{ $editingId ? 'Edit' : 'Create' }} Category
            </h2>

            <div class="grid grid-cols-1 gap-5">
                <div>
                    <label class="block text-[10px] text-gray-600 uppercase
                                          tracking-wider mb-1.5">Name</label>
                    <input type="text" wire:model="name" placeholder="e.g. Technical Issues" class="w-full bg-gray-800 border border-gray-700 text-gray-100
                                          text-sm rounded-md px-3 py-2 placeholder-gray-600
                                          focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    @error('name')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[10px] text-gray-600 uppercase
                                          tracking-wider mb-1.5">Description</label>
                    <textarea wire:model="description" rows="3" placeholder="Briefly describe what this category covers..."
                        class="w-full bg-gray-800 border border-gray-700 text-gray-100
                                             text-sm rounded-md px-3 py-2 placeholder-gray-600
                                             focus:outline-none focus:ring-1 focus:ring-indigo-500
                                             resize-none"></textarea>
                    @error('description')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button wire:click="resetForm" class="text-xs text-gray-500 hover:text-gray-300 transition-colors">
                    Cancel
                </button>
                <button wire:click="{{ $editingId ? 'update' : 'create' }}" wire:loading.attr="disabled" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs
                                       px-4 py-1.5 rounded-md transition-colors">
                    {{ $editingId ? 'Update' : 'Save' }}
                </button>
            </div>
        </div>
    @endif

    {{-- Messages --}}
    @if (session()->has('success'))
        <div class="mb-6 p-3 bg-green-950/30 border border-green-900/50 rounded-md
                            text-xs text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-3 bg-red-950/30 border border-red-900/50 rounded-md
                            text-xs text-red-400">
            {{ session('error') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="overflow-hidden rounded-lg border border-gray-800">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-900 border-b border-gray-800">
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-4 py-2.5 text-right text-xs font-medium
                               text-gray-500 uppercase tracking-wider w-32">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse($categoryList as $category)
                    <tr class="hover:bg-gray-900 transition-colors" wire:key="cat-{{ $category->id }}">
                        <td class="px-4 py-3 text-sm text-gray-200">
                            {{ $category->name }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ $category->description ?: '—' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="edit({{ $category->id }})" class="text-xs text-indigo-400 hover:text-indigo-300
                                                   mr-3 transition-colors">
                                Edit
                            </button>
                            <button wire:click="delete({{ $category->id }})" wire:confirm="Confirm category deletion?"
                                class="text-xs text-red-400/70 hover:text-red-400
                                                   transition-colors">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-12 text-center">
                            <p class="text-sm text-gray-500">No categories defined</p>
                            <p class="text-xs text-gray-600 mt-1">
                                Add categories to help organize support tickets.
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>