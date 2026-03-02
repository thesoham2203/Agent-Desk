<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ============================================================
 * FILE: Category.php
 * LAYER: Model
 * ============================================================
 *
 * WHAT IS THIS?
 * Represents a ticket category like "Billing" or "Technical Support".
 *
 * WHY DOES IT EXIST?
 * To allow grouping of tickets for better routing, statistics,
 * and AI-driven classification.
 *
 * HOW IT FITS IN THE APP:
 * Tickets belong to a category. The AI triage engine uses categories
 * defined in this table to suggest labels for new tickets.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Eloquent models handle database attributes automatically.
 * Here, we use $fillable to protect against mass-assignment
 * and define a HasMany relationship to link categories to tickets.
 * ============================================================
 *
 * @property-read int $id
 * @property-read string $name
 * @property-read string|null $description
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Returns all tickets assigned to this category.
     *
     * @return HasMany<Ticket, $this>
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
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
            'name' => 'string',
            'description' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [Category.php], the next logical file
 * to read is:
 *
 * → [app/Models/Ticket.php]
 *
 * WHY: After defining the categories, we define the core entity
 *       of the system: the Ticket itself.
 * ============================================================
 */
