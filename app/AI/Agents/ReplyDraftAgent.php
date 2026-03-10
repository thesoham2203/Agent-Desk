<?php

declare(strict_types=1);

namespace App\AI\Agents;

use App\AI\DTOs\KbSnippetDTO;
use App\AI\DTOs\ReplyDraftInput;
use App\AI\DTOs\ReplyDraftResult;
use App\AI\Tools\SearchKnowledgeBaseTool;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;
use RuntimeException;

/**
 * ============================================================
 * FILE: ReplyDraftAgent.php
 * LAYER: AI Agent
 * ============================================================
 *
 * WHAT IS THIS?
 * A sophisticated AI Agent that assists human support agents by
 * drafting replies to customer tickets.
 *
 * WHY DOES IT EXIST?
 * To speed up response times while ensuring accuracy. By grounding
 * the reply in our knowledge base, we provide consistent answers
 * across the support team.
 *
 * HOW IT FITS IN THE APP:
 * This agent is called from a queued Job (DraftTicketReplyJob). It
 * uses the SearchKnowledgeBaseTool to fetch relevant articles and
 * then calls the AI facade to generate a draft reply.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * laravel/ai is Laravel 12's official AI package. It provides
 * a unified AI facade that works with multiple providers
 * (Groq, OpenAI, Anthropic) through a single API.
 * This replaces the community lucianotonet/groq-laravel package.
 * ============================================================
 */
final class ReplyDraftAgent implements Agent
{
    use Promptable;

    /**
     * Constructor injection for our KB search tool.
     */
    public function __construct(
        private SearchKnowledgeBaseTool $kbTool,
    ) {}

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): string
    {
        return <<<'PROMPT'
        You are a helpful support agent drafting a reply to a customer ticket.
        Use the knowledge base articles provided as context.
        Respond ONLY with valid JSON matching this exact structure:
        {
          "draft": "string (the full reply text to send to the customer)",
          "next_steps": ["array of internal action items for the agent"],
          "risk_flags": ["array of potential issues or escalation concerns"]
        }
        Be professional, empathetic, and solution-focused.
        Do not include any text outside the JSON object.
        PROMPT;
    }

    /**
     * Handles the drafting of a reply for a specific ticket.
     *
     * @param  ReplyDraftInput  $input  The ticket and session info for drafting
     * @return ReplyDraftResult The draft, next steps, and risk flags
     *
     * @throws RuntimeException If the AI response cannot be parsed or decoded
     */
    public function handle(ReplyDraftInput $input): ReplyDraftResult
    {
        // 1. Search KB for relevant articles using the ticket title as a query:
        $kbSnippets = $this->kbTool->execute($input->ticketTitle);
        $kbContext = collect($kbSnippets)
            ->map(fn (KbSnippetDTO $s): string => "KB: {$s->title}\n{$s->excerpt}")
            ->join("\n\n");

        // 2. Build user message:
        $userMessage = "Ticket: {$input->ticketTitle}\n\n"
            ."Thread:\n{$input->threadFormatted}\n\n"
            ."Knowledge Base Context:\n{$kbContext}\n\n"
            ."Summary provided: {$input->threadSummary}";

        // 3. Use Promptable's prompt() method
        $response = $this->prompt($userMessage);
        $content = $response->text;

        // 6. Strip markdown fences:
        $content = mb_trim(preg_replace('/^```json\s*|\s*```$/s', '', $content) ?? '');

        // 7. Decode JSON:
        $data = json_decode($content, true);
        throw_unless(is_array($data), RuntimeException::class, "ReplyDraftAgent: AI returned invalid JSON: {$content}");

        /** @var array<int, string> $nextSteps */
        $nextSteps = is_array($data['next_steps'] ?? null) ? $data['next_steps'] : [];

        /** @var array<int, string> $riskFlags */
        $riskFlags = is_array($data['risk_flags'] ?? null) ? $data['risk_flags'] : [];

        return new ReplyDraftResult(
            draft: is_string($data['draft'] ?? null) ? $data['draft'] : '',
            nextSteps: $nextSteps,
            riskFlags: $riskFlags,
        );
    }
}
