<div>
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('agent.queue') }}" class="text-xs text-gray-500 hover:text-gray-300 transition-colors">
                ← Triage Queue
            </a>
            <span class="text-gray-700">/</span>
            <span class="font-mono text-xs text-gray-500">#{{ $ticket->id }}</span>
        </div>
        <h1 class="text-xl font-medium text-gray-100">{{ $ticket->title }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- LEFT 3/4: Thread and controls --}}
        <div class="lg:col-span-3 space-y-4">

            {{-- AI Panel: Persistent until resolved --}}
            @if($ticket->status !== \App\Enums\TicketStatus::Resolved)
                @livewire('agent.ai-panel', ['ticketId' => $ticket->id])
            @endif

            {{-- Thread --}}
            <div class="space-y-3">
                @foreach($this->threadMessages as $message)
                            @php
                                $isInternal = $message->type === \App\Enums\TicketMessageType::Internal;
                                $isAgent = $message->author->role === \App\Enums\UserRole::Agent ||
                                    $message->author->role === \App\Enums\UserRole::Admin;
                            @endphp
                            <div class="rounded-lg border p-4 {{ $isInternal
                    ? 'bg-amber-950/20 border-amber-900/40'
                    : 'bg-gray-900 border-gray-800' }}">

                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-medium text-gray-300">
                                            {{ $message->author->name }}
                                        </span>
                                        @if($isInternal)
                                            <span
                                                class="font-mono text-[10px] bg-amber-950
                                                                                                                 text-amber-400 px-1.5 py-0.5 rounded">
                                                Internal Note
                                            </span>
                                        @elseif($isAgent)
                                            <span
                                                class="font-mono text-[10px] bg-indigo-950
                                                                                                                 text-indigo-300 px-1.5 py-0.5 rounded">
                                                Agent
                                            </span>
                                        @endif
                                    </div>
                                    <span class="font-mono text-[10px] text-gray-600">
                                        {{ $message->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <p class="text-sm text-gray-300 leading-relaxed whitespace-pre-wrap">{{ $message->body }}</p>

                                @if($message->attachments->count() > 0)
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach($message->attachments as $attachment)
                                            <a href="{{ route('attachments.download', $attachment) }}"
                                                class="inline-flex items-center gap-1 font-mono text-[10px]
                                                                                                                              bg-gray-800 text-gray-400 hover:text-gray-200
                                                                                                                              px-2 py-1 rounded transition-colors">
                                                ↓ {{ $attachment->original_name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                @endforeach
            </div>

            {{-- Response Editor --}}
            @if($ticket->status !== \App\Enums\TicketStatus::Resolved)
                <div class="bg-gray-900 border border-gray-800 rounded-lg overflow-hidden">
                    {{-- Tabs --}}
                    <div class="flex border-b border-gray-800">
                        <button wire:click="$set('showInternalNoteForm', false)" class="px-4 py-2 text-xs font-medium transition-colors
                                       {{ !$showInternalNoteForm
        ? 'bg-gray-800 text-gray-100'
        : 'text-gray-500 hover:text-gray-300' }}">
                            Public Reply
                        </button>
                        <button wire:click="$set('showInternalNoteForm', true)" class="px-4 py-2 text-xs font-medium transition-colors
                                       {{ $showInternalNoteForm
        ? 'bg-amber-950/40 text-amber-400'
        : 'text-gray-500 hover:text-gray-300' }}">
                            Internal Note
                        </button>
                    </div>

                    <div class="p-4">
                        <textarea wire:model="{{ $showInternalNoteForm ? 'noteBody' : 'replyBody' }}" rows="5"
                            placeholder="{{ $showInternalNoteForm ? 'Add a private note for staff...' : 'Respond to requester...' }}"
                            class="w-full bg-gray-800 border border-gray-700 text-gray-100
                                         text-sm rounded-md px-3 py-2 placeholder-gray-600
                                         focus:outline-none focus:ring-1
                                         {{ $showInternalNoteForm ? 'focus:ring-amber-500 focus:border-amber-500' : 'focus:ring-indigo-500 focus:border-indigo-500' }}
                                         resize-none mb-4"></textarea>

                        <div class="flex items-center justify-between">
                            <input type="file" wire:model="replyAttachments" multiple class="text-xs text-gray-500
                                          file:mr-2 file:py-1 file:px-2 file:rounded
                                          file:border-0 file:text-xs file:bg-gray-700
                                          file:text-gray-300 hover:file:bg-gray-600">

                            <button wire:click="{{ $showInternalNoteForm ? 'addInternalNote' : 'postReply' }}"
                                class="text-xs px-4 py-1.5 rounded-md text-white transition-colors
                                           {{ $showInternalNoteForm ? 'bg-amber-700 hover:bg-amber-600' : 'bg-indigo-600 hover:bg-indigo-500' }}">
                                {{ $showInternalNoteForm ? 'Save Note' : 'Send Reply' }}
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 text-center">
                    <p class="text-sm text-gray-400">This ticket has been resolved. No further replies can be added.</p>
                </div>
            @endif
        </div>

        {{-- RIGHT 1/4: Sidebar --}}
        <div class="space-y-4">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-4 space-y-6">

                {{-- Status --}}
                <div>
                    <label class="block text-[10px] text-gray-600 uppercase
                                  tracking-wider mb-2">Status</label>
                    <select wire:model.live="newStatus" wire:change="updateStatus" class="w-full bg-gray-800 border border-gray-700 text-gray-200
                                   text-xs rounded px-2 py-1.5 focus:outline-none
                                   focus:ring-1 focus:ring-indigo-500">
                        @foreach(\App\Enums\TicketStatus::cases() as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    @error('newStatus')
                        <span class="text-[10px] text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Priority --}}
                <div>
                    <label class="block text-[10px] text-gray-600 uppercase
                                  tracking-wider mb-2">Priority</label>
                    <select wire:model.live="newPriority" wire:change="updatePriority" class="w-full bg-gray-800 border border-gray-700 text-gray-200
                                   text-xs rounded px-2 py-1.5 focus:outline-none
                                   focus:ring-1 focus:ring-indigo-500">
                        @foreach(\App\Enums\TicketPriority::cases() as $priority)
                            <option value="{{ $priority->value }}">{{ $priority->label() }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Assignee --}}
                <div>
                    <label class="block text-[10px] text-gray-600 uppercase
                                  tracking-wider mb-2">Assignee</label>
                    <div class="space-y-2">
                        <select wire:model.live="assignToAgentId" wire:change="assignToAgent" class="w-full bg-gray-800 border border-gray-700 text-gray-200
                                       text-xs rounded px-2 py-1.5 focus:outline-none
                                       focus:ring-1 focus:ring-indigo-500">
                            <option value="">{{ $ticket->assignee?->name ?? 'Unassigned' }}</option>
                            @foreach($this->availableAgents as $agent)
                                @if($agent->id !== $ticket->assigned_to)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        @if(!$ticket->assigned_to || $ticket->assigned_to !== auth()->id())
                            <button wire:click="assignToSelf" class="w-full text-[10px] text-indigo-400 border
                                                           border-indigo-900 bg-indigo-950/30 py-1
                                                           rounded hover:bg-indigo-950 transition-colors">
                                Assign to me
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Metadata --}}
                <div class="pt-4 border-t border-gray-800 space-y-3">
                    <div>
                        <dt class="text-[10px] text-gray-600 uppercase mb-1">Requester</dt>
                        <dd class="text-xs text-gray-300">{{ $ticket->requester->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] text-gray-600 uppercase mb-1">Category</dt>
                        <dd class="text-xs text-gray-300">
                            {{ $ticket->category?->name ?? 'Uncategorized' }}
                        </dd>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>