<?php

declare(strict_types=1);

namespace App\AI\DTOs;

/**
 * ============================================================
 * FILE: TriageResult.php
 * LAYER: DTO
 * ============================================================
 *
 * WHAT IS THIS?
 * The structured output returned by the TriageAgent AI.
 *
 * WHY DOES IT EXIST?
 * To capture the AI's classification results (category, priority, tags)
 * in a type-safe PHP object for easier application logic.
 *
 * HOW IT FITS IN THE APP:
 * The TriageAgent populates this object based on Ticket data,
 * and the Ticket controller uses it to suggest updates in the UI.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Readonly classes are perfect for DTOs; they allow you to
 * define values once and ensure they remain constant throughout
 * the request lifecycle.
 * ============================================================
 */
final readonly class TriageResult
{
    /**
     * Initializes the immutable results from the Triage AI.
     *
     * @param  string  $category  The suggested category for the ticket.
     * @param  string  $priority  The suggested priority level (low, medium, high, urgent).
     * @param  string  $summary  A concise, high-level summary of the issue.
     * @param  array<int, string>  $tags  A list of keyword tags generated for the issue.
     * @param  bool  $escalationFlag  Indicates if this ticket requires urgent escalation.
     * @param  string  $clarifyingQuestion  A follow-up question the AI suggests asking the user.
     */
    public function __construct(
        public string $category,
        public string $priority,
        public string $summary,
        public array $tags,
        public bool $escalationFlag,
        public string $clarifyingQuestion,
    ) {}
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [TriageResult.php], the next logical file
 * to read is:
 *
 * → [app/AI/DTOs/ReplyDraftInput.php]
 *
 * WHY: After finalizing the triage state, we move into the
 *       AI response generation phase.
 * ============================================================
 */
