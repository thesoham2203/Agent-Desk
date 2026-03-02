<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attachment>
 */
final class AttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'message_id' => TicketMessage::factory(),
            'disk' => 'private',
            'path' => 'attachments/'.$this->faker->uuid(),
            'original_name' => 'file.txt',
            'mime_type' => 'text/plain',
            'size' => 1024,
        ];
    }
}
