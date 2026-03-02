<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * ============================================================
 * FILE: DatabaseSeeder.php
 * LAYER: Seeder
 * ============================================================
 *
 * WHAT IS THIS?
 * The main entry point for the database seeding process.
 *
 * WHY DOES IT EXIST?
 * To orchestrate the execution of individual seeders in the correct
 * relational order (e.g., creating users before creating tickets).
 *
 * HOW IT FITS IN THE APP:
 * When you run `php artisan db:seed`, this class is executed,
 * populating the entire system with the data defined in its sub-seeders.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This file uses the $this->call() method to delegate work to
 * specialized seeder classes, keeping the codebase organized.
 * ============================================================
 */
final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Calls individual seeders in a logical order to satisfy foreign keys.
     */
    public function run(): void
    {
        $this->call([
            /** 1. Core configuration and subjects. */
            SlaConfigSeeder::class,
            CategorySeeder::class,

            /** 2. Actors and knowledge base tools. */
            UserSeeder::class,
            KbArticleSeeder::class,
            MacroSeeder::class,

            /** 3. Transactional ticket data and history. */
            TicketSeeder::class,
            AuditLogSeeder::class,
        ]);
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [DatabaseSeeder.php], the next logical file
 * to read is:
 *
 * → [tutor.md]
 *
 * WHY: You have finished reading Day 2 files.
 *      Open tutor.md and follow the verification commands
 *      to confirm everything works.
 * ============================================================
 */
