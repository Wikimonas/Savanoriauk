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
                <a href="{{ route('events.manage') }}" class="btn btn-primary mb-3">{{ __('app.manage event') }}</a>
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
                        <strong>{{ __('app.address') }}:</strong> {{ $event->address }} <br>

                        @auth
                            @if(auth()->user()->role === 'user')
                                @php
                                    $application = $event->applications->firstWhere('user_id', auth()->id());
                                @endphp

                                @if($application)
                                    <p class="mt-2">
                                        <strong>{{ __('app.application status') }}:</strong>
                                        @if($application->status === 'pending')
                                            <span class="badge bg-warning text-dark">{{ __('app.pending') }}</span>
                                        @elseif($application->status === 'accepted')
                                            <span class="badge bg-success">{{ __('app.accepted') }}</span>
                                        @elseif($application->status === 'denied')
                                            <span class="badge bg-danger">{{ __('app.denied') }}</span>
                                        @endif
                                    </p>
                                @else
                                    <a href="{{ route('events.apply', $event->id) }}" class="btn btn-success mt-2">{{ __('app.apply') }}</a>
                                @endif
                            @endif

                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary mt-2">{{ __('app.login to apply') }}</a>
                        @endauth
                    </li>
                @endforeach
            </ul>
            {{ $events->links() }} <!-- Pagination -->
        @endif
    </div>
@endsection
