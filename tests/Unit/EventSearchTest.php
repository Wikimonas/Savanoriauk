<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Http\Controllers\EventController;
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
    public function it_returns_no_events_when_nothing_matches(): void
    {
        // Arrange: Create some events that don't match the search query
        Event::factory(3)->create(['name' => 'Random Event', 'description' => 'Some description']);

        // Act: Execute search with a non-matching query
        $result = $this->executeSearch('NonExistent');

        // Assert: Ensure no events are returned
        $this->assertInstanceOf(View::class, $result);
        $this->assertCount(0, $result->getData()['events']);
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
