<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\KbArticle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KbArticle>
 */
final class KbArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            /** A realistic-looking IT support documentation title. */
            'title' => $this->faker->sentence(4),

            /** 3-4 paragraphs of documentation content. */
            'body' => $this->faker->paragraphs(4, true),
        ];
    }
}
