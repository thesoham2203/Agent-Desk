<?php

declare(strict_types=1);

use App\Actions\AssignTicketAction;
use App\Actions\ChangeTicketStatusAction;
use App\Actions\PostReplyAction;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\RequesterRepliedNotification;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketResolvedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();
});

it('agent is notified when ticket is assigned to them', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create(['status' => TicketStatus::New]);

    new AssignTicketAction()->execute($admin, $ticket, $agent);

    Notification::assertSentTo($agent, TicketAssignedNotification::class);
});

it('agent is notified when requester replies', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = Ticket::factory()->create([
        'requester_id' => $requester->id,
        'assigned_to' => $agent->id,
        'status' => TicketStatus::InProgress,
    ]);

    new PostReplyAction()->execute($requester, $ticket, 'This is a reply');

    Notification::assertSentTo($agent, RequesterRepliedNotification::class);
});

it('agent replying does NOT trigger RequesterRepliedNotification', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create([
        'assigned_to' => $agent->id,
        'status' => TicketStatus::InProgress,
    ]);

    new PostReplyAction()->execute($agent, $ticket, 'Agent reply');

    Notification::assertNotSentTo($agent, RequesterRepliedNotification::class);
});

it('requester is notified when ticket is resolved', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = Ticket::factory()->create([
        'requester_id' => $requester->id,
        'status' => TicketStatus::InProgress,
    ]);

    new ChangeTicketStatusAction()->execute($admin, $ticket, TicketStatus::Resolved);

    Notification::assertSentTo($requester, TicketResolvedNotification::class);
});

it('first_responded_at is set when agent replies', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create([
        'status' => TicketStatus::InProgress,
        'first_responded_at' => null,
    ]);

    new PostReplyAction()->execute($agent, $ticket, 'Agent reply');

    expect($ticket->fresh()->first_responded_at)->not->toBeNull();
});

it('first_responded_at is NOT set when requester replies', function (): void {
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = Ticket::factory()->create([
        'requester_id' => $requester->id,
        'status' => TicketStatus::InProgress,
        'first_responded_at' => null,
    ]);

    new PostReplyAction()->execute($requester, $ticket, 'Requester reply');

    expect($ticket->fresh()->first_responded_at)->toBeNull();
});

it('TicketResolvedNotification contains correct ticket title', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $requester = User::factory()->create(['role' => UserRole::Requester]);

    $ticket = Ticket::factory()->create([
        'requester_id' => $requester->id,
        'title' => 'My resolve title test',
        'status' => TicketStatus::InProgress,
    ]);

    new ChangeTicketStatusAction()->execute($admin, $ticket, TicketStatus::Resolved);

    Notification::assertSentTo($requester, TicketResolvedNotification::class, function ($notification) use ($requester): bool {
        $data = $notification->toDatabase($requester);

        return $data['ticket_title'] === 'My resolve title test';
    });
});
