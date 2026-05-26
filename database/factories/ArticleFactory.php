<?php

namespace Database\Factories;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
     $title = $this->faker->sentence(6);
        $status = $this->faker->randomElement(ArticleStatus::values());

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1, 1000),
            'content' => $this->faker->paragraphs(5, true),
            'user_id' => User::factory(),
            'status' => $status,
            'published_at' => $status === ArticleStatus::PUBLISHED->value ? now()->subDays(rand(1, 30)) : null,
        ];
    }
}
