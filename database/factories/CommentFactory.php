<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       return [
            'content' => $this->faker->sentence(),
            'user_id' => User::factory(),
            // نترك الـ morph fields ليتم تخصيصها داخل الـ Seeder
            'commentable_id' => null,
            'commentable_type' => null,
        ];
    }
}
