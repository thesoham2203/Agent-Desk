<div class="border-l-2 border-purple-600 bg-purple-950/20 rounded-r-lg p-5 mb-6"
     wire:poll.3s="refresh">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4"
         x-data="{ open: true }">
        <div class="flex items-center gap-2">
            <span class="text-purple-400 text-sm">⚡</span>
            <span class="font-mono text-xs font-medium text-purple-300
                         uppercase tracking-wider">
                AI Assistant
            </span>
        </div>
        <button @click="open = !open"
                class="text-xs text-gray-600 hover:text-gray-400 transition-colors">
            <span x-show="open">▾ collapse</span>
            <span x-show="!open" x-cloak>▸ expand</span>
        </button>
    </div>

    {{-- Error flash --}}
    @if(session('error'))
        <div class="mb-4 text-xs text-red-400 bg-red-950/50 border border-red-900
                    rounded px-3 py-2">
            {{ session('error') }}
        </div>
    @endif

    <div x-show="open">

        {{-- ── TRIAGE SECTION ── --}}
        <div class="mb-4">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-gray-400 uppercase
                             tracking-wider">
                    Ticket Triage
                </span>

                {{-- Status badge --}}
                @if($this->latestTriageRun?->status === \App\Enums\AiRunStatus::Queued)
                    <span class="font-mono text-[10px] bg-amber-950 text-amber-300
                                 px-2 py-0.5 rounded animate-pulse">
                        ○ Queued
                    </span>
                @elseif($this->latestTriageRun?->status === \App\Enums\AiRunStatus::Running)
                    <span class="font-mono text-[10px] bg-blue-950 text-blue-300
                                 px-2 py-0.5 rounded">
                        ◌ Running
                    </span>
                @elseif($this->latestTriageRun?->status === \App\Enums\AiRunStatus::Succeeded)
                    <span class="font-mono text-[10px] bg-green-950 text-green-300
                                 px-2 py-0.5 rounded">
                        ✓ Complete
                    </span>
                @elseif($this->latestTriageRun?->status === \App\Enums\AiRunStatus::Failed)
                    <span class="font-mono text-[10px] bg-red-950 text-red-300
                                 px-2 py-0.5 rounded">
                        ✕ Failed
                    </span>
                @endif
            </div>

            @if(!$this->latestTriageRun)
                <p class="text-xs text-gray-600 mb-3">No triage run yet.</p>
                <button wire:click="runTriage"
                        wire:loading.attr="disabled"
                        wire:target="runTriage"
                        class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs
                               px-3 py-1.5 rounded-md transition-colors disabled:opacity-50">
                    <span wire:loading.remove wire:target="runTriage">Run Triage</span>
                    <span wire:loading wire:target="runTriage">Triageing...</span>
                </button>

            @elseif($this->latestTriageRun->status === \App\Enums\AiRunStatus::Queued)
                <p class="text-xs text-gray-500">Waiting for queue worker...</p>

            @elseif($this->latestTriageRun->status === \App\Enums\AiRunStatus::Running)
                <p class="text-xs text-gray-500">Analyzing via Groq API...</p>

            @elseif($this->latestTriageRun->status === \App\Enums\AiRunStatus::Succeeded)
                <div class="space-y-3">
                    {{-- Category + Priority grid --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-[10px] text-gray-600 uppercase
                                      tracking-wider mb-1">Category</p>
                            <p class="text-xs text-gray-200">
                                {{ $this->latestTriageRun->output_json['category'] }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-600 uppercase
                                      tracking-wider mb-1">Priority</p>
                            @include('partials.priority-badge', [
                                'priority' => $this->latestTriageRun->output_json['priority']
                            ])
                        </div>
                    </div>

                    {{-- Summary --}}
                    <div>
                        <p class="text-[10px] text-gray-600 uppercase
                                  tracking-wider mb-1">Summary</p>
                        <p class="text-xs text-gray-300 leading-relaxed">
                            {{ $this->latestTriageRun->output_json['summary'] }}
                        </p>
                    </div>

                    {{-- Tags --}}
                    @if(!empty($this->latestTriageRun->output_json['tags']))
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($this->latestTriageRun->output_json['tags'] as $tag)
                                <span class="font-mono text-[10px] bg-gray-800
                                             text-gray-400 px-2 py-0.5 rounded">
                                    #{{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Escalation --}}
                    @if($this->latestTriageRun->output_json['escalation_flag'])
                        <div class="flex items-center gap-1.5 text-xs text-red-400">
                            <span>⚠</span>
                            <span class="font-mono text-[10px] uppercase tracking-wide">
                                Escalation Recommended
                            </span>
                        </div>
                    @endif

                    {{-- Clarifying question --}}
                    @if(!empty($this->latestTriageRun->output_json['clarifying_question']))
                        <div class="border-l-2 border-blue-700 pl-3 py-1">
                            <p class="text-[10px] text-blue-400 uppercase
                                      tracking-wider mb-1">Suggested Question</p>
                            <p class="text-xs text-gray-300 italic">
                                "{{ $this->latestTriageRun->output_json['clarifying_question'] }}"
                            </p>
                        </div>
                    @endif

                    <button wire:click="runTriage"
                            wire:loading.attr="disabled"
                            wire:target="runTriage"
                            class="text-xs text-purple-400 hover:text-purple-300
                                   transition-colors disabled:opacity-50">
                        <span wire:loading.remove wire:target="runTriage">↺ Re-run Triage</span>
                        <span wire:loading wire:target="runTriage">↺ Re-running...</span>
                    </button>
                </div>

            @elseif($this->latestTriageRun->status === \App\Enums\AiRunStatus::Failed)
                <p class="text-xs text-red-400 mb-2">
                    {{ $this->latestTriageRun->error_message }}
                </p>
                <button wire:click="runTriage"
                        class="text-xs text-indigo-400 hover:text-indigo-300
                               transition-colors">
                    Retry
                </button>
            @endif
        </div>

        {{-- Divider --}}
        <div class="border-t border-gray-800 my-4"></div>

        {{-- ── DRAFT REPLY SECTION ── --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-gray-400 uppercase
                             tracking-wider">
                    Draft Reply
                </span>

                @if($this->latestReplyDraftRun?->status === \App\Enums\AiRunStatus::Queued)
                    <span class="font-mono text-[10px] bg-amber-950 text-amber-300
                                 px-2 py-0.5 rounded animate-pulse">
                        ○ Queued
                    </span>
                @elseif($this->latestReplyDraftRun?->status === \App\Enums\AiRunStatus::Running)
                    <span class="font-mono text-[10px] bg-blue-950 text-blue-300
                                 px-2 py-0.5 rounded">
                        ◌ Drafting
                    </span>
                @elseif($this->latestReplyDraftRun?->status === \App\Enums\AiRunStatus::Succeeded)
                    <span class="font-mono text-[10px] bg-green-950 text-green-300
                                 px-2 py-0.5 rounded">
                        ✓ Ready
                    </span>
                @elseif($this->latestReplyDraftRun?->status === \App\Enums\AiRunStatus::Failed)
                    <span class="font-mono text-[10px] bg-red-950 text-red-300
                                 px-2 py-0.5 rounded">
                        ✕ Failed
                    </span>
                @endif
            </div>

            @if(!$this->latestReplyDraftRun)
                <p class="text-xs text-gray-600 mb-3">No draft generated yet.</p>
                <button wire:click="runReplyDraft"
                        wire:loading.attr="disabled"
                        wire:target="runReplyDraft"
                        class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs
                               px-3 py-1.5 rounded-md transition-colors disabled:opacity-50">
                    <span wire:loading.remove wire:target="runReplyDraft">Generate Draft</span>
                    <span wire:loading wire:target="runReplyDraft">Preparing Draft...</span>
                </button>

            @elseif($this->latestReplyDraftRun->status === \App\Enums\AiRunStatus::Queued)
                <p class="text-xs text-gray-500">Waiting for queue worker...</p>

            @elseif($this->latestReplyDraftRun->status === \App\Enums\AiRunStatus::Running)
                <p class="text-xs text-gray-500">
                    Drafting with KB grounding...
                </p>

            @elseif($this->latestReplyDraftRun->status === \App\Enums\AiRunStatus::Succeeded)
                <div class="space-y-3">
                    {{-- Draft text --}}
                    <div class="bg-gray-900 border border-gray-800 rounded-md p-3">
                        <p class="font-mono text-xs text-gray-300 leading-relaxed
                                  whitespace-pre-wrap">
                            {{ $this->latestReplyDraftRun->output_json['draft'] }}
                        </p>
                        <button type="button"
                                x-on:click="$dispatch('use-draft', {
                                    draft: @js($this->latestReplyDraftRun->output_json['draft'])
                                })"
                                class="mt-3 text-xs text-indigo-400 hover:text-indigo-300
                                       font-mono uppercase tracking-wide transition-colors">
                            → Copy into reply box
                        </button>
                    </div>

                    {{-- Next steps --}}
                    @if(!empty($this->latestReplyDraftRun->output_json['next_steps']))
                        <div>
                            <p class="text-[10px] text-gray-600 uppercase
                                      tracking-wider mb-1.5">Next Steps</p>
                            <ul class="space-y-1">
                                @foreach($this->latestReplyDraftRun->output_json['next_steps'] as $step)
                                    <li class="text-xs text-gray-400 flex gap-2">
                                        <span class="text-gray-600">•</span>
                                        {{ $step }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Risk flags --}}
                    @if(!empty($this->latestReplyDraftRun->output_json['risk_flags']))
                        <div class="bg-orange-950/30 border border-orange-900/50
                                    rounded-md p-3">
                            <p class="text-[10px] text-orange-400 uppercase
                                      tracking-wider mb-1.5">Risk Flags</p>
                            @foreach($this->latestReplyDraftRun->output_json['risk_flags'] as $risk)
                                <p class="text-xs text-orange-300/70 flex gap-2">
                                    <span>⚠</span> {{ $risk }}
                                </p>
                            @endforeach
                        </div>
                    @endif

                    <button wire:click="runReplyDraft"
                            wire:loading.attr="disabled"
                            wire:target="runReplyDraft"
                            class="text-xs text-purple-400 hover:text-purple-300
                                   transition-colors disabled:opacity-50">
                        <span wire:loading.remove wire:target="runReplyDraft">↺ Regenerate Draft</span>
                        <span wire:loading wire:target="runReplyDraft">↺ Regenerating...</span>
                    </button>
                </div>

            @elseif($this->latestReplyDraftRun->status === \App\Enums\AiRunStatus::Failed)
                <p class="text-xs text-red-400 mb-2">
                    {{ $this->latestReplyDraftRun->error_message }}
                </p>
                <button wire:click="runReplyDraft"
                        class="text-xs text-indigo-400 hover:text-indigo-300
                               transition-colors">
                    Retry
                </button>
            @endif
        </div>
    </div>
</div>