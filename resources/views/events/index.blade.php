@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ __('app.all events') }}</h1>

        <form action="{{ route('events.search') }}" method="GET" class="mb-3">
            <input type="text" name="query" class="form-control" placeholder="{{ __('app.event name') }}" required>
            <button type="submit" class="btn btn-primary mt-2">{{ __('app.search') }}</button>
        </form>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @auth
            @if(auth()->user()->role === 'organiser')
                <a href="{{ route('events.create') }}" class="btn btn-primary mb-3">{{ __('app.create event') }}</a>
            @endif
        @endauth

        @if($events->isEmpty())
            <h2><strong>{{ __('app.no events found') }}</strong></h2>
        @else
            <ul class="list-group mt-3">
                @foreach($events as $event)
                    <li class="list-group-item">
                        <a href="{{ route('events.index', $event->id) }}"><strong>{{ $event->name }}</strong></a> - {{ $event->event_date }} <br>
                        {{ $event->description }} <br>
                        <strong>{{ __('app.address') }}:</strong> {{ $event->address }}
                    </li>
                @endforeach
            </ul>
            {{ $events->links() }} <!-- Pagination -->
        @endif
    </div>
@endsection
