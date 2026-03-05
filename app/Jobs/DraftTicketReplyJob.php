<?php

declare(strict_types=1);

/**
 * ============================================================
 * FILE: DraftTicketReplyJob.php
 * LAYER: Job
 * ============================================================
 *
 * WHAT IS THIS?
 * A queueable job that draft a response for a ticket.
 * It reloads the AiRun and Ticket models, calls the ReplyDraftAgent,
 * and saves the generated draft.
 *
 * WHY DOES IT EXIST?
 * To ensure AI drafting (which can take several seconds) happens
 * asynchronously in the background. This keeps the application
 * responsive for the user who initiated the run.
 *
 * HOW IT FITS IN THE APP:
 * This job is dispatched by AiPanel::runReplyDraft(). It uses the
 * ReplyDraftAgent to perform the actual analysis and drafting.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This is a Laravel Queue Job. When dispatched, it's serialized and
 * stored in the `jobs` database table. A background process
 * (php artisan queue:work) picks it up and executes the handle() method.
 * As with all our AI jobs, we pass individual IDs to avoid serialization issues
 * with large objects.
 * ============================================================
 */

namespace App\Jobs;

use App\AI\Agents\ReplyDraftAgent;
use App\AI\DTOs\ReplyDraftInput;
use App\Enums\AiRunStatus;
use App\Enums\AiRunType;
use App\Models\AiRun;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

final class DraftTicketReplyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     * We pass the ID only to ensure a fresh model is loaded in the worker.
     */
    public function __construct(
        public readonly int $aiRunId
    ) {}

    /**
     * Execute the job.
     * ReplyDraftAgent is automatically injected by Laravel's service container.
     */
    public function handle(ReplyDraftAgent $agent): void
    {
        // 1. Load the AiRun:
        $aiRun = AiRun::query()->findOrFail($this->aiRunId);

        try {
            // 2. Update status to Running:
            // We do this immediately so any UI polling sees progress.
            $aiRun->update([
                'status' => AiRunStatus::Running->value,
                'provider' => 'groq',
                'model' => config('groq.model', 'llama3-8b-8192'),
            ]);

            // 3. Load the ticket environment:
            $ticket = $aiRun->ticket;

            // 4. Try to load threadSummary from triage run if it exists:
            // This allows the ReplyDraftAgent to have a summary context.
            $latestTriage = AiRun::query()->where('ticket_id', $ticket->id)
                ->where('run_type', AiRunType::Triage->value)
                ->where('status', AiRunStatus::Succeeded->value)
                ->latest()
                ->first();

            $threadSummary = '';
            if ($latestTriage !== null && is_array($latestTriage->output_json)) {
                $summaryValue = $latestTriage->output_json['summary'] ?? '';
                $threadSummary = is_string($summaryValue) ? $summaryValue : '';
            }

            // 5. Build ReplyDraftInput DTO:
            $input = new ReplyDraftInput(
                ticketId: (int) $ticket->id,
                threadSummary: $threadSummary,
                initiatedByUserId: (int) $aiRun->initiated_by_user_id,
            );

            // 6. Call agent:
            // The agent performs the actual Groq API call and KB search.
            $result = $agent->handle($input);

            // 7. Save result:
            // We map the DTO back to an array for JSON storage in the DB.
            $aiRun->update([
                'status' => AiRunStatus::Succeeded->value,
                'output_json' => [
                    'draft' => $result->draft,
                    'next_steps' => $result->nextSteps,
                    'risk_flags' => $result->riskFlags,
                ],
            ]);

        } catch (Throwable $e) {
            // 8. Error handling:
            // Always ensure the run record marks the failure for the UI.
            $aiRun->update([
                'status' => AiRunStatus::Failed->value,
                'error_message' => $e->getMessage(),
            ]);
            throw $e; // Rethrow to let Laravel's queue system handle retries
        }
    }

    /**
     * Handle a job failure after all retries are exhausted.
     */
    public function failed(Throwable $e): void
    {
        AiRun::query()->where('id', $this->aiRunId)->update([
            'status' => AiRunStatus::Failed->value,
            'error_message' => 'Job failed after max retries: '.$e->getMessage(),
        ]);
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [app/Actions/CreateTicketAction.php]
 * WHY: Now that we have the background jobs, we need to trigger
 * them. The first trigger is when a ticket is created.
 * ============================================================
 */
