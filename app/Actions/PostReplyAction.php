<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\TicketMessageType;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Notifications\RequesterRepliedNotification;
use DomainException;

/**
 * ============================================================
 * FILE: PostReplyAction.php
 * LAYER: Action
 * ============================================================
 *
 * WHAT IS THIS?
 * This action handles posting a public reply to an existing ticket.
 * It ensures business rules are followed, such as preventing replies
 * on resolved tickets.
 *
 * WHY DOES IT EXIST?
 * Posting a reply involves multiple steps: checking the ticket status,
 * creating the message, and logging the action. An Action class keeps
 * this logic centralized and reusable.
 *
 * HOW IT FITS IN THE APP:
 * Called by the TicketDetail Livewire component. Depends on Ticket,
 * TicketMessage, and AuditLog models.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Business logic belongs in Actions rather than the component or
 * model. This makes the logic easier to test in isolation without
 * dealing with the UI or HTTP request lifecycle.
 * ============================================================
 */
final class PostReplyAction
{
    /**
     * Executes the reply posting process.
     *
     * @param  User  $author  The user posting the reply.
     * @param  Ticket  $ticket  The ticket being replied to.
     * @param  string  $body  The reply content.
     * @return TicketMessage The newly created message.
     *
     * @throws DomainException If the ticket is resolved.
     */
    public function execute(User $author, Ticket $ticket, string $body): TicketMessage
    {
        // 1. Verify ticket is not resolved
        throw_if($ticket->status === TicketStatus::Resolved, DomainException::class, 'Cannot reply to resolved ticket.');

        // 2. Create TicketMessage
        $message = TicketMessage::query()->create([
            'ticket_id' => $ticket->id,
            'author_id' => $author->id,
            'type' => TicketMessageType::Public->value,
            'body' => $body,
        ]);

        // 3. Handle notifications and SLA logic
        // If a requester replied, notify the assigned agent
        if ($author->role === UserRole::Requester && $ticket->assignee !== null) {
            $ticket->assignee->notify(
                new RequesterRepliedNotification($ticket, $author)
            );
        }

        // If an AGENT replied, update first_responded_at:
        if ($author->role === UserRole::Agent && $ticket->first_responded_at === null) {
            /**
             * Record the first agent response time.
             * This is used by CheckOverdueTargetsJob to determine
             * if the first response SLA has been met.
             */
            $ticket->update(['first_responded_at' => now()]);
        }

        // 4. Create AuditLog entry
        AuditLog::query()->create([
            'action' => 'reply.posted',
            'user_id' => $author->id,
            'ticket_id' => $ticket->id,
            'old_values' => null,
            'new_values' => [
                'type' => 'public',
                'author_role' => $author->role->value,
            ],
        ]);

        return $message;
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand the actions, the next logical
 * file to read is:
 *
 * → app/Livewire/Requester/TicketCreateForm.php
 *
 * WHY: This Livewire component uses the CreateTicketAction to
 * allow requesters to create tickets via the UI.
 * ============================================================
 */
