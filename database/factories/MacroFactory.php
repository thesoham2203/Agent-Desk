<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Macro;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Macro>
 */
final class MacroFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Macro>
     */
    protected $model = Macro::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->words(3, true),
            'body' => $this->faker->paragraph(),
        ];
    }
}
