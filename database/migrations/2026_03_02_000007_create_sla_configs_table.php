<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================================
 * FILE: 2026_03_02_000007_create_sla_configs_table.php
 * LAYER: Migration
 * ============================================================
 *
 * WHAT IS THIS?
 * Creates the database table for global Service Level Agreement (SLA)
 * configuration settings.
 *
 * WHY DOES IT EXIST?
 * To define target response and resolution times, so the system can
 * alert agents when a ticket is overdue.
 *
 * HOW IT FITS IN THE APP:
 * This table will only contain one row, which stores global settings.
 * Scheduler refers to these values to check ticket deadlines.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This migration uses unsignedInteger columns for hours,
 * preventing invalid negative values and ensuring precise input.
 * ============================================================
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sla_configs', function (Blueprint $table): void {
            /** Primary auto-incrementing ID. Usually only ID 1 is used. */
            $table->id();

            /** Target hours for an initial agent response. Default: 4 hours. */
            $table->unsignedInteger('first_response_hours')->default(4);

            /** Target hours for setting a ticket to status 'resolved'. Default: 24 hours. */
            $table->unsignedInteger('resolution_hours')->default(24);

            /** Configuration timestamps. */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sla_configs');
    }
};

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [create_sla_configs_table.php], the next logical file
 * to read is:
 *
 * → [database/migrations/2026_03_02_000008_create_kb_articles_table.php]
 *
 * WHY: After defining system compliance, we define the knowledge
 *       base utilized by agents and AI.
 * ============================================================
 */
