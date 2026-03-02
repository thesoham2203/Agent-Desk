<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * ============================================================
 * FILE: AiRunType.php
 * LAYER: Enum
 * ============================================================
 *
 * WHAT IS THIS?
 * Defines the type of AI operation being performed for a specific ticket.
 *
 * WHY DOES IT EXIST?
 * To allow the application to track, audit, and display progress for
 * different AI tasks independently.
 *
 * HOW IT FITS IN THE APP:
 * The AiRun model's 'run_type' column is cast to this Enum,
 * enabling system components to correctly process and save AI results.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Laravel's Enum-based casting provides a clean, self-documenting way
 * to ensure database-level consistency and type safety in high-level code.
 * ============================================================
 */
enum AiRunType: string
{
    /**
     * AI is classifying the ticket (category, priority, etc.).
     */
    case Triage = 'triage';

    /**
     * AI is generating a draft response based on the ticket history and knowledge base.
     */
    case ReplyDraft = 'reply_draft';

    /**
     * AI is creating a concise summary of the entire ticket thread.
     */
    case ThreadSummary = 'thread_summary';

    /**
     * Returns a human-readable label for the UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::Triage => 'Ticket Triage',
            self::ReplyDraft => 'Suggest Reply',
            self::ThreadSummary => 'Summarize Discussion',
        };
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [AiRunType.php], the next logical file
 * to read is:
 *
 * → [app/Enums/AiRunStatus.php]
 *
 * WHY: After defining the type of AI work, we need to track its performance.
 * ============================================================
 */
