<?php

declare(strict_types=1);

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Livewire\Agent\TriageQueue;
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

it('agent can view triage queue', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);

    $this->actingAs($agent)
        ->get(route('agent.queue'))
        ->assertOk();
});

it('triage queue shows only unassigned new tickets', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);

    // 3 Unassigned New tickets
    $new1 = Ticket::factory()->create(['status' => TicketStatus::New->value, 'assigned_to' => null]);
    $new2 = Ticket::factory()->create(['status' => TicketStatus::New->value, 'assigned_to' => null]);
    $new3 = Ticket::factory()->create(['status' => TicketStatus::New->value, 'assigned_to' => null]);

    // 2 Assigned tickets (even if New)
    $assigned1 = Ticket::factory()->create(['status' => TicketStatus::New->value, 'assigned_to' => $agent->id]);
    $assigned2 = Ticket::factory()->create(['status' => TicketStatus::New->value, 'assigned_to' => $agent->id]);

    // 1 Triaged ticket (even if Unassigned)
    $triaged = Ticket::factory()->create(['status' => TicketStatus::Triaged->value, 'assigned_to' => null]);

    $this->actingAs($agent)
        ->get(route('agent.queue'))
        ->assertSee($new1->title)
        ->assertSee($new2->title)
        ->assertSee($new3->title)
        ->assertDontSee($assigned1->title)
        ->assertDontSee($assigned2->title)
        ->assertDontSee($triaged->title);
});

it('requester cannot access triage queue', function (): void {
    $requester = User::factory()->create(['role' => UserRole::Requester]);

    $this->actingAs($requester)
        ->get(route('agent.queue'))
        ->assertRedirect();
});

it('agent can assign ticket to themselves from queue', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create(['status' => TicketStatus::New->value, 'assigned_to' => null]);

    $this->actingAs($agent);

    Livewire::test(TriageQueue::class)
        ->call('assignToSelf', $ticket->id)
        ->assertDispatched('ticket-assigned');

    $this->assertDatabaseHas('tickets', [
        'id' => $ticket->id,
        'assigned_to' => $agent->id,
        'status' => TicketStatus::Triaged->value,
    ]);

    $this->assertDatabaseHas('audit_logs', [
        'action' => 'ticket.assigned',
        'ticket_id' => $ticket->id,
        'user_id' => $agent->id,
    ]);
});
