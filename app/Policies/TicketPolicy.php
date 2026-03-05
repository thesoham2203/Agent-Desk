<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;

/**
 * ============================================================
 * FILE: TicketPolicy.php
 * LAYER: Policy
 * ============================================================
 *
 * WHAT IS THIS?
 * A policy class to determine user permissions related to tickets.
 * It strictly dictates who can view, create, update, or delete tickets.
 *
 * WHY DOES IT EXIST?
 * To enforce the core access controls and business rules from the HLD
 * regarding user ticket access and action permissions.
 *
 * HOW IT FITS IN THE APP:
 * Controllers and Livewire components will check this policy via Laravel's Gate
 * before allowing users to interact with Ticket models.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * A Policy in Laravel is a dedicated class that answers one question: can this
 * user perform this action on this model? Laravel automatically discovers policies
 * by naming convention and routes authorization checks through the Gate facade.
 * ============================================================
 */
final class TicketPolicy
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
     * HLD §8.2: Agents and Admins can see all tickets.
     * Requesters can see all via viewAny, but query will be scoped later.
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * HLD §8.2: Requester can ONLY view their own tickets.
     * SupportAgent can view tickets assigned to them OR tickets in triage queue (New && unassigned).
     */
    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->role === UserRole::Requester) {
            return $ticket->requester_id === $user->id;
        }

        if ($user->role === UserRole::Agent) {
            return $ticket->assigned_to === $user->id
                || ($ticket->status === TicketStatus::New && $ticket->assigned_to === null);
        }

        return false;
    }

    /**
     * HLD §8.2: Any authenticated user can open a ticket.
     */
    public function create(): bool
    {
        return true;
    }

    /**
     * HLD §8.2: Only Agent or Admin can change status/assign/tag.
     */
    public function update(User $user): bool
    {
        return $user->role === UserRole::Agent || $user->role === UserRole::Admin;
    }

    /**
     * HLD §8.2: Only Agent or Admin can add internal notes.
     */
    public function addInternalNote(User $user): bool
    {
        return $user->role === UserRole::Agent || $user->role === UserRole::Admin;
    }

    /**
     * HLD §8.2: Only Agent or Admin can assign/unassign tickets.
     */
    public function assign(User $user): bool
    {
        return $user->role === UserRole::Agent || $user->role === UserRole::Admin;
    }

    /**
     * HLD §8.2: Only SupportAgent or Admin can trigger AI tools.
     */
    public function runAi(User $user): bool
    {
        return $user->role === UserRole::Agent;
    }

    /**
     * HLD: Not required by HLD. Always deny.
     */
    public function delete(): bool
    {
        return false;
    }
}
