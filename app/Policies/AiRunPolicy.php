<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\AiRun;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

/**
 * ============================================================
 * FILE: AiRunPolicy.php
 * LAYER: Policy
 * ============================================================
 *
 * WHAT IS THIS?
 * A policy class determining who can trigger or view AI execution traces.
 *
 * WHY DOES IT EXIST?
 * Because AI runs consume external resources, and their logs are sensitive.
 *
 * HOW IT FITS IN THE APP:
 * Checked when an agent triggers triage or drafting, and when presenting AI outputs.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * A Policy in Laravel is a dedicated class that answers one question: can this
 * user perform this action on this model? Laravel automatically discovers policies
 * by naming convention and routes authorization checks through the Gate facade.
 * ============================================================
 */
final class AiRunPolicy
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
     * HLD §9.3: AI runs viewer is read-only for Admin. Agents cannot browse global list.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Agent || $user->role === UserRole::Admin;
    }

    /**
     * Check that user can view the parent ticket.
     */
    public function view(User $user, AiRun $aiRun): bool
    {
        return Gate::forUser($user)->allows('view', $aiRun->ticket);
    }

    /**
     * HLD §12: Only an Agent or Admin can trigger AI runs.
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::Agent || $user->role === UserRole::Admin;
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
 * Now that you understand AiRunPolicy.php, the next logical file
 * to read is:
 *
 * → app/Policies/AuditLogPolicy.php
 *
 * WHY: Finally, auditing provides an immutable history of sensitive
 * actions matching the rules set out by all these preceding policies.
 * ============================================================
 */
