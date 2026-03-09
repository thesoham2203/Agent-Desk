<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-base font-medium text-gray-100">Response Macros</h1>
            <p class="text-xs text-gray-500 mt-0.5">Canned responses to speed up ticket resolution</p>
        </div>
        <button wire:click="$set('showForm', true)" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs
                       px-3 py-1.5 rounded-md transition-colors">
            + Add Macro
        </button>
    </div>

    {{-- Form Section --}}
    @if($showForm)
        <div class="mb-8 p-6 bg-gray-900 border border-gray-800 rounded-lg space-y-5">
            <h2 class="text-sm font-medium text-gray-100">
                {{ $editingId ? 'Edit' : 'Create' }} Macro
            </h2>

            <div class="space-y-5">
                <div>
                    <label class="block text-[10px] text-gray-600 uppercase
                                      tracking-wider mb-1.5">Title</label>
                    <input type="text" wire:model="title" placeholder="e.g. Password Reset Instructions" class="w-full bg-gray-800 border border-gray-700 text-gray-100
                                      text-sm rounded-md px-3 py-2 placeholder-gray-600
                                      focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    @error('title')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[10px] text-gray-600 uppercase
                                      tracking-wider mb-1.5">Response Body</label>
                    <textarea wire:model="body" rows="5" placeholder="Type the macro content here..." class="w-full bg-gray-800 border border-gray-700 text-gray-100
                                         text-sm rounded-md px-3 py-2 placeholder-gray-600
                                         focus:outline-none focus:ring-1 focus:ring-indigo-500
                                         resize-none"></textarea>
                    @error('body')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button wire:click="resetForm" class="text-xs text-gray-500 hover:text-gray-300 transition-colors">
                    Cancel
                </button>
                <button wire:click="{{ $editingId ? 'update' : 'create' }}" wire:loading.attr="disabled" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs
                                   px-4 py-1.5 rounded-md transition-colors">
                    {{ $editingId ? 'Update' : 'Save Macro' }}
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

    {{-- Table --}}
    <div class="overflow-hidden rounded-lg border border-gray-800">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-900 border-b border-gray-800">
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider">Macro Title</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium
                               text-gray-500 uppercase tracking-wider w-1/2">Preview</th>
                    <th class="px-4 py-2.5 text-right text-xs font-medium
                               text-gray-500 uppercase tracking-wider w-32">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse($macroList as $macro)
                    <tr class="hover:bg-gray-900 transition-colors" wire:key="macro-{{ $macro->id }}">
                        <td class="px-4 py-3 text-sm text-gray-200">
                            {{ $macro->title }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 truncate max-w-xs">
                            {{ Str::limit($macro->body, 80) }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="edit({{ $macro->id }})" class="text-xs text-indigo-400 hover:text-indigo-300
                                               mr-3 transition-colors">
                                Edit
                            </button>
                            <button wire:click="delete({{ $macro->id }})" wire:confirm="Confirm macro deletion?" class="text-xs text-red-400/70 hover:text-red-400
                                               transition-colors">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-12 text-center text-sm text-gray-600">
                            No macros found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>