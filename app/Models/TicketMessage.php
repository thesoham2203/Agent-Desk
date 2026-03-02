<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TicketMessageType;
use Carbon\CarbonInterface;
use Database\Factories\TicketMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ============================================================
 * FILE: TicketMessage.php
 * LAYER: Model
 * ============================================================
 *
 * WHAT IS THIS?
 * Represents an individual entry (reply or note) within a ticket thread.
 *
 * WHY DOES IT EXIST?
 * To differentiate between public responses seen by the customer
 * and private internal notes used by agents.
 *
 * HOW IT FITS IN THE APP:
 * Messages make up the body of the ticket thread. They are linked to
 * an author (User) and can have associated file attachments.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This model uses the TicketMessageType enum to control visibility
 * and has a BelongsTo relationship to connect back to the ticket.
 * ============================================================
 *
 * @property-read int $id
 * @property-read int $ticket_id
 * @property-read int $author_id
 * @property-read TicketMessageType $type
 * @property-read string $body
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read Ticket $ticket
 * @property-read User $author
 */
final class TicketMessage extends Model
{
    /** @use HasFactory<TicketMessageFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ticket_id',
        'author_id',
        'type',
        'body',
    ];

    /**
     * The ticket thread this message belongs to.
     *
     * @return BelongsTo<Ticket, $this>
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * The user (agent or requester) who wrote this message.
     *
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Any files that were uploaded specifically as part of this message.
     *
     * @return HasMany<Attachment, $this>
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'message_id');
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
            'author_id' => 'integer',
            'type' => TicketMessageType::class,
            'body' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [TicketMessage.php], the next logical file
 * to read is:
 *
 * → [app/Models/Attachment.php]
 *
 * WHY: After defining the core discussion, we define how file
 *       uploads are stored and managed.
 * ============================================================
 */
