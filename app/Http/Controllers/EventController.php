<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Http\Requests\EventSearchRequest;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::paginate(10);

        return view('events.index', compact('events'));
    }

    public function search(EventSearchRequest $request)
    {
        $validated = $request->validated();

        $query = $validated['query'];

        $events = Event::where(function ($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%");
        })
            ->paginate(10);

        return view('events.index', compact('events'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(StoreEventRequest $request)
    {
        $validated = $request->validated();

        $event = Event::create([
            'name'         => $validated['name'],
            'description'  => $validated['description'],
            'address'      => $validated['address'],
            'event_date'   => $validated['event_date'],
            'organiser_id' => auth()->id(),
        ]);

        LogHelper::logAction('Event created', $event);

        return redirect()
            ->route('events.manage')
            ->with('success', 'Event created successfully!');
    }

    public function manage()
    {
        $userId = Auth::id();

        $events = Event::where('organiser_id', $userId)->get();

        return view('events.manage', compact('events'));
    }

    public function edit(Event $event)
    {
        $this->authorizeOrganizer($event);

        return view('events.edit', compact('event'));
    }

    public function update(UpdateEventRequest $request, int $id)
    {
        $event = Event::findOrFail($id);
        
        $this->authorizeOrganizer($event);

        $oldData = $event->toArray();
        $validated = $request->validated();

        $event->update([
            'name'         => $validated['name'],
            'description'  => $validated['description'],
            'address'      => $validated['address'],
            'event_date'   => $validated['event_date'],
            'organiser_id' => auth()->id(),
        ]);

        LogHelper::logAction('Event updated', $event, [
            'before' => $oldData,
            'after'  => $event->toArray(),
        ]);

        return redirect()
            ->route('events.manage')
            ->with('success', 'Event updated successfully!');
    }

    public function destroy(int $id)
    {
        $event = Event::findOrFail($id);

        $this->authorizeOrganizer($event);

        LogHelper::logAction('Event Deleted', $event);
        $event->delete();

        return redirect()
            ->route('events.manage')
            ->with('success', 'Event deleted successfully.');
    }

    /**
     * Check if authenticated user is organiser of the event.
     */
    private function authorizeOrganizer(Event $event): void
    {
        if (auth()->id() !== $event->organiser_id) {
            abort(403, 'Unauthorized action.');
        }
    }
}
