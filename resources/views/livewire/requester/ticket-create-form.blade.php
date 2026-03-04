<div class="max-w-3xl mx-auto py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Create New Ticket</h1>
        <p class="mt-2 text-sm text-gray-600">Please describe your issue in detail. We will get back to you as soon as
            possible.</p>
    </div>

    @if($submitted)
        <!-- Success State -->
        <div class="rounded-md bg-green-50 p-4 mb-6 relative">
            <div class="flex">
                <div class="flex-shrink-0">
                    <!-- Heroicon matching solid/check-circle -->
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Ticket Created</h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>Your ticket has been successfully submitted. You are being redirected...</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Create Form -->
        <form wire:submit="submit" class="bg-white shadow sm:rounded-lg p-6 space-y-6">
            <!-- Category Dropdown -->
            <div>
                <label for="categoryId" class="block text-sm font-medium text-gray-700">Category</label>
                <select id="categoryId" wire:model="categoryId"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Select a Category...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('categoryId')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Title Input -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" id="title" wire:model="title" placeholder="Brief summary of the issue"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('title')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Body Textarea -->
            <div>
                <label for="body" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea id="body" wire:model="body" rows="6" placeholder="Please provide as much detail as possible..."
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                @error('body')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- File Upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Attachments</label>
                <input type="file" wire:model="attachments" multiple
                    accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.xls,.xlsx,.txt,.zip"
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />

                @error('attachments.*')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                @if(count($this->attachments) > 0)
                    <p class="mt-2 text-sm text-blue-600 font-medium">
                        {{ count($this->attachments) }} file(s) selected
                    </p>
                @endif
            </div>

            <!-- Submit Button with Loading State -->
            <div class="flex justify-end">
                <button type="submit"
                    class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submit">Submit Ticket</span>
                    <span wire:loading wire:target="submit">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Submitting...
                    </span>
                </button>
            </div>
        </form>
    @endif
</div>