<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Generate a user if not provided
            'action' => $this->faker->randomElement(['show', 'hide']), // Randomly choose between 'show' or 'hide'
            'condition' => $this->faker->randomElement(['contains', 'starts_with', 'ends_with', 'exact']), // Random condition
            'url' => $this->faker->url(), // Generate a random valid URL
        ];
    }
}
