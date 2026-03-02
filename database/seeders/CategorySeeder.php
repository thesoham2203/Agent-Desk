<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

/**
 * ============================================================
 * FILE: CategorySeeder.php
 * LAYER: Seeder
 * ============================================================
 *
 * WHAT IS THIS?
 * Seeds the initial set of ticket categories for the helpdesk.
 *
 * WHY DOES IT EXIST?
 * To provide agents and AI with a realistic taxonomy for classify
 * incoming technical and administrative requests.
 *
 * HOW IT FITS IN THE APP:
 * These categories are used by the AI Triage Agent to suggest
 * labels and by human agents for manual classification.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This seeder demonstrating creating multiple records at once,
 * ensuring a consistent initial state for any developer's environment.
 * ============================================================
 */
final class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Populates the categories table with common helpdesk topics.
     */
    public function run(): void
    {
        /**
         * A predefined list of realistic helpdesk categories.
         */
        $categories = [
            ['name' => 'Technical Support', 'description' => 'Issues related to software bugs, hardware failures, or network connectivity.'],
            ['name' => 'Billing & Billing Issues', 'description' => 'Inquiries about invoices, payments, subscriptions, and refunds.'],
            ['name' => 'Account Access', 'description' => 'Password resets, login issues, and MFA configuration assistance.'],
            ['name' => 'Feature Request', 'description' => 'Suggestions for new functionality or improvements to existing tools.'],
            ['name' => 'General Inquiry', 'description' => 'General questions that do not fall into other technical categories.'],
            ['name' => 'Bug Report', 'description' => 'Detailed reports of unexpected behavior in our core systems.'],
        ];

        foreach ($categories as $category) {
            Category::query()->create($category);
        }
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [CategorySeeder.php], the next logical file
 * to read is:
 *
 * → [database/seeders/UserSeeder.php]
 *
 * WHY: After defining the subjects, we define the human actors
 *       who will be using them.
 * ============================================================
 */
