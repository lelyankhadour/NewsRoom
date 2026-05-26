<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            "user_id" =>User::factory(),
            "job_title"=>$this->faker->jobTitle(),
            "department"=>$this->faker->randomElement(["tech","backend","frontend","devops"]),
            'phone_number' => $this->faker->phoneNumber(),
            "bio"=>$this->faker->paragraph(), ];
    }
}
