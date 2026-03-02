<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AiRunStatus;
use App\Enums\AiRunType;
use App\Models\AiRun;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiRun>
 */
final class AiRunFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<AiRun>
     */
    protected $model = AiRun::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'initiated_by_user_id' => User::factory(),
            'run_type' => AiRunType::Triage,
            'status' => AiRunStatus::Succeeded,
            'input_hash' => $this->faker->sha256(),
            'output_json' => ['result' => $this->faker->sentence()],
            'provider' => 'groq',
            'model' => 'llama-3.3-70b-versatile',
        ];
    }
}
