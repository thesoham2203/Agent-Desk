<?php

declare(strict_types=1);

/**
 * ============================================================
 * FILE: AttachmentPolicyTest.php
 * LAYER: Test
 * ============================================================
 *
 * WHAT IS THIS?
 * Verifies the AttachmentPolicy logic.
 *
 * WHY DOES IT EXIST?
 * Secures file downloads, making sure a requester cannot steal files from
 * a ticket they do not own.
 *
 * HOW IT FITS IN THE APP:
 * Part of the testing suite.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Feature Tests allow us to use Laravel's models and factories to create
 * temporary states in the testing database. We can "act as" a specific user
 * and ensure that the Gate facade allows or denies specific actions.
 * ============================================================
 */

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Attachment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

// Verifies HLD Module B: Requester can download on own ticket
it('allows requester to download attachment on their own ticket', function (): void {
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = Ticket::factory()->create(['requester_id' => $requester->id]);
    $attachment = Attachment::factory()->create(['ticket_id' => $ticket->id]);

    $this->actingAs($requester);

    expect(Gate::allows('view', $attachment))->toBeTrue();
});

// Verifies HLD Module B: Requester denied on others' ticket
it('denies requester from downloading attachment on another users ticket', function (): void {
    $requester1 = User::factory()->create(['role' => UserRole::Requester]);
    $requester2 = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = Ticket::factory()->create(['requester_id' => $requester2->id]);
    $attachment = Attachment::factory()->create(['ticket_id' => $ticket->id]);

    $this->actingAs($requester1);

    expect(Gate::allows('view', $attachment))->toBeFalse();
});

// Verifies HLD Module B: Agents can download attachments (assuming they can view the ticket)
it('allows agent to download any attachment', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create(['status' => TicketStatus::New, 'assigned_to' => null]);
    $attachment = Attachment::factory()->create(['ticket_id' => $ticket->id]);

    $this->actingAs($agent);

    expect(Gate::allows('view', $attachment))->toBeTrue();
});

// Verifies HLD §9.1: Requester can delete while ticket is New
it('allows requester to delete attachment while ticket is new', function (): void {
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = Ticket::factory()->create(['requester_id' => $requester->id, 'status' => TicketStatus::New]);
    $attachment = Attachment::factory()->create(['ticket_id' => $ticket->id]);

    $this->actingAs($requester);

    expect(Gate::allows('delete', $attachment))->toBeTrue();
});

// Verifies HLD §9.1: Requester cannot delete attachment after New
it('denies requester from deleting attachment after ticket is no longer new', function (): void {
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = Ticket::factory()->create(['requester_id' => $requester->id, 'status' => TicketStatus::InProgress]);
    $attachment = Attachment::factory()->create(['ticket_id' => $ticket->id]);

    $this->actingAs($requester);

    expect(Gate::allows('delete', $attachment))->toBeFalse();
});

// Verifies HLD §9.1: Agents can always delete attachments
it('allows agent to delete any attachment', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create();
    $attachment = Attachment::factory()->create(['ticket_id' => $ticket->id]);

    $this->actingAs($agent);

    expect(Gate::allows('delete', $attachment))->toBeTrue();
});

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand AttachmentPolicyTest.php, the next logical file
 * to read is:
 *
 * → tests/Feature/Policies/AdminPolicyTest.php
 *
 * WHY: The final test file bunches the strictly admin policies together,
 * proving that administrative functions remain secured.
 * ============================================================
 */
