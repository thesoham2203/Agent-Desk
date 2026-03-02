<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * ============================================================
 * FILE: TicketMessageType.php
 * LAYER: Enum
 * ============================================================
 *
 * WHAT IS THIS?
 * Classifies a ticket message as either public (visible to the requester)
 * or internal (visible only to agents).
 *
 * WHY DOES IT EXIST?
 * To prevent accidental disclosure of private agent-to-agent notes
 * or AI-driven internal deliberations to the customer.
 *
 * HOW IT FITS IN THE APP:
 * TicketMessage model's 'type' column is cast to this Enum.
 * Policies and views use it to restrict visibility.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Enums in Laravel provide a clean way to implement role-based or type-based
 * logic without using multiple boolean flags or complex database queries.
 * ============================================================
 */
enum TicketMessageType: string
{
    /**
     * Visible to the requester and all support staff.
     */
    case Public = 'public';

    /**
     * Visible only to agents and administrators.
     */
    case Internal = 'internal';

    /**
     * Returns a human-readable label for the UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::Public => 'Public Reply',
            self::Internal => 'Internal Note',
        };
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [TicketMessageType.php], the next logical file
 * to read is:
 *
 * → [app/Enums/AiRunType.php]
 *
 * WHY: We need to categorize the various ways AI interacts with the system.
 * ============================================================
 */
