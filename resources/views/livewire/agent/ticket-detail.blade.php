<div>
    <!--
    ============================================================
    FILE: ticket-detail.blade.php
    LAYER: View (Livewire)
    ============================================================
    WHAT IS THIS?
    The UI for the complete ticket detail screen (internal & public) for agents.
    ============================================================
    -->
    <div class="max-w-7xl mx-auto py-8 lg:px-8">
        <!-- HEADER -->
        <div class="bg-white shadow sm:rounded-lg mb-8">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-start">
                <div>
                    <h3 class="text-2xl leading-6 font-semibold text-gray-900">
                        {{ $ticket->title }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-500">
                        <span class="font-medium text-gray-900">Ticket ID:</span> #{{ $ticket->id }}
                        &middot;
                        <span class="font-medium text-gray-900">Created:</span>
                        {{ $ticket->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                    <!-- Requester -->
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Requester</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $ticket->requester->name }} <br>
                            <span class="text-gray-500">{{ $ticket->requester->email }}</span>
                        </dd>
                    </div>

                    <!-- Assignee -->
                    <div class="sm:col-span-1 border-l border-gray-100 pl-4">
                        <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($ticket->assignee)
                                {{ $ticket->assignee->name }}
                            @else
                                <span class="italic text-gray-500">Unassigned</span>
                                <button wire:click="assignToSelf"
                                    class="ml-2 inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none">
                                    Assign to me
                                </button>
                            @endif
                        </dd>
                    </div>

                    <!-- Category -->
                    <div class="sm:col-span-2 border-t border-gray-100 pt-4">
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $ticket->category->name }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- STATUS + PRIORITY CONTROLS -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <div class="bg-white shadow sm:rounded-lg p-5">
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="status" wire:model="newStatus" wire:change="updateStatus"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @foreach(\App\Enums\TicketStatus::cases() as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-5">
                <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                <select id="priority" wire:model="newPriority" wire:change="updatePriority"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @foreach(\App\Enums\TicketPriority::cases() as $priority)
                        <option value="{{ $priority->value }}">{{ $priority->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- MESSAGE THREAD -->
        <div class="bg-white shadow sm:rounded-lg mb-8">
            <div class="px-4 py-5 sm:px-6">
                <h4 class="text-lg leading-6 font-medium text-gray-900">Thread</h4>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6 space-y-6">
                @foreach($this->threadMessages as $message)
                    <div
                        class="rounded-lg p-4 border @if($message->type === \App\Enums\TicketMessageType::Internal) bg-amber-50 border-amber-200 @else bg-white border-gray-200 @endif leading-relaxed">
                        <div
                            class="flex justify-between items-center mb-4 pb-2 border-b @if($message->type === \App\Enums\TicketMessageType::Internal) border-amber-200 @else border-gray-100 @endif">
                            <div class="flex items-center space-x-3">
                                <span class="font-bold text-gray-900">{{ $message->author->name }}</span>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $message->author->role->label() }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500 flex items-center space-x-3">
                                <span>{{ $message->created_at->diffForHumans() }}</span>
                                @if($message->type === \App\Enums\TicketMessageType::Internal)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Internal
                                        Note</span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Public
                                        Reply</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-gray-700 whitespace-pre-wrap">{{ $message->body }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- REPLY TABS & FORMS -->
        <div class="bg-white shadow sm:rounded-lg p-6">
            <div class="flex border-b border-gray-200 mb-6">
                <button wire:click="$set('showInternalNoteForm', false)"
                    class="pb-4 px-4 font-semibold text-sm mr-2 {{ !$showInternalNoteForm ? 'border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Public Reply
                </button>
                <button wire:click="$set('showInternalNoteForm', true)"
                    class="pb-4 px-4 font-semibold text-sm {{ $showInternalNoteForm ? 'border-b-2 border-amber-500 text-amber-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Internal Note
                </button>
            </div>

            <!-- SUCCESS FLASHES -->
            @if($replySent)
                <div class="rounded-md bg-green-50 p-4 mb-6 relative">
                    <p class="text-sm font-medium text-green-800">Reply sent successfully!</p>
                    <button wire:click="$set('replySent', false)"
                        class="absolute top-4 right-4 text-green-500">&times;</button>
                </div>
            @endif

            @if($noteSaved)
                <div class="rounded-md bg-amber-50 p-4 mb-6 relative">
                    <p class="text-sm font-medium text-amber-800">Internal note saved!</p>
                    <button wire:click="$set('noteSaved', false)"
                        class="absolute top-4 right-4 text-amber-500">&times;</button>
                </div>
            @endif

            <form wire:submit="{{ $showInternalNoteForm ? 'addInternalNote' : 'postReply' }}">
                <div class="mb-4">
                    <label for="body" class="sr-only">Message Body</label>
                    <textarea id="body" rows="4" wire:model="{{ $showInternalNoteForm ? 'noteBody' : 'replyBody' }}"
                        class="block w-full rounded-md shadow-sm sm:text-sm {{ $showInternalNoteForm ? 'border-amber-300 focus:border-amber-500 focus:ring-amber-500 bg-amber-50' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500' }}"
                        placeholder="{{ $showInternalNoteForm ? 'Type an internal note... (Agents only)' : 'Type a public reply... (Visible to requester)' }}"></textarea>

                    @if($showInternalNoteForm)
                        <div class="text-error text-sm mt-1">
                            @error('noteBody') <span class="text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <p class="mt-2 text-sm text-amber-600 font-medium">⚠️ Only visible to agents and admins</p>
                    @else
                        <div class="text-error text-sm mt-1">
                            @error('replyBody') <span class="text-red-600">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $showInternalNoteForm ? 'bg-amber-600 hover:bg-amber-700 focus:ring-amber-500' : 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500' }}"
                        wire:loading.attr="disabled">
                        <span
                            wire:loading.remove>{{ $showInternalNoteForm ? 'Add Internal Note' : 'Send Public Reply' }}</span>
                        <span wire:loading>Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>