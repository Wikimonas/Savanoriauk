<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::all();
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

        return redirect()->route('events.index')->with('success', 'Event created successfully!');
    }
}
