<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\TicketMessageType;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * ============================================================
 * FILE: TicketSeeder.php
 * LAYER: Seeder
 * ============================================================
 *
 * WHAT IS THIS?
 * Seeds the system with realistic tickets and conversation threads.
 *
 * WHY DOES IT EXIST?
 * To demonstrate the full workflow including unassigned triage,
 * assigned work, and overdue tickets for SLA testing.
 *
 * HOW IT FITS IN THE APP:
 * These records appear in the Triage Queue and Agent Dashboard
 * immediately after seeding.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This seeder demonstrates complex relational pairing,
 * backdating timestamps, and chaining message creation to
 * parent tickets.
 * ============================================================
 */
final class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates a mix of new, active, and overdue tickets for development.
     */
    public function run(): void
    {
        $requesters = User::query()->where('role', UserRole::Requester)->get();
        $agents = User::query()->where('role', UserRole::Agent)->get();
        $categories = Category::all();

        /** 1. Create 5 "New" unassigned tickets specifically for the Triage Queue demo. */
        foreach (range(1, 5) as $i) {
            $ticket = Ticket::query()->create([
                'requester_id' => $requesters->random()->id,
                'status' => TicketStatus::New,
                'priority' => TicketPriority::Medium,
                'title' => 'Question about feature '.$i,
                'body' => 'I am having trouble finding where the new export button is.',
                'category_id' => null,
                'assigned_to' => null,
            ]);

            $this->addRandomMessages($ticket, $ticket->requester_id);
        }

        /** 2. Create 3 backdated "Overdue" tickets for the scheduler demo. */
        foreach (range(1, 3) as $i) {
            $createdAt = now()->subHours(12); // Backdated beyond the 4-hour SLA

            $ticket = Ticket::query()->create([
                'requester_id' => $requesters->random()->id,
                'status' => TicketStatus::New,
                'priority' => TicketPriority::High,
                'title' => 'URGENT: Broken link '.$i,
                'body' => 'This page is returning a 404 error and I need it fixed now.',
                'category_id' => $categories->random()->id,
                'assigned_to' => null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'first_responded_at' => null, // Critical: no response makes it overdue
            ]);

            $this->addRandomMessages($ticket, $ticket->requester_id);
        }

        /** 3. Create 12 more random tickets covering all statuses and priorities. */
        foreach (range(1, 12) as $i) {
            $status = collect(TicketStatus::cases())->random();
            $priority = collect(TicketPriority::cases())->random();

            $ticket = Ticket::query()->create([
                'requester_id' => $requesters->random()->id,
                'assigned_to' => $agents->random()->id,
                'category_id' => $categories->random()->id,
                'status' => $status,
                'priority' => $priority,
                'title' => 'Random support request #'.$i,
                'body' => 'Detailed description of issue #'.$i.' requiring agent attention.',
                'first_responded_at' => $status !== TicketStatus::New ? now()->subHours(2) : null,
                'resolved_at' => $status === TicketStatus::Resolved ? now() : null,
            ]);

            $this->addRandomMessages($ticket, $ticket->requester_id, $ticket->assigned_to);
        }
    }

    /**
     * Adds 2-4 messages to a ticket, mixing public replies and internal notes.
     */
    private function addRandomMessages(Ticket $ticket, int $requesterId, ?int $agentId = null): void
    {
        $count = random_int(2, 4);
        foreach (range(1, $count) as $i) {
            $isInternal = random_int(0, 4) === 1; // 20% chance of internal note
            $authorId = ($i % 2 === 0 && $agentId) ? $agentId : $requesterId;

            TicketMessage::query()->create([
                'ticket_id' => $ticket->id,
                'author_id' => $authorId,
                'type' => $isInternal ? TicketMessageType::Internal : TicketMessageType::Public,
                'body' => 'Discussion message #'.$i.' for ticket context.',
            ]);
        }
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [TicketSeeder.php], the next logical file
 * to read is:
 *
 * → [database/seeders/AuditLogSeeder.php]
 *
 * WHY: After defining the core action, we define the logs that
 *       track how that action occurred.
 * ============================================================
 */
