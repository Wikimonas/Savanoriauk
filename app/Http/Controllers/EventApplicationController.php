<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventApplication;
use Illuminate\Http\Request;

class EventApplicationController extends Controller
{
    public function index(Event $event)
    {
        // Only allow the organiser to view their own event's applications
        if (auth()->id() !== $event->organiser_id) {
            abort(403);
        }

        $event->load('applications'); // load applications relationship
        return view('events.applications', compact('event'));
    }

    public function showApplicationForm(Event $event)
    {
        return view('events.apply', ['event' => $event]);
    }

    public function store(Request $request, Event $event)
    {
        $request->validate([
            'answers' => 'required|array',
        ]);

        EventApplication::create([
            'event_id' => $event->id,
            'user_id' => auth()->id(),
            'answers' => $request->answers,
        ]);

        return redirect()->route('events.index')->with('success', 'Application submitted!');
    }

    public function updateStatus(Request $request, $applicationId)
    {
        $request->validate([
            'status' => 'required|in:accepted,denied'
        ]);

        $application = EventApplication::findOrFail($applicationId);

        // Only the organiser of the event can update status
        if (auth()->id() !== $application->event->organiser_id) {
            abort(403);
        }

        $application->status = $request->status;
        $application->save();

        return back()->with('success', 'Application status updated.');
    }

}

