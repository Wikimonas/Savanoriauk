<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EventDeleteTest extends TestCase
{
    use RefreshDatabase;

    #[Test] public function non_organiser_cannot_delete_event()
    {
        // Create an organizer and another user
        $organizer = User::factory()->organiser()->create();
        $randomUser = User::factory()->create();

        // Create an event
        $event = Event::factory()->create(['organiser_id' => $organizer->id]);

        // Act as the random user
        $this->actingAs($randomUser);

        // Attempt to delete event
        $response = $this->delete(route('events.destroy', $event->id));

        // Assert event still exists
        $this->assertDatabaseHas('events', ['id' => $event->id]);

    }

    #[Test] public function organiser_can_delete_event()
    {
        // Create an organizer
        $organizer = User::factory()->organiser()->create();

        // Create an event
        $event = Event::factory()->create(['organiser_id' => $organizer->id]);

        // Act as the organizer
        $this->actingAs($organizer);

        // Send delete request
        $response = $this->delete(route('events.destroy', $event->id));

        // Assert database no longer contains event
        $this->assertDatabaseMissing('events', ['id' => $event->id]);

        // Assert redirection after deletion
        $response->assertRedirect(route('events.manage'));
    }

    #[Test] public function organiser_cannot_delete_another_organisers_event()
    {
        $organiser1 = User::factory()->create(['role' => 'organiser']);
        $organiser2 = User::factory()->create(['role' => 'organiser']);

        $event = Event::factory()->create([
            'organiser_id' => $organiser1->id,
        ]);

        $response = $this->actingAs($organiser2)
            ->delete(route('events.destroy', $event->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
        ]);
    }

    #[Test] public function deleting_a_nonexistent_event_returns_404()
    {
        $organiser = \App\Models\User::factory()->create(['role' => 'organiser']);

        $nonExistentEventId = 999;

        $response = $this->actingAs($organiser)
            ->delete(route('events.destroy', $nonExistentEventId));

        $response->assertNotFound(); // or ->assertStatus(404);
    }

}
