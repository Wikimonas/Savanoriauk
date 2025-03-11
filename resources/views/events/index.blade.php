@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>All Events</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @auth
            @if(auth()->user()->role === 'organiser')
                <a href="{{ route('events.create') }}" class="btn btn-primary">Create Event</a>
            @endif
        @endauth

        <ul class="list-group mt-3">
            @foreach($events as $event)
                <li class="list-group-item">
                    <strong>{{ $event->name }}</strong> - {{ $event->event_date }} <br>
                    {{ $event->description }}
                </li>
            @endforeach
        </ul>
    </div>
@endsection
