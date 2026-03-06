<?php

declare(strict_types=1);

/**
 * ============================================================
 * FILE: TicketResolvedNotification.php
 * LAYER: Notification
 * ============================================================
 *
 * WHAT IS THIS?
 * A notification sent to the requester when their ticket has been marked as resolved.
 *
 * WHY DOES IT EXIST?
 * To close the loop with the customer and let them know their issue is addressed.
 *
 * HOW IT FITS IN THE APP:
 * Triggered by ChangeTicketStatusAction.php when the status is updated to Resolved.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This notification targets the customer (requester). By using the database channel,
 * we can show a "Resolved" badge in their ticket list, and the mail channel
 * provides an external closure notice.
 * ============================================================
 */

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class TicketResolvedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Ticket $ticket,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Notify the customer via database and mail.
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Send a friendly resolution email to the requester.
        return (new MailMessage)
            ->subject("Ticket Resolved: #{$this->ticket->id}")
            ->line('Your ticket has been resolved.')
            ->line($this->ticket->title)
            ->action('View Ticket', url('/my/tickets/'.$this->ticket->id));
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        // Store resolution data for the requester's portal.
        return [
            'type' => 'ticket.resolved',
            'ticket_id' => $this->ticket->id,
            'ticket_title' => $this->ticket->title,
            'url' => '/my/tickets/'.$this->ticket->id,
        ];
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [app/Notifications/TicketOverdueNotification.php]
 * WHY: After manual triggers, see how automated system triggers
 *      like the SLA check send alerts.
 * ============================================================
 */
