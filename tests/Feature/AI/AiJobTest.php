<?php

declare(strict_types=1);

namespace Tests\Feature\AI;

use App\AI\Agents\ReplyDraftAgent;
use App\AI\Agents\TriageAgent;
use App\AI\DTOs\TriageResult;
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
use Mockery;
use RuntimeException;
use Tests\TestCase;

final class AiJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful job execution lifecycle including status updates.
     */
    public function test_run_ticket_triage_job_updates_ai_run_status_to_running_then_succeeded(): void
    {
        // 1. Arrange: Setup ticket and AiRun in 'Queued' state
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['requester_id' => $user->id]);
        $aiRun = AiRun::factory()->create([
            'ticket_id' => $ticket->id,
            'run_type' => AiRunType::Triage->value,
            'status' => AiRunStatus::Queued->value,
            'initiated_by_user_id' => $user->id,
        ]);

        // 2. Mock Agent to return success
        $mockAgent = Mockery::mock(TriageAgent::class);
        $mockAgent->shouldReceive('handle')
            ->once()
            ->andReturn(new TriageResult(
                category: 'Technical',
                priority: 'high',
                summary: 'Job summary test.',
                tags: ['test'],
                escalationFlag: false,
                clarifyingQuestion: ''
            ));

        $job = new RunTicketTriageJob($aiRun->id);

        // 3. Act: Manually call handle() (acting as a worker)
        $job->handle($mockAgent);

        // 4. Assert: Database matches expected outcomes
        $this->assertDatabaseHas('ai_runs', [
            'id' => $aiRun->id,
            'status' => AiRunStatus::Succeeded,
        ]);

        $aiRun->refresh();
        $this->assertEquals('Technical', $aiRun->output_json['category']);
        $this->assertEquals('Job summary test.', $aiRun->output_json['summary']);
    }

    /**
     * Test error handling when an agent fails.
     */
    public function test_run_ticket_triage_job_sets_status_to_failed_on_exception(): void
    {
        // 1. Arrange
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['requester_id' => $user->id]);
        $aiRun = AiRun::factory()->create([
            'ticket_id' => $ticket->id,
            'run_type' => AiRunType::Triage->value,
            'status' => AiRunStatus::Queued->value,
            'initiated_by_user_id' => $user->id,
        ]);

        // 2. Mock Agent to throw exception
        $mockAgent = Mockery::mock(TriageAgent::class);
        $mockAgent->shouldReceive('handle')
            ->once()
            ->andThrow(new RuntimeException('Connection timeout!'));

        $job = new RunTicketTriageJob($aiRun->id);

        // 2 & 3. Act & Assert: Should rethrow for queue system
        $this->expectException(RuntimeException::class);
        $job->handle($mockAgent);

        // 4. Assert: Status is updated to Failed in DB
        $this->assertDatabaseHas('ai_runs', [
            'id' => $aiRun->id,
            'status' => AiRunStatus::Failed,
            'error_message' => 'Connection timeout!',
        ]);
    }

    /**
     * Test input_hash deduplication logic.
     */
    public function test_run_ticket_triage_job_skips_duplicate_runs_using_input_hash(): void
    {
        // 1. Arrange: Create a ticket and an EXISTING succeeded run
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'requester_id' => $user->id,
            'title' => 'Dedupe issue',
        ]);

        $inputHash = hash('sha256', $ticket->title.$ticket->body.($ticket->category?->name ?? ''));

        AiRun::factory()->create([
            'ticket_id' => $ticket->id,
            'run_type' => AiRunType::Triage->value,
            'status' => AiRunStatus::Succeeded->value,
            'input_hash' => $inputHash,
            'output_json' => ['cached' => true],
            'initiated_by_user_id' => $user->id,
        ]);

        // Create the new queued run
        $aiRun = AiRun::factory()->create([
            'ticket_id' => $ticket->id,
            'run_type' => AiRunType::Triage->value,
            'status' => AiRunStatus::Queued->value,
            'initiated_by_user_id' => $user->id,
        ]);

        // 2. Mock Agent: SHOULD NOT be called
        $mockAgent = Mockery::mock(TriageAgent::class);
        $mockAgent->shouldNotReceive('handle');

        $job = new RunTicketTriageJob($aiRun->id);

        // 3. Act
        $job->handle($mockAgent);

        // 4. Assert: Status is succeeded and it reused the cached data
        $aiRun->refresh();
        $this->assertEquals(AiRunStatus::Succeeded, $aiRun->status);
        $this->assertTrue($aiRun->output_json['cached']);
    }

    /**
     * Test UI-level rate limiting through the Livewire component.
     */
    public function test_rate_limiter_prevents_more_than_5_triage_requests(): void
    {
        // 1. Arrange: As an Agent
        $agentUser = User::factory()->create(['role' => UserRole::Agent]);
        $ticket = Ticket::factory()->create(['requester_id' => User::factory()->create()]);

        $this->actingAs($agentUser);
        Queue::fake();

        // 2. Act: Trigger triage 6 times
        $component = Livewire::test(AiPanel::class, ['ticketId' => $ticket->id]);

        for ($i = 0; $i < 5; $i++) {
            $component->call('runTriage');
        }

        // 6th call should trigger rate limit flash message
        $component->call('runTriage')
            ->assertSet('polling', true)
            ->assertSee('Too many AI requests');

        // 3. Assert: Only 5 jobs were dispatched
        Queue::assertPushed(RunTicketTriageJob::class, 5);
        $this->assertEquals(5, AiRun::query()->count());
    }

    public function test_ai_panel_run_triage_creates_ai_run_with_correct_run_type(): void
    {
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
    }

    public function test_draft_ticket_reply_job_sets_status_to_failed_on_exception(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['requester_id' => $user->id]);
        $aiRun = AiRun::factory()->create([
            'ticket_id' => $ticket->id,
            'run_type' => AiRunType::ReplyDraft->value,
            'status' => AiRunStatus::Queued->value,
            'initiated_by_user_id' => $user->id,
        ]);

        $mockAgent = Mockery::mock(ReplyDraftAgent::class);
        $mockAgent->shouldReceive('handle')
            ->once()
            ->andThrow(new RuntimeException('API Failure!'));

        $job = new DraftTicketReplyJob($aiRun->id);

        $this->expectException(RuntimeException::class);
        $job->handle($mockAgent);

        $this->assertDatabaseHas('ai_runs', [
            'id' => $aiRun->id,
            'status' => AiRunStatus::Failed->value,
            'error_message' => 'API Failure!',
        ]);
    }
}
