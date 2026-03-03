<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

/**
 * ============================================================
 * FILE: AuditLogPolicy.php
 * LAYER: Policy
 * ============================================================
 *
 * WHAT IS THIS?
 * A policy limiting access to system audit trails.
 *
 * WHY DOES IT EXIST?
 * Audit logs record very sensitive details of system activity.
 * Only administrators can review these historical traces.
 *
 * HOW IT FITS IN THE APP:
 * Validated upon accessing the audit interface.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * A Policy in Laravel is a dedicated class that answers one question: can this
 * user perform this action on this model? Laravel automatically discovers policies
 * by naming convention and routes authorization checks through the Gate facade.
 * ============================================================
 */
final class AuditLogPolicy
{
    /**
     * HLD §8.1: Admins bypass all checks.
     */
    public function before(User $user): ?bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return null;
    }

    /**
     * HLD §8.1: Only Admin can view the audit screen. Admin bypasses via before.
     */
    public function viewAny(): bool
    {
        return false;
    }

    /**
     * HLD §8.1: Return false (Admin handles this via before).
     */
    public function view(): bool
    {
        return false;
    }

    public function create(): bool
    {
        return false;
    }

    public function update(): bool
    {
        return false;
    }

    public function delete(): bool
    {
        return false;
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand AuditLogPolicy.php, the next logical file
 * to read is:
 *
 * → app/Providers/AppServiceProvider.php
 *
 * WHY: Policies MUST be registered with the framework so Laravel
 * knows they exist. The AppServiceProvider wires them up.
 * ============================================================
 */
