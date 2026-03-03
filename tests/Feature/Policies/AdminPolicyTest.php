<?php

declare(strict_types=1);

/**
 * ============================================================
 * FILE: AdminPolicyTest.php
 * LAYER: Test
 * ============================================================
 *
 * WHAT IS THIS?
 * A consolidated suite for all administration-level policies.
 *
 * WHY DOES IT EXIST?
 * To compactly prove that categories, SLAs, macros, and audit logs
 * are safe from non-administrators.
 *
 * HOW IT FITS IN THE APP:
 * Part of the suite to verify system authorization.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Feature Tests allow us to use Laravel's models and factories to create
 * temporary states in the testing database. We can "act as" a specific user
 * and ensure that the Gate facade allows or denies specific actions.
 * ============================================================
 */

use App\Enums\UserRole;
use App\Models\AiRun;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Macro;
use App\Models\SlaConfig;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

// Admin category management
it('allows admin to manage categories', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin);

    expect(Gate::allows('create', Category::class))->toBeTrue();
});

// Category denial
it('denies agent from creating a category', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);

    $this->actingAs($agent);

    expect(Gate::allows('create', Category::class))->toBeFalse();
});

it('denies requester from creating a category', function (): void {
    $requester = User::factory()->create(['role' => UserRole::Requester]);

    $this->actingAs($requester);

    expect(Gate::allows('create', Category::class))->toBeFalse();
});

// Admin SLA management
it('allows admin to update sla config', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $sla = SlaConfig::factory()->create();

    $this->actingAs($admin);

    expect(Gate::allows('update', $sla))->toBeTrue();
});

// SLA denial
it('denies agent from updating sla config', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $sla = SlaConfig::factory()->create();

    $this->actingAs($agent);

    expect(Gate::allows('update', $sla))->toBeFalse();
});

// Admin Macro management
it('allows admin to manage macros', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin);

    expect(Gate::allows('create', Macro::class))->toBeTrue();
});

// Agent macro view
it('allows agent to view macros', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);

    $this->actingAs($agent);

    expect(Gate::allows('viewAny', Macro::class))->toBeTrue();
});

// Requester macro view denied
it('denies requester from viewing macros', function (): void {
    $requester = User::factory()->create(['role' => UserRole::Requester]);

    $this->actingAs($requester);

    expect(Gate::allows('viewAny', Macro::class))->toBeFalse();
});

// Admin audit view
it('allows admin to view audit logs', function (): void {
    $admin = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($admin);

    expect(Gate::allows('viewAny', AuditLog::class))->toBeTrue();
});

// Agent audit view denied
it('denies agent from viewing audit logs', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);

    $this->actingAs($agent);

    expect(Gate::allows('viewAny', AuditLog::class))->toBeFalse();
});

// Agent AI trigger allowed
it('allows agent to trigger an ai run', function (): void {
    $agent = User::factory()->create(['role' => UserRole::Agent]);
    $ticket = Ticket::factory()->create(); // Using create to test creation of an AI Run on a ticket

    $this->actingAs($agent);

    expect(Gate::allows('create', [AiRun::class, $ticket]))->toBeTrue();
});

// Requester AI trigger denied
it('denies requester from triggering an ai run', function (): void {
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = Ticket::factory()->create();

    $this->actingAs($requester);

    expect(Gate::allows('create', [AiRun::class, $ticket]))->toBeFalse();
});

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Congratulations! You have reached the end of the Day 3 materials.
 * Run `php artisan test --compact tests/Feature/Policies/` and
 * `composer test:types` to verify completion.
 *
 * → NEXT MODULE: Day 4 (Requester Flow)
 *
 * WHY: Once authorization rules are rock solid, we can securely
 * begin assembling UI and letting users input data.
 * ============================================================
 */
