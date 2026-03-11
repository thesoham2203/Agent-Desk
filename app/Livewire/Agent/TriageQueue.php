<?php

declare(strict_types=1);

namespace App\Livewire\Agent;

use App\Actions\AssignTicketAction;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * ============================================================
 * FILE: TriageQueue.php
 * LAYER: Livewire Component
 * ============================================================
 *
 * WHAT IS THIS?
 * The component that displays unassigned "New" tickets to agents.
 *
 * WHY DOES IT EXIST?
 * To allow agents to find and claim incoming tickets before they get lost or ignored.
 *
 * HOW IT FITS IN THE APP:
 * This is the first screen agents see (at /agent/queue). It lists tickets and allows claiming them.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Computed properties in Livewire act like accessors or getters for complex queries.
 * They run each time the component is rendered but aren't stored in Livewire's internal state.
 * ============================================================
 */
/**
 * @property-read LengthAwarePaginator<int, Ticket> $tickets
 */
#[Layout('layouts.app')]
final class TriageQueue extends Component
{
    use WithPagination;

    /**
     * Term to search for tickets by title.
     * Public to bind to the search input in the UI.
     */
    public string $search = '';

    /**
     * Filter tickets by a specific priority enum value.
     * Public to bind to the priority dropdown.
     */
    public string $priorityFilter = '';

    /**
     * Retrieves the unassigned new tickets formatted for display.
     *
     * @return LengthAwarePaginator<int, Ticket>
     */
    #[Computed]
    public function tickets(): LengthAwarePaginator
    {
        // This is the triage queue — only unassigned New tickets appear here.
        // Once assigned, they leave this view.
        $query = Ticket::query()->where('status', TicketStatus::New->value)
            ->whereNull('assigned_to')
            ->with(['requester', 'category']);

        if ($this->search !== '') {
            $query->where('title', 'like', '%'.$this->search.'%');
        }

        if ($this->priorityFilter !== '') {
            $query->where('priority', $this->priorityFilter);
        }

        return $query->latest()->paginate(15);
    }

    /**
     * Assigns the ticket to the currently logged in agent.
     */
    public function assignToSelf(int $ticketId): void
    {
        $ticket = Ticket::query()->findOrFail($ticketId);

        // Ensure the agent is allowed to assign tickets
        $this->authorize('assign', $ticket);

        /** @var User $user */
        $user = auth()->user();

        resolve(AssignTicketAction::class)->execute(
            $user,
            $ticket,
            $user
        );

        $this->dispatch('ticket-assigned');

        // Redirect to My Tickets list so the agent sees their claimed work immediately.
        $this->redirect(route('agent.my-tickets'), navigate: true);
    }

    /**
     * Initializes the Livewire component.
     */
    public function mount(): void
    {
        // No specific mount logic needed here, just listing data
    }

    public function render(): View
    {
        return view('livewire.agent.triage-queue');
    }
}
/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [resources/views/livewire/agent/triage-queue.blade.php]
 * WHY: This component supplies the data, we now need to see how the user interface displays it.
 * ============================================================
 */
