<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\TicketMessageType;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

/**
 * ============================================================
 * FILE: TicketMessagePolicy.php
 * LAYER: Policy
 * ============================================================
 *
 * WHAT IS THIS?
 * A policy class to handle permissions for ticket messages.
 *
 * WHY DOES IT EXIST?
 * To satisfy HLD rules around message visibility, specifically hiding
 * internal notes from requesters while ensuring agents have access.
 *
 * HOW IT FITS IN THE APP:
 * Enforced when rendering the ticket thread UI or processing API requests
 * dealing with messages.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * A Policy in Laravel is a dedicated class that answers one question: can this
 * user perform this action on this model? Laravel automatically discovers policies
 * by naming convention and routes authorization checks through the Gate facade.
 * ============================================================
 */
final class TicketMessagePolicy
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
        return true;
    }

    /**
     * HLD §8.1: Requesters cannot see internal notes.
     * Public replies use ticket visibility checking.
     */
    public function view(User $user, TicketMessage $message): bool
    {
        $ticket = $message->ticket;

        if ($message->type === TicketMessageType::Internal && $user->role === UserRole::Requester) {
            return false;
        }

        return Gate::forUser($user)->allows('view', $ticket);
    }

    /**
     * HLD §8.2: Delegate to ticket viewability.
     */
    public function create(User $user, Ticket $ticket): bool
    {
        return Gate::forUser($user)->allows('view', $ticket);
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
 * Now that you understand TicketMessagePolicy.php, the next logical file
 * to read is:
 *
 * → app/Policies/AttachmentPolicy.php
 *
 * WHY: Attachments are the other primary piece of ticket data
 * requested and managed by users, continuing the theme of ticket access.
 * ============================================================
 */
