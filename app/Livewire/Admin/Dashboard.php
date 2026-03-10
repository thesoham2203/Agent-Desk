<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * ============================================================
 * FILE: Dashboard.php
 * LAYER: Livewire Component
 * ============================================================
 *
 * WHAT IS THIS?
 * The high-level overview for administrators.
 *
 * WHY DOES IT EXIST?
 * To provide instant visibility into system volume: how many
 * customers, how many agents, and the current state of tickets.
 *
 * HOW IT FITS IN THE APP:
 * This is the landing page for any user with the 'admin' role.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This component uses Eloquent counts to aggregate data for
 * performance.
 * ============================================================
 */
#[Layout('layouts.app')]
final class Dashboard extends Component
{
    /**
     * Retrieves the stats for the dashboard cards.
     *
     * @return array{requesters: int, agents: int, activeTickets: int, resolvedTickets: int}
     */
    public function getStatsProperty(): array
    {
        return [
            'requesters' => User::query()->where('role', UserRole::Requester->value)->count(),
            'agents' => User::query()->whereIn('role', [UserRole::Agent->value, UserRole::Admin->value])->count(),
            'activeTickets' => Ticket::query()
                ->whereIn('status', [TicketStatus::New->value, TicketStatus::InProgress->value])
                ->count(),
            'resolvedTickets' => Ticket::query()
                ->where('status', TicketStatus::Resolved->value)
                ->count(),
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.dashboard', [
            'stats' => $this->getStatsProperty(),
        ]);
    }
}
