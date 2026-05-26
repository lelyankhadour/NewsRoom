<?php

namespace Database\Factories;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attachment>
 */
class AttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'file_path' => 'attachments/' . $this->faker->uuid() . '.jpg',
            'file_name' => $this->faker->word() . '.jpg',
            'file_type' => 'image/jpeg',
            'file_size' => $this->faker->numberBetween(1024, 512000), // أحجام بين 1 كيلوبايت و 500 كيلوبايت
            'attachable_id' => null,
            'attachable_type' => null,
        ];
    }
}
