<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

/**
 * ============================================================
 * FILE: KbArticlePolicy.php
 * LAYER: Policy
 * ============================================================
 *
 * WHAT IS THIS?
 * A policy to restrict access and actions on Knowledge Base articles.
 *
 * WHY DOES IT EXIST?
 * To satisfy HLD rules specifying that only Admins can create or update KB content,
 * while Agents need read access.
 *
 * HOW IT FITS IN THE APP:
 * Controls exposure of KB articles to support operations.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * A Policy in Laravel is a dedicated class that answers one question: can this
 * user perform this action on this model? Laravel automatically discovers policies
 * by naming convention and routes authorization checks through the Gate facade.
 * ============================================================
 */
final class KbArticlePolicy
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
     * Agents use KB articles for grounding in ReplyDraftAgent.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::Agent || $user->role === UserRole::Admin;
    }

    /**
     * Agents can read KB articles.
     */
    public function view(User $user): bool
    {
        return $user->role === UserRole::Agent || $user->role === UserRole::Admin;
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
 * Now that you understand KbArticlePolicy.php, the next logical file
 * to read is:
 *
 * → app/Policies/AiRunPolicy.php
 *
 * WHY: Knowing how knowledge is handled leads into how AI
 * operations, anchored on this knowledge, are governed.
 * ============================================================
 */
