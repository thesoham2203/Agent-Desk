<?php

declare(strict_types=1);

namespace App\Livewire\Agent;

use App\Actions\AddInternalNoteAction;
use App\Actions\AssignTicketAction;
use App\Actions\ChangeTicketStatusAction;
use App\Actions\PostReplyAction;
use App\Actions\StoreAttachmentAction;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * ============================================================
 * FILE: TicketDetail.php
 * LAYER: Livewire Component
 * ============================================================
 *
 * WHAT IS THIS?
 * The component that displays the complete thread + controls for an individual ticket to agents.
 *
 * WHY DOES IT EXIST?
 * To allow agents to manage tickets effectively: view details, assign, reply, internally note, and change status/priority.
 *
 * HOW IT FITS IN THE APP:
 * This is the central operational screen for agents (at /agent/tickets/{ticket}).
 *
 * LARAVEL CONCEPT EXPLAINED:
 * We leverage $this->authorize() here, meaning policies decide whether the user is allowed to act on this specific ticket.
 * It's cleaner here than enforcing permissions in the Blade view itself.
 * ============================================================
 */
/**
 * @property-read Collection<int, TicketMessage> $threadMessages
 * @property-read Collection<int, User> $availableAgents
 */
#[Layout('layouts.app')]
final class TicketDetail extends Component
{
    use WithFileUploads;

    /**
     * The loaded Ticket model.
     */
    public Ticket $ticket;

    /**
     * Storage for the new public reply contents.
     */
    #[Validate('required|min:5|max:5000')]
    public string $replyBody = '';

    /**
     * Storage for the new internal note contents.
     */
    #[Validate('required|min:3|max:5000', as: 'note body')]
    public string $noteBody = '';

    /**
     * Current status enum string representation, used in the select dropdown.
     */
    public string $newStatus = '';

    /**
     * Current priority enum string representation, used in the select dropdown.
     */
    public string $newPriority = '';

    /**
     * Toggle between standard reply mode and internal note mode.
     */
    public bool $showInternalNoteForm = false;

    /**
     * The ID of the agent to whom the ticket will be assigned.
     */
    public ?int $assignToAgentId = null;

    /**
     * Tracks successfully sent replies for flash message rendering.
     */
    public bool $replySent = false;

    /**
     * Tracks successfully saved notes for flash message rendering.
     */
    public bool $noteSaved = false;

    /**
     * The files attached to the reply or note.
     *
     * @var array<int, UploadedFile>
     */
    #[Validate(['replyAttachments.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png,gif,doc,docx,xls,xlsx,txt,zip'])]
    public array $replyAttachments = [];

    /**
     * Applies the AI-generated draft to the reply body.
     *
     * @param  string  $draft  The draft content from the AI Panel.
     */
    #[On('use-draft')]
    public function applyDraft(string $draft): void
    {
        $this->replyBody = $draft;

        // Also ensure the public reply form is visible if it was hidden
        $this->showInternalNoteForm = false;

        $this->dispatch('reply-body-updated'); // Optional: triggering a frontend event if needed
    }

    /**
     * Component Initialization. Load ticket relations and auth checks.
     */
    public function mount(Ticket $ticket): void
    {
        $this->ticket = Ticket::with([
            'messages.author',
            'messages.attachments',
            'category',
            'requester',
            'assignee',
            'attachments',
        ])->findOrFail($ticket->id);

        $this->authorize('view', $this->ticket);

        $this->newStatus = $this->ticket->status->value;
        $this->newPriority = $this->ticket->priority->value;
    }

    /**
     * Retrieves the complete conversation history for this ticket.
     *
     * @return Collection<int, TicketMessage>
     */
    #[Computed]
    public function threadMessages(): Collection
    {
        return $this->ticket->messages()->with(['author', 'attachments'])->oldest()->get();
    }

    /**
     * Retrieves all available agents to assign tickets to.
     *
     * @return Collection<int, User>
     */
    #[Computed]
    public function availableAgents(): Collection
    {
        /** @var Collection<int, User> $agents */
        $agents = User::query()
            ->where('role', UserRole::Agent->value)
            ->orderBy('name')
            ->get(['id', 'name']);

        return $agents;
    }

