<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Http\Controllers\EventController;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Mockery;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class EventSearchTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_matching_events_based_on_query(): void
    {
        // Setup mock and expectations
        $this->mockEventSearch('Charity', [
            (object) ['name' => 'Charity Marathon', 'description' => 'A fun event'],
        ]);

        // Execute search
        $result = $this->executeSearch('Charity');

        // Assert results
        $this->assertInstanceOf(View::class, $result);
        $this->assertCount(1, $result->getData()['events']);
    }

    #[Test]
    public function it_returns_no_events_when_nothing_matches(): void
    {
        // Setup mock and expectations
        $this->mockEventSearch('NonExistent', []);

        // Execute search
        $result = $this->executeSearch('NonExistent');

        // Assert results
        $this->assertInstanceOf(View::class, $result);
        $this->assertCount(0, $result->getData()['events']);
    }

    /**
     * Helper method to set up the Event mock
     */
    private function mockEventSearch(string $query, array $results): void
    {
        $eventMock = Mockery::mock('alias:' . Event::class);

        $eventMock->shouldReceive('where')
            ->with('name', 'LIKE', "%{$query}%")
            ->andReturnSelf();

        $eventMock->shouldReceive('orWhere')
            ->with('description', 'LIKE', "%{$query}%")
            ->andReturnSelf();

        // Create paginator with results
        $paginator = new LengthAwarePaginator(
            $results,
            count($results),
            10,
            1
        );

        $eventMock->shouldReceive('paginate')
            ->with(10)
            ->andReturn($paginator);
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
