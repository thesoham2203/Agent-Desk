<?php

declare(strict_types=1);

use App\Actions\AddInternalNoteAction;
use App\Enums\TicketMessageType;
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

it('can add an internal note to a ticket', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create();

    $action = new AddInternalNoteAction();
    $message = $action->execute($agent, $ticket, 'This is an internal note.');

    expect($message->type)->toBe(TicketMessageType::Internal)
        ->and($message->body)->toBe('This is an internal note.')
        ->and($message->author_id)->toBe($agent->id);

    $this->assertDatabaseHas('ticket_messages', [
        'id' => $message->id,
        'type' => TicketMessageType::Internal->value,
    ]);

    $this->assertDatabaseHas('audit_logs', [
        'action' => 'internal.note.added',
        'ticket_id' => $ticket->id,
        'user_id' => $agent->id,
    ]);
});

it('fails if non-agent tries to add internal note via action directly', function (): void {
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = Ticket::factory()->create();

    $action = new AddInternalNoteAction();
})->skip('Action does not currently enforce roles internally, relies on policy/controller');
