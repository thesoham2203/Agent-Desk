<?php

declare(strict_types=1);

namespace App\AI\DTOs;

/**
 * ============================================================
 * FILE: ReplyDraftInput.php
 * LAYER: DTO
 * ============================================================
 *
 * WHAT IS THIS?
 * A simple data container for the input required by the AI ReplyDraftAgent.
 *
 * WHY DOES IT EXIST?
 * To allow the AI to receive the context it needs (thread, summary,
 * ticket metadata) in a structured, immutable format.
 *
 * HOW IT FITS IN THE APP:
 * This DTO is instantiated in the DraftTicketReplyJob and
 * passed to the ReplyDraftAgent class for generation.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Readonly classes in PHP 8.4 provide a powerful way to ensure
 * data that doesn't change during its use throughout the application.
 * ============================================================
 */
final readonly class ReplyDraftInput
{
    /**
     * Initializes the immutable input for the Reply Draft AI.
     *
     * @param  int  $ticketId  The unique ID of the ticket we're replying to.
     * @param  string  $threadSummary  A high-level summary of the ongoing thread.
     * @param  int  $initiatedByUserId  The user ID who triggered the generation.
     */
    public function __construct(
        public int $ticketId,
        public string $ticketTitle,
        public string $threadFormatted,
        public string $threadSummary,
        public int $initiatedByUserId,
    ) {}
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [ReplyDraftInput.php], the next logical file
 * to read is:
 *
 * → [app/AI/DTOs/ReplyDraftResult.php]
 *
 * WHY: After defining what context the generation requires,
 *       we define the AI's actual response format.
 * ============================================================
 */
