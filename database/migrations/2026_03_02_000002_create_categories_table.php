<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================================
 * FILE: 2026_03_02_000002_create_categories_table.php
 * LAYER: Migration
 * ============================================================
 *
 * WHAT IS THIS?
 * Creates the database table for ticket categories (e.g., "Billing", "Technical Support").
 *
 * WHY DOES IT EXIST?
 * To allow support staff to group tickets logically and assign relevant expertise
 * to those categories.
 *
 * HOW IT FITS IN THE APP:
 * Tickets belong to a Category. AI Triage uses these records to match incoming
 * ticket text to a specific domain.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This migration creates a lookup table that allows for one-to-many relationships
 * where many tickets can belong to a single category name.
 * ============================================================
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table): void {
            /** Primary auto-incrementing ID. */
            $table->id();

            /** The display name of the category. Must be unique. */
            $table->string('name')->unique();

            /** A short explanation of what kind of requests fall into this category. */
            $table->text('description')->nullable();

            /** created_at and updated_at timestamps. */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [create_categories_table.php], the next logical file
 * to read is:
 *
 * → [database/migrations/2026_03_02_000003_create_tickets_table.php]
 *
 * WHY: After defining subjects, we create the table to store the tickets
 *       themselves.
 * ============================================================
 */