    /**
     * Submits a public reply to the ticket thread.
     */
    public function postReply(): void
    {
        $this->authorize('update', $this->ticket);

        $this->validateOnly('replyBody');

        /** @var User $user */
        $user = auth()->user();

        $message = resolve(PostReplyAction::class)->execute(
            $user,
            $this->ticket,
            $this->replyBody
        );

        // Store each uploaded file as an Attachment
        foreach ($this->replyAttachments as $file) {
            /** @var UploadedFile $file */
            resolve(StoreAttachmentAction::class)->execute(
                $this->ticket,
                $message,
                $file
            );
        }

        $this->replyBody = '';
        $this->replyAttachments = [];
        $this->replySent = true;

        $this->ticket->load(['messages.author', 'messages.attachments', 'attachments']);
        $this->ticket->refresh();
        unset($this->threadMessages);
    }

    /**
     * Submits an internal note to the ticket thread.
     */
    public function addInternalNote(): void
    {
        $this->authorize('addInternalNote', $this->ticket);

        $this->validateOnly('noteBody');

        /** @var User $user */
        $user = auth()->user();

        $message = resolve(AddInternalNoteAction::class)->execute(
            $user,
            $this->ticket,
            $this->noteBody
        );

        // Store each uploaded file as an Attachment
        foreach ($this->replyAttachments as $file) {
            /** @var UploadedFile $file */
            resolve(StoreAttachmentAction::class)->execute(
                $this->ticket,
                $message,
                $file
            );
        }

        $this->noteBody = '';
        $this->replyAttachments = [];
        $this->noteSaved = true;

        $this->ticket->load(['messages.author', 'messages.attachments', 'attachments']);
        $this->ticket->refresh();
        unset($this->threadMessages);
    }

    /**
     * Changes the ticket's status based on the dropdown selection.
     */
    public function updateStatus(): void
    {
        $this->authorize('update', $this->ticket);

        $status = TicketStatus::from($this->newStatus);

        if ($status === $this->ticket->status) {
            $this->addError('newStatus', 'Ticket is already in this status.');

            return;
        }

        /** @var User $user */
        $user = auth()->user();

        resolve(ChangeTicketStatusAction::class)->execute(
            $user,
            $this->ticket,
            $status
        );

        $this->ticket->refresh();
    }

    /**
     * Changes the ticket's priority based on the dropdown selection.
     */
    public function updatePriority(): void
    {
        $this->authorize('update', $this->ticket);

        $oldPriority = $this->ticket->priority;
        $newPriorityEnum = TicketPriority::from($this->newPriority);

        if ($oldPriority !== $newPriorityEnum) {
            $this->ticket->update(['priority' => $newPriorityEnum]);

            AuditLog::query()->create([
                'action' => 'priority.changed',
                'user_id' => auth()->id(),
                'ticket_id' => $this->ticket->id,
                'old_values' => ['priority' => $oldPriority->value],
                'new_values' => ['priority' => $newPriorityEnum->value],
            ]);
        }

        $this->ticket->refresh();
    }

    public function assignToSelf(): void
    {
        /** @var User $user */
        $user = auth()->user();

        $this->authorize('assign', $this->ticket);

        resolve(AssignTicketAction::class)->execute(
            $user,
            $this->ticket,
            $user
        );

        $this->ticket->refresh();
    }

    /**
     * Assigns the ticket to a specific agent selected from the dropdown.
     */
    public function assignToAgent(): void
    {
        $this->authorize('assign', $this->ticket);

        if ($this->assignToAgentId === null) {
            return;
        }

        $assignee = User::query()->findOrFail($this->assignToAgentId);

        /** @var User $user */
        $user = auth()->user();

        resolve(AssignTicketAction::class)->execute(
            $user,
            $this->ticket,
            $assignee
        );

        $this->assignToAgentId = null;
        /** @var Ticket $freshTicket */
        $freshTicket = $this->ticket->fresh(['assignee', 'requester']);
        $this->ticket = $freshTicket;

        $this->redirect(route('agent.queue'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.agent.ticket-detail');
    }
}
/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [resources/views/livewire/agent/ticket-detail.blade.php]
 * WHY: Now that we have the controller logic for managing a ticket, we can review the UI.
 * ============================================================
 */
