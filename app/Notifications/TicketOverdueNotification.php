<?php

declare(strict_types=1);

/**
 * ============================================================
 * FILE: TicketOverdueNotification.php
 * LAYER: Notification
 * ============================================================
 *
 * WHAT IS THIS?
 * A critical notification sent when a ticket breaches an SLA target
 * (first response or resolution time).
 *
 * WHY DOES IT EXIST?
 * To ensure that overdue work is escalated and handled promptly.
 *
 * HOW IT FITS IN THE APP:
 * Triggered by CheckOverdueTargetsJob.php. It is sent to the assigned
 * agent (if any) and all administrators.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This notification handles multi-recipient routing. The job identifies
 * plural 'notifiable' users (Agent + Admins) and sends them each a copy.
 * The 'overdueType' context allows one class to handle multiple SLA breach types.
 * ============================================================
 */

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class TicketOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param  string  $overdueType  Either 'first_response' or 'resolution'
     * @param  int  $hoursOverdue  How many hours past the SLA target
     */
    public function __construct(
        public readonly Ticket $ticket,
        public readonly string $overdueType,
        public readonly int $hoursOverdue,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Critical alerts should always use both database and mail.
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // High-importance email alerting staff of an SLA breach.
        return (new MailMessage)
            ->subject("⚠️ SLA Breach: Ticket #{$this->ticket->id}")
            ->line("Ticket #{$this->ticket->id} has breached its SLA target.")
            ->line("Overdue type: {$this->overdueType}")
            ->line("Hours overdue: {$this->hoursOverdue}")
            ->action('View Ticket', url('/agent/tickets/'.$this->ticket->id));
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        // Store breach metadata for management reports and agent dashboards.
        return [
            'type' => 'ticket.overdue',
            'ticket_id' => $this->ticket->id,
            'ticket_title' => $this->ticket->title,
            'overdue_type' => $this->overdueType,
            'hours_overdue' => $this->hoursOverdue,
            'url' => '/agent/tickets/'.$this->ticket->id,
        ];
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [app/Jobs/CheckOverdueTargetsJob.php]
 * WHY: Now that notifications are ready, see the logic that
 *      finds overdue tickets and triggers these alerts.
 * ============================================================
 */
