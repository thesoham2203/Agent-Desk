<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SlaConfig;
use Illuminate\Database\Seeder;

/**
 * ============================================================
 * FILE: SlaConfigSeeder.php
 * LAYER: Seeder
 * ============================================================
 *
 * WHAT IS THIS?
 * Seeds the initial global configuration for Service Level Agreements (SLA).
 *
 * WHY DOES IT EXIST?
 * To ensure the application has default response and resolution time
 * targets immediately upon installation.
 *
 * HOW IT FITS IN THE APP:
 * This seeder creates exactly one row in the sla_configs table,
 * which is then read by the background scheduler to track performance.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Seeders are PHP classes used to populate the database with initial
 * or "dummy" data. This is crucial for local development and testing.
 * ============================================================
 */
final class SlaConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates a single default configuration for the helpdesk.
     */
    public function run(): void
    {
        /**
         * Seeds the default targets:
         * 4 hours for first response, 24 hours for full resolution.
         */
        SlaConfig::query()->create([
            'first_response_hours' => 4,
            'resolution_hours' => 24,
        ]);
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [SlaConfigSeeder.php], the next logical file
 * to read is:
 *
 * → [database/seeders/CategorySeeder.php]
 *
 * WHY: After defining system targets, we define the ticket
 *       categories those targets apply to.
 * ============================================================
 */
