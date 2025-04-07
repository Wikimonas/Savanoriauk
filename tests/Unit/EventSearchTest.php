<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Http\Controllers\EventController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class EventSearchTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_matching_events_based_on_query(): void
    {
        // Arrange: Create events in the database
        $matchingEvent = Event::factory()->create(['name' => 'Charity Marathon', 'description' => 'A fun event']);
        Event::factory(3)->create(); // Create some unrelated events

        // Act: Execute search
        $result = $this->executeSearch('Charity');

        // Assert: Check if the correct events are returned
        $this->assertInstanceOf(View::class, $result);
        $events = $result->getData()['events'];
        $this->assertCount(1, $events);
        $this->assertEquals('Charity Marathon', $events->first()->name);
    }

    #[Test]
    public function it_displays_a_message_if_no_events_match_search_query()
    {
        $user = User::factory()->create(); // Or leave unauthenticated if search is public

        $response = $this->actingAs($user)->get(route('events.search', [
            'query' => 'Nonexistent Event Name 12345',
        ]));

        $response->assertStatus(200);
        $response->assertSee(__('app.no events found'));
    }

    #[Test] public function it_shows_validation_error_if_search_query_is_too_short()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('events.search', [
            'query' => 'ab', // ❌ too short
        ]));

        $response->assertSessionHasErrors('query'); // Validate query field
    }

    #[Test] public function it_returns_event_when_searching_by_first_3_characters_of_name()
    {
        $user = User::factory()->create();

        Event::factory()->create([
            'name' => 'Volunteer Fair',
            'description' => 'Join us to help the community',
        ]);

        $response = $this->actingAs($user)->get(route('events.search', [
            'query' => 'Vol', // ✅ first 3 letters of name
        ]));

        $response->assertStatus(200);
        $response->assertSee('Volunteer Fair');
    }
    /**
     * Helper method to execute the search
     */
    private function executeSearch(string $query): View
    {
        $controller = new EventController();
        $request = new Request(['query' => $query]);
        return $controller->search($request);
    }
}
