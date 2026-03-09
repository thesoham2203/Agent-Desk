<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * ============================================================
 * FILE: UserRole.php
 * LAYER: Enum
 * ============================================================
 *
 * WHAT IS THIS?
 * Defines the roles users can have in the AgentDesk system.
 *
 * WHY DOES IT EXIST?
 * To control access to different parts of the application (RBAC),
 * such as ticketing vs. administrative configuration.
 *
 * HOW IT FITS IN THE APP:
 * The User model's 'role' column is cast to this Enum,
 * and Laravel policies use it to authorize actions.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Laravel can automatically cast database string colors to Enums,
 * preventing 'magic string' bugs in your authorization logic.
 * ============================================================
 */
enum UserRole: string
{
    /**
     * Staff with full administrative access to all system settings and audit logs.
     */
    case Admin = 'admin';

    /**
     * Support staff who can triage, assign, and respond to tickets.
     */
    case Agent = 'agent';

    /**
     * External users who create and track their own tickets.
     */
    case Requester = 'requester';

    /**
     * Returns a human-readable label for the UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Agent => 'Support Agent',
            self::Requester => 'Customer',
        };
    }

    /**
     * Returns the name of the dashboard route for this role.
     */
    public function dashboardRoute(): string
    {
        return match ($this) {
            self::Admin => 'admin.audit-log',
            self::Agent => 'agent.queue',
            self::Requester => 'requester.tickets.index',
        };
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [UserRole.php], the next logical file
 * to read is:
 *
 * → [database/migrations/2026_03_02_000001_add_role_to_users_table.php]
 *
 * WHY: After defining roles in code, we need to add the role column
 *       to our users table in the database.
 * ============================================================
 */
