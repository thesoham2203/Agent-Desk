<?php

declare(strict_types=1);

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Livewire\Requester\MyTicketsTable;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Storage::fake('private');
    Queue::fake();
});

it('requester sees only their own tickets', function (): void {
    /** @var User $requesterA */
    $requesterA = User::factory()->create(['role' => UserRole::Requester]);

    /** @var User $requesterB */
    $requesterB = User::factory()->create(['role' => UserRole::Requester]);

    Ticket::factory()->count(3)->create([
        'requester_id' => $requesterA->id,
        'title' => 'Ticket for A',
    ]);

    Ticket::factory()->count(2)->create([
        'requester_id' => $requesterB->id,
        'title' => 'Ticket for B',
    ]);

    Livewire::actingAs($requesterA)
        ->test(MyTicketsTable::class)
        ->assertSee('Ticket for A')
        ->assertDontSee('Ticket for B');
});

it('requester can search their tickets by title', function (): void {
    /** @var User $requester */
    $requester = User::factory()->create(['role' => UserRole::Requester]);

    Ticket::factory()->create(['requester_id' => $requester->id, 'title' => 'Apple issue']);
    Ticket::factory()->create(['requester_id' => $requester->id, 'title' => 'Banana problem']);

    Livewire::actingAs($requester)
        ->test(MyTicketsTable::class)
        ->set('search', 'Apple')
        ->assertSee('Apple issue')
        ->assertDontSee('Banana problem');
});

it('requester can filter tickets by status', function (): void {
    /** @var User $requester */
    $requester = User::factory()->create(['role' => UserRole::Requester]);

    Ticket::factory()->create([
        'requester_id' => $requester->id,
        'title' => 'New ticket',
        'status' => TicketStatus::New->value,
    ]);

    Ticket::factory()->create([
        'requester_id' => $requester->id,
        'title' => 'Resolved ticket',
        'status' => TicketStatus::Resolved->value,
    ]);

    Livewire::actingAs($requester)
        ->test(MyTicketsTable::class)
        ->set('statusFilter', TicketStatus::Resolved->value)
        ->assertSee('Resolved ticket')
        ->assertDontSee('New ticket');
});
