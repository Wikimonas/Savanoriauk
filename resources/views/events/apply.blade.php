@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Apply for {{ $event->name }}</h2>

        <form action="{{ route('events.apply.store', $event->id) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="answers[name]" value="{{ auth()->user()->name ?? '' }}" class="form-control">
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="answers[email]" value="{{ auth()->user()->email ?? '' }}" class="form-control">
            </div>

            <div class="mb-3">
                <label>Phone</label>
                <input type="text" name="answers[phone]" value="{{ auth()->user()->phone ?? '' }}" class="form-control">
            </div>

            <div class="mb-3">
                <label>Address</label>
                <input type="text" name="answers[address]" value="{{ auth()->user()->address ?? '' }}" class="form-control">
            </div>

            @foreach($event->questions as $question)
                <div class="mb-3">
                    <label>{{ $question->question }}</label>
                    <input type="text" name="answers[{{ $question->id }}]" class="form-control">
                </div>
            @endforeach

            <button type="submit" class="btn btn-success">Submit Application</button>
        </form>
    </div>
@endsection
