<?php

declare(strict_types=1);

namespace Tests\Feature\AI;

use App\AI\Agents\ReplyDraftAgent;
use App\AI\DTOs\KbSnippetDTO;
use App\AI\DTOs\ReplyDraftInput;
use App\AI\DTOs\ReplyDraftResult;
use App\AI\Tools\SearchKnowledgeBaseTool;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LucianoTonet\GroqPHP\Groq;
use Mockery;
use Tests\TestCase;

final class ReplyDraftAgentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the agent correctly generates a grounded response.
     */
    public function test_it_returns_reply_draft_result_dto_with_draft_and_next_steps(): void
    {
        // 1. Arrange: Create test data (ticket + author)
        $user = User::factory()->create(['name' => 'John Doe']);
        $ticket = Ticket::factory()->create([
            'requester_id' => $user->id,
            'title' => 'Cannot reset password',
        ]);

        // 2. Mock Groq response:
        $fakeResponse = [
            'choices' => [
                [
                    'message' => [
                        'content' => json_encode([
                            'draft' => 'Hello, please try using the reset link...',
                            'next_steps' => ['Send link'],
                            'risk_flags' => [],
                        ]),
                    ],
                ],
            ],
        ];

        $mockGroq = Mockery::mock(Groq::class);
        $mockGroq->shouldReceive('chat->completions->create')
            ->once()
            ->andReturn($fakeResponse);

        // 3. Mock SearchKnowledgeBaseTool response:
        $mockKbTool = Mockery::mock(SearchKnowledgeBaseTool::class);
        $mockKbTool->shouldReceive('execute')
            ->once()
            ->andReturn([
                new KbSnippetDTO(
                    articleId: 1,
                    title: 'Password Resets',
                    excerpt: 'To reset, go to /reset...',
                    relevanceScore: 1.0
                ),
            ]);

        $agent = new ReplyDraftAgent($mockGroq, $mockKbTool);
        $input = new ReplyDraftInput($ticket->id, 'Summary', $user->id);

        // 4. Act: Call the agent
        $result = $agent->handle($input);

        // 5. Assert: Verify the result DTO fields
        $this->assertInstanceOf(ReplyDraftResult::class, $result);
        $this->assertStringContainsString('Hello', $result->draft);
        $this->assertContains('Send link', $result->nextSteps);
    }

    /**
     * Test handle behavior when the knowledge base returns nothing.
     */
    public function test_it_handles_empty_kb_results_gracefully(): void
    {
        // 1. Arrange
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'requester_id' => $user->id,
            'title' => 'Unknown issue',
        ]);

        $mockGroq = Mockery::mock(Groq::class);
        $mockGroq->shouldReceive('chat->completions->create')
            ->once()
            ->andReturn([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'draft' => 'No info found.',
                                'next_steps' => [],
                                'risk_flags' => ['no-grounding'],
                            ]),
                        ],
                    ],
                ],
            ]);

        $mockKbTool = Mockery::mock(SearchKnowledgeBaseTool::class);
        $mockKbTool->shouldReceive('execute')
            ->once()
            ->andReturn([]); // Empty result

        $agent = new ReplyDraftAgent($mockGroq, $mockKbTool);
        $input = new ReplyDraftInput($ticket->id, '', $user->id);

        // 2. Act
        $result = $agent->handle($input);

        // 3. Assert
        $this->assertInstanceOf(ReplyDraftResult::class, $result);
        $this->assertContains('no-grounding', $result->riskFlags);
    }
}
