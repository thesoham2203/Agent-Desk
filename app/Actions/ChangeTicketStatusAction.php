<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\TicketStatus;
use App\Models\AuditLog;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketResolvedNotification;
use DomainException;

/**
 * ============================================================
 * FILE: ChangeTicketStatusAction.php
 * LAYER: Action
 * ============================================================
 *
 * WHAT IS THIS?
 * This action handles changing a ticket's status and logging the change.
 *
 * WHY DOES IT EXIST?
 * To enforce valid state transitions and ensure all status changes are audit logged.
 *
 * HOW IT FITS IN THE APP:
 * Called by the Agent TicketDetail component to update ticket status.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Private helper methods in Actions allow us to encapsulate complex validation logic
 * (like state machine transitions) without cluttering the main execute method.
 * ============================================================
 */
final class ChangeTicketStatusAction
{
    /**
     * Executes the status change action.
     */
    public function execute(User $changedBy, Ticket $ticket, TicketStatus $newStatus): Ticket
    {
        $oldStatus = $ticket->status;

        if (! $this->isValidTransition($oldStatus, $newStatus)) {
            throw new DomainException("Cannot transition ticket from {$oldStatus->label()} to {$newStatus->label()}.");
        }

        $updates = ['status' => $newStatus];

        if ($newStatus === TicketStatus::Resolved) {
            $updates['resolved_at'] = now();
        }

        $ticket->update($updates);

        // Notify requester when their ticket is resolved
        if ($newStatus === TicketStatus::Resolved) {
            $ticket->requester->notify(
                new TicketResolvedNotification($ticket)
            );
        }

        AuditLog::query()->create([
            'action' => 'status.changed',
            'user_id' => $changedBy->id,
            'ticket_id' => $ticket->id,
            'old_values' => ['status' => $oldStatus->value],
            'new_values' => ['status' => $newStatus->value],
        ]);

        return $ticket;
    }

    /**
     * Validates if the transition from one status to another is allowed.
     */
    private function isValidTransition(TicketStatus $from, TicketStatus $to): bool
    {
        return match ($from) {
            TicketStatus::New => in_array($to, [TicketStatus::Triaged, TicketStatus::InProgress], strict: true),
            TicketStatus::Triaged => in_array($to, [TicketStatus::InProgress, TicketStatus::Waiting], strict: true),
            TicketStatus::InProgress => in_array($to, [TicketStatus::Waiting, TicketStatus::Resolved], strict: true),
            TicketStatus::Waiting => in_array($to, [TicketStatus::InProgress, TicketStatus::Resolved], strict: true),
            TicketStatus::Resolved => false,
        };
    }
}
/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [app/Actions/AddInternalNoteAction.php]
 * WHY: Changing status might require leaving an internal note to explain why.
 * ============================================================
 */
