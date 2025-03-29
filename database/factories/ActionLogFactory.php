<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActionLog>
 */
class ActionLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create()->id, // Create a new User for each log
            'action' => $this->faker->randomElement(['User Registered', 'Event Created', 'Event Updated', 'Event Deleted']),
            'model_type' => $this->faker->randomElement(['App\Models\Event', 'App\Models\User']),
            'model_id' => function (array $attributes) {
                // If the model type is an Event, create an event and use its ID, else use null
                return $attributes['model_type'] === 'App\Models\Event' ? \App\Models\Event::factory()->create()->id : null;
            },
            'changes' => json_encode([
                'before' => $this->faker->sentence,
                'after' => $this->faker->sentence,
            ]), // Example of changes, can be expanded as needed
            'ip_address' => $this->faker->ipv4,
        ];
    }
}
