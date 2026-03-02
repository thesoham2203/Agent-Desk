<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\MacroFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ============================================================
 * FILE: Macro.php
 * LAYER: Model
 * ============================================================
 *
 * WHAT IS THIS?
 * Represents a saved, pre-written response for support agents.
 *
 * WHY DOES IT EXIST?
 * To save time for agents by providing standard text snippets
 * for common troubleshooting or greetings.
 *
 * HOW IT FITS IN THE APP:
 * Agents can pick a macro from a list in the UI to populate the
 * reply field instantly.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * A simple model with only fillables and timestamps, requiring no
 * relationships as it is a generic template store.
 * ============================================================
 *
 * @property-read int $id
 * @property-read string $title
 * @property-read string $body
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class Macro extends Model
{
    /** @use HasFactory<MacroFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'body',
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
            'title' => 'string',
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
 * Now that you understand [Macro.php], the next logical file
 * to read is:
 *
 * → [app/Models/SlaConfig.php]
 *
 * WHY: After defining help tools, we define system-wide
 *       performance metrics for the helpdesk.
 * ============================================================
 */
