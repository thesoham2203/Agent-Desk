<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\SlaConfigFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ============================================================
 * FILE: SlaConfig.php
 * LAYER: Model
 * ============================================================
 *
 * WHAT IS THIS?
 * Represents global speed-of-response targets for the helpdesk.
 *
 * WHY DOES IT EXIST?
 * To allow administrators to define how fast an initial response
 * and a full resolution should occur.
 *
 * HOW IT FITS IN THE APP:
 * This model is read by the scheduler used to find and notify
 * about overdue tickets.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * A configuration model often has only one record, making it a
 * singleton-like data holder in the database.
 * ============================================================
 *
 * @property-read int $id
 * @property-read int $first_response_hours
 * @property-read int $resolution_hours
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class SlaConfig extends Model
{
    /** @use HasFactory<SlaConfigFactory> */
    use HasFactory;

    /**
     * The table name explicitly specified if different from plural convention.
     * (Default is sla_configs).
     */
    protected $table = 'sla_configs';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_response_hours',
        'resolution_hours',
    ];

    /**
     * Casts database columns into their intended PHP types.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'integer',
            'first_response_hours' => 'integer',
            'resolution_hours' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [SlaConfig.php], the next logical file
 * to read is:
 *
 * → [app/Models/KbArticle.php]
 *
 * WHY: After defining business metrics, we define the technical
 *       knowledge used by agents and AI to meet them.
 * ============================================================
 */
