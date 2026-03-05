<?php

declare(strict_types=1);

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
 * then calls the Groq API to generate a draft reply.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This agent uses dependency injection to pull in both the Groq client
 * and our custom KB tool. It also uses Eloquent's eager loading (with 'messages')
 * to fetch the necessary ticket thread history before building the prompt.
 * ============================================================
 */

namespace App\AI\Agents;

use App\AI\DTOs\KbSnippetDTO;
use App\AI\DTOs\ReplyDraftInput;
use App\AI\DTOs\ReplyDraftResult;
use App\AI\Tools\SearchKnowledgeBaseTool;
use App\Models\Ticket;
use App\Models\TicketMessage;
use LucianoTonet\GroqPHP\Groq;
use RuntimeException;

class ReplyDraftAgent
{
    /**
     * Constructor injection for the Groq client and our KB search tool.
     */
    public function __construct(
        private readonly Groq $groq,
        private readonly SearchKnowledgeBaseTool $kbTool
    ) {}

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
        // 1. Load the ticket with message history:
        // This is the ONLY DB access allowed in an agent. No writes here.
        $ticket = Ticket::with('messages.author')->findOrFail($input->ticketId);

        // 2. Build thread history from messages:
        // We iterate through all historical messages to provide context.
        $thread = $ticket->messages
            ->map(fn (TicketMessage $m): string => "[{$m->author->name}]: {$m->body}")
            ->join("\n\n");

        // 3. Search KB for relevant articles using the ticket title as a query:
        // We use our SearchKnowledgeBaseTool to ground the reply in actual info.
        $kbSnippets = $this->kbTool->execute($ticket->title);
        $kbContext = collect($kbSnippets)
            ->map(fn (KbSnippetDTO $s): string => "KB: {$s->title}\n{$s->excerpt}")
            ->join("\n\n");

        // 4. Build system prompt:
        // We instruct the model to be professional and empathetic.
        $systemPrompt = <<<'PROMPT'
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

        // 5. Build user message:
        // We provide the ticket info, full history, KB context, and any summary.
        $userMessage = "Ticket: {$ticket->title}\n\n"
            ."Thread:\n{$thread}\n\n"
            ."Knowledge Base Context:\n{$kbContext}\n\n"
            ."Summary provided: {$input->threadSummary}";

        // 6. Call Groq API:
        // temperature: 0.5 = slightly more creative for drafting personalized replies.
        /** @var array<string, mixed> $response */
        $response = $this->groq->chat()->completions()->create([
            'model' => config('groq.model', 'llama3-8b-8192'),
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage],
            ],
            'temperature' => 0.5,
            'max_tokens' => 800,
        ]);

        // 7. Extract content from response:
        /** @var array<int, array{message: array{content: string|null}}> $choices */
        $choices = $response['choices'] ?? [];
        $content = (string) ($choices[0]['message']['content'] ?? '');

        // 8. Strip markdown fences:
        $replaced = preg_replace('/^```json\s*|\s*```$/s', '', $content);
        $content = mb_trim(is_string($replaced) ? $replaced : $content);

        // 9. Decode JSON:
        $data = json_decode($content, true);
        throw_unless(is_array($data), RuntimeException::class, "ReplyDraftAgent: Groq returned invalid JSON: {$content}");

        /** @var array<string, mixed> $data */

        // 10. Map the raw array to a ReplyDraftResult DTO:
        $draftContent = $data['draft'] ?? '';

        return new ReplyDraftResult(
            draft: is_string($draftContent) ? $draftContent : '',
            nextSteps: array_values(array_filter((array) ($data['next_steps'] ?? []), is_string(...))),
            riskFlags: array_values(array_filter((array) ($data['risk_flags'] ?? []), is_string(...)))
        );
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [app/Jobs/RunTicketTriageJob.php]
 * WHY: AI Agents have tasks but no schedules. Now we create Jobs
 * that orchestrate these agents asynchronously in the background.
 * ============================================================
 */
