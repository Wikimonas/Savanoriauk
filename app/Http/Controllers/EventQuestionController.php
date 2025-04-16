<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventQuestion;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

class EventQuestionController extends Controller
{
    public function store(Request $request, Event $event)
    {
        if (auth()->user()->role !== 'organiser') {
            abort(403);
        }

        $request->validate(['question' => 'required|string|max:255|min:5']);

        EventQuestion::create([
            'event_id' => $event->id,
            'question' => $request->question,
        ]);

        return redirect()->route('events.edit', $event->id)->with('success', 'Question added!');
    }

    public function destroy($id)
    {
        $question = EventQuestion::findOrFail($id);

        if (auth()->user()->role !== 'organiser') {
            abort(403);
        }

        $eventId = $question->event_id;
        $question->delete();

        return redirect()->route('events.edit', $eventId)->with('success', 'Question deleted.');
    }

    public function suggest(Event $event)
    {
        $prompt = "Based on this event, suggest 2 short and helpful screening questions that an organiser should ask a volunteer:\n\n".
            "Event Title: {$event->name}\n".
            "Description: {$event->description}\n\n".
            "List them as bullet points.";

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $text = $response->choices[0]->message->content;
        $questions = collect(explode("\n", $text))
            ->filter(fn($line) => str_contains($line, '?'))
            ->map(fn($line) => trim(ltrim($line, "-•1234567890. ")))
            ->take(2); // just in case it gives more than 2

        foreach ($questions as $question) {
            EventQuestion::create([
                'event_id' => $event->id,
                'question' => $question,
            ]);
        }

        return redirect()->route('events.edit', $event->id)->with('success', 'AI-generated questions added!');
    }
}

