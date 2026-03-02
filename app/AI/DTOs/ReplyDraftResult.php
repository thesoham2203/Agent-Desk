<?php

declare(strict_types=1);

namespace App\AI\DTOs;

/**
 * ============================================================
 * FILE: ReplyDraftResult.php
 * LAYER: DTO
 * ============================================================
 *
 * WHAT IS THIS?
 * The structured output returned by the ReplyDraftAgent AI.
 *
 * WHY DOES IT EXIST?
 * To capture the AI's generated response draft, recommended actions,
 * and any potential risk factors in a type-safe object.
 *
 * HOW IT FITS IN THE APP:
 * The ReplyDraftAgent populates this based on context, and the
 * Support Agent can then edit and send the resulting draft.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * By using classes instead of arrays, we can define properties once
 * and ensure they're always there when another system component uses them.
 * ============================================================
 */
final readonly class ReplyDraftResult
{
    /**
     * Initializes the immutable results from the Reply Draft AI.
     *
     * @param  string  $draft  The proposed text for the customer response.
     * @param  array<int, string>  $nextSteps  A list of actions the agent should perform.
     * @param  array<int, string>  $riskFlags  Any potential issues identified in the draft or request.
     */
    public function __construct(
        public string $draft,
        public array $nextSteps,
        public array $riskFlags,
    ) {}
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [ReplyDraftResult.php], the next logical file
 * to read is:
 *
 * → [app/AI/DTOs/KbSnippetDTO.php]
 *
 * WHY: After finalizing the AI output format, we move to the
 *       data utilized by the AI for grounding.
 * ============================================================
 */
