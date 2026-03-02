<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Macro;
use Illuminate\Database\Seeder;

/**
 * ============================================================
 * FILE: MacroSeeder.php
 * LAYER: Seeder
 * ============================================================
 *
 * WHAT IS THIS?
 * Seeds the application with canned responses for agents.
 *
 * WHY DOES IT EXIST?
 * To show how agents can use pre-written text to maintain consistent
 * communication and speed.
 *
 * HOW IT FITS IN THE APP:
 * These records are displayed as a list of selectable items for
 * agents when drafting a reply to a ticket.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * A simple seeder for static text templates.
 * ============================================================
 */
final class MacroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates five key macros for common agent-customer interactions.
     */
    public function run(): void
    {
        $macros = [
            ['title' => 'Greeting & Opening', 'body' => 'Hello! Thank you for contacting our support team. We have received your request and an agent will be with you shortly.'],
            ['title' => 'Status Update', 'body' => 'We are still processing your request. Thank you for your patience as our technical team investigates the issue further.'],
            ['title' => 'Closing - Resolved', 'body' => 'We have marked this issue as resolved. If you need any further assistance, please do not hesitate to open a new ticket.'],
            ['title' => 'Clarification Needed', 'body' => 'To help us resolve this faster, could you please provide more details or a screenshot of the error you are seeing?'],
            ['title' => 'Escalation Notice', 'body' => 'I am escalating your ticket to our senior engineering team for further review. You can expect an update within the next 24 hours.'],
        ];

        foreach ($macros as $macro) {
            Macro::query()->create($macro);
        }
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand [MacroSeeder.php], the next logical file
 * to read is:
 *
 * → [database/seeders/TicketSeeder.php]
 *
 * WHY: After defining all supporting data, we define the tickets
 *       at the heart of the system.
 * ============================================================
 */
