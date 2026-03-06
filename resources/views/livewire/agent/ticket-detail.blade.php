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
        <!-- AI PANEL (Day 8 Implementation) -->
        <div class="mb-8 bg-white shadow sm:rounded-lg overflow-hidden p-6 border-l-4 border-indigo-500">
            @livewire('agent.ai-panel', ['ticketId' => $ticket->id])
        </div>

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
                            {{ $ticket->requester?->name ?? 'Unknown' }} <br>
                            <span class="text-gray-500">{{ $ticket->requester?->email ?? 'no-email@example.com'
                                }}</span>
                        </dd>
                    </div>

                    <!-- Assignee -->
                    <div class="sm:col-span-1 border-l border-gray-100 pl-4">
                        <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <div class="flex items-center gap-2">
                                <div class="flex-grow">
                                    @if ($ticket->assignee)
                                        <span class="font-medium text-gray-900">{{ $ticket->assignee->name }}</span>
                                    @else
                                        <span class="italic text-gray-500">Unassigned</span>
                                    @endif
                                </div>
                                <div class="w-48">
                                    <select wire:model.live="assignToAgentId" wire:change="assignToAgent"
                                        class="block w-full rounded-md border-gray-300 py-1 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">(Reassign...)</option>
                                        @foreach ($this->availableAgents as $agent)
                                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if (!$ticket->assigned_to || $ticket->assigned_to !== auth()->id())
                                    <button wire:click="assignToSelf"
                                        class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none">
                                        Take
                                    </button>
                                @endif
                            </div>
                        </dd>
                    </div>

                    <!-- Category -->
                    <div class="sm:col-span-2 border-t border-gray-100 pt-4">
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $ticket->category?->name ?? 'Uncategorized' }}
                            </span>
                        </dd>
                    </div>

                    <!-- Attachments -->
                    @if ($ticket->attachments->count() > 0)
                        <div class="sm:col-span-2 border-t border-gray-100 pt-4">
                            <dt class="text-sm font-medium text-gray-500">Ticket Attachments</dt>
                            <dd class="mt-2 flex flex-wrap gap-2">
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
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- STATUS + PRIORITY CONTROLS -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <div class="bg-white shadow sm:rounded-lg p-5">
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="status" wire:model="newStatus" wire:change="updateStatus"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @foreach (\App\Enums\TicketStatus::cases() as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-5">
                <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                <select id="priority" wire:model="newPriority" wire:change="updatePriority"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @foreach (\App\Enums\TicketPriority::cases() as $priority)
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
                @foreach ($this->threadMessages as $message)
                    <div
                        class="rounded-lg p-4 border @if ($message->type === \App\Enums\TicketMessageType::Internal) bg-amber-50 border-amber-200 @else bg-white border-gray-200 @endif leading-relaxed">
                        <div
                            class="flex justify-between items-center mb-4 pb-2 border-b @if ($message->type === \App\Enums\TicketMessageType::Internal) border-amber-200 @else border-gray-100 @endif">
                            <div class="flex items-center space-x-3">
                                <span class="font-bold text-gray-900">{{ $message->author?->name ?? 'Deleted User' }}</span>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $message->author?->role?->label() ?? 'Unknown' }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500 flex items-center space-x-3">
                                <span>{{ $message->created_at->diffForHumans() }}</span>
                                @if ($message->type === \App\Enums\TicketMessageType::Internal)
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

                        @if ($message->attachments->count() > 0)
                            <div
                                class="mt-4 pt-3 border-t @if ($message->type === \App\Enums\TicketMessageType::Internal) border-amber-200 @else border-gray-100 @endif flex flex-wrap gap-2">
                                @foreach ($message->attachments as $attachment)
                                    <a href="{{ route('attachments.download', $attachment) }}"
                                        class="inline-flex items-center px-2 py-1 rounded-md bg-white border @if ($message->type === \App\Enums\TicketMessageType::Internal) border-amber-300 text-amber-700 hover:bg-amber-100 @else border-gray-200 text-gray-700 hover:bg-gray-50 @endif text-xs font-medium transition-colors">
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
            @if ($replySent)
                <div class="rounded-md bg-green-50 p-4 mb-6 relative">
                    <p class="text-sm font-medium text-green-800">Reply sent successfully!</p>
                    <button wire:click="$set('replySent', false)"
                        class="absolute top-4 right-4 text-green-500">&times;</button>
                </div>
            @endif

            @if ($noteSaved)
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

                    @if ($showInternalNoteForm)
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

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Attachments</label>
                    <input type="file" wire:model="replyAttachments" multiple
                        accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.xls,.xlsx,.txt,.zip"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold {{ $showInternalNoteForm ? 'file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200' : 'file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100' }}" />

                    @error('replyAttachments.*')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    @if (count($this->replyAttachments) > 0)
                        <p
                            class="mt-2 text-sm {{ $showInternalNoteForm ? 'text-amber-600' : 'text-indigo-600' }} font-medium">
                            {{ count($this->replyAttachments) }} file(s) selected
                        </p>
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