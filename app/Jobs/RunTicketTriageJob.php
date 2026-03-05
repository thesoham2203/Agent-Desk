<?php

declare(strict_types=1);

/**
 * ============================================================
 * FILE: RunTicketTriageJob.php
 * LAYER: Job
 * ============================================================
 *
 * WHAT IS THIS?
 * A queueable job that orchestrates the AI triage process for a ticket.
 * It reloads the AiRun and Ticket models, calls the TriageAgent, and
 * saves the structured results.
 *
 * WHY DOES IT EXIST?
 * To ensure the AI analysis (which can take several seconds) happens
 * asynchronously in the background. This keeps the application
 * responsive for the user who initiated the run.
 *
 * HOW IT FITS IN THE APP:
 * This job is dispatched either automatically by CreateTicketAction
 * or manually by AiPanel::runTriage(). It uses the TriageAgent to
 * perform the actual LLM analysis.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This is a Laravel Queue Job. When dispatched, it's serialized and
 * stored in the `jobs` database table. A background process
 * (php artisan queue:work) picks it up and executes the handle() method.
 * We use the aiRunId instead of the model to avoid serialization issues
 * with large objects.
 * ============================================================
 */

namespace App\Jobs;

use App\AI\Agents\TriageAgent;
use App\AI\DTOs\TriageInput;
use App\Enums\AiRunStatus;
use App\Enums\AiRunType;
use App\Models\AiRun;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

final class RunTicketTriageJob implements ShouldQueue
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
     * TriageAgent is automatically injected by Laravel's service container.
     */
    public function handle(TriageAgent $agent): void
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

            // 3. Load the ticket:
            $ticket = $aiRun->ticket()->with('category')->firstOrFail();

            // 4. Build TriageInput DTO:
            $input = new TriageInput(
                title: (string) $ticket->title,
                body: (string) $ticket->body,
                category: $ticket->category ? $ticket->category->name : '',
            );

            // 5. Check for duplicate run using input_hash:
            // This prevents redundant API calls if the ticket content hasn't changed.
            $inputHash = hash('sha256', $input->title.$input->body.$input->category);
            $existing = AiRun::query()->where('ticket_id', $ticket->id)
                ->where('run_type', AiRunType::Triage->value)
                ->where('status', AiRunStatus::Succeeded->value)
                ->where('input_hash', $inputHash)
                ->first();

            if ($existing !== null) {
                $aiRun->update([
                    'status' => AiRunStatus::Succeeded->value,
                    'output_json' => $existing->output_json,
                    'input_hash' => $inputHash,
                ]);

                return;
            }

            // 6. Call agent:
            // The agent performs the actual Groq API call.
            $result = $agent->handle($input);

            // 7. Save result:
            // We map the DTO back to an array for JSON storage in the DB.
            $aiRun->update([
                'status' => AiRunStatus::Succeeded->value,
                'input_hash' => $inputHash,
                'output_json' => [
                    'category' => $result->category,
                    'priority' => $result->priority,
                    'summary' => $result->summary,
                    'tags' => $result->tags,
                    'escalation_flag' => $result->escalationFlag,
                    'clarifying_question' => $result->clarifyingQuestion,
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
 * → [app/Jobs/DraftTicketReplyJob.php]
 * WHY: This job handles triage; the next job handles the more
 * complex task of drafting a grounded reply for the agent.
 * ============================================================
 */
