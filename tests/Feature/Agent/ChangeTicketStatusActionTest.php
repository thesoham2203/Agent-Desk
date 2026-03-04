<?php

declare(strict_types=1);

use App\Actions\ChangeTicketStatusAction;
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

it('allows New to Triaged transition', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create(['status' => TicketStatus::New->value]);

    $action = new ChangeTicketStatusAction();
    $updatedTicket = $action->execute($agent, $ticket, TicketStatus::Triaged);

    expect($updatedTicket->status)->toBe(TicketStatus::Triaged);
});

it('allows InProgress to Resolved transition', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create(['status' => TicketStatus::InProgress->value]);

    $action = new ChangeTicketStatusAction();
    $updatedTicket = $action->execute($agent, $ticket, TicketStatus::Resolved);

    expect($updatedTicket->status)->toBe(TicketStatus::Resolved);
});

it('denies Resolved to New transition', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create(['status' => TicketStatus::Resolved->value]);

    $action = new ChangeTicketStatusAction();

    expect(fn (): Ticket => $action->execute($agent, $ticket, TicketStatus::New))
        ->toThrow(DomainException::class);
});

it('denies New to Resolved transition', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create(['status' => TicketStatus::New->value]);

    $action = new ChangeTicketStatusAction();

    expect(fn (): Ticket => $action->execute($agent, $ticket, TicketStatus::Resolved))
        ->toThrow(DomainException::class);
});

it('sets resolved_at when resolving ticket', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create(['status' => TicketStatus::InProgress->value, 'resolved_at' => null]);

    $action = new ChangeTicketStatusAction();
    $updatedTicket = $action->execute($agent, $ticket, TicketStatus::Resolved);

    expect($updatedTicket->status)->toBe(TicketStatus::Resolved)
        ->and($updatedTicket->resolved_at)->not->toBeNull();
});
