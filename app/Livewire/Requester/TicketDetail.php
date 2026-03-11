<?php

declare(strict_types=1);

namespace App\Livewire\Requester;

use App\Actions\PostReplyAction;
use App\Actions\StoreAttachmentAction;
use App\Enums\TicketMessageType;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
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
 * Resolves and displays a single ticket with its conversation thread.
 * It also includes a form allowing the requester to post replies.
 *
 * WHY DOES IT EXIST?
 * To allow requesters to view updates on their tickets and continue
 * communication with the agent. The component strictly isolates
 * requester access from internal agent notes.
 *
 * HOW IT FITS IN THE APP:
 * Tied to the numeric ticket ID from the 'requester.tickets.show'
 * route. Uses PostReplyAction to save replies.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * `mount` accepts route parameters directly (e.g. integer $ticketId).
 * `computed` properties are recalculated and cached to prevent
 * expensive or repeated collection filtering.
 * ============================================================
 */
#[Layout('layouts.app')]
final class TicketDetail extends Component
{
    use WithFileUploads;

    /**
     * The deeply loaded ticket object.
     * Public so Blade can read its title, status, etc.
     */
    public Ticket $ticket;

    /**
     * The input for typing a reply.
     * Bound to a textarea.
     */
    #[Validate('required|min:5|max:500')]
    public string $replyBody = '';

    /**
     * Set to true when the reply successfully posts.
     */
    public bool $replySent = false;

    /**
     * The files attached to the reply.
     *
     * @var array<int, UploadedFile>
     */
    #[Validate(['replyAttachments.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png,gif,doc,docx,xls,xlsx,txt,zip'])]
    public array $replyAttachments = [];

    /**
     * Called once when component loads via URL match.
     */
    public function mount(int $ticketId): void
    {
        // 1. Fetch data
        $this->ticket = Ticket::with([
            'messages.author',
            'messages.attachments',
            'attachments',
            'category',
        ])->findOrFail($ticketId);

        // 2. Authorize
        // We authorize in mount() so if the user has
        // no access, they get a 403 immediately on page load —
        // before seeing any ticket data.
        Gate::authorize('view', $this->ticket);
    }

    /**
     * Requesters only see public messages.
     * Admins see all messages.
     *
     * @return Collection<int, TicketMessage>
     */
    #[Computed]
    public function publicMessages(): Collection
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role === UserRole::Admin) {
            return $this->ticket->messages;
        }

        return $this->ticket->messages->filter(fn (TicketMessage $message): bool => $message->type === TicketMessageType::Public)->values();
    }

    /**
     * Handles adding a new response to the ticket thread.
     */
    public function postReply(PostReplyAction $action): void
    {
        // 1. Authorize: Are they allowed to reply?
        Gate::authorize('create', [TicketMessage::class, $this->ticket]);

        // 2. Validate properties manually using only replyBody rules
        $this->validateOnly('replyBody');

        // 3. Execute
        /** @var User $user */
        $user = auth()->user();
        $message = $action->execute($user, $this->ticket, $this->replyBody);

        // Store each uploaded file as an Attachment
        foreach ($this->replyAttachments as $file) {
            /** @var UploadedFile $file */
            resolve(StoreAttachmentAction::class)->execute(
                $this->ticket,
                $message,
                $file
            );
        }

        // 4. Update component state
        $this->replyBody = '';
        $this->replyAttachments = [];
        $this->replySent = true;

        // 5. Reload relationships so new message appears instantly
        /** @var Ticket $freshTicket */
        $freshTicket = $this->ticket->fresh(['messages.author', 'messages.attachments', 'attachments', 'category']);
        $this->ticket = $freshTicket;
    }

    public function render(): View // removed type hint for Blade view return
    {
        return view('livewire.requester.ticket-detail');
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand the ticketing detail UI,
 * the next logical file to read is:
 *
 * → resources/views/livewire/requester/ticket-detail.blade.php
 *
 * WHY: This blade view renders the ticket threaded view bound to this component.
 * ============================================================
 */
