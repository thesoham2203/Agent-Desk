<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AiRunStatus;
use App\Enums\AiRunType;
use Carbon\CarbonInterface;
use Database\Factories\AiRunFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ============================================================
 * FILE: AiRun.php
 * LAYER: Model
 * ============================================================
 *
 * WHAT IS THIS?
 * Represents a single invocation of an AI Agent on a specific ticket.
 *
 * WHY DOES IT EXIST?
 * To track, audit, and provide persistence for AI results,
 * including successes and failures.
 *
 * HOW IT FITS IN THE APP:
 * Every time an agent clicks "Generate Reply" or "Run Triage",
 * a record is created here to track the status (queued, running, etc.)
 * and store the final output.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This model uses Enum casting to maintain type integrity for status
 * and run_type, and the 'array' cast for the json result field.
 * ============================================================
 *
 * @property-read int $id
 * @property-read int $ticket_id
 * @property-read int $initiated_by_user_id
 * @property-read AiRunType $run_type
 * @property-read AiRunStatus $status
 * @property-read string|null $input_hash
 * @property-read array<string, mixed>|null $output_json
 * @property-read string|null $provider
 * @property-read string|null $model
 * @property-read string|null $error_message
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read Ticket $ticket
 * @property-read User $initiatedBy
 */
final class AiRun extends Model
{
    /** @use HasFactory<AiRunFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ticket_id',
        'initiated_by_user_id',
        'run_type',
        'status',
        'input_hash',
        'output_json',
        'provider',
        'model',
        'error_message',
    ];

    /**
     * The ticket related to this specific AI operation.
     *
     * @return BelongsTo<Ticket, $this>
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * The human user who triggered this AI action.
     *
     * @return BelongsTo<User, $this>
     */
    public function initiatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by_user_id');
    }

    /**
     * Casts database columns into their intended PHP types.
     *
     * @return array<string, string|class-string>
     */
    public function casts(): array
    {
        return [
            'id' => 'integer',
            'ticket_id' => 'integer',
            'initiated_by_user_id' => 'integer',
            'run_type' => AiRunType::class,
            'status' => AiRunStatus::class,
            'input_hash' => 'string',
            'output_json' => 'array',
            'provider' => 'string',
            'model' => 'string',
            'error_message' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [AiRun.php], the next logical file
 * to read is:
 *
 * → [app/Models/AuditLog.php]
 *
 * WHY: After defining automated operations, we define tracking
 *       for all human actions in the system.
 * ============================================================
 */
