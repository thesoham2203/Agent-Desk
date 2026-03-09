<?php

declare(strict_types=1);

/**
 * ============================================================
 * FILE: CheckOverdueTargetsJob.php
 * LAYER: Job
 * ============================================================
 *
 * WHAT IS THIS?
 * A background job that scans the database for tickets hitting
 * SLA breaches and sends alerts to agents and admins.
 *
 * WHY DOES IT EXIST?
 * To ensure that SLAs are monitored automatically without human
 * intervention, maintaining the health of the helpdesk.
 *
 * HOW IT FITS IN THE APP:
 * Triggered by the Laravel Scheduler (routes/console.php) every hour.
 * It queries SlaConfig and compares against Ticket timestamps.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Why overdue checks run as a job and not directly in the scheduler closure:
 * 1. Performance: The scheduler is synchronous. Moving the scan to a job
 *    allows the scheduler process to finish quickly while the queue worker
 *    handles the heavy lifting of querying and notifying.
 * 2. Retries: If an email fails, the job can be retried automatically by the queue.
 * ============================================================
 */

namespace App\Jobs;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\SlaConfig;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketOverdueNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class CheckOverdueTargetsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Load SLA config: SlaConfig always has exactly ONE row.
        $slaConfig = SlaConfig::query()->firstOrFail();

        // 2. Get current time: used as the comparison baseline for all checks.
        $now = now();

        // 3. Find tickets overdue for FIRST RESPONSE.
        // We look for tickets where:
        // - No agent has replied yet (first_responded_at IS NULL)
        // - Ticket is NOT resolved
        // - Ticket was created more than first_response_hours ago
        $firstResponseOverdue = Ticket::query()
            ->whereNull('first_responded_at')
            ->whereNull('first_response_breached_at')
            ->whereNotIn('status', [
                TicketStatus::Resolved->value,
            ])
            ->where('created_at', '<=', $now->copy()->subHours($slaConfig->first_response_hours))
            ->with(['assignee', 'requester'])
            ->get();

        // 4. Find tickets overdue for RESOLUTION.
        // We look for tickets where:
        // - Ticket is NOT resolved (resolved_at IS NULL)
        // - Ticket is NOT currently in Resolved status
        // - Ticket was created more than resolution_hours ago
        $resolutionOverdue = Ticket::query()
            ->whereNull('resolved_at')
            ->whereNull('resolution_breached_at')
            ->whereNotIn('status', [
                TicketStatus::Resolved->value,
            ])
            ->where('created_at', '<=', $now->copy()->subHours($slaConfig->resolution_hours))
            ->with(['assignee', 'requester'])
            ->get();

        // 5. Notify for first response overdue
        foreach ($firstResponseOverdue as $ticket) {
            $hoursOverdue = (int) $ticket->created_at->diffInHours($now) - $slaConfig->first_response_hours;

            $notification = new TicketOverdueNotification(
                ticket: $ticket,
                overdueType: 'first_response',
                hoursOverdue: $hoursOverdue,
            );

            // Notify assigned agent if they exist.
            if ($ticket->assignee !== null) {
                $ticket->assignee->notify($notification);
            }

            // Notify ALL admins so the breach is visible to management.
            User::query()->where('role', UserRole::Admin->value)
                ->get()
                ->each(fn (User $admin) => $admin->notify($notification));

            // Record the breach timestamp to avoid double notification
            $ticket->update(['first_response_breached_at' => $now]);

            // Audit log the breach with technical details.
            AuditLog::query()->create([
                'ticket_id' => $ticket->id,
                'user_id' => $ticket->assigned_to ?? User::query()->where('role', UserRole::Admin->value)->value('id'),
                'action' => 'sla.first_response.breached',
                'old_values' => null,
                'new_values' => [
                    'hours_overdue' => $hoursOverdue,
                    'sla_target' => $slaConfig->first_response_hours,
                ],
            ]);
        }

        // 6. Notify for resolution overdue (same pattern as first response)
        foreach ($resolutionOverdue as $ticket) {
            $hoursOverdue = (int) $ticket->created_at->diffInHours($now) - $slaConfig->resolution_hours;

            $notification = new TicketOverdueNotification(
                ticket: $ticket,
                overdueType: 'resolution',
                hoursOverdue: $hoursOverdue,
            );

            // Notify assigned agent if they exist.
            if ($ticket->assignee !== null) {
                $ticket->assignee->notify($notification);
            }

            // Notify ALL admins.
            User::query()->where('role', UserRole::Admin->value)
                ->get()
                ->each(fn (User $admin) => $admin->notify($notification));

            // Record the breach timestamp to avoid double notification
            $ticket->update(['resolution_breached_at' => $now]);

            // Audit log the resolution breach.
            AuditLog::query()->create([
                'ticket_id' => $ticket->id,
                'user_id' => $ticket->assigned_to ?? User::query()->where('role', UserRole::Admin->value)->value('id'),
                'action' => 'sla.resolution.breached',
                'old_values' => null,
                'new_values' => [
                    'hours_overdue' => $hoursOverdue,
                    'sla_target' => $slaConfig->resolution_hours,
                ],
            ]);
        }
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [routes/console.php]
 * WHY: After generating the job, see how it is registered in
 *      the Laravel Scheduler to run every hour.
 * ============================================================
 */
