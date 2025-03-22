 @extends('layouts.app')

        @section('content')
            <div class="container">
                <h2>{{ __('app.manage events') }}</h2>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif


                @if($events->isEmpty())
                    <h2><strong>{{ __('app.no events found') }}</strong></h2>
                @else
                <table class="table">
                    <thead>
                    <tr>
                        <th>{{ __('app.event name') }}</th>
                        <th>{{ __('app.event date') }}</th>
                        <th>{{ __('app.address') }}</th>
                        <th>{{ __('app.action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($events as $event)
                        <tr>
                            <td>{{ $event->name }}</td>
                            <td>{{ $event->event_date }}</td>
                            <td>{{ $event->address }}</td>
                            <td>
                                <form action="{{ route('events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">{{ __('app.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @endif
    </div>
@endsection




