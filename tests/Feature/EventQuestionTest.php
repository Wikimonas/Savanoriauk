<?php
use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EventQuestionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function organiser_cannot_add_empty_question_to_event()
    {
        $organiser = User::factory()->create(['role' => 'organiser']);
        $event = Event::factory()->create(['organiser_id' => $organiser->id]);

        $response = $this->actingAs($organiser)
            ->post(route('event_questions.store', $event->id), [
                'question' => '',
            ]);

        $response->assertSessionHasErrors(['question']);

        // Optionally confirm it did not create anything
        $this->assertDatabaseMissing('event_questions', [
            'event_id' => $event->id,
        ]);
    }

    #[Test] public function organiser_can_add_question_with_valid_length()
    {
        $organiser = User::factory()->create(['role' => 'organiser']);
        $event = Event::factory()->create(['organiser_id' => $organiser->id]);

        $validQuestion = str_repeat('A', 100); // Between 6 and 254 chars

        $response = $this->actingAs($organiser)
            ->post(route('event_questions.store', $event->id), [
                'question' => $validQuestion,
            ]);

        $response->assertRedirect(route('events.edit', $event->id));
        $this->assertDatabaseHas('event_questions', [
            'event_id' => $event->id,
            'question' => $validQuestion,
        ]);
    }

    #[Test] public function organiser_cannot_add_question_with_invalid_length()
    {
        $organiser = User::factory()->create(['role' => 'organiser']);
        $event = Event::factory()->create(['organiser_id' => $organiser->id]);

        $tooShort = 'abc'; // 3 symbols
        $tooLong = str_repeat('x', 256); // 256 symbols

        // Test too short
        $responseShort = $this->actingAs($organiser)
            ->post(route('event_questions.store', $event->id), [
                'question' => $tooShort,
            ]);
        $responseShort->assertSessionHasErrors(['question']);
        $this->assertDatabaseMissing('event_questions', [
            'event_id' => $event->id,
            'question' => $tooShort,
        ]);

        // Test too long
        $responseLong = $this->actingAs($organiser)
            ->post(route('event_questions.store', $event->id), [
                'question' => $tooLong,
            ]);
        $responseLong->assertSessionHasErrors(['question']);
        $this->assertDatabaseMissing('event_questions', [
            'event_id' => $event->id,
            'question' => $tooLong,
        ]);
    }

    #[Test] public function regular_user_cannot_add_or_delete_event_questions()
    {
        $user = User::factory()->create(['role' => 'user']);
        $organiser = User::factory()->create(['role' => 'organiser']);
        $event = Event::factory()->create(['organiser_id' => $organiser->id]);
        $question = \App\Models\EventQuestion::factory()->create(['event_id' => $event->id]);

        // ❌ Try to add a question as regular user
        $addResponse = $this->actingAs($user)->post(route('event_questions.store', $event->id), [
            'question' => 'Why do you want to volunteer?',
        ]);

        $addResponse->assertForbidden(); // 403
        $this->assertDatabaseMissing('event_questions', [
            'event_id' => $event->id,
            'question' => 'Why do you want to volunteer?',
        ]);

        // ❌ Try to delete an existing question as regular user
        $deleteResponse = $this->actingAs($user)->delete(route('event_questions.destroy', $question->id));

        $deleteResponse->assertForbidden(); // 403
        $this->assertDatabaseHas('event_questions', [
            'id' => $question->id,
        ]);
    }

    #[Test] public function organiser_can_delete_their_own_question()
    {
        // ✅ Create organiser and event
        $organiser = User::factory()->create(['role' => 'organiser']);
        $event = Event::factory()->create(['organiser_id' => $organiser->id]);

        // ✅ Create a question linked to that event
        $question = \App\Models\EventQuestion::factory()->create([
            'event_id' => $event->id,
            'question' => 'Sample question?',
        ]);

        // ✅ Perform delete as the organiser
        $response = $this->actingAs($organiser)
            ->delete(route('event_questions.destroy', $question->id));

        // ✅ Redirected to event edit page
        $response->assertRedirect(route('events.edit', $event->id));

        // ✅ Question no longer in database
        $this->assertDatabaseMissing('event_questions', [
            'id' => $question->id,
        ]);
    }

    #[Test] public function organiser_gets_404_when_deleting_nonexistent_question()
    {
        $organiser = User::factory()->create(['role' => 'organiser']);

        // 👻 Non-existent question ID
        $nonExistentQuestionId = 999;

        $response = $this->actingAs($organiser)
            ->delete(route('event_questions.destroy', $nonExistentQuestionId));

        $response->assertNotFound(); // or ->assertStatus(404);
    }

}
