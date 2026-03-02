<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * ============================================================
 * FILE: TicketPriority.php
 * LAYER: Enum
 * ============================================================
 *
 * WHAT IS THIS?
 * Defines the priority levels that can be assigned to a support ticket,
 * determining its urgency.
 *
 * WHY DOES IT EXIST?
 * To prevent the use of magic strings and ensure that priority levels
 * are consistent throughout the application database and logic.
 *
 * HOW IT FITS IN THE APP:
 * The Ticket model casts its 'priority' column to this Enum,
 * and the Triage AI agent uses it to suggest urgency based on the content.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Laravel's Enum casting automatically converts database strings back into
 * these objects on model retrieval, and ensures only these values
 * can be saved back to the database.
 * ============================================================
 */
enum TicketPriority: string
{
    /**
     * Non-urgent issues or general questions.
     */
    case Low = 'low';

    /**
     * Standard support requests; default for most tickets.
     */
    case Medium = 'medium';

    /**
     * Important issues that require attention within the business day.
     */
    case High = 'high';

    /**
     * Critical failures that stop business operations.
     */
    case Urgent = 'urgent';

    /**
     * Returns a human-readable label for the UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::Low => 'Low',
            self::Medium => 'Medium',
            self::High => 'High',
            self::Urgent => 'Urgent',
        };
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [TicketPriority.php], the next logical file
 * to read is:
 *
 * → [app/Enums/TicketMessageType.php]
 *
 * WHY: After defining ticket metrics, we need to classify how
 *       messages within a ticket are handled.
 * ============================================================
 */
