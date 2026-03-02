<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\KbArticleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ============================================================
 * FILE: KbArticle.php
 * LAYER: Model
 * ============================================================
 *
 * WHAT IS THIS?
 * Represents a documentation entry in the Knowledge Base (KB).
 *
 * WHY DOES IT EXIST?
 * To allow agents to create tutorials and answer frequently
 * asked questions, reducing ticket volumes.
 *
 * HOW IT FITS IN THE APP:
 * SearchKnowledgeBaseTool uses these articles to find grounding data
 * for AI response drafting.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * A simple model designed for the storage and retrieval of long-form
 * documentation content (body).
 * ============================================================
 *
 * @property-read int $id
 * @property-read string $title
 * @property-read string $body
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class KbArticle extends Model
{
    /** @use HasFactory<KbArticleFactory> */
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
 * Now that you understand [KbArticle.php], the next logical file
 * to read is:
 *
 * → [app/Models/AiRun.php]
 *
 * WHY: After defining documentation sources, we define how AI
 *       instruments record and audit their work from those sources.
 * ============================================================
 */
