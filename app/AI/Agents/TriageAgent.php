<?php

declare(strict_types=1);

/**
 * ============================================================
 * FILE: TriageAgent.php
 * LAYER: AI Agent
 * ============================================================
 *
 * WHAT IS THIS?
 * A dedicated AI Agent that analyzes new support tickets to categorize
 * them, assign a priority, and generate a brief summary.
 *
 * WHY DOES IT EXIST?
 * To help support human agents by automating the initial triage step.
 * It provides a consistent "first look" at every ticket that enters the system.
 *
 * HOW IT FITS IN THE APP:
 * This agent is called from a queued Job (RunTicketTriageJob). It takes
 * a TriageInput DTO and returns a TriageResult DTO. It does NOT touch
 * the database directly.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This is a final plain PHP class. We use dependency injection to provide
 * the Groq client. It uses DTOs for strict input/output contracts, keeping
 * the domain logic separate from the database implementation.
 * ============================================================
 */

namespace App\AI\Agents;

use App\AI\DTOs\TriageInput;
use App\AI\DTOs\TriageResult;
use LucianoTonet\GroqPHP\Groq;
use RuntimeException;

class TriageAgent
{
    /**
     * Constructor property promotion for the Groq client.
     */
    public function __construct(
        private readonly Groq $groq
    ) {}

    /**
     * Handles the triage process for a specific ticket.
     *
     * @param  TriageInput  $input  The ticket content to be triaged
     * @return TriageResult The structured result from the AI analysis
     *
     * @throws RuntimeException If the AI response cannot be parsed or decoded
     */
    public function handle(TriageInput $input): TriageResult
    {
        // 1. Build the system prompt:
        // We instruct the model to return ONLY JSON for reliable parsing.
        $systemPrompt = <<<'PROMPT'
You are a helpdesk triage assistant. Analyze the support ticket
and respond ONLY with valid JSON matching this exact structure:
{
  "category": "string (one of: Technical, Billing, General, Feature Request, Bug Report, Other)",
  "priority": "string (one of: low, medium, high, urgent)",
  "summary": "string (2-3 sentence summary of the issue)",
  "tags": ["array", "of", "relevant", "tags"],
  "escalation_flag": boolean,
  "clarifying_question": "string or empty string"
}
Do not include any text outside the JSON object.
PROMPT;

        // 2. Build the user message:
        // We provide the title, body, and current category if available.
        $userMessage = "Title: {$input->title}\n\nDescription: {$input->body}";
        if ($input->category !== '') {
            $userMessage .= "\n\nCurrent category: {$input->category}";
        }

        // 3. Call Groq API:
        // temperature: 0.3 = less creative, more consistent structured output.
        // We use the model configured in the .env file.
        /** @var array<string, mixed> $response */
        $response = $this->groq->chat()->completions()->create([
            'model' => config('groq.model', 'llama3-8b-8192'),
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage],
            ],
            'temperature' => 0.3,
            'max_tokens' => 500,
        ]);

        // 4. Extract content from response:
        // Groq returns an array; we grab the content of the first choice.
        /** @var array<int, array{message: array{content: string|null}}> $choices */
        $choices = $response['choices'] ?? [];
        $content = (string) ($choices[0]['message']['content'] ?? '');

        // 5. Strip markdown fences if the model wrapped the JSON:
        // Sometimes the LLM will output ```json ... ``` despite instructions.
        $replaced = preg_replace('/^```json\s*|\s*```$/s', '', $content);
        $content = mb_trim(is_string($replaced) ? $replaced : $content);

        // 6. Decode JSON:
        // We decode into an associative array for mapping back to the DTO.
        $data = json_decode($content, true);
        throw_unless(is_array($data), RuntimeException::class, "TriageAgent: Groq returned invalid JSON: {$content}");

        /** @var array<string, mixed> $data */

        // 7. Map the raw array to a TriageResult DTO:
        // We provide sensible defaults if specific fields are missing.
        $category = $data['category'] ?? 'General';
        $priority = $data['priority'] ?? 'medium';
        $summary = $data['summary'] ?? '';
        $question = $data['clarifying_question'] ?? '';

        return new TriageResult(
            category: is_string($category) ? $category : 'General',
            priority: is_string($priority) ? $priority : 'medium',
            summary: is_string($summary) ? $summary : '',
            tags: array_values(array_filter((array) ($data['tags'] ?? []), is_string(...))),
            escalationFlag: (bool) ($data['escalation_flag'] ?? false),
            clarifyingQuestion: is_string($question) ? $question : ''
        );
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [app/AI/Agents/ReplyDraftAgent.php]
 * WHY: After Triage, the next common AI task is drafting a response
 * based on knowledge base context and the ticket history.
 * ============================================================
 */
