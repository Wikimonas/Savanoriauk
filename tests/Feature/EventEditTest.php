<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventEditTest extends TestCase
{
    use RefreshDatabase;

    // Resets database before each test

    #[Test] public function organiser_can_edit_event()
    {
        // Create a user (event organizer)
        $organizer = User::factory()->organiser()->create();

        // Create an event belonging to the organizer
        $event = Event::factory()->create(['organiser_id' => $organizer->id]);

        // Simulate the organizer being logged in
        $this->actingAs($organizer);

        // New event data to update
        $updatedData = [
            'name' => 'Updated Event Name',
            'description' => 'Updated event description.',
            'address' => 'Updated Address',
            'event_date' => now()->addDays(5)->toDateTimeString(),
        ];

        // Send a PUT request to update the event
        $response = $this->put(route('events.update', $event->id), $updatedData);

        // Assert database was updated
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'name' => 'Updated Event Name',
            'description' => 'Updated event description.',
            'address' => 'Updated Address',
            'event_date' => now()->addDays(5)->toDateTimeString(),
        ]);

        // Assert redirection after successful update
        $response->assertRedirect(route('events.manage'));
    }

    #[Test] public function non_organiser_cannot_edit_event()
    {
        // Create an event organizer and a separate user
        $organizer = User::factory()->organiser()->create();
        $randomUser = User::factory()->create();

        // Create an event owned by the organizer
        $event = Event::factory()->create(['organiser_id' => $organizer->id]);

        // Simulate a different user (not the organizer) trying to edit the event
        $this->actingAs($randomUser);

        // Attempt to update the event
        $response = $this->put(route('events.update', $event->id), [
            'name' => 'Unauthorized Update',
            'description' => 'This should not be updated',
            'address' => 'Fake Address',
            'event_date' => now()->addDays(5)->toDateTimeString(),
        ]);

        // Assert database did NOT change
        $this->assertDatabaseMissing('events', [
            'id' => $event->id,
            'name' => 'Unauthorized Update',
        ]);
    }

    #[Test] public function event_edit_fails_with_invalid_data()
    {
        // Create an event organizer
        $organizer = User::factory()->organiser()->create();

        // Create an event owned by the organizer
        $event = Event::factory()->create(['organiser_id' => $organizer->id]);

        // Simulate the organizer being logged in
        $this->actingAs($organizer);

        // Attempt to update with invalid data (missing required fields)
        $response = $this->put(route('events.update', $event->id), [
            'name' => '',
            'description' => '',
            'address' => '',
            'event_date' => 'invalid-date',
        ]);

        // Assert validation errors are returned
        $response->assertSessionHasErrors(['name', 'description', 'address', 'event_date']);

        // Assert database did NOT change
        $this->assertDatabaseHas('events', [
            'id' => $event->id, // Ensures the original event data still exists
        ]);
    }
}
