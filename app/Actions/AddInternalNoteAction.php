<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\TicketMessageType;
use App\Models\AuditLog;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;

/**
 * ============================================================
 * FILE: AddInternalNoteAction.php
 * LAYER: Action
 * ============================================================
 *
 * WHAT IS THIS?
 * This action adds a private internal note to a ticket thread.
 *
 * WHY DOES IT EXIST?
 * To allow agents to discuss a ticket without the requester seeing it, while still recording it.
 *
 * HOW IT FITS IN THE APP:
 * Called by the Agent TicketDetail component when submitting the internal note form.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * We use an Enum for the `type` field to distinguish between public replies and internal notes.
 * This makes it easy to filter out internal notes in our queries for requesters.
 * ============================================================
 */
final class AddInternalNoteAction
{
    /**
     * Executes the internal note addition.
     */
    public function execute(User $author, Ticket $ticket, string $body): TicketMessage
    {
        // Internal notes are NEVER visible to requesters.
        // The type field is what controls visibility — the policy
        // enforces this at the authorization level, and the
        // Requester TicketDetail component enforces it at the query level.
        $message = TicketMessage::query()->create([
            'ticket_id' => $ticket->id,
            'author_id' => $author->id,
            'type' => TicketMessageType::Internal->value,
            'body' => $body,
        ]);

        AuditLog::query()->create([
            'action' => 'internal.note.added',
            'user_id' => $author->id,
            'ticket_id' => $ticket->id,
            'new_values' => ['note_length' => mb_strlen($body)],
        ]);

        return $message;
    }
}
/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [app/Livewire/Agent/TriageQueue.php]
 * WHY: Now that we have actions to assign tickets, we can build the queue UI.
 * ============================================================
 */
