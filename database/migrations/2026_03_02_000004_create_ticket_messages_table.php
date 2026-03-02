<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================================
 * FILE: 2026_03_02_000004_create_ticket_messages_table.php
 * LAYER: Migration
 * ============================================================
 *
 * WHAT IS THIS?
 * Creates the database table for all messages within a ticket thread.
 *
 * WHY DOES IT EXIST?
 * To store conversation history, including requester replies, public agent
 * responses, and private internal notes.
 *
 * HOW IT FITS IN THE APP:
 * A Ticket can have many Messages. Each message is linked to an author
 * (User) and can have associated attachments.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This migration implements a "hasMany" relationship on the Ticket model
 * and uses string-based flags to determine message visibility (public/internal).
 * ============================================================
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ticket_messages', function (Blueprint $table): void {
            /** Primary ID for the message. */
            $table->id();

            /** The parent ticket this message belongs to. */
            $table->foreignId('ticket_id')
                ->constrained('tickets')
                ->cascadeOnDelete();

            /** The user who wrote the message. Required for all messages. */
            $table->foreignId('author_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /** Visibility type (public/internal). Cast to TicketMessageType enum. */
            $table->string('type')
                ->default('public');

            /** The actual text content of the message. */
            $table->text('body');

            /** standard timestamps for creation and update. */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_messages');
    }
};

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [create_ticket_messages_table.php], the next logical file
 * to read is:
 *
 * → [database/migrations/2026_03_02_000005_create_attachments_table.php]
 *
 * WHY: Once we have messages, we need to handle file uploads
 *       associated with those messages.
 * ============================================================
 */
