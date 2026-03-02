<?php

declare(strict_types=1);

namespace App\AI\DTOs;

/**
 * ============================================================
 * FILE: TriageInput.php
 * LAYER: DTO
 * ============================================================
 *
 * WHAT IS THIS?
 * A simple data container for the input required by the AI Triage Agent.
 *
 * WHY DOES IT EXIST?
 * To ensure that the AI subsystem receives structured, validated data
 * regardless of the source (web form, API, or automated re-run).
 *
 * HOW IT FITS IN THE APP:
 * This DTO is instantiated in the TicketTriageJob and passed to
 * the TriageAgent class.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * A Data Transfer Object (DTO) in PHP 8.4 is a readonly class that
 * bundles variables together. This replaces associative arrays with
 * a type-safe object that cannot be modified after it's created.
 * ============================================================
 */
final readonly class TriageInput
{
    /**
     * Initializes the immutable input for the Triage AI.
     *
     * @param  string  $title  The main subject/title of the ticket.
     * @param  string  $body  The full description or initial body content of the ticket.
     * @param  string  $category  The category name if already selected, or an empty string.
     */
    public function __construct(
        public string $title,
        public string $body,
        public string $category = '',
    ) {}
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [TriageInput.php], the next logical file
 * to read is:
 *
 * → [app/AI/DTOs/TriageResult.php]
 *
 * WHY: After defining what goes INTO the triage process, we
 *       define what comes OUT of it.
 * ============================================================
 */
