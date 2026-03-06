<?php

declare(strict_types=1);

/**
 * ============================================================
 * FILE: AdminAuthTest.php
 * LAYER: Testing (Feature)
 * ============================================================
 *
 * WHAT IS THIS?
 * Security tests for the /admin route group.
 *
 * WHY DOES IT EXIST?
 * To ensure that only users with the 'admin' role can access
 * administrative management and monitoring screens.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Pest's actingAs() helper simulates an authenticated session
 * for a specific user, allowing us to test middleware and gates.
 * ============================================================
 */
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are redirected to login when accessing admin routes', function (): void {
    $this->get('/admin/categories')->assertRedirect('/login');
    $this->get('/admin/macros')->assertRedirect('/login');
    $this->get('/admin/sla')->assertRedirect('/login');
    $this->get('/admin/kb-articles')->assertRedirect('/login');
    $this->get('/admin/audit-log')->assertRedirect('/login');
    $this->get('/admin/ai-runs')->assertRedirect('/login');
});

test('requesters are redirected from admin routes', function (): void {
    $user = User::factory()->create(['role' => UserRole::Requester]);

    $this->actingAs($user)
        ->get('/admin/categories')
        ->assertRedirect(route('requester.tickets.index'));
});

test('agents are redirected from admin routes', function (): void {
    $user = User::factory()->create(['role' => UserRole::Agent]);

    $this->actingAs($user)
        ->get('/admin/categories')
        ->assertRedirect(route('agent.queue'));
});

test('admins can access admin routes', function (): void {
    $user = User::factory()->create(['role' => UserRole::Admin]);

    $this->actingAs($user)
        ->get('/admin/categories')
        ->assertOk();

    $this->actingAs($user)
        ->get('/admin/macros')
        ->assertOk();

    $this->actingAs($user)
        ->get('/admin/sla')
        ->assertOk();

    $this->actingAs($user)
        ->get('/admin/kb-articles')
        ->assertOk();

    $this->actingAs($user)
        ->get('/admin/audit-log')
        ->assertOk();

    $this->actingAs($user)
        ->get('/admin/ai-runs')
        ->assertOk();
});

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [tests/Feature/Admin/AdminCrudTest.php]
 * WHY: Now that we know they can get in, we test what they can DO.
 * ============================================================
 */
