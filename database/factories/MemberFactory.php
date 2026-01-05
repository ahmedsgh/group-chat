<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->e164PhoneNumber(),
            'type' => fake()->randomElement(['student', 'parent']),
            'gender' => fake()->randomElement(['male', 'female']),
            'last_seen_at' => fake()->optional(0.7)->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
