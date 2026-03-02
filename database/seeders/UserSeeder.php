<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * ============================================================
 * FILE: UserSeeder.php
 * LAYER: Seeder
 * ============================================================
 *
 * WHAT IS THIS?
 * Seeds the application with initial users, including demo accounts.
 *
 * WHY DOES IT EXIST?
 * To provide immediate access for manual testing and a baseline of
 * historical data for the helpdesk.
 *
 * HOW IT FITS IN THE APP:
 * This seeder creates Administrators, Support Agents, and Requesters,
 * allowing you to test role-based access control (RBAC) instantly.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This seeder combines manual record creation (for demo accounts)
 * with factory-driven generation for bulk data.
 * ============================================================
 */
final class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Populates the users table with demo accounts and random users.
     */
    public function run(): void
    {
        /** 1. Create fixed demo accounts for easy manual login. */
        $password = Hash::make('password');

        User::query()->create([
            'name' => 'Main Admin',
            'email' => 'admin@agentdesk.test',
            'password' => $password,
            'role' => UserRole::Admin,
            'email_verified_at' => now(),
        ]);

        User::query()->create([
            'name' => 'Support Agent',
            'email' => 'agent@agentdesk.test',
            'password' => $password,
            'role' => UserRole::Agent,
            'email_verified_at' => now(),
        ]);

        User::query()->create([
            'name' => 'Demo Requester',
            'email' => 'requester@agentdesk.test',
            'password' => $password,
            'role' => UserRole::Requester,
            'email_verified_at' => now(),
        ]);

        /** 2. Seed additional random agents and requesters via Faker. */
        User::factory()->count(3)->create(['role' => UserRole::Agent]);
        User::factory()->count(10)->create(['role' => UserRole::Requester]);
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [UserSeeder.php], the next logical file
 * to read is:
 *
 * → [database/seeders/KbArticleSeeder.php]
 *
 * WHY: After defining the people, we define the knowledge
 *       they use to solve problems.
 * ============================================================
 */
