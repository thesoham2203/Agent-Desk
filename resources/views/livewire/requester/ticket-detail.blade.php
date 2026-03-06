<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Ticket Header Section -->
    <div class="bg-white shadow sm:rounded-lg mb-8">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-start">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    #{{ $ticket->id }} - {{ $ticket->title }}
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Created {{ $ticket->created_at->format('M j, Y h:ia') }}
                    @if ($ticket->category)
                        • Category: {{ $ticket->category->name }}
                    @endif
                </p>
            </div>
            <div class="flex space-x-2">
                <!-- Status Badge -->
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                    {{ match ($ticket->status) {
    \App\Enums\TicketStatus::New => 'bg-blue-100 text-blue-800',
    \App\Enums\TicketStatus::Triaged => 'bg-indigo-100 text-indigo-800',
    \App\Enums\TicketStatus::InProgress => 'bg-yellow-100 text-yellow-800',
    \App\Enums\TicketStatus::Waiting => 'bg-orange-100 text-orange-800',
    \App\Enums\TicketStatus::Resolved => 'bg-green-100 text-green-800',
    default => 'bg-gray-100 text-gray-800'
} }}">
                    {{ $ticket->status->label() }}
                </span>

                <!-- Priority Badge -->
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    {{ $ticket->priority->label() }}
                </span>
            </div>
        </div>
        <!-- Original description as first message equivalent -->
        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
            <div class="prose max-w-none text-sm text-gray-700">
                {!! nl2br(e($ticket->body)) !!}
            </div>

            @if ($ticket->attachments->count() > 0)
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ($ticket->attachments as $attachment)
                        <a href="{{ route('attachments.download', $attachment) }}"
                            class="inline-flex items-center px-3 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-700 hover:bg-gray-200">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            {{ $attachment->original_name }} ({{ number_format($attachment->size / 1024, 1) }} KB)
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Thread Section -->
    <div class="space-y-6 mb-8">
        @foreach ($this->publicMessages as $message)
            <!-- Message Block -->
            <div
                class="flex {{ $message->author->role === \App\Enums\UserRole::Requester ? 'justify-end' : 'justify-start' }}">
                <div
                    class="max-w-[80%] rounded-lg p-4 {{ $message->author->role === \App\Enums\UserRole::Requester ? 'bg-blue-50 border border-blue-100' : 'bg-white border border-gray-200 shadow-sm' }}">
                    <div class="flex items-center justify-between mb-2">
                        <span
                            class="text-xs font-semibold {{ $message->author->role === \App\Enums\UserRole::Requester ? 'text-blue-900' : 'text-gray-900' }}">
                            {{ $message->author->name }}
                            @if ($message->author->role !== \App\Enums\UserRole::Requester)
                                <span
                                    class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-[10px] ml-2 font-medium">Support
                                    Agent</span>
                            @endif
                        </span>
                        <span
                            class="text-xs {{ $message->author->role === \App\Enums\UserRole::Requester ? 'text-blue-500' : 'text-gray-500' }} ml-4">
                            {{ $message->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <div
                        class="prose max-w-none text-sm {{ $message->author->role === \App\Enums\UserRole::Requester ? 'text-blue-800' : 'text-gray-700' }}">
                        {!! nl2br(e($message->body)) !!}
                    </div>

                    @if ($message->attachments->count() > 0)
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($message->attachments as $attachment)
                                <a href="{{ route('attachments.download', $attachment) }}"
                                    class="inline-flex items-center px-2 py-1 rounded-md {{ $message->author->role === \App\Enums\UserRole::Requester ? 'bg-blue-100 text-blue-700 hover:bg-blue-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} text-[10px] font-medium transition-colors">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    {{ $attachment->original_name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Reply Form Section -->
    @if ($ticket->status !== \App\Enums\TicketStatus::Resolved)
        <div class="bg-white shadow sm:rounded-lg px-4 py-5 sm:px-6">
            <h4 class="text-base font-medium text-gray-900 mb-4">Post a Reply</h4>

            @if ($replySent)
                <div class="rounded-md bg-green-50 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">Reply posted successfully.</p>
                        </div>
                    </div>
                </div>
            @endif

            <form wire:submit="postReply">
                <div>
                    <label for="replyBody" class="sr-only">Reply body</label>
                    <textarea id="replyBody" wire:model="replyBody" rows="4"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        placeholder="Type your reply here..."></textarea>
                    @error('replyBody')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Attachments</label>
                    <input type="file" wire:model="replyAttachments" multiple
                        accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.xls,.xlsx,.txt,.zip"
                        class="block w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />

                    @error('replyAttachments.*')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    @if (count($this->replyAttachments) > 0)
                        <p class="mt-1 text-xs text-blue-600 font-medium">
                            {{ count($this->replyAttachments) }} file(s) selected
                        </p>
                    @endif
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="postReply">Post Reply</span>
                        <span wire:loading wire:target="postReply">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Posting...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    @else
        <!-- Resolved Notice -->
        <div class="rounded-md bg-gray-50 p-4 border border-gray-200">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <p class="text-sm text-gray-600">
                    This ticket has been resolved. Open a new ticket if you need further assistance.
                </p>
            </div>
        </div>
    @endif
</div>