<?php

declare(strict_types=1);

/**
 * ============================================================
 * FILE: AiPanel.php
 * LAYER: Livewire Component
 * ============================================================
 *
 * WHAT IS THIS?
 * The interactive control center for AI actions on a ticket. It allows
 * support agents to trigger triage and response drafting manually and
 * see the results in real-time.
 *
 * WHY DOES IT EXIST?
 * While some AI tasks happen automatically (triage on create), agents
 * often need to re-run AI or generate custom drafts. This component
 * provides a user-friendly interface for those background processes.
 *
 * HOW IT FITS IN THE APP:
 * This component is embedded in the TicketDetail view. It communicates
 * with the DB to track `AiRun` statuses and dispatches background Jobs.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This is a Livewire component. It maintains its own state (using public
 * properties like `$latestTriageRun`). We use `wire:poll` in the view to
 * call the `refresh()` method every few seconds, allowing the UI to
 * update automatically when the background job finishes.
 * ============================================================
 */

namespace App\Livewire\Agent;

use App\Enums\AiRunStatus;
use App\Enums\AiRunType;
use App\Jobs\DraftTicketReplyJob;
use App\Jobs\RunTicketTriageJob;
use App\Models\AiRun;
use App\Models\Ticket;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * @property-read AiRun|null $latestTriageRun
 * @property-read AiRun|null $latestReplyDraftRun
 * @property-read bool $polling
 * @property-read bool $isAnyRunInProgress
 */
final class AiPanel extends Component
{
    /**
     * The ID of the ticket this panel is analyzing.
     */
    public int $ticketId;

    /**
     * Whether the UI should poll for updates.
     * Calculated based on current run statuses.
     */
    #[Computed]
    public function polling(): bool
    {
        return $this->isAnyRunInProgress();
    }

    public function mount(int $ticketId): void
    {
        $this->ticketId = $ticketId;
    }

    /**
     * The most recent triage run for this ticket.
     */
    #[Computed]
    public function latestTriageRun(): ?AiRun
    {
        return AiRun::query()
            ->where('ticket_id', $this->ticketId)
            ->where('run_type', AiRunType::Triage->value)
            ->latest('id')
            ->first();
    }

    /**
     * The most recent reply draft run for this ticket.
     */
    #[Computed]
    public function latestReplyDraftRun(): ?AiRun
    {
        return AiRun::query()
            ->where('ticket_id', $this->ticketId)
            ->where('run_type', AiRunType::ReplyDraft->value)
            ->latest('id')
            ->first();
    }

    #[Computed]
    public function isAnyRunInProgress(): bool
    {
        $inProgressStatuses = [
            AiRunStatus::Queued,
            AiRunStatus::Running,
        ];

        $triageInProgress = $this->latestTriageRun instanceof AiRun && in_array($this->latestTriageRun->status, $inProgressStatuses, true);
        $replyInProgress = $this->latestReplyDraftRun instanceof AiRun && in_array($this->latestReplyDraftRun->status, $inProgressStatuses, true);

        return $triageInProgress || $replyInProgress;
    }

    /**
     * Manual trigger to run the ticket triage process.
     */
    public function runTriage(): void
    {
        $ticket = Ticket::query()->findOrFail($this->ticketId);

        // 1. Authorize the action:
        Gate::authorize('runAi', $ticket);

        // 2. Apply rate limiting to prevent API abuse:
        $limitKey = 'ai-triage:'.auth()->id();
        if (! RateLimiter::attempt($limitKey, 5, fn (): true => true)) {
            session()->flash('error', 'Too many AI requests. Please wait.');

            return;
        }

        // 3. Create the AiRun record (status: Queued):
        $aiRun = AiRun::query()->create([
            'ticket_id' => $this->ticketId,
            'initiated_by_user_id' => auth()->id(),
            'run_type' => AiRunType::Triage->value,
            'status' => AiRunStatus::Queued->value,
        ]);

        // 4. Dispatch the background Job:
        dispatch(new RunTicketTriageJob($aiRun->id));

        // 5. Reload state: (No-op now, computed properties handled it)
    }

    /**
     * Manual trigger to generate a reply draft.
     */
    public function runReplyDraft(): void
    {
        $ticket = Ticket::query()->findOrFail($this->ticketId);

        // 1. Authorize:
        Gate::authorize('runAi', $ticket);

        // 2. Rate limit:
        $limitKey = 'ai-reply:'.auth()->id();
        if (! RateLimiter::attempt($limitKey, 5, fn (): true => true)) {
            session()->flash('error', 'Too many AI requests. Please wait.');

            return;
        }

        // 3. Create AiRun record:
        $aiRun = AiRun::query()->create([
            'ticket_id' => $this->ticketId,
            'initiated_by_user_id' => auth()->id(),
            'run_type' => AiRunType::ReplyDraft->value,
            'status' => AiRunStatus::Queued->value,
        ]);

        // 4. Dispatch Job:
        dispatch(new DraftTicketReplyJob($aiRun->id));

        // 5. Reload state:
    }

    /**
     * Re-renders the component.
     */
    public function refresh(): void
    {
        // Computed properties handle the refresh automatically.
    }

    /**
     * Renders the component view.
     */
    public function render(): View
    {
        return view('livewire.agent.ai-panel');
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [resources/views/livewire/agent/ai-panel.blade.php]
 * WHY: This is the visual part of the component. It uses the
 * properties we defined here to show badges, summaries, and drafts.
 * ============================================================
 */
