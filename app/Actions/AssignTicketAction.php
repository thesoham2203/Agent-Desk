<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\TicketStatus;
use App\Models\AuditLog;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketAssignedNotification;

/**
 * ============================================================
 * FILE: AssignTicketAction.php
 * LAYER: Action
 * ============================================================
 *
 * WHAT IS THIS?
 * This action handles assigning a ticket to an agent, or unassigning it.
 *
 * WHY DOES IT EXIST?
 * To encapsulate the business logic of ticket assignment, including status transitions and audit logging.
 *
 * HOW IT FITS IN THE APP:
 * Called by Livewire components (TriageQueue, TicketDetail) to update the ticket's assigned_to field.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Actions are simple, single-responsibility PHP classes that contain business logic.
 * They keep controllers and Livewire components thin and make testing easier.
 * ============================================================
 */
final class AssignTicketAction
{
    /**
     * Executes the ticket assignment action.
     */
    public function execute(User $assignedBy, Ticket $ticket, ?User $assignee): Ticket
    {
        $oldAssignedTo = $ticket->assigned_to;

        $updates = ['assigned_to' => $assignee?->id];

        // Assigning a New ticket automatically moves it to Triaged — it's no longer in the open queue.
        if ($ticket->status === TicketStatus::New && $assignee instanceof User) {
            $updates['status'] = TicketStatus::Triaged;
        }

        $ticket->update($updates);

        // Notify the assigned agent if there is one
        if ($assignee instanceof User) {
            $assignee->notify(
                new TicketAssignedNotification($ticket, $assignedBy)
            );
        }

        AuditLog::query()->create([
            'action' => $assignee instanceof User ? 'ticket.assigned' : 'ticket.unassigned',
            'user_id' => $assignedBy->id,
            'ticket_id' => $ticket->id,
            'old_values' => ['assigned_to' => $oldAssignedTo],
            'new_values' => ['assigned_to' => $assignee?->id],
        ]);

        return $ticket;
    }
}
/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [app/Actions/ChangeTicketStatusAction.php]
 * WHY: After assigning a ticket, agents often need to change its status.
 * ============================================================
 */
