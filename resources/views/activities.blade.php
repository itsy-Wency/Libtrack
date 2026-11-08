{{-- Admin audit view: list who performed each recorded action and when. --}}
@extends('layouts.app')

@section('title', 'Activity Logs | LibTrack')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">System overview</p>
            <h1>Activity logs</h1>
            <p class="muted">Recent actions recorded across the library.</p>
        </div>
        <a class="btn secondary" href="{{ route('admin.dashboard') }}">&larr; Dashboard</a>
    </div>

    <section class="card table-wrap" aria-label="Activity log entries">
        <table>
            <thead><tr><th>User</th><th>Action</th><th>Details</th><th>IP address</th><th>Date</th></tr></thead>
            <tbody>
                @forelse ($activities as $activity)
                    <tr>
                        <td>{{ $activity->user?->name ?? 'System' }}</td>
                        <td>{{ $activity->action }}</td>
                        <td>{{ $activity->details }}</td>
                        <td>{{ $activity->ip_address }}</td>
                        <td>{{ $activity->created_at }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">No activity has been recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    {{ $activities->links() }}
@endsection
