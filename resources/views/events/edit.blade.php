@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>{{ __('app.edit event') }}</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('events.update', $event->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">{{ __('app.event name') }}</label>
                <input type="text" id="name" name="name" value="{{ $event->name }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">{{ __('app.event description') }}</label>
                <textarea id="description" name="description" class="form-control" required>{{ $event->description }}</textarea>
            </div>

            <div class="mb-3">
                <label for="event_date" class="form-label">{{ __('app.event date') }}</label>
                <input type="date" id="event_date" name="event_date" value="{{ $event->event_date }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">{{ __('app.address') }}</label>
                <input type="text" id="address" name="address" value="{{ $event->address }}" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">{{ __('app.update event') }}</button>
        </form>

        <hr>

        <h3>{{ __('app.manage questions') }}</h3>

        <form action="{{ route('event_questions.store', $event->id) }}" method="POST">
            @csrf
            <div class="input-group mb-3">
                <input type="text" name="question" class="form-control" placeholder="{{ __('app.enter a question') }}" required>
                <button type="submit" class="btn btn-success">{{ __('app.add question') }}</button>
            </div>
        </form>
        <form method="POST" action="{{ route('event_questions.suggest', $event->id) }}">
            @csrf
            <button type="submit" class="btn btn-info">Generate Questions (AI)</button>
        </form>

        @if ($event->questions->isNotEmpty())
            <ul class="list-group">
                @foreach ($event->questions as $question)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $question->question }}
                        <form action="{{ route('event_questions.destroy', $question->id) }}" method="POST" onsubmit="return confirm('{{ __('app.confirm delete question') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">{{ __('app.delete') }}</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @else
            <p>{{ __('app.no questions yet') }}</p>
        @endif

    </div>
@endsection
