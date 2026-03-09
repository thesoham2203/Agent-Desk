<div class="max-w-2xl">
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-base font-medium text-gray-100">New Support Request</h1>
        <p class="text-xs text-gray-500 mt-0.5">
            Describe your issue in detail. Our team will respond as soon as possible.
        </p>
    </div>

    {{-- Form card --}}
    <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 space-y-5">

        {{-- Title --}}
        <div>
            <label class="block text-xs font-medium text-gray-400 uppercase
                          tracking-wider mb-1.5">
                Title <span class="text-red-500">*</span>
            </label>
            <input type="text" wire:model="title" placeholder="Brief summary of the issue" class="w-full bg-gray-800 border border-gray-700 text-gray-100
                          text-sm rounded-md px-3 py-2 placeholder-gray-600
                          focus:outline-none focus:ring-1 focus:ring-indigo-500
                          focus:border-indigo-500">
            @error('title')
                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Category --}}
        <div>
            <label class="block text-xs font-medium text-gray-400 uppercase
                          tracking-wider mb-1.5">
                Category <span class="text-red-500">*</span>
            </label>
            <select wire:model="categoryId" class="w-full bg-gray-800 border border-gray-700 text-gray-100
                           text-sm rounded-md px-3 py-2
                           focus:outline-none focus:ring-1 focus:ring-indigo-500
                           focus:border-indigo-500">
                <option value="">Select a category...</option>
                @foreach($this->categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            @error('categoryId')
                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Body --}}
        <div>
            <label class="block text-xs font-medium text-gray-400 uppercase
                          tracking-wider mb-1.5">
                Description <span class="text-red-500">*</span>
            </label>
            <textarea wire:model="body" rows="6"
                placeholder="Describe the issue in detail. Include steps to reproduce, error messages, and what you expected to happen."
                class="w-full bg-gray-800 border border-gray-700 text-gray-100
                             text-sm rounded-md px-3 py-2 placeholder-gray-600
                             focus:outline-none focus:ring-1 focus:ring-indigo-500
                             focus:border-indigo-500 resize-none"></textarea>
            @error('body')
                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Attachments --}}
        <div>
            <label class="block text-xs font-medium text-gray-400 uppercase
                          tracking-wider mb-1.5">
                Attachments <span class="text-gray-600">(optional)</span>
            </label>
            <input type="file" wire:model="attachments" multiple class="w-full text-xs text-gray-400
                          file:mr-3 file:py-1.5 file:px-3
                          file:rounded file:border-0
                          file:text-xs file:bg-gray-700 file:text-gray-300
                          hover:file:bg-gray-600 file:cursor-pointer">
            <p class="text-xs text-gray-600 mt-1">Max 10MB per file</p>
        </div>

        {{-- Submit --}}
        <div class="pt-2">
            <button wire:click="submit" wire:loading.attr="disabled" class="w-full bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50
                           text-white text-sm font-medium px-4 py-2.5 rounded-md
                           transition-colors">
                <span wire:loading.remove>Submit Request</span>
                <span wire:loading>Submitting...</span>
            </button>
        </div>
    </div>
</div>