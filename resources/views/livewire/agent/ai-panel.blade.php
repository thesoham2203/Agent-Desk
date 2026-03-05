<div class="space-y-6" {{ $polling ? 'wire:poll.2000ms="refresh"' : '' }}>
    <!-- AI Assistant Header -->
    <div class="flex items-center gap-2 pb-2 border-b border-gray-200">
        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        <h3 class="text-lg font-bold text-gray-900">AI Assistant</h3>
    </div>

    @if(session('error'))
        <div class="p-3 text-sm text-red-600 rounded-md bg-red-50">
            {{ session('error') }}
        </div>
    @endif

    <!-- Triage Section -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Ticket Triage</h4>

            @if($latestTriageRun?->status === \App\Enums\AiRunStatus::Queued)
                <span
                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 animate-pulse">
                    ⏳ Queued
                </span>
            @elseif($latestTriageRun?->status === \App\Enums\AiRunStatus::Running)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                    <svg class="animate-spin -ml-1 mr-2 h-3 w-3 text-blue-800" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    🔄 Running
                </span>
            @elseif($latestTriageRun?->status === \App\Enums\AiRunStatus::Succeeded)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                    ✅ Complete
                </span>
            @elseif($latestTriageRun?->status === \App\Enums\AiRunStatus::Failed)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                    ❌ Failed
                </span>
            @endif
        </div>

        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            @if(!$latestTriageRun)
                <p class="text-sm text-gray-500 italic">No triage run yet.</p>
                <div class="mt-3">
                    <button wire:click="runTriage"
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                        Run Triage
                    </button>
                </div>
            @elseif($latestTriageRun->status === \App\Enums\AiRunStatus::Queued)
                <p class="text-sm text-gray-600">Waiting for queue worker...</p>
            @elseif($latestTriageRun->status === \App\Enums\AiRunStatus::Running)
                <p class="text-sm text-gray-600">Analyzing ticket content via Groq API...</p>
            @elseif($latestTriageRun->status === \App\Enums\AiRunStatus::Succeeded)
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="font-bold block text-gray-500 uppercase">Category</span>
                            <span class="text-gray-900">{{ $latestTriageRun->output_json['category'] }}</span>
                        </div>
                        <div>
                            <span class="font-bold block text-gray-500 uppercase">Priority</span>
                            <span class="text-gray-900 capitalize">{{ $latestTriageRun->output_json['priority'] }}</span>
                        </div>
                    </div>
                    <div>
                        <span class="font-bold block text-xs text-gray-500 uppercase">AI Summary</span>
                        <p class="text-sm text-gray-900">{{ $latestTriageRun->output_json['summary'] }}</p>
                    </div>
                    <div>
                        <span class="font-bold block text-xs text-gray-500 uppercase">Tags</span>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @foreach($latestTriageRun->output_json['tags'] as $tag)
                                <span
                                    class="px-2 py-0.5 rounded-full text-[10px] bg-gray-200 text-gray-700 font-medium">#{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                    @if($latestTriageRun->output_json['escalation_flag'])
                        <div class="flex items-center gap-1 text-red-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" />
                            </svg>
                            <span class="text-xs font-bold uppercase">Escalation Recommended</span>
                        </div>
                    @endif
                    @if($latestTriageRun->output_json['clarifying_question'])
                        <div class="p-2 bg-blue-50 border-l-4 border-blue-400 rounded-r">
                            <span class="block text-[10px] font-bold text-blue-700 uppercase">Clarifying Question</span>
                            <p class="text-sm text-blue-900 italic">"{{ $latestTriageRun->output_json['clarifying_question'] }}"
                            </p>
                        </div>
                    @endif

                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <button wire:click="runTriage" class="text-xs text-indigo-600 hover:text-indigo-900 font-medium">
                            🔄 Re-run Triage
                        </button>
                    </div>
                </div>
            @elseif($latestTriageRun->status === \App\Enums\AiRunStatus::Failed)
                <div class="space-y-2">
                    <p class="text-xs text-red-600">Error: {{ $latestTriageRun->error_message }}</p>
                    <button wire:click="runTriage"
                        class="inline-flex items-center px-3 py-1.5 border border-red-300 text-xs font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none">
                        Retry
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Reply Section -->
    <div class="space-y-4 pt-4 border-t border-gray-100">
        <div class="flex items-center justify-between">
            <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Draft Reply</h4>

            @if($latestReplyDraftRun?->status === \App\Enums\AiRunStatus::Queued)
                <span
                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 animate-pulse">
                    ⏳ Queued
                </span>
            @elseif($latestReplyDraftRun?->status === \App\Enums\AiRunStatus::Running)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                    <svg class="animate-spin -ml-1 mr-2 h-3 w-3 text-blue-800" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    🔄 Drafting
                </span>
            @elseif($latestReplyDraftRun?->status === \App\Enums\AiRunStatus::Succeeded)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                    ✅ Ready
                </span>
            @elseif($latestReplyDraftRun?->status === \App\Enums\AiRunStatus::Failed)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                    ❌ Failed
                </span>
            @endif
        </div>

        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            @if(!$latestReplyDraftRun)
                <p class="text-sm text-gray-500 italic">No draft generated yet.</p>
                <div class="mt-3">
                    <button wire:click="runReplyDraft"
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                        Generate Draft
                    </button>
                </div>
            @elseif($latestReplyDraftRun->status === \App\Enums\AiRunStatus::Queued)
                <p class="text-sm text-gray-600">Waiting for queue worker...</p>
            @elseif($latestReplyDraftRun->status === \App\Enums\AiRunStatus::Running)
                <p class="text-sm text-gray-600">Drafting personalized response using Knowledge Base grounding...</p>
            @elseif($latestReplyDraftRun->status === \App\Enums\AiRunStatus::Succeeded)
                <div class="space-y-4">
                    <div class="p-3 bg-white rounded border border-gray-200 shadow-sm relative group">
                        <p class="text-sm text-gray-900 whitespace-pre-wrap leading-relaxed">
                            {{ $latestReplyDraftRun->output_json['draft'] }}
                        </p>
                        <div class="mt-3 block">
                            <button type="button"
                                x-on:click="$dispatch('use-draft', { draft: @js($latestReplyDraftRun->output_json['draft']) })"
                                class="text-xs text-indigo-600 font-bold hover:underline">
                                COPY INTO REPLY BOX
                            </button>
                        </div>
                    </div>

                    @if(!empty($latestReplyDraftRun->output_json['next_steps']))
                        <div>
                            <span class="font-bold block text-xs text-gray-500 uppercase">Suggested Next Steps</span>
                            <ul class="list-disc list-inside text-xs text-gray-700 mt-1 space-y-1">
                                @foreach($latestReplyDraftRun->output_json['next_steps'] as $step)
                                    <li>{{ $step }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(!empty($latestReplyDraftRun->output_json['risk_flags']))
                        <div class="p-2 bg-orange-50 rounded border border-orange-200">
                            <span class="block text-[10px] font-bold text-orange-700 uppercase">Risk Awareness</span>
                            @foreach($latestReplyDraftRun->output_json['risk_flags'] as $risk)
                                <div class="flex items-center gap-1 text-[11px] text-orange-800 mt-0.5">
                                    <span class="w-1 h-1 bg-orange-400 rounded-full"></span>
                                    {{ $risk }}
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="pt-2">
                        <button wire:click="runReplyDraft"
                            class="text-xs text-indigo-600 hover:text-indigo-900 font-medium">
                            🔄 Regenerate Draft
                        </button>
                    </div>
                </div>
            @elseif($latestReplyDraftRun->status === \App\Enums\AiRunStatus::Failed)
                <div class="space-y-2">
                    <p class="text-xs text-red-600">Error: {{ $latestReplyDraftRun->error_message }}</p>
                    <button wire:click="runReplyDraft"
                        class="inline-flex items-center px-3 py-1.5 border border-red-300 text-xs font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none">
                        Retry
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>