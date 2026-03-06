<?php

declare(strict_types=1);

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Jobs\CheckOverdueTargetsJob;
use App\Models\AuditLog;
use App\Models\SlaConfig;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketOverdueNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();
    // Ensure SLA config is created for the tests
    SlaConfig::factory()->create([
        'first_response_hours' => 4,
        'resolution_hours' => 24,
    ]);
});

it('finds tickets overdue for first response and notifies admins', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    // Create an overdue ticket (created 10 hours ago, no first response)
    $overdueTicket = Ticket::factory()->create([
        'created_at' => now()->subHours(10),
        'first_responded_at' => null,
        'status' => TicketStatus::New,
    ]);

    // Handle the job
    new CheckOverdueTargetsJob()->handle();

    // Verify notifications sent to admin
    Notification::assertSentTo($admin, TicketOverdueNotification::class, fn ($notification): bool => $notification->ticket->id === $overdueTicket->id &&
        $notification->overdueType === 'first_response');
});

it('does not notify for resolved tickets', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    // Resolved ticket created 10 hours ago
    Ticket::factory()->create([
        'created_at' => now()->subHours(10),
        'status' => TicketStatus::Resolved,
        'resolved_at' => now()->subHours(5),
    ]);

    new CheckOverdueTargetsJob()->handle();

    Notification::assertNothingSent();
});

it('notifies assigned agent for overdue ticket', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);

    $overdueTicket = Ticket::factory()->create([
        'created_at' => now()->subHours(10),
        'first_responded_at' => null,
        'assigned_to' => $agent->id,
        'status' => TicketStatus::Triaged,
    ]);

    new CheckOverdueTargetsJob()->handle();

    Notification::assertSentTo($agent, TicketOverdueNotification::class);
});

it('creates audit log for each overdue ticket', function (): void {
    User::factory()->create(['role' => UserRole::Admin]); // To ensure there is a user for the log if no assignee

    Ticket::factory()->create([
        'created_at' => now()->subHours(10),
        'first_responded_at' => null,
        'status' => TicketStatus::New,
    ]);

    new CheckOverdueTargetsJob()->handle();

    expect(AuditLog::query()->where('action', 'sla.first_response.breached')->exists())->toBeTrue();
});

it('does not notify if ticket has first_responded_at set', function (): void {
    User::factory()->create(['role' => UserRole::Admin]);

    Ticket::factory()->create([
        'created_at' => now()->subHours(10),
        'first_responded_at' => now()->subHours(9),
        'status' => TicketStatus::Triaged,
    ]);

    new CheckOverdueTargetsJob()->handle();

    Notification::assertNothingSent();
});
