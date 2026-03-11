<?php

declare(strict_types=1);

namespace App\Livewire\Agent;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @property-read LengthAwarePaginator<int, Ticket> $tickets
 */
#[Layout('layouts.app')]
final class MyTickets extends Component
{
    use WithPagination;

    /**
     * Term to search for tickets by title.
     */
    public string $search = '';

    /**
     * Filter tickets by a specific priority enum value.
     */
    public string $priorityFilter = '';

    /**
     * Retrieves the assigned tickets for the agent.
     *
     * @return LengthAwarePaginator<int, Ticket>
     */
    #[Computed]
    public function tickets(): LengthAwarePaginator
    {
        $query = Ticket::query()->where('assigned_to', auth()->id())
            ->where('status', '!=', TicketStatus::Resolved->value) // Optionally hide resolved? Let's show all or maybe not. Standard is show all assigned.
            ->with(['requester', 'category']);

        if ($this->search !== '') {
            $query->where('title', 'like', '%'.$this->search.'%');
        }

        if ($this->priorityFilter !== '') {
            $query->where('priority', $this->priorityFilter);
        }

        return $query->latest()->paginate(15);
    }

    public function render(): View
    {
        return view('livewire.agent.my-tickets');
    }
}
