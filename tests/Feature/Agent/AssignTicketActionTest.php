<?php

declare(strict_types=1);

use App\Actions\AssignTicketAction;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Queue::fake();
    Category::factory()->create(); // Ensure we have a category for the factories
});

it('can assign a ticket to an agent', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create(['status' => TicketStatus::New->value, 'assigned_to' => null]);

    $action = new AssignTicketAction();
    $action->execute($admin, $ticket, $agent);

    expect($ticket->assigned_to)->toBe($agent->id)
        ->and($ticket->status)->toBe(TicketStatus::Triaged);

    $this->assertDatabaseHas('audit_logs', [
        'action' => 'ticket.assigned',
        'ticket_id' => $ticket->id,
        'user_id' => $admin->id,
    ]);
});

it('can unassign a ticket', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create(['status' => TicketStatus::Triaged->value, 'assigned_to' => $agent->id]);

    $action = new AssignTicketAction();
    $action->execute($admin, $ticket, null);

    expect($ticket->assigned_to)->toBeNull();

    $this->assertDatabaseHas('audit_logs', [
        'action' => 'ticket.unassigned',
        'ticket_id' => $ticket->id,
        'user_id' => $admin->id,
    ]);
});

it('automatically triages a new ticket when assigned', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create(['status' => TicketStatus::New->value, 'assigned_to' => null]);

    $action = new AssignTicketAction();
    $action->execute($agent, $ticket, $agent);

    expect($ticket->status)->toBe(TicketStatus::Triaged);
});

it('agent cannot assign ticket to a requester user', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = Ticket::factory()->create(['status' => TicketStatus::New->value, 'assigned_to' => null]);

    $action = new AssignTicketAction();

    $this->expectException(InvalidArgumentException::class);
    $action->execute($admin, $ticket, $requester);
});
