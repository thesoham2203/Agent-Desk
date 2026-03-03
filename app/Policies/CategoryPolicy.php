<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

/**
 * ============================================================
 * FILE: CategoryPolicy.php
 * LAYER: Policy
 * ============================================================
 *
 * WHAT IS THIS?
 * A policy class to determine user permissions related to categories.
 *
 * WHY DOES IT EXIST?
 * To satisfy HLD rules stating that Admin manages categories, while everyone reads them.
 *
 * HOW IT FITS IN THE APP:
 * Checked by controllers and Livewire components managing categories.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * A Policy in Laravel is a dedicated class that answers one question: can this
 * user perform this action on this model? Laravel automatically discovers policies
 * by naming convention and routes authorization checks through the Gate facade.
 * ============================================================
 */
final class CategoryPolicy
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
     * All roles viewAny categories.
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * All roles view categories.
     */
    public function view(): bool
    {
        return true;
    }

    /**
     * Admin only via before.
     */
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

    /**
     * Admin only via before.
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
 * Now that you understand CategoryPolicy.php, the next logical file
 * to read is:
 *
 * → app/Policies/MacroPolicy.php
 *
 * WHY: Macros share the same administrative restriction profile
 * as categories.
 * ============================================================
 */
