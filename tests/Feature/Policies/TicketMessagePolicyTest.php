<?php

declare(strict_types=1);

/**
 * ============================================================
 * FILE: TicketMessagePolicyTest.php
 * LAYER: Test
 * ============================================================
 *
 * WHAT IS THIS?
 * Tests for the TicketMessagePolicy.
 *
 * WHY DOES IT EXIST?
 * To prove that requesters cannot access internal notes and that agents
 * possess the correct rights to view and create messages.
 *
 * HOW IT FITS IN THE APP:
 * Runs in the automated test suite.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Feature Tests allow us to use Laravel's models and factories to create
 * temporary states in the testing database. We can "act as" a specific user
 * and ensure that the Gate facade allows or denies specific actions.
 * ============================================================
 */

use App\Enums\TicketMessageType;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

// Verifies HLD rule: Agents can read internal notes
it('allows agent to view an internal note', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create([
        'status' => TicketStatus::New,
        'assigned_to' => null,
    ]); // Viewable by agent
    $message = TicketMessage::factory()->create([
        'ticket_id' => $ticket->id,
        'type' => TicketMessageType::Internal,
    ]);

    $this->actingAs($agent);

    expect(Gate::allows('view', $message))->toBeTrue();
});

// Verifies HLD rule: Requesters cannot read internal notes
it('denies requester from viewing an internal note', function (): void {
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = Ticket::factory()->create([
        'requester_id' => $requester->id,
    ]);
    $message = TicketMessage::factory()->create([
        'ticket_id' => $ticket->id,
        'type' => TicketMessageType::Internal,
    ]);

    $this->actingAs($requester);

    expect(Gate::allows('view', $message))->toBeFalse();
});

// Verifies HLD rule: Requesters can view public replies on their ticket
it('allows requester to view a public message on their own ticket', function (): void {
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = Ticket::factory()->create([
        'requester_id' => $requester->id,
    ]);
    $message = TicketMessage::factory()->create([
        'ticket_id' => $ticket->id,
        'type' => TicketMessageType::Public,
    ]);

    $this->actingAs($requester);

    expect(Gate::allows('view', $message))->toBeTrue();
});

// Verifies HLD rule: Agents can create messages on tickets they can view
it('allows agent to create a message on a visible ticket', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create([
        'status' => TicketStatus::New,
        'assigned_to' => null,
    ]); // Agent can view this ticket

    $this->actingAs($agent);

    expect(Gate::allows('create', [TicketMessage::class, $ticket]))->toBeTrue();
});

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand TicketMessagePolicyTest.php, the next logical file
 * to read is:
 *
 * → tests/Feature/Policies/AttachmentPolicyTest.php
 *
 * WHY: Checking attachment permission logic properly secures the
 * final portion of the core ticketing entity trio.
 * ============================================================
 */
