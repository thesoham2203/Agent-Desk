<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\AuditLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ============================================================
 * FILE: AuditLog.php
 * LAYER: Model
 * ============================================================
 *
 * WHAT IS THIS?
 * Represents an entry in the system audit trail.
 *
 * WHY DOES IT EXIST?
 * To allow administrators to trace every change to a ticket,
 * ensuring high accountability and transparency in the helpdesk.
 *
 * HOW IT FITS IN THE APP:
 * Every significant business action (title change, assignment,
 * AI runs) creates an entry here with old and new values.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This model uses json casting for old_values and new_values,
 * letting you store snapshots of database state over time.
 * ============================================================
 *
 * @property-read int $id
 * @property-read int|null $ticket_id
 * @property-read int $user_id
 * @property-read string $action
 * @property-read array<string, mixed>|null $old_values
 * @property-read array<string, mixed>|null $new_values
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read Ticket|null $ticket
 * @property-read User $user
 */
final class AuditLog extends Model
{
    /** @use HasFactory<AuditLogFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ticket_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
    ];

    /**
     * The ticket related to this audit log entry, if any.
     *
     * @return BelongsTo<Ticket, $this>
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * The user who performed the logged action.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Casts database columns into their intended PHP types.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'integer',
            'ticket_id' => 'integer',
            'user_id' => 'integer',
            'action' => 'string',
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [AuditLog.php], the next logical file
 * to read is:
 *
 * → [app/AI/DTOs/TriageInput.php]
 *
 * WHY: After defining how we persist data, we define how we
 *       transfer it to the AI system.
 * ============================================================
 */
