<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================================
 * FILE: 2026_03_02_000010_create_audit_logs_table.php
 * LAYER: Migration
 * ============================================================
 *
 * WHAT IS THIS?
 * Creates the database table for recording administrative and system actions.
 *
 * WHY DOES IT EXIST?
 * To satisfy auditing requirements, showing who changed which ticket,
 * when things were assigned, and what status changes occurred.
 *
 * HOW IT FITS IN THE APP:
 * Every important business action (assigning, closing, AI runs)
 * creates an entry here with old/new state details.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This migration uses nullable JSON columns for simple diffing and
 * nullOnDelete for ticket_id to preserve the log if the ticket is deleted.
 * ============================================================
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table): void {
            /** Primary auto-incrementing ID. */
            $table->id();

            /** The related ticket, if any. Null if system-wide or if ticket deleted. */
            $table->foreignId('ticket_id')
                ->nullable()
                ->constrained('tickets')
                ->nullOnDelete();

            /** The user who performed the action. Required. Cascade delete logic. */
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /** Short name of the event (e.g., 'ticket.created', 'status.changed'). */
            $table->string('action');

            /** snapshot of the data before the change. Cast to 'array' in model. */
            $table->json('old_values')->nullable();

            /** snapshot of the data after the change. Cast to 'array' in model. */
            $table->json('new_values')->nullable();

            /** standard timestamps for the events. */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [audit_logs_table.php], the next logical file
 * to read is:
 *
 * → [app/Models/User.php]
 *
 * WHY: After defining our database tables, we need to create
 *       Eloquent models to interact with them in PHP.
 * ============================================================
 */
