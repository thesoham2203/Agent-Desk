<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

/**
 * ============================================================
 * FILE: SlaConfigPolicy.php
 * LAYER: Policy
 * ============================================================
 *
 * WHAT IS THIS?
 * Policy handling SLA configuration rules.
 *
 * WHY DOES IT EXIST?
 * SLAs are critical business settings. Only admins should touch them.
 *
 * HOW IT FITS IN THE APP:
 * Enforced on the SLA settings interface.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * A Policy in Laravel is a dedicated class that answers one question: can this
 * user perform this action on this model? Laravel automatically discovers policies
 * by naming convention and routes authorization checks through the Gate facade.
 * ============================================================
 */
final class SlaConfigPolicy
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

    public function viewAny(): bool
    {
        return false;
    }

    /**
     * Admin only via before.
     */
    public function view(): bool
    {
        return false;
    }

    public function create(): bool
    {
        return false;
    }

    /**
     * Admin only via before.
     */
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
 * Now that you understand SlaConfigPolicy.php, the next logical file
 * to read is:
 *
 * → app/Policies/KbArticlePolicy.php
 *
 * WHY: Moving into the knowledge base policies, which guide
 * both agent usage and AI generation grounding.
 * ============================================================
 */
