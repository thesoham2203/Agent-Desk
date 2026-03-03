<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Attachment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

/**
 * ============================================================
 * FILE: AttachmentPolicy.php
 * LAYER: Policy
 * ============================================================
 *
 * WHAT IS THIS?
 * A policy to restrict access and actions on ticket file attachments.
 *
 * WHY DOES IT EXIST?
 * To satisfy secure file access requirements from the HLD (Module B).
 *
 * HOW IT FITS IN THE APP:
 * Enforced during attachment download and upload mechanisms.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * A Policy in Laravel is a dedicated class that answers one question: can this
 * user perform this action on this model? Laravel automatically discovers policies
 * by naming convention and routes authorization checks through the Gate facade.
 * ============================================================
 */
final class AttachmentPolicy
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
     * HLD Module B: Authorized downloads only based on ticket visibility.
     */
    public function view(User $user, Attachment $attachment): bool
    {
        return Gate::forUser($user)->allows('view', $attachment->ticket);
    }

    /**
     * Any user who can view ticket can create.
     */
    public function create(User $user, Ticket $ticket): bool
    {
        return Gate::forUser($user)->allows('view', $ticket);
    }

    public function update(): bool
    {
        return false;
    }

    /**
     * HLD §9.1: Requester can delete only while ticket is New and if they own it.
     */
    public function delete(User $user, Attachment $attachment): bool
    {
        $ticket = $attachment->ticket;

        if ($user->role === UserRole::Agent) {
            return true;
        }

        if ($user->role === UserRole::Requester) {
            return $ticket->requester_id === $user->id && $ticket->status === TicketStatus::New;
        }

        return false;
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand AttachmentPolicy.php, the next logical file
 * to read is:
 *
 * → app/Policies/CategoryPolicy.php
 *
 * WHY: It shifts focus to administrative models that
 * govern ticket classification.
 * ============================================================
 */
