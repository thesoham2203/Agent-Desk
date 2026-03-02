<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\AttachmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ============================================================
 * FILE: Attachment.php
 * LAYER: Model
 * ============================================================
 *
 * WHAT IS THIS?
 * Represents a file upload related to a helpdesk ticket or message.
 *
 * WHY DOES IT EXIST?
 * To securely store file metadata (path, name, size, type) and link it
 * to the ticket thread. Supports private storage by default.
 *
 * HOW IT FITS IN THE APP:
 * Attachments are used for sharing screenshots, logs, or documentation
 * during a support interaction.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This model defines relationships back to Ticket and TicketMessage,
 * allowing you to query for files at the ticket level or specific message level.
 * ============================================================
 *
 * @property-read int $id
 * @property-read int $ticket_id
 * @property-read int|null $message_id
 * @property-read string $disk
 * @property-read string $path
 * @property-read string $original_name
 * @property-read string $mime_type
 * @property-read int $size
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read Ticket $ticket
 * @property-read TicketMessage|null $message
 */
final class Attachment extends Model
{
    /** @use HasFactory<AttachmentFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ticket_id',
        'message_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    /**
     * The ticket this file is related to.
     *
     * @return BelongsTo<Ticket, $this>
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * The specific message this file was part of, if any.
     *
     * @return BelongsTo<TicketMessage, $this>
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(TicketMessage::class);
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
            'message_id' => 'integer',
            'disk' => 'string',
            'path' => 'string',
            'original_name' => 'string',
            'mime_type' => 'string',
            'size' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [Attachment.php], the next logical file
 * to read is:
 *
 * → [app/Models/Macro.php]
 *
 * WHY: After defining the core entities, we define helper entities
 *       like macros for agent efficiency.
 * ============================================================
 */
