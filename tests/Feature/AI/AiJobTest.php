<?php

declare(strict_types=1);
use App\AI\Agents\ReplyDraftAgent;
use App\AI\Agents\TriageAgent;
use App\Enums\AiRunStatus;
use App\Enums\AiRunType;
use App\Enums\UserRole;
use App\Jobs\DraftTicketReplyJob;
use App\Jobs\RunTicketTriageJob;
use App\Livewire\Agent\AiPanel;
use App\Models\AiRun;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // We must fake each agent specifically since we are using Promptable
    TriageAgent::fake([
        json_encode([
            'category' => 'Technical',
            'priority' => 'medium',
            'summary' => 'Test ticket',
            'tags' => [],
            'escalation_flag' => false,
            'clarifying_question' => '',
        ]),
    ]);

    ReplyDraftAgent::fake([
        json_encode([
            'draft' => 'Test draft',
            'next_steps' => [],
            'risk_flags' => [],
        ]),
    ]);
});

it('run ticket triage job updates ai run status to running then succeeded', function (): void {
    // 1. Arrange
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['requester_id' => $user->id]);
    $aiRun = AiRun::factory()->create([
        'ticket_id' => $ticket->id,
        'run_type' => AiRunType::Triage,
        'status' => AiRunStatus::Queued,
        'initiated_by_user_id' => $user->id,
    ]);

    $job = new RunTicketTriageJob($aiRun->id);

    // 2. Act
    $job->handle();

    // 3. Assert
    expect(AiRun::query()->find($aiRun->id)->status)->toBe(AiRunStatus::Succeeded);
    expect(AiRun::query()->find($aiRun->id)->output_json['category'])->toBe('Technical');
});

it('run ticket triage job sets status to failed on exception', function (): void {
    // 1. Arrange
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['requester_id' => $user->id]);
    $aiRun = AiRun::factory()->create([
        'ticket_id' => $ticket->id,
        'run_type' => AiRunType::Triage,
        'status' => AiRunStatus::Queued,
        'initiated_by_user_id' => $user->id,
    ]);

    // Force failure via invalid JSON since AI is called inside handle()
    TriageAgent::fake(['invalid json']);

    $job = new RunTicketTriageJob($aiRun->id);

    // 2. Act & Assert
    expect(fn () => $job->handle())->toThrow(RuntimeException::class);

    expect(AiRun::query()->find($aiRun->id)->status)->toBe(AiRunStatus::Failed);
});

it('run ticket triage job skips duplicate runs using input_hash', function (): void {
    // 1. Arrange
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create([
        'requester_id' => $user->id,
        'title' => 'Dedupe issue',
    ]);

    $inputHash = hash('sha256', $ticket->title.$ticket->body.($ticket->category?->name ?? ''));

    AiRun::factory()->create([
        'ticket_id' => $ticket->id,
        'run_type' => AiRunType::Triage,
        'status' => AiRunStatus::Succeeded,
        'input_hash' => $inputHash,
        'output_json' => ['cached' => true],
        'initiated_by_user_id' => $user->id,
    ]);

    $aiRun = AiRun::factory()->create([
        'ticket_id' => $ticket->id,
        'run_type' => AiRunType::Triage,
        'status' => AiRunStatus::Queued,
        'initiated_by_user_id' => $user->id,
    ]);

    $job = new RunTicketTriageJob($aiRun->id);

    // 2. Act
    $job->handle();

    // 3. Assert
    $aiRun->refresh();
    expect($aiRun->status)->toBe(AiRunStatus::Succeeded);
    expect($aiRun->output_json['cached'])->toBeTrue();
});

it('rate limiter prevents more than 5 triage requests', function (): void {
    // 1. Arrange
    $agentUser = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create(['requester_id' => User::factory()->create()]);

    $this->actingAs($agentUser);
    Queue::fake();

    // 2. Act
    $component = Livewire::test(AiPanel::class, ['ticketId' => $ticket->id]);

    for ($i = 0; $i < 5; $i++) {
        $component->call('runTriage');
    }

    $component->call('runTriage')
        ->assertSet('polling', true)
        ->assertSee('Too many AI requests');

    // 3. Assert
    Queue::assertPushed(RunTicketTriageJob::class, 5);
    expect(AiRun::query()->count())->toBe(5);
});

it('ai panel run triage creates ai run with correct run type', function (): void {
    $agentUser = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create(['requester_id' => User::factory()->create()]);

    $this->actingAs($agentUser);
    Queue::fake();

    Livewire::test(AiPanel::class, ['ticketId' => $ticket->id])
        ->call('runTriage');

    $this->assertDatabaseHas('ai_runs', [
        'ticket_id' => $ticket->id,
        'run_type' => AiRunType::Triage->value,
        'status' => AiRunStatus::Queued->value,
    ]);
});

it('draft ticket reply job sets status to failed on exception', function (): void {
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create(['requester_id' => $user->id]);
    $aiRun = AiRun::factory()->create([
        'ticket_id' => $ticket->id,
        'run_type' => AiRunType::ReplyDraft,
        'status' => AiRunStatus::Queued,
        'initiated_by_user_id' => $user->id,
    ]);

    // Force failure via invalid JSON
    ReplyDraftAgent::fake(['invalid json']);

    $job = new DraftTicketReplyJob($aiRun->id);

    expect(fn () => $job->handle())->toThrow(RuntimeException::class);

    expect(AiRun::query()->find($aiRun->id)->status)->toBe(AiRunStatus::Failed);
});
