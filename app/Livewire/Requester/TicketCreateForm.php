<?php

declare(strict_types=1);

namespace App\Livewire\Requester;

use App\Actions\CreateTicketAction;
use App\Actions\CreateTicketData;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * ============================================================
 * FILE: TicketCreateForm.php
 * LAYER: Livewire Component
 * ============================================================
 *
 * WHAT IS THIS?
 * A component that renders and processes the ticket creation form
 * for requesters. It collects a title, body, and category.
 *
 * WHY DOES IT EXIST?
 * To allow requesters or admins to create a new ticket in the system
 * using a reactive form without full page reloads.
 *
 * HOW IT FITS IN THE APP:
 * Uses CreateTicketAction to actually write the data. Authorizes
 * against TicketPolicy. Redirects to TicketDetail on success.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Livewire components are PHP classes that represent a piece of UI.
 * Public properties are automatically available in the Blade view
 * and can be bound to inputs using `wire:model`. The `#[Validate]`
 * attribute simplifies validation directly on the properties.
 * ============================================================
 */
final class TicketCreateForm extends Component
{
    /**
     * The title of the new ticket.
     * Public so Livewire can bind it to the template input.
     */
    #[Validate('required|min:5|max:255')]
    public string $title = '';

    /**
     * The body/description of the new ticket.
     * Public so Livewire can bind it to the template textarea.
     */
    #[Validate('required|min:10')]
    public string $body = '';

    /**
     * The ID of the primary category for the ticket.
     * Public so Livewire can bind it to the template select.
     */
    #[Validate('nullable|exists:categories,id')]
    public ?int $categoryId = null;

    /**
     * Tracks whether the form has been successfully submitted.
     * Public to show success state in the view.
     */
    public bool $submitted = false;

    /**
     * Available categories.
     *
     * @var Collection<int, Category>
     */
    public Collection $categories;

    /**
     * Runs once when the component initially loads.
     * We populate dropdown data here rather than on every render.
     */
    public function mount(): void
    {
        // Pre-load categories for the dropdown.
        $this->categories = Category::query()->orderBy('name')->get();

        // No action injection needed here, we do it via app() or dependency injection in the submit method.
    }

    /**
     * Handles the form submission.
     *
     * @param  CreateTicketAction  $action  The action that handles the business logic.
     */
    public function submit(CreateTicketAction $action): void
    {
        // 1. Authorize: Check policy before doing anything
        Gate::authorize('create', Ticket::class);

        // 2. Validate: Enforces rules from #[Validate] attributes
        $this->validate();

        // 3. Execute business logic via Action
        /** @var User $user */
        $user = auth()->user();
        $ticket = $action->execute($user, new CreateTicketData(
            title: $this->title,
            body: $this->body,
            categoryId: $this->categoryId,
        ));

        // 4. Update component state
        $this->submitted = true;

        // 5. Redirect cleanly
        $this->redirect(route('requester.tickets.show', $ticket->id));
    }

    public function render(): View
    {
        return view('livewire.requester.ticket-create-form');
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand the ticket creation UI component,
 * the next logical file to read is:
 *
 * → resources/views/livewire/requester/ticket-create-form.blade.php
 *
 * WHY: This blade view renders the form that interacts with this
 * component's properties and methods.
 * ============================================================
 */
