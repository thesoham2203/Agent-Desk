<?php

declare(strict_types=1);

namespace App\Livewire\Requester;

use App\Models\Ticket;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * ============================================================
 * FILE: MyTicketsTable.php
 * LAYER: Livewire Component
 * ============================================================
 *
 * WHAT IS THIS?
 * A table component showing the logged-in requester's tickets.
 * Includes search and status filtering without page reloads.
 *
 * WHY DOES IT EXIST?
 * To allow requesters to quickly find their tickets, see the
 * status of each, and jump to the detail view.
 *
 * HOW IT FITS IN THE APP:
 * Uses the Ticket model with query scoping. Embedded in the
 * 'requester.tickets.index' route.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Computed properties use `#[Computed]`. They cache data
 * automatically for the single request. `WithPagination` tells
 * Livewire to bind to the URL query string and handle page turns
 * reactively without full page loads.
 * ============================================================
 */
#[Layout('layouts.app')]
final class MyTicketsTable extends Component
{
    use WithPagination;

    /**
     * Search term for filtering tickets by text.
     * Public so it binds to wire:model.live in blade.
     */
    public string $search = '';

    /**
     * Status filter to view specific statuses.
     * Public so it binds to a dropdown in blade.
     */
    public string $statusFilter = '';

    /**
     * How many tickets per page.
     * Public to potentially allow users to change it (or hardcode here).
     */
    public int $perPage = 10;

    /**
     * Updates pagination when search changes.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Updates pagination when filter changes.
     */
    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Returns the tickets for the data table.
     * We scope to the current user's tickets here in the query.
     * The TicketPolicy::viewAny() returns true for all roles —
     * the actual ownership filter is enforced HERE in the query,
     * not in the policy. This is the Laravel convention for list views.
     *
     * @return LengthAwarePaginator<int, Ticket>
     */
    #[Computed]
    public function tickets(): LengthAwarePaginator
    {
        return Ticket::query()->where('requester_id', auth()->id())
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $q): void {
                    $q->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('body', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter !== '', function (Builder $query): void {
                $query->where('status', $this->statusFilter);
            })
            // Avoid N+1 — always eager load relations used in blade
            ->with(['category', 'messages'])
            ->latest()
            ->paginate($this->perPage);
    }

    public function render(): View
    {
        return view('livewire.requester.my-tickets-table');
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand the ticketing list UI,
 * the next logical file to read is:
 *
 * → resources/views/livewire/requester/my-tickets-table.blade.php
 *
 * WHY: This blade view renders the paginated results bound to this component.
 * ============================================================
 */
