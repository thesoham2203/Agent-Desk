<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SlaConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SlaConfig>
 */
final class SlaConfigFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<SlaConfig>
     */
    protected $model = SlaConfig::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_response_hours' => 24,
            'resolution_hours' => 72,
        ];
    }
}
