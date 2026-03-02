<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * ============================================================
 * FILE: AuditLogSeeder.php
 * LAYER: Seeder
 * ============================================================
 *
 * WHAT IS THIS?
 * Seeds the historical record of actions taken on tickets.
 *
 * WHY DOES IT EXIST?
 * To populate the admin viewer with realistic logs showing status
 * changes and manual assignments.
 *
 * HOW IT FITS IN THE APP:
 * These records are fetched by the Audit Log viewer in the
 * administrative dashboard.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This seeder demonstrates how to store JSON data (snapshots)
 * for historical tracking.
 * ============================================================
 */
final class AuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Generates logs for ticket creation and assignment changes.
     */
    public function run(): void
    {
        $tickets = Ticket::query()->whereNotNull('assigned_to')->take(10)->get();
        $admin = User::query()->where('role', UserRole::Admin)->first()
            ?? User::factory()->create(['role' => UserRole::Admin]);

        foreach ($tickets as $ticket) {
            /** Log the initial creation. */
            AuditLog::query()->create([
                'ticket_id' => $ticket->id,
                'user_id' => $ticket->requester_id,
                'action' => 'ticket.created',
                'new_values' => ['title' => $ticket->title, 'status' => 'new'],
            ]);

            /** Log the manual assignment by an administrator. */
            AuditLog::query()->create([
                'ticket_id' => $ticket->id,
                'user_id' => $admin->id,
                'action' => 'ticket.assigned',
                'old_values' => ['assigned_to' => null],
                'new_values' => ['assigned_to' => $ticket->assigned_to],
            ]);
        }
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [AuditLogSeeder.php], the next logical file
 * to read is:
 *
 * → [database/seeders/DatabaseSeeder.php]
 *
 * WHY: This matches the entry point that brings all seeders together.
 * ============================================================
 */
