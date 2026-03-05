<?php

declare(strict_types=1);

namespace Tests\Feature\AI;

use App\AI\Agents\TriageAgent;
use App\AI\DTOs\TriageInput;
use App\AI\DTOs\TriageResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LucianoTonet\GroqPHP\Groq;
use Mockery;
use RuntimeException;
use Tests\TestCase;

final class TriageAgentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the agent correctly parses a successful JSON response from Groq.
     */
    public function test_it_returns_triage_result_dto_with_correct_fields(): void
    {
        // 1. Arrange: Mock the Groq client response
        $fakeResponse = [
            'choices' => [
                [
                    'message' => [
                        'content' => json_encode([
                            'category' => 'Technical',
                            'priority' => 'high',
                            'summary' => 'User cannot login.',
                            'tags' => ['login', 'auth'],
                            'escalation_flag' => false,
                            'clarifying_question' => '',
                        ]),
                    ],
                ],
            ],
        ];

        $mockGroq = Mockery::mock(Groq::class);
        $mockGroq->shouldReceive('chat->completions->create')
            ->once()
            ->andReturn($fakeResponse);

        $agent = new TriageAgent($mockGroq);
        $input = new TriageInput('Login issue', 'I cannot sign in to my account.', '');

        // 2. Act: Call the agent
        $result = $agent->handle($input);

        // 3. Assert: Verify the DTO fields
        $this->assertInstanceOf(TriageResult::class, $result);
        $this->assertEquals('Technical', $result->category);
        $this->assertEquals('high', $result->priority);
        $this->assertContains('login', $result->tags);
        $this->assertFalse($result->escalationFlag);
    }

    /**
     * Test that the agent handles invalid JSON from the LLM by throwing an exception.
     */
    public function test_it_throws_runtime_exception_when_groq_returns_invalid_json(): void
    {
        // 1. Arrange: Return non-JSON string
        $fakeResponse = [
            'choices' => [
                [
                    'message' => [
                        'content' => 'This is not valid JSON.',
                    ],
                ],
            ],
        ];

        $mockGroq = Mockery::mock(Groq::class);
        $mockGroq->shouldReceive('chat->completions->create')
            ->once()
            ->andReturn($fakeResponse);

        $agent = new TriageAgent($mockGroq);
        $input = new TriageInput('Broken', 'Very broken', '');

        // 2. Act & Assert: Expect an exception
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('TriageAgent: Groq returned invalid JSON');

        $agent->handle($input);
    }

    /**
     * Test that the agent correctly strips markdown fences (```json ... ```).
     */
    public function test_it_strips_markdown_fences_from_groq_response(): void
    {
        // 1. Arrange: Wrap JSON in markdown fences
        $json = json_encode([
            'category' => 'Billing',
            'priority' => 'low',
            'summary' => 'Price check',
            'tags' => ['billing'],
            'escalation_flag' => false,
            'clarifying_question' => '',
        ]);

        $fakeResponse = [
            'choices' => [
                [
                    'message' => [
                        'content' => "```json\n".$json."\n```",
                    ],
                ],
            ],
        ];

        $mockGroq = Mockery::mock(Groq::class);
        $mockGroq->shouldReceive('chat->completions->create')
            ->once()
            ->andReturn($fakeResponse);

        $agent = new TriageAgent($mockGroq);
        $input = new TriageInput('Billing query', 'How much is it?', '');

        // 2. Act
        $result = $agent->handle($input);

        // 3. Assert: Success implies fences were stripped
        $this->assertEquals('Billing', $result->category);
    }
}
