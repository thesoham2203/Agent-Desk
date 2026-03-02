<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * ============================================================
 * FILE: AiRunStatus.php
 * LAYER: Enum
 * ============================================================
 *
 * WHAT IS THIS?
 * Represents the current execution status of an AI run.
 *
 * WHY DOES IT EXIST?
 * To allow tracking of progress during slow-running or queued AI jobs
 * and to distinguish between success and failure in the audit log.
 *
 * HOW IT FITS IN THE APP:
 * The AiRun model's 'status' column is cast to this Enum,
 * and Livewire components use it to show progress spinners.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Laravel's Enum support ensures that only valid states
 * can be stored in the database, avoiding "invalid state" errors.
 * ============================================================
 */
enum AiRunStatus: string
{
    /**
     * Initial state when the AI job has been dispatched but not yet processed.
     */
    case Queued = 'queued';

    /**
     * Set when the AI worker has started processing the request.
     */
    case Running = 'running';

    /**
     * Set when the AI operation has finished successfully.
     */
    case Succeeded = 'succeeded';

    /**
     * Set when the AI operation encountered an error.
     */
    case Failed = 'failed';

    /**
     * Returns a human-readable label for the UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::Queued => 'In Queue',
            self::Running => 'Processing...',
            self::Succeeded => 'Completed',
            self::Failed => 'Failed',
        };
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [AiRunStatus.php], the next logical file
 * to read is:
 *
 * → [app/Enums/UserRole.php]
 *
 * WHY: After defining system-level types, we define the human actors.
 * ============================================================
 */
