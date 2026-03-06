<?php

declare(strict_types=1);

/**
 * ============================================================
 * FILE: TicketAssignedNotification.php
 * LAYER: Notification
 * ============================================================
 *
 * WHAT IS THIS?
 * A notification sent to a support agent when a ticket is assigned to them.
 * It uses both database and mail channels to ensure visibility.
 *
 * WHY DOES IT EXIST?
 * To instantly alert agents of new work assigned to them so they can
 * begin the triage or resolution process.
 *
 * HOW IT FITS IN THE APP:
 * Triggered by AssignTicketAction.php. The agent receives this in their
 * notification bell (database) and via email (mail).
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Notifications in Laravel are modular classes that can be delivered
 * over multiple channels (mail, database, slack, etc.). By using the
 * 'Notifiable' trait on the User model, we can call $user->notify($instance).
 * Database notifications are stored in the 'notifications' table, while
 * mail notifications use the 'toMail' method to build an email message.
 * ============================================================
 */

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class TicketAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Ticket $ticket,
        public readonly User $assignedBy,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Deliver via both database for the UI bell and mail for external alerts.
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Build the email sent to the assigned agent.
        return (new MailMessage)
            ->subject("Ticket Assigned: #{$this->ticket->id}")
            ->line("You have been assigned ticket #{$this->ticket->id}")
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
        // Store structured data in the notifications table for the UI to read.
        return [
            'type' => 'ticket.assigned',
            'ticket_id' => $this->ticket->id,
            'ticket_title' => $this->ticket->title,
            'assigned_by' => $this->assignedBy->name,
            'url' => '/agent/tickets/'.$this->ticket->id,
        ];
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [app/Notifications/RequesterRepliedNotification.php]
 * WHY: After learning how assignment notifications work, see how
 *      replies from customers trigger agent alerts.
 * ============================================================
 */
