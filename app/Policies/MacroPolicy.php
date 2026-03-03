<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

/**
 * ============================================================
 * FILE: MacroPolicy.php
 * LAYER: Policy
 * ============================================================
 *
 * WHAT IS THIS?
 * A policy determining who can use and manage text macros.
 *
 * WHY DOES IT EXIST?
 * Admins create/edit, while Agents view/use.
 *
 * HOW IT FITS IN THE APP:
 * Checked when displaying available macros to agents.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * A Policy in Laravel is a dedicated class that answers one question: can this
 * user perform this action on this model? Laravel automatically discovers policies
 * by naming convention and routes authorization checks through the Gate facade.
 * ============================================================
 */
final class MacroPolicy
{
    /**
     * Admin bypasses all checks.
     */
    public function before(User $user): ?bool
    {
        if ($user->role === UserRole::Admin) {
            return true;
        }

        return null;
    }

    /**
     * Agents can read macros.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Agent || $user->role === UserRole::Admin;
    }

    /**
     * Agents can read macros.
     */
    public function view(User $user): bool
    {
        return $user->role === UserRole::Agent || $user->role === UserRole::Admin;
    }

    /**
     * Admin only.
     */
    public function create(): bool
    {
        return false;
    }

    /**
     * Admin only.
     */
    public function update(): bool
    {
        return false;
    }

    /**
     * Admin only.
     */
    public function delete(): bool
    {
        return false;
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand MacroPolicy.php, the next logical file
 * to read is:
 *
 * → app/Policies/SlaConfigPolicy.php
 *
 * WHY: SLA configurations are further core system settings
 * governed strictly by administration.
 * ============================================================
 */
