<?php

declare(strict_types=1);

/**
 * ============================================================
 * FILE: RequesterRepliedNotification.php
 * LAYER: Notification
 * ============================================================
 *
 * WHAT IS THIS?
 * A notification sent to the assigned agent when a requester posts a reply.
 *
 * WHY DOES IT EXIST?
 * To notify agents that a customer is waiting for a response or has
 * provided requested information.
 *
 * HOW IT FITS IN THE APP:
 * Triggered by PostReplyAction.php when the author has the 'Requester' role.
 * Only sent if an agent is actually assigned to the ticket.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This notification demonstrates conditional alerting. The application logic
 * ensures that $user->notify() is only called if a 'notifiable' entity (the agent)
 * is linked to the ticket.
 * ============================================================
 */

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class RequesterRepliedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Ticket $ticket,
        public readonly User $requester,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Notify via database and mail.
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Inform the agent that the customer has replied.
        return (new MailMessage)
            ->subject("New Reply: #{$this->ticket->id}")
            ->line("{$this->requester->name} replied to ticket #{$this->ticket->id}")
            ->line($this->ticket->title)
            ->action('View Ticket', url('/agent/tickets/'.$this->ticket->id));
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        // Store reply metadata for the agent's notification feed.
        return [
            'type' => 'requester.replied',
            'ticket_id' => $this->ticket->id,
            'ticket_title' => $this->ticket->title,
            'requester' => $this->requester->name,
            'url' => '/agent/tickets/'.$this->ticket->id,
        ];
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [app/Notifications/TicketResolvedNotification.php]
 * WHY: See how notifications are used to close the loop with customers.
 * ============================================================
 */
