<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * ============================================================
 * FILE: TicketStatus.php
 * LAYER: Enum
 * ============================================================
 *
 * WHAT IS THIS?
 * This file defines a string-backed enumeration for all possible states
 * a support ticket can exist in during its lifecycle.
 *
 * WHY DOES IT EXIST?
 * To ensure that ticket statuses are constrained to a predefined set of values,
 * preventing data corruption and enabling type-safe comparisons in business logic.
 *
 * HOW IT FITS IN THE APP:
 * The Ticket model uses this enum to cast the 'status' database column.
 * Controllers and Livewire components use it to filter and update tickets.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * An Enum in PHP is a special type that contains a fixed set of named values.
 * Laravel uses Enums to cast database string values into type-safe PHP objects automatically,
 * allowing you to use $ticket->status === TicketStatus::New instead of fragile strings.
 * ============================================================
 */
enum TicketStatus: string
{
    /**
     * Initial state when a requester first submits a ticket.
     */
    case New = 'new';

    /**
     * Set when the AI or a Support Agent has performed initial classification.
     */
    case Triaged = 'triaged';

    /**
     * Set when a Support Agent is actively working on the resolution.
     */
    case InProgress = 'in_progress';

    /**
     * Set when the agent is waiting for a response from the requester.
     */
    case Waiting = 'waiting';

    /**
     * Final state when the issue has been resolved to the user's satisfaction.
     */
    case Resolved = 'resolved';

    /**
     * Returns a human-readable label for the UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Triaged => 'Triaged',
            self::InProgress => 'In Progress',
            self::Waiting => 'Waiting for Customer',
            self::Resolved => 'Resolved',
        };
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [TicketStatus.php], the next logical file
 * to read is:
 *
 * → [app/Enums/TicketPriority.php]
 *
 * WHY: After defining the lifecycle state, we need to define the
 *       urgency of the tickets.
 * ============================================================
 */
