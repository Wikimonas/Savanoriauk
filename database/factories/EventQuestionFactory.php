<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventQuestionFactory extends Factory
{
    protected $model = EventQuestion::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'question' => $this->faker->sentence(6, true),
        ];
    }
}
