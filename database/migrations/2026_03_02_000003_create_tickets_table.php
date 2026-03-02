<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================================
 * FILE: 2026_03_02_000003_create_tickets_table.php
 * LAYER: Migration
 * ============================================================
 *
 * WHAT IS THIS?
 * Creates the core database table for storing support tickets.
 *
 * WHY DOES IT EXIST?
 * To track customer issues, assign them to agents, categorize them,
 * and manage their priority and lifecycle status.
 *
 * HOW IT FITS IN THE APP:
 * This is the central entity. Users, Categories, Messages, and AI Runs
 * all revolve around this table.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This migration uses foreign keys to create relational links between
 * tables, ensuring data integrity (e.g., you can't have a ticket from
 * a user that doesn't exist).
 * ============================================================
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table): void {
            /** Primary ID for the ticket. */
            $table->id();

            /** The user who created the ticket. Cascade delete because no user means no ticket. */
            $table->foreignId('requester_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /** The support agent assigned to handle the ticket. Nullable initially. */
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /** The logical group for this ticket. Nullable if not triaged yet. */
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            /** Current lifecycle state. Default is 'new'. Cast to TicketStatus enum in code. */
            $table->string('status')->default('new');

            /** Urgency level. Defaults to 'medium'. Cast to TicketPriority enum in code. */
            $table->string('priority')->default('medium');

            /** Brief subject summary of the issue. */
            $table->string('title');

            /** Full textual description of the issue. */
            $table->text('body');

            /** Timestamp of the first public reply from an agent. Used for SLA tracking. */
            $table->timestamp('first_responded_at')->nullable();

            /** Timestamp when status was set to 'resolved'. Used for resolution time metrics. */
            $table->timestamp('resolved_at')->nullable();

            /** standard timestamps for creation and update. */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [create_tickets_table.php], the next logical file
 * to read is:
 *
 * → [database/migrations/2026_03_02_000004_create_ticket_messages_table.php]
 *
 * WHY: After defining the ticket container, we need to store the
 *       actual conversations inside it.
 * ============================================================
 */
