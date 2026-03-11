<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketAssignedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('renders the notification bell component', function (): void {
    Date::setTestNow(now());
    $user = User::factory()->create();
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $user->notify(new TicketAssignedNotification(
        Ticket::factory()->create(),
        $admin
    ));

    Volt::actingAs($user)
        ->test('layout.notification-bell')
        ->assertSee('Recent Notifications')
        ->assertSee($user->notifications()->first()->created_at->diffForHumans(short: true));
});
