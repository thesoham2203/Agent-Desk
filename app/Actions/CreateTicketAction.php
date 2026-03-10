<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\AiRunStatus;
use App\Enums\AiRunType;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Jobs\RunTicketTriageJob;
use App\Models\AiRun;
use App\Models\AuditLog;
use App\Models\Ticket;
use App\Models\User;

/**
 * ============================================================
 * FILE: CreateTicketAction.php
 * LAYER: Action
 * ============================================================
 *
 * WHAT IS THIS?
 * This action handles the creation of a new ticket by a requester.
 * It encapsulates the logic of creating the ticket, logging the
 * creation event, and dispatching a triage job.
 *
 * WHY DOES IT EXIST?
 * We want to keep controllers and Livewire components thin. Business
 * logic like creating logs and dispatching jobs belongs in a dedicated
 * action class. This makes it testable and reusable.
 *
 * HOW IT FITS IN THE APP:
 * Called by TicketCreateForm component. It uses Ticket and AuditLog
 * models and dispatches RunTicketTriageJob.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Actions are simple, single-purpose classes that represent a specific
 * business process. They are not built into Laravel, but are a common
 * pattern to prevent bloated controllers or models. They typically have
 * a single public execute() method.
 * ============================================================
 */
final readonly class CreateTicketData
{
    public function __construct(
        public string $title,
        public string $body,
        public ?int $categoryId,
    ) {
    }
}

final class CreateTicketAction
{
    /**
     * Executes the ticket creation process.
     *
     * @param  User  $requester  The user creating the ticket.
     * @param  CreateTicketData  $data  The ticket data.
     * @return Ticket The newly created ticket.
     */
    public function execute(User $requester, CreateTicketData $data): Ticket
    {
        // 1. Create the Ticket
        $ticket = Ticket::query()->create([
            'requester_id' => $requester->id,
            'status' => TicketStatus::New ->value,
            'priority' => TicketPriority::Medium->value,
            'title' => $data->title,
            'body' => $data->body,
            'category_id' => $data->categoryId,
        ]);

        // 2. Create the initial TicketMessage (so it appears in the thread)
        \App\Models\TicketMessage::query()->create([
            'ticket_id' => $ticket->id,
            'author_id' => $requester->id,
            'type' => \App\Enums\TicketMessageType::Public ->value,
            'body' => $data->body,
        ]);

        // 3. Create an AuditLog entry
        AuditLog::query()->create([
            'action' => 'ticket.created',
            'user_id' => $requester->id,
            'ticket_id' => $ticket->id,
            'old_values' => null,
            'new_values' => [
                'title' => $ticket->title,
                'status' => $ticket->status,
            ],
        ]);

        // 4. Dispatch RunTicketTriageJob
        // Every new ticket automatically gets triage queued.
        $aiRun = AiRun::query()->create([
            'ticket_id' => $ticket->id,
            'initiated_by_user_id' => $requester->id,
            'run_type' => AiRunType::Triage->value,
            'status' => AiRunStatus::Queued->value,
        ]);

        dispatch(new RunTicketTriageJob($aiRun->id));

        return $ticket;
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand how tickets are created via the Action,
 * the next logical file to read is:
 *
 * → app/Actions/PostReplyAction.php
 *
 * WHY: This action handles the other primary write operation for
 * requesters: posting replies to a ticket.
 * ============================================================
 */
