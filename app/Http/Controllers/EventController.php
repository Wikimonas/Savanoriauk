<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::paginate(10);
        return view('events.index', compact('events'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $events = Event::where('name',  'LIKE', "%$query%") -> orWhere('description', 'LIKE', "%$query%") ->paginate(10);
        return view('events.index', compact('events'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'event_date' => 'required|date',
        ]);

        Event::create([
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'event_date' => $request->event_date,
            'organiser_id' => auth()->id(),
        ]);


        return redirect()->route('events.manage')->with('success', 'Event created successfully!');
    }

    public function manage()
    {
        $userId = Auth::id(); // Get logged-in user ID

        // Fetch events for the logged-in organizer
        $events = Event::where('organiser_id', $userId)->get();

        return view('events.manage', compact('events')); // Send data to the view
    }

    public function update(Request $request, $id)
    {

        $event = Event::find($id);

        if (auth()->id() !== $event->organiser_id) {
            abort(403, 'Unauthorized action.');
        }


        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'event_date' => 'required|date',
        ]);


        $request->merge
        ([
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'event_date' => $request->event_date,
            'organiser_id' => auth()->id(),
        ]);

        $event->update($request->only([
            'name',
            'description',
            'address',
            'event_date',
            'organiser_id'
        ]));

        return redirect()->route('events.manage')->with('success', 'Event updated successfully!');
    }
    public function edit($id)
    {

        $event = Event::findOrFail($id);
        if (auth()->id() !== $event->organiser_id)
        {
            abort(403, "Unauthorized action.");
        }
        return view('events.edit', compact('event'));
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        if (auth()->id() !== $event->organiser_id)
        {
            abort(403, "Unauthorized action.");
        }
        else
        {
            $event->delete();
            return redirect()->route('events.manage')->with('success', 'Event deleted successfully.');
        }
    }

}
