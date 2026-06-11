<?php
namespace Database\Factories;

use App\Models\ApiRequestLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApiRequestLogFactory extends Factory
{
    protected $model = ApiRequestLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), 
            'method' => $this->faker->randomElement(['GET', 'POST', 'PUT', 'DELETE']),
            'url' => $this->faker->url(),
            'payload' => json_encode(['key' => $this->faker->word()]),
            'ip_address' => $this->faker->ipv4(),
            'status_code' => $this->faker->randomElement([200, 201, 400, 401, 404, 500]),
            'execution_time_ms' => $this->faker->randomFloat(2, 10, 500),
        ];
    }
}