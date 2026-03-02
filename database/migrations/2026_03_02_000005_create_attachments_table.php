<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================================
 * FILE: 2026_03_02_000005_create_attachments_table.php
 * LAYER: Migration
 * ============================================================
 *
 * WHAT IS THIS?
 * Creates the database table for tracking file attachments.
 *
 * WHY DOES IT EXIST?
 * To allow requesters and agents to upload files (logs, screenshots)
 * that clarify technical issues.
 *
 * HOW IT FITS IN THE APP:
 * An Attachment belongs to a Ticket and can be optionally linked to a
 * specific TicketMessage for contextual reference.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This migration uses an unsignedBigInteger for the file size,
 * ensuring high capacity, and stores path/name data for retrieval.
 * ============================================================
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table): void {
            /** Primary ID for the attachment. */
            $table->id();

            /** The parent ticket this file is attached to. Cascade delete with ticket. */
            $table->foreignId('ticket_id')
                ->constrained('tickets')
                ->cascadeOnDelete();

            /** The specific message where this file was uploaded. Nullable for ticket-level. */
            $table->foreignId('message_id')
                ->nullable()
                ->constrained('ticket_messages')
                ->nullOnDelete();

            /** The storage driver used (default: 'private'). */
            $table->string('disk')->default('private');

            /** The full path to the file in the storage driver. */
            $table->string('path');

            /** original filename for human-friendly downloads. */
            $table->string('original_name');

            /** Mime type (e.g., 'image/png', 'application/pdf'). Used for secure download headers. */
            $table->string('mime_type');

            /** File size in bytes. */
            $table->unsignedBigInteger('size');

            /** Created at/updated at timestamps. */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [create_attachments_table.php], the next logical file
 * to read is:
 *
 * → [database/migrations/2026_03_02_000006_create_macros_table.php]
 *
 * WHY: After handling file transfers, we define canned responses
 *       for agents to quickly answer common questions.
 * ============================================================
 */
