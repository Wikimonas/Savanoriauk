@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Create Event</h1>

        <form action="{{ route('events.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">{{ __('app.event name') }}</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('app.description') }}</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('app.event date') }}</label>
                <input type="datetime-local" name="event_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('app.address') }}</label>
                <input type="text" name="address" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">{{ __('app.create event') }}</button>
        </form>
    </div>
@endsection
