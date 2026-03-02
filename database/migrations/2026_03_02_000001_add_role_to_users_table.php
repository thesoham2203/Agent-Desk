<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================================
 * FILE: 2026_03_02_000001_add_role_to_users_table.php
 * LAYER: Migration
 * ============================================================
 *
 * WHAT IS THIS?
 * This migration adds the 'role' column to the default Laravel 'users' table.
 *
 * WHY DOES IT EXIST?
 * To support Role-Based Access Control (RBAC). Every user in AgentDesk must
 * have a defined role (Admin, Agent, or Requester) to determine their permissions.
 *
 * HOW IT FITS IN THE APP:
 * The User model casts this column to the UserRole enum. Policies read this
 * value to authorize or deny access to specific UI features and API endpoints.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * A Migration is a version-controlled instruction for your database schema.
 * Instead of sharing SQL files, developers share migrations to ensure
 * everyone's database structure is identical.
 * ============================================================
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            /**
             * The system-wide role for this user.
             * Defaults to 'requester' for self-registered users.
             */
            $table->string('role')
                ->default('requester')
                ->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('role');
        });
    }
};

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [add_role_to_users_table.php], the next logical file
 * to read is:
 *
 * → [database/migrations/2026_03_02_000002_create_categories_table.php]
 *
 * WHY: After defining who uses the system, we define how tickets
 *       are categorized.
 * ============================================================
 */
