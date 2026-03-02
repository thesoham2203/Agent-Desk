<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================================
 * FILE: 2026_03_02_000006_create_macros_table.php
 * LAYER: Migration
 * ============================================================
 *
 * WHAT IS THIS?
 * Creates the database table for "Macros" (canned responses).
 *
 * WHY DOES IT EXIST?
 * To allow agents to quickly insert common, pre-written responses
 * like greetings or troubleshooting steps, reducing repetitive typing.
 *
 * HOW IT FITS IN THE APP:
 * Macro model stores the title and content. The UI displays these
 * to agents when drafting a reply.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This migration creates a simple CRUD-enabled table that stores
 * rich text content (body) in a text column for flexibility.
 * ============================================================
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('macros', function (Blueprint $table): void {
            /** Primary ID for the macro. */
            $table->id();

            /** The short, identifiable name of the macro. */
            $table->string('title');

            /** The pre-written text of the macro. */
            $table->text('body');

            /** creation and update timestamps. */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('macros');
    }
};

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [create_macros_table.php], the next logical file
 * to read is:
 *
 * → [database/migrations/2026_03_02_000007_create_sla_configs_table.php]
 *
 * WHY: After defining repetitive content, we define system-wide
 *       performance targets for those replies.
 * ============================================================
 */
