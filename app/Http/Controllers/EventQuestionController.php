<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventQuestion;
use Illuminate\Http\Request;

class EventQuestionController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $request->validate(['question' => 'required|string|max:255|min:5']);

        EventQuestion::create([
            'event_id' => $event->id,
            'question' => $request->question,
        ]);

        return redirect()->route('events.edit', $event->id)->with('success', 'Question added!');
    }

    public function destroy($id)
    {
        $question = EventQuestion::findOrFail($id); // returns 404 if not found

        $eventId = $question->event_id;
        $question->delete();

        return redirect()->route('events.edit', $eventId)->with('success', 'Question deleted.');
    }
}

