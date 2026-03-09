<?php

declare(strict_types=1);

namespace App\AI\Agents;

use App\AI\DTOs\TriageInput;
use App\AI\DTOs\TriageResult;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;
use RuntimeException;

/**
 * ============================================================
 * FILE: TriageAgent.php
 * LAYER: AI Agent
 * ============================================================
 *
 * WHAT IS THIS?
 * Analyzes a support ticket and returns structured triage data.
 *
 * WHY DOES IT EXIST?
 * Automates ticket categorization, priority setting, and
 * identification of escalation needs.
 *
 * HOW IT FITS IN THE APP:
 * Called by RunTicketTriageJob. Uses laravel/ai facade
 * with Groq as the provider.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * laravel/ai is Laravel 12's official AI package. It provides
 * a unified AI facade that works with multiple providers
 * (Groq, OpenAI, Anthropic) through a single API.
 * The facade is configured via config/ai.php and .env.
 * This replaces the community lucianotonet/groq-laravel package.
 * ============================================================
 */
final class TriageAgent implements Agent
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): string
    {
        return <<<'PROMPT'
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
    }

    public function handle(TriageInput $input): TriageResult
    {
        // [Keep exact same user message building as before]
        $userMessage = "Title: {$input->title}\n\nDescription: {$input->body}";
        if ($input->category !== '') {
            $userMessage .= "\n\nCurrent category: {$input->category}";
        }

        // Use Promptable's prompt() method
        $response = $this->prompt($userMessage);
        $content = $response->text;

        // [Keep exact same JSON stripping, parsing, and DTO mapping]
        $content = mb_trim(preg_replace('/^```json\s*|\s*```$/s', '', $content) ?? '');

        $data = json_decode($content, true);
        throw_unless(is_array($data), RuntimeException::class, "TriageAgent: AI returned invalid JSON: {$content}");

        /** @var array<int, string> $tags */
        $tags = is_array($data['tags'] ?? null) ? $data['tags'] : [];

        return new TriageResult(
            category: is_string($data['category'] ?? null) ? $data['category'] : 'General',
            priority: is_string($data['priority'] ?? null) ? $data['priority'] : 'medium',
            summary: is_string($data['summary'] ?? null) ? $data['summary'] : '',
            tags: $tags,
            escalationFlag: is_bool($data['escalation_flag'] ?? null) && $data['escalation_flag'],
            clarifyingQuestion: is_string($data['clarifying_question'] ?? null) ? $data['clarifying_question'] : '',
        );
    }
}
