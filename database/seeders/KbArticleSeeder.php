<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\KbArticle;
use Illuminate\Database\Seeder;

/**
 * ============================================================
 * FILE: KbArticleSeeder.php
 * LAYER: Seeder
 * ============================================================
 *
 * WHAT IS THIS?
 * Seeds documentation for the system's Knowledge Base (KB).
 *
 * WHY DOES IT EXIST?
 * To populate the system with documentation so that the AI
 * Triage and Reply Agents have reference data to "ground" their outputs.
 *
 * HOW IT FITS IN THE APP:
 * The SearchKnowledgeBaseTool queries these articles when drafting
 * responses for agents.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * This seeder uses raw text content to demonstrate how long-form
 * data is stored in the database.
 * ============================================================
 */
final class KbArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Populates the KB with technical documentation.
     */
    public function run(): void
    {
        /** A list of common support documentation topics. */
        $articles = [
            ['title' => 'Resetting Your Account Password', 'body' => 'If you have forgotten your password, go to the login page and click "Forgot Password". You will receive an email instruction to verify your identity and set a new credential.'],
            ['title' => 'Enabling Multi-Factor Authentication (MFA)', 'body' => 'Security is our priority. To enable MFA, visit your Account Settings, select "Security", and follow the prompts to link your mobile authenticator app.'],
            ['title' => 'Troubleshooting VPN Connectivity', 'body' => 'If you are unable to connect to the corporate VPN, first ensure your internet connection is stable. Then, check if your VPN client software is updated to the latest version.'],
            ['title' => 'Billing Schedule and Invoicing', 'body' => 'Invoices are generated on the 1st of every month. Payments are automatically processed within 3 business days using your primary payment method on file.'],
            ['title' => 'Reporting a Security Vulnerability', 'body' => 'If you discover a security issue, please do not share it publicly. Instead, create a ticket with the "Bug Report" category and mark it as Urgent for priority handling.'],
        ];

        foreach ($articles as $article) {
            KbArticle::query()->create($article);
        }

        /** Add random articles to reach the requested count. */
        KbArticle::factory()->count(5)->create();
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [KbArticleSeeder.php], the next logical file
 * to read is:
 *
 * → [database/seeders/MacroSeeder.php]
 *
 * WHY: After defining documentation, we define canned
 *       responses for fast agent execution.
 * ============================================================
 */
