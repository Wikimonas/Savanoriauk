@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>{{ __('app.applications for') }} {{ $event->name }}</h2>

        @php
            $pendingApplications = $event->applications->where('status', 'pending');
        @endphp

        @if($pendingApplications->isEmpty())
            <p>{{ __('app.no applications') }}</p>
        @else
            <div class="row">
                @foreach($pendingApplications as $application)
                    <div class="col-md-12 mb-4">
                        <div class="card border-secondary shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span><strong>{{ $application->answers['name'] ?? __('app.unknown user') }}</strong></span>
                                <span class="badge
                                    @if($application->status === 'pending') bg-warning text-dark
                                    @elseif($application->status === 'accepted') bg-success
                                    @else bg-danger
                                    @endif">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </div>
                            <div class="card-body">
                                <p><strong>{{ __('app.name') }}:</strong> {{ $application->answers['name'] ?? '-' }}</p>
                                <p><strong>{{ __('app.email') }}:</strong> {{ $application->answers['email'] ?? '-' }}</p>
                                <p><strong>{{ __('app.phone') }}:</strong> {{ $application->answers['phone'] ?? '-' }}</p>
                                <p><strong>{{ __('app.address') }}:</strong> {{ $application->answers['address'] ?? '-' }}</p>

                                <hr>
                                <h5 class="mb-2">{{ __('app.answers to questions') }}:</h5>
                                <ul>
                                    @foreach($application->answers as $key => $answer)
                                        @if(is_numeric($key))
                                            @php
                                                $question = $event->questions->firstWhere('id', $key);
                                            @endphp
                                            @if($question)
                                                <li><strong>{{ $question->question }}:</strong> {{ $answer }}</li>
                                            @endif
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                            <div class="card-footer text-end">
                                <form action="{{ route('applications.updateStatus', $application->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button name="status" value="accepted" class="btn btn-success btn-sm me-2">{{ __('app.accept') }}</button>
                                    <button name="status" value="denied" class="btn btn-danger btn-sm">{{ __('app.deny') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
