<?php

declare(strict_types=1);

/**
 * ============================================================
 * FILE: TicketPolicyTest.php
 * LAYER: Test
 * ============================================================
 *
 * WHAT IS THIS?
 * A Pest feature test suite verifying the rules defined in TicketPolicy.
 *
 * WHY DOES IT EXIST?
 * To ensure that the business rules from the HLD (§8.2) regarding who can
 * read and modify tickets are actually enforced by the system.
 *
 * HOW IT FITS IN THE APP:
 * It executes automatically in CI whenever code changes are made.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Feature Tests allow us to use Laravel's models and factories to create
 * temporary states in the testing database. We can "act as" a specific user
 * and ensure that the Gate facade allows or denies specific actions.
 * ============================================================
 */

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

// Verifies HLD rule: Requester viewing own tickets
it('allows requester to view their own ticket', function (): void {
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = Ticket::factory()->create(['requester_id' => $requester->id]);

    $this->actingAs($requester);

    expect(Gate::allows('view', $ticket))->toBeTrue();
});

// Verifies HLD rule: Requester cannot view other's tickets
it('denies requester from viewing another users ticket', function (): void {
    $requester1 = User::factory()->create(['role' => UserRole::Requester]);
    $requester2 = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = Ticket::factory()->create(['requester_id' => $requester2->id]);

    $this->actingAs($requester1);

    expect(Gate::allows('view', $ticket))->toBeFalse();
});

// Verifies HLD rule: Agents can view unassigned New tickets
it('allows agent to view an unassigned new ticket', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create([
        'status' => TicketStatus::New,
        'assigned_to' => null,
    ]);

    $this->actingAs($agent);

    expect(Gate::allows('view', $ticket))->toBeTrue();
});

// Verifies HLD rule: Agents can view tickets assigned to them
it('allows agent to view a ticket assigned to them', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create([
        'assigned_to' => $agent->id,
    ]);

    $this->actingAs($agent);

    expect(Gate::allows('view', $ticket))->toBeTrue();
});

// Verifies HLD rule: Agents cannot view unassigned tickets if not New, nor assigned to others
it('denies agent from viewing a ticket assigned to someone else that is not new', function (): void {
    $agent1 = User::factory()->create(['role' => UserRole::Agent]);
    $agent2 = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create([
        'status' => TicketStatus::InProgress,
        'assigned_to' => $agent2->id,
    ]);

    $this->actingAs($agent1);

    expect(Gate::allows('view', $ticket))->toBeFalse();
});

// Verifies HLD rule: Admin bypasses all checks via before()
it('allows admin to view any ticket', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $ticket = Ticket::factory()->create(['assigned_to' => null, 'status' => TicketStatus::InProgress]);

    $this->actingAs($admin);

    expect(Gate::allows('view', $ticket))->toBeTrue();
});

// Verifies HLD rule: Anyone can create
it('allows any user to create a ticket', function (): void {
    $user = User::factory()->create(['role' => UserRole::Requester]);

    $this->actingAs($user);

    expect(Gate::allows('create', Ticket::class))->toBeTrue();
});

// Verifies HLD rule: Agents and Admins can update tickets
it('allows agent to update a ticket', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create();

    $this->actingAs($agent);

    expect(Gate::allows('update', $ticket))->toBeTrue();
});

// Verifies HLD rule: Requester cannot update a ticket
it('denies requester from updating a ticket', function (): void {
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = Ticket::factory()->create(['requester_id' => $requester->id]);

    $this->actingAs($requester);

    expect(Gate::allows('update', $ticket))->toBeFalse();
});

// Verifies HLD constraint: Ticket deletion not required
it('denies all roles from deleting a ticket', function (): void {
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket1 = Ticket::factory()->create(['requester_id' => $requester->id]);
    $ticket2 = Ticket::factory()->create();

    $this->actingAs($requester);
    expect(Gate::allows('delete', $ticket1))->toBeFalse();

    $this->actingAs($agent);
    expect(Gate::allows('delete', $ticket2))->toBeFalse();
});

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand TicketPolicyTest.php, the next logical file
 * to read is:
 *
 * → tests/Feature/Policies/TicketMessagePolicyTest.php
 *
 * WHY: Verifying ticket message policies continues the process of validating
 * the core rules established around tickets and threads.
 * ============================================================
 */
