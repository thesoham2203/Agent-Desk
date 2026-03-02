<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================================
 * FILE: 2026_03_02_000008_create_kb_articles_table.php
 * LAYER: Migration
 * ============================================================
 *
 * WHAT IS THIS?
 * Creates the database table for Knowledge Base (KB) articles.
 *
 * WHY DOES IT EXIST?
 * To allow agents to store technical documentation and guides.
 * These articles are used by the AI Agent to draft responses.
 *
 * HOW IT FITS IN THE APP:
 * SearchKnowledgeBaseTool queries this table to ground AI replies.
 * Admins can perform CRUD operations in the knowledge base management view.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This migration uses longText for the body, allowing for very detailed
 * articles including formatted text or data.
 * ============================================================
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kb_articles', function (Blueprint $table): void {
            /** Primary auto-incrementing ID for the article. */
            $table->id();

            /** The display title of the article. */
            $table->string('title');

            /** The full content of the documentation. */
            $table->longText('body');

            /** Standard timestamps (created_at/updated_at). */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kb_articles');
    }
};

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [create_kb_articles_table.php], the next logical file
 * to read is:
 *
 * → [database/migrations/2026_03_02_000009_create_ai_runs_table.php]
 *
 * WHY: After defining documentation, we define the history of AI operations
 *       referencing that data.
 * ============================================================
 */
