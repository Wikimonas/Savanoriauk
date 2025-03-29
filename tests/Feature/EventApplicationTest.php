<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\EventQuestion;
use App\Models\EventApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EventApplicationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function organiser_can_add_a_question_to_an_event()
    {
        $organiser = User::factory()->create(['role' => 'organiser']);
        $event = Event::factory()->create(['organiser_id' => $organiser->id]);

        $response = $this->actingAs($organiser)->post(route('event_questions.store', $event), [
            'question' => 'Why do you want to volunteer?',
        ]);

        $response->assertRedirect(route('events.edit', $event->id));
        $this->assertDatabaseHas('event_questions', [
            'event_id' => $event->id,
            'question' => 'Why do you want to volunteer?',
        ]);
    }

    #[Test]
    public function user_can_submit_application_with_basic_info_and_answers()
    {
        $organiser = User::factory()->create(['role' => 'organiser']);
        $event = Event::factory()->create(['organiser_id' => $organiser->id]);
        $question = EventQuestion::factory()->create([
            'event_id' => $event->id,
            'question' => 'Do you have any experience?',
        ]);

        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->post(route('events.apply.store', $event), [
            'answers' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => '123456789',
                'address' => 'Example Street 5',
                $question->id => 'Yes, I have volunteered before.',
            ]
        ]);

        $response->assertRedirect(route('events.index'));
        $this->assertDatabaseHas('event_applications', [
            'event_id' => $event->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals('Yes, I have volunteered before.', EventApplication::first()->answers[$question->id]);
    }
}
