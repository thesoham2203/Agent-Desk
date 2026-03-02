<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
final class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'requester_id' => User::factory(),
            'assigned_to' => null,
            'category_id' => Category::factory(),
            'status' => TicketStatus::New,
            'priority' => TicketPriority::Medium,
            'title' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
        ];
    }
}
