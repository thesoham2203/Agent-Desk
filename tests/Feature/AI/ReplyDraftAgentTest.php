<?php

declare(strict_types=1);

use App\AI\Agents\ReplyDraftAgent;
use App\AI\DTOs\KbSnippetDTO;
use App\AI\DTOs\ReplyDraftInput;
use App\AI\DTOs\ReplyDraftResult;
use App\AI\Tools\SearchKnowledgeBaseTool;
use App\Models\Ticket;
use App\Models\User;
use Mockery\MockInterface;

beforeEach(function (): void {
    // ReplyDraftAgent::fake() is now available since we added Promptable trait
    ReplyDraftAgent::fake([
        json_encode([
            'draft' => 'Hello, please try using the reset link...',
            'next_steps' => ['Send link'],
            'risk_flags' => [],
        ]),
    ]);
});

it('returns reply draft result DTO with draft and next steps', function (): void {
    // 1. Arrange
    $user = User::factory()->create(['name' => 'John Doe']);
    $ticket = Ticket::factory()->create([
        'requester_id' => $user->id,
        'title' => 'Cannot reset password',
    ]);

    // Mock SearchKnowledgeBaseTool
    $this->mock(SearchKnowledgeBaseTool::class, function (MockInterface $mock): void {
        $mock->shouldReceive('execute')
            ->once()
            ->andReturn([
                new KbSnippetDTO(
                    articleId: 1,
                    title: 'Password Resets',
                    excerpt: 'To reset, go to /reset...',
                    relevanceScore: 1.0
                ),
            ]);
    });

    $agent = resolve(ReplyDraftAgent::class);
    $input = new ReplyDraftInput($ticket->id, $ticket->title, '', 'Summary', $user->id);

    // 2. Act
    $result = $agent->handle($input);

    // 3. Assert
    expect($result)->toBeInstanceOf(ReplyDraftResult::class);
    expect($result->draft)->toContain('Hello');
    expect($result->nextSteps)->toContain('Send link');
});

it('handles empty KB results gracefully', function (): void {
    // 1. Arrange
    $user = User::factory()->create();
    $ticket = Ticket::factory()->create([
        'requester_id' => $user->id,
        'title' => 'Unknown issue',
    ]);

    ReplyDraftAgent::fake([
        json_encode([
            'draft' => 'No info found.',
            'next_steps' => [],
            'risk_flags' => ['no-grounding'],
        ]),
    ]);

    // Mock SearchKnowledgeBaseTool returning empty
    $this->mock(SearchKnowledgeBaseTool::class, function (MockInterface $mock): void {
        $mock->shouldReceive('execute')
            ->once()
            ->andReturn([]);
    });

    $agent = resolve(ReplyDraftAgent::class);
    $input = new ReplyDraftInput($ticket->id, $ticket->title, '', '', $user->id);

    // 2. Act
    $result = $agent->handle($input);

    // 3. Assert
    expect($result)->toBeInstanceOf(ReplyDraftResult::class);
    expect($result->riskFlags)->toContain('no-grounding');
});
