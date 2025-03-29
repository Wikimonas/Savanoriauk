@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Action Logs</h2>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('logs.index') }}">
            <div class="mb-3">
                <label for="username" class="form-label">Filter by Username</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ request()->input('name') }}">
            </div>

            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <table class="table">
            <thead>
            <tr>
                <th>User</th>
                <th>Action</th>
                <th>Model</th>
                <th>Changes</th>
                <th>IP</th>
                <th>Date</th>
            </tr>
            </thead>
            <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ $log->user->name ?? 'Guest' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->model_type }} (ID: {{ $log->model_id }})</td>
                    <td><pre>{{ json_encode($log->changes, JSON_PRETTY_PRINT) }}</pre></td>
                    <td>{{ $log->ip_address }}</td>
                    <td>{{ $log->created_at }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $logs->links() }}
    </div>
@endsection
