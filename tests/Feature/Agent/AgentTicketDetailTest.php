<?php

declare(strict_types=1);

use App\Enums\TicketMessageType;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Livewire\Agent\TicketDetail;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Queue::fake();
    Category::factory()->create(); // Ensure we have a category for the factories
});

it('agent can view ticket assigned to them', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create(['assigned_to' => $agent->id]);

    $this->actingAs($agent)
        ->get(route('agent.tickets.show', $ticket))
        ->assertOk()
        ->assertSee($ticket->title);
});

it('agent can view unassigned new ticket', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create(['status' => TicketStatus::New->value, 'assigned_to' => null]);

    $this->actingAs($agent)
        ->get(route('agent.tickets.show', $ticket))
        ->assertOk()
        ->assertSee($ticket->title);
});

it('agent cannot view ticket assigned to different agent that is not new', function (): void {
    $agent1 = User::factory()->create(['role' => UserRole::Agent]);
    $agent2 = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create(['status' => TicketStatus::InProgress->value, 'assigned_to' => $agent2->id]);

    $this->actingAs($agent1)
        ->get(route('agent.tickets.show', $ticket))
        ->assertForbidden();
});

it('agent can change ticket status', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create(['status' => TicketStatus::New->value, 'assigned_to' => $agent->id]);

    $this->actingAs($agent);

    Livewire::test(TicketDetail::class, ['ticket' => $ticket])
        ->set('newStatus', TicketStatus::Triaged->value)
        ->call('updateStatus');

    $this->assertDatabaseHas('tickets', [
        'id' => $ticket->id,
        'status' => TicketStatus::Triaged->value,
    ]);

    $this->assertDatabaseHas('audit_logs', [
        'action' => 'status.changed',
        'ticket_id' => $ticket->id,
        'user_id' => $agent->id,
    ]);
});

it('invalid status transition throws exception', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create(['status' => TicketStatus::Resolved->value, 'assigned_to' => $agent->id]);

    $this->actingAs($agent);

    $component = Livewire::test(TicketDetail::class, ['ticket' => $ticket])
        ->set('newStatus', TicketStatus::New->value);

    // In Livewire testing, authorization failures often result in a 403 Forbidden status.
    $component->call('updateStatus')
        ->assertForbidden();
});

it('agent can add internal note', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create(['assigned_to' => $agent->id]);

    $this->actingAs($agent);

    $component = Livewire::test(TicketDetail::class, ['ticket' => $ticket])
        ->set('noteBody', 'This is an internal note.')
        ->call('addInternalNote');

    $component->assertSet('noteSaved', true);

    $this->assertDatabaseHas('ticket_messages', [
        'ticket_id' => $ticket->id,
        'author_id' => $agent->id,
        'type' => TicketMessageType::Internal->value,
        'body' => 'This is an internal note.',
    ]);

    $this->assertDatabaseHas('audit_logs', [
        'action' => 'internal.note.added',
        'ticket_id' => $ticket->id,
        'user_id' => $agent->id,
    ]);
});

it('agent can post public reply', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create(['assigned_to' => $agent->id]);

    $this->actingAs($agent);

    Livewire::test(TicketDetail::class, ['ticket' => $ticket])
        ->set('replyBody', 'This is a public reply.')
        ->call('postReply')
        ->assertSet('replySent', true);

    $this->assertDatabaseHas('ticket_messages', [
        'ticket_id' => $ticket->id,
        'author_id' => $agent->id,
        'type' => TicketMessageType::Public->value,
        'body' => 'This is a public reply.',
    ]);
});

it('requester cannot access agent ticket detail route', function (): void {
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = Ticket::factory()->create(['requester_id' => $requester->id]);

    $this->actingAs($requester)
        ->get(route('agent.tickets.show', $ticket))
        ->assertRedirect();
});
