<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Carbon\CarbonInterface;
use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ============================================================
 * FILE: Ticket.php
 * LAYER: Model
 * ============================================================
 *
 * WHAT IS THIS?
 * The central entity of the helpdesk, representing a customer issue.
 *
 * WHY DOES IT EXIST?
 * To store all metadata for a request: status, priority, category,
 * assigned agent, and the customer who created it.
 *
 * HOW IT FITS IN THE APP:
 * Everything revolves around the Ticket. Messages, Attachments,
 * and AI Runs are all linked back here.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This model uses BelongsTo and HasMany relationships to build a
 * graph of related data, allowing for complex queries like
 * $ticket->requester->name or $ticket->messages->count().
 * ============================================================
 *
 * @property-read int $id
 * @property-read int $requester_id
 * @property-read int|null $assigned_to
 * @property-read int|null $category_id
 * @property-read TicketStatus $status
 * @property-read TicketPriority $priority
 * @property-read string $title
 * @property-read string $body
 * @property-read CarbonInterface|null $first_responded_at
 * @property-read CarbonInterface|null $resolved_at
 * @property-read CarbonInterface|null $first_response_breached_at
 * @property-read CarbonInterface|null $resolution_breached_at
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read User $requester
 * @property-read User|null $assignee
 * @property-read Category|null $category
 */
final class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'requester_id',
        'assigned_to',
        'category_id',
        'status',
        'priority',
        'title',
        'body',
        'first_responded_at',
        'resolved_at',
        'first_response_breached_at',
        'resolution_breached_at',
    ];

    /**
     * The customer who created the ticket.
     *
     * @return BelongsTo<User, $this>
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * The support agent currently assigned to handle the ticket.
     *
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * The business domain categorization of the ticket.
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * The full list of messages in the discussion thread.
     *
     * @return HasMany<TicketMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }

    /**
     * All files uploaded by either agent or customer for this ticket.
     *
     * @return HasMany<Attachment, $this>
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    /**
     * Every individual AI invocation performed on this ticket.
     *
     * @return HasMany<AiRun, $this>
     */
    public function aiRuns(): HasMany
    {
        return $this->hasMany(AiRun::class);
    }

    /**
     * The audit history of all status and assignment changes.
     *
     * @return HasMany<AuditLog, $this>
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
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
            'requester_id' => 'integer',
            'assigned_to' => 'integer',
            'category_id' => 'integer',
            'status' => TicketStatus::class,
            'priority' => TicketPriority::class,
            'title' => 'string',
            'body' => 'string',
            'first_responded_at' => 'datetime',
            'resolved_at' => 'datetime',
            'first_response_breached_at' => 'datetime',
            'resolution_breached_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [Ticket.php], the next logical file
 * to read is:
 *
 * → [app/Models/TicketMessage.php]
 *
 * WHY: After defining the ticket container, we define the content
 *       of the conversation inside it.
 * ============================================================
 */
