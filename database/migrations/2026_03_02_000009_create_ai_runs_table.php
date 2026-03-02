<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================================
 * FILE: 2026_03_02_000009_create_ai_runs_table.php
 * LAYER: Migration
 * ============================================================
 *
 * WHAT IS THIS?
 * Creates the database table for recording all AI Agent invocations.
 *
 * WHY DOES IT EXIST?
 * To track, audit, and provide persistence for AI results,
 * ensuring agents can clearly see what the AI has suggested and why.
 *
 * HOW IT FITS IN THE APP:
 * An AI Run is linked to a Ticket and an initiating User.
 * Results and error messages are saved here for the UI to display progress.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This migration uses a json column to store structured data safely
 * and multiple nullable strings for flexible tracking (hash, model, provider).
 * ============================================================
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_runs', function (Blueprint $table): void {
            /** Primary auto-incrementing ID. */
            $table->id();

            /** The ticket this AI operation was performed on. Cascade on delete. */
            $table->foreignId('ticket_id')
                ->constrained('tickets')
                ->cascadeOnDelete();

            /** The human user who triggered this AI action. Cascade on delete. */
            $table->foreignId('initiated_by_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /** Categorization: triage, reply_draft, thread_summary. Cast to AiRunType. */
            $table->string('run_type');

            /** Execution status: queued, running, succeeded, failed. Cast to AiRunStatus. */
            $table->string('status')->default('queued');

            /** MD5/SHA hash of inputs used for simple caching/dedup. */
            $table->string('input_hash')->nullable();

            /** The raw JSON response from the LLM provider. Cast to 'array' in model. */
            $table->json('output_json')->nullable();

            /** The name of the LLM provider (e.g., 'groq'). */
            $table->string('provider')->nullable();

            /** The specific model used (e.g., 'llama-3.1-70b-versatile'). */
            $table->string('model')->nullable();

            /** Any error message or response code returned from the provider. */
            $table->text('error_message')->nullable();

            /** standard creation/update timestamps. */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_runs');
    }
};

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [create_ai_runs_table.php], the next logical file
 * to read is:
 *
 * → [database/migrations/2026_03_02_000010_create_audit_logs_table.php]
 *
 * WHY: After defining automated operations, we define tracking
 *       for all human actions in the system.
 * ============================================================
 */
