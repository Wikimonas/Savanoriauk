<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'address' => $this->faker->address(),
            'event_date' => $this->faker->dateTimeBetween('+1 week', '+1 year'),
            'organiser_id' => User::factory()->organiser(),
        ];
    }
}
