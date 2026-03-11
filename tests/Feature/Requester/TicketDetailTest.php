<?php

declare(strict_types=1);

use App\Enums\TicketMessageType;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Livewire\Requester\TicketDetail;
use App\Models\Ticket;
use App\Models\TicketMessage;
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

it('requester can view their own ticket detail', function (): void {
    /** @var User $requester */
    $requester = User::factory()->create(['role' => UserRole::Requester]);

    $ticket = Ticket::factory()->create([
        'requester_id' => $requester->id,
        'title' => 'My secret ticket',
    ]);

    Livewire::actingAs($requester)
        ->test(TicketDetail::class, ['ticketId' => $ticket->id])
        ->assertSee('My secret ticket');
});

it('requester cannot view another users ticket', function (): void {
    /** @var User $requesterA */
    $requesterA = User::factory()->create(['role' => UserRole::Requester]);

    /** @var User $requesterB */
    $requesterB = User::factory()->create(['role' => UserRole::Requester]);

    $ticketA = Ticket::factory()->create(['requester_id' => $requesterA->id]);

    $this->actingAs($requesterB)
        ->get(route('requester.tickets.show', ['ticketId' => $ticketA->id]))
        ->assertForbidden();
});

it('requester can post a public reply', function (): void {
    /** @var User $requester */
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = Ticket::factory()->create(['requester_id' => $requester->id]);

    Livewire::actingAs($requester)
        ->test(TicketDetail::class, ['ticketId' => $ticket->id])
        ->set('replyBody', 'This is a new public reply.')
        ->call('postReply')
        ->assertHasNoErrors()
        ->assertSet('replyBody', '')
        ->assertSet('replySent', true);

    $this->assertDatabaseHas('ticket_messages', [
        'ticket_id' => $ticket->id,
        'type' => TicketMessageType::Public->value,
        'body' => 'This is a new public reply.',
    ]);
});

it('requester cannot post reply on resolved ticket', function (): void {
    /** @var User $requester */
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = Ticket::factory()->create([
        'requester_id' => $requester->id,
        'status' => TicketStatus::Resolved->value,
    ]);

    Livewire::actingAs($requester)
        ->test(TicketDetail::class, ['ticketId' => $ticket->id])
        ->set('replyBody', 'Trying to reply to resolved')
        ->call('postReply')
        ->assertForbidden();
});

it('requester cannot see internal notes in thread', function (): void {
    /** @var User $requester */
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = Ticket::factory()->create(['requester_id' => $requester->id]);

    /** @var User $agent */
    $agent = User::factory()->create(['role' => UserRole::Agent]);

    TicketMessage::factory()->create([
        'ticket_id' => $ticket->id,
        'author_id' => $agent->id,
        'body' => 'Public agent response',
        'type' => TicketMessageType::Public->value,
    ]);

    TicketMessage::factory()->create([
        'ticket_id' => $ticket->id,
        'author_id' => $agent->id,
        'body' => 'Secret internal note',
        'type' => TicketMessageType::Internal->value,
    ]);

    Livewire::actingAs($requester)
        ->test(TicketDetail::class, ['ticketId' => $ticket->id])
        ->assertSee('Public agent response')
        ->assertDontSee('Secret internal note');
});

it('requester cannot post reply on another users ticket', function (): void {
    /** @var User $requesterA */
    $requesterA = User::factory()->create(['role' => UserRole::Requester]);

    /** @var User $requesterB */
    $requesterB = User::factory()->create(['role' => UserRole::Requester]);

    $ticketA = Ticket::factory()->create(['requester_id' => $requesterA->id]);

    Livewire::actingAs($requesterB)
        ->test(TicketDetail::class, ['ticketId' => $ticketA->id])
        ->assertForbidden();
});
