<?php

declare(strict_types=1);

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Livewire\Agent\MyTickets;
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

it('agent can view my tickets', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);

    $this->actingAs($agent)
        ->get(route('agent.my-tickets'))
        ->assertOk();
});

it('my tickets shows only assigned tickets and omits resolved tickets', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);

    $assigned1 = Ticket::factory()->create(['status' => TicketStatus::Triaged->value, 'assigned_to' => $agent->id]);
    $assigned2 = Ticket::factory()->create(['status' => TicketStatus::InProgress->value, 'assigned_to' => $agent->id]);

    $resolved = Ticket::factory()->create(['status' => TicketStatus::Resolved->value, 'assigned_to' => $agent->id]);

    $agent2 = User::factory()->create(['role' => UserRole::Agent]);
    $otherAssigned = Ticket::factory()->create(['status' => TicketStatus::Triaged->value, 'assigned_to' => $agent2->id]);

    $unassigned = Ticket::factory()->create(['status' => TicketStatus::New->value, 'assigned_to' => null]);

    $this->actingAs($agent)
        ->get(route('agent.my-tickets'))
        ->assertSee($assigned1->title)
        ->assertSee($assigned2->title)
        ->assertDontSee($resolved->title)
        ->assertDontSee($otherAssigned->title)
        ->assertDontSee($unassigned->title);
});

it('requester cannot access agent my tickets', function (): void {
    $requester = User::factory()->create(['role' => UserRole::Requester]);

    $this->actingAs($requester)
        ->get(route('agent.my-tickets'))
        ->assertRedirect();
});

it('agent search works in my tickets', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $assigned1 = Ticket::factory()->create(['title' => 'Software bug', 'status' => TicketStatus::Triaged->value, 'assigned_to' => $agent->id]);
    $assigned2 = Ticket::factory()->create(['title' => 'Hardware issue', 'status' => TicketStatus::Triaged->value, 'assigned_to' => $agent->id]);

    $this->actingAs($agent);

    Livewire::test(MyTickets::class)
        ->set('search', 'Software')
        ->assertSee($assigned1->title)
        ->assertDontSee($assigned2->title);
});
