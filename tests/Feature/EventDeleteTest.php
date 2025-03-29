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

    #[Test] public function organiser_can_view_their_events()
    {
        // Create an organizer
        $organizer = User::factory()->organiser()->create();

        // Create multiple events
        Event::factory()->count(5)->create(['organiser_id' => $organizer->id]);

        // Act as organizer
        $this->actingAs($organizer);

        // Visit the manage page
        $response = $this->get(route('events.manage'));

        // Assert events appear
        $response->assertSee('Manage your events');
        $this->assertEquals(5, Event::where('organiser_id', $organizer->id)->count());
    }
}
