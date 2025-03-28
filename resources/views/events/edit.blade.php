@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>{{__('app.edit event')}}</h2>

        <form action="{{ route('events.update', $event->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('app.event name') }}</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $event->name) }}" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">{{__('app.description')}}</label>
                <textarea name="description" id="description" class="form-control" required>{{ old('description', $event->description) }}</textarea>
            </div>

            <div class-="mb-3">
                <label for="address" class="form-label">{{ __('app.address') }}</label>
                <textarea name="address" id="address" class="form-control" required>{{old('address', $event->address)}}</textarea>
            </div>

            <div class="mb-3">
                <label for="date" class="form-label">{{ __('app.event date') }}</label>
                <input type="datetime-local" name="event_date" id="event_date" class="form-control" value="{{ old('event_date', $event->event_date) }}" required>
            </div>

            <button type="submit" class="btn btn-success">{{__('app.save')}}</button>
        </form>

    </div>
@endsection
