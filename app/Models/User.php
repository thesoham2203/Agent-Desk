<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use Carbon\CarbonInterface;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * ============================================================
 * FILE: User.php
 * LAYER: Model
 * ============================================================
 *
 * WHAT IS THIS?
 * The core User model for the application, handling authentication
 * and Role-Based Access Control (RBAC).
 *
 * WHY DOES IT EXIST?
 * To manage user identities and differentiate between Administrators,
 * Support Agents, and Customers (Requesters).
 *
 * HOW IT FITS IN THE APP:
 * This is the root of the ticketing system. Every ticket belongs to a user,
 * and every action is logged against a user.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * An Eloquent model is a PHP class that represents a database table.
 * Laravel uses these objects to run SQL queries behind the scenes,
 * so you interact with data as objects rather than raw strings.
 * ============================================================
 *
 * @property-read int $id
 * @property-read string $name
 * @property-read string $email
 * @property-read CarbonInterface|null $email_verified_at
 * @property-read string $password
 * @property-read UserRole $role
 * @property-read string|null $remember_token
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 */
final class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts database columns into their intended PHP types or Enums.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'integer',
            'name' => 'string',
            'email' => 'string',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'remember_token' => 'string',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Returns tickets submitted by this user as a customer.
     *
     * @return HasMany<Ticket, $this>
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'requester_id');
    }

    /**
     * Returns tickets currently assigned to this user as a support agent.
     *
     * @return HasMany<Ticket, $this>
     */
    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    /**
     * Returns the full history of administrative and system actions taken by this user.
     *
     * @return HasMany<AuditLog, $this>
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [User.php], the next logical file
 * to read is:
 *
 * → [app/Models/Category.php]
 *
 * WHY: After defining who uses the system, we define how tickets
 *       are grouped and categorized.
 * ============================================================
 */
