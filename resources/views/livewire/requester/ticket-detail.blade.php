<div>
    {{-- Page header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('requester.tickets.index') }}"
            class="text-xs text-gray-500 hover:text-gray-300 transition-colors">
            ← My Tickets
        </a>
        <span class="text-gray-700">/</span>
        <span class="font-mono text-xs text-gray-500">#{{ $ticket->id }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT: Thread + Reply --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Message thread --}}
            <div class="space-y-3">
                @foreach($this->publicMessages as $message)
                            @php
                                $isRequester = $message->author->id === auth()->id();
                            @endphp
                            <div class="rounded-lg border p-4 {{ $isRequester
                    ? 'bg-gray-900 border-gray-800'
                    : 'bg-indigo-950/30 border-indigo-900/50' }}">

                                {{-- Author line --}}
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full bg-gray-700
                                                                     flex items-center justify-center
                                                                     font-mono text-[10px] text-gray-300">
                                            {{ strtoupper(substr($message->author->name, 0, 1)) }}
                                        </span>
                                        <span class="text-xs font-medium text-gray-300">
                                            {{ $message->author->name }}
                                        </span>
                                        @if(!$isRequester)
                                            <span class="font-mono text-[10px] bg-indigo-950
                                                                                 text-indigo-300 px-1.5 py-0.5 rounded">
                                                Support
                                            </span>
                                        @endif
                                    </div>
                                    <span class="font-mono text-[10px] text-gray-600">
                                        {{ $message->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                {{-- Body --}}
                                <p class="text-sm text-gray-300 leading-relaxed">
                                    {{ $message->body }}
                                </p>

                                {{-- Attachments --}}
                                @if($message->attachments->count() > 0)
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach($message->attachments as $attachment)
                                            <a href="{{ route('attachments.download', $attachment) }}" class="inline-flex items-center gap-1 font-mono text-[10px]
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

            {{-- Reply form --}}
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-4">
                <label class="block text-xs font-medium text-gray-500
                              uppercase tracking-wider mb-2">
                    Reply
                </label>
                <textarea wire:model="replyBody" rows="4" placeholder="Type your reply..." class="w-full bg-gray-800 border border-gray-700 text-gray-100
                                 text-sm rounded-md px-3 py-2 placeholder-gray-600
                                 focus:outline-none focus:ring-1 focus:ring-indigo-500
                                 focus:border-indigo-500 resize-none mb-3"></textarea>

                <div class="flex items-center justify-between">
                    <input type="file" wire:model="replyAttachments" multiple class="text-xs text-gray-500
                                  file:mr-2 file:py-1 file:px-2 file:rounded
                                  file:border-0 file:text-xs file:bg-gray-700
                                  file:text-gray-300 hover:file:bg-gray-600">
                    <button wire:click="postReply" class="bg-indigo-600 hover:bg-indigo-500 text-white
                                   text-xs px-4 py-1.5 rounded-md transition-colors">
                        Send Reply
                    </button>
                </div>
            </div>
        </div>

        {{-- RIGHT: Sidebar --}}
        <div class="space-y-4">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-4">
                <h2 class="text-sm font-medium text-gray-100 mb-4 pb-3
                           border-b border-gray-800">
                    {{ $ticket->title }}
                </h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs text-gray-600 uppercase tracking-wider mb-1">
                            Status
                        </dt>
                        <dd>
                            @include(
                                'partials.status-badge',
                                ['status' => $ticket->status->value]
                            )
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-600 uppercase tracking-wider mb-1">
                            Priority
                        </dt>
                        <dd>
                            @include(
                                'partials.priority-badge',
                                ['priority' => $ticket->priority->value]
                            )
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-600 uppercase tracking-wider mb-1">
                            Category
                        </dt>
                        <dd class="text-sm text-gray-300">
                            {{ $ticket->category?->name ?? '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-600 uppercase tracking-wider mb-1">
                            Assigned To
                        </dt>
                        <dd class="text-sm text-gray-300">
                            {{ $ticket->assignee?->name ?? 'Unassigned' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-600 uppercase tracking-wider mb-1">
                            Opened
                        </dt>
                        <dd class="font-mono text-xs text-gray-500">
                            {{ $ticket->created_at->diffForHumans() }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>