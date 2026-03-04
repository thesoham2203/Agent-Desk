<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Livewire\Requester\TicketCreateForm;
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

it('requester can create a ticket', function (): void {
    /** @var User $requester */
    $requester = User::factory()->create(['role' => UserRole::Requester]);

    Livewire::actingAs($requester)
        ->test(TicketCreateForm::class)
        ->set('title', 'Valid Ticket Title')
        ->set('body', 'This is a valid body with more than 10 characters.')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(); // should redirect to the show page

    $this->assertDatabaseHas('tickets', [
        'title' => 'Valid Ticket Title',
    ]);

    // Verify job was dispatched without running it
    // TODO: Queue::assertPushed(RunTicketTriageJob::class);
});

it('ticket creation fails without a title', function (): void {
    /** @var User $requester */
    $requester = User::factory()->create(['role' => UserRole::Requester]);

    Livewire::actingAs($requester)
        ->test(TicketCreateForm::class)
        ->set('title', '')
        ->set('body', 'This is a valid body.')
        ->call('submit')
        ->assertHasErrors(['title']);
});

it('ticket creation fails with title shorter than 5 chars', function (): void {
    /** @var User $requester */
    $requester = User::factory()->create(['role' => UserRole::Requester]);

    Livewire::actingAs($requester)
        ->test(TicketCreateForm::class)
        ->set('title', 'abcd')
        ->set('body', 'This is a valid body.')
        ->call('submit')
        ->assertHasErrors(['title' => 'min']);
});

it('agent cannot access requester ticket create page', function (): void {
    /** @var User $agent */
    $agent = User::factory()->create(['role' => UserRole::Agent]);

    $response = $this->actingAs($agent)
        ->get(route('requester.tickets.create'));

    // Role middleware configures redirection/forbidden response
    if ($response->status() === 403) {
        $response->assertForbidden();
    } else {
        $response->assertRedirect();
    }
});
