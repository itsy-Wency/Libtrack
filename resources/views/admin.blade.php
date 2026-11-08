{{-- Admin overview: show health metrics first, followed by library totals and audit activity. --}}
@extends('layouts.app')

@section('title', 'Admin Dashboard | LibTrack')

@section('content')
    <div class="page-heading">
        <div>
            <h1>System monitoring</h1>
            <p class="muted">Live traffic and recent health checks for your library.</p>
        </div>
        <div class="button-row">
            <a class="btn secondary" href="{{ route('admin.dashboard') }}"><svg class="icon"><use href="#icon-refresh" /></svg>Refresh metrics</a>
            <a class="btn secondary" href="{{ route('admin.activities') }}"><svg class="icon"><use href="#icon-list" /></svg>View activity</a>
        </div>
    </div>

    <section class="monitor-overview" aria-labelledby="monitor-status-title">
        <div class="monitor-overview-top">
            <div>
                <p class="eyebrow">Application dependencies</p>
                <h2 id="monitor-status-title">{{ $metrics['health']['status'] }}</h2>
                <p>Checks probe the configured web URL, database connection, and storage volume.</p>
            </div>
            <span class="status-pill {{ $metrics['health']['status'] === 'All checks passing' ? 'is-healthy' : ($metrics['health']['status'] === 'Dependencies degraded' ? 'is-degraded' : 'is-stale') }}">
                {{ $metrics['health']['status'] }}
            </span>
        </div>
        <div class="monitor-overview-detail">
            <span><strong>Web app:</strong> {{ $metrics['health']['application'] }}</span>
            <span><strong>Database:</strong> {{ $metrics['health']['database'] }}</span>
            <span><strong>Storage:</strong> {{ $metrics['health']['storage'] }}</span>
            <span><strong>Last check:</strong>
                {{ $metrics['health']['checked_at'] ? \Illuminate\Support\Carbon::parse($metrics['health']['checked_at'])->format('M j, Y g:i:s A') : 'Not yet recorded' }}
            </span>
        </div>
    </section>

    <div class="monitor-grid" aria-label="System monitoring metrics">
        <article class="card monitor-card">
            <div class="monitor-card-head"><svg class="icon"><use href="#icon-clock" /></svg>Uptime, last 24 hours</div>
            <p class="monitor-value">{{ $metrics['uptime'] }}</p>
            <p class="monitor-note">
                @if ($metrics['uptime_percent'] !== null)
                    {{ number_format($metrics['uptime_percent'], 1) }}% of recorded checks passed
                @else
                    Waiting for scheduled health checks
                @endif
            </p>
        </article>

        <article class="card monitor-card">
            <div class="monitor-card-head"><svg class="icon"><use href="#icon-alert" /></svg>Downtime, last 24 hours</div>
            <p class="monitor-value">{{ $metrics['downtime'] }}</p>
            <p class="monitor-note">Time recorded in failed database or storage checks</p>
        </article>

        <article class="card monitor-card">
            <div class="monitor-card-head"><svg class="icon"><use href="#icon-pulse" /></svg>Requests per minute</div>
            <p class="monitor-value">{{ number_format($metrics['requests_per_minute']) }}</p>
            <p class="monitor-note">Requests received in the current minute</p>
        </article>

        <article class="card monitor-card">
            <div class="monitor-card-head"><svg class="icon"><use href="#icon-alert" /></svg>Error rate</div>
            <p class="monitor-value">{{ $metrics['error_rate_percent'] === null ? 'No data' : number_format($metrics['error_rate_percent'], 1) . '%' }}</p>
            <p class="monitor-note">HTTP 5xx responses over the last 5 minutes ({{ number_format($metrics['five_minute_requests']) }} requests)</p>
        </article>

        <article class="card monitor-card">
            <div class="monitor-card-head"><svg class="icon"><use href="#icon-users" /></svg>Active users</div>
            <p class="monitor-value">{{ $metrics['active_users'] === null ? 'N/A' : number_format($metrics['active_users']) }}</p>
            <p class="monitor-note">Unique signed-in users active in the last 5 minutes</p>
        </article>

        <article class="card monitor-card monitor-card-storage">
            <div class="monitor-card-head"><svg class="icon"><use href="#icon-drive" /></svg>Storage capacity</div>
            <p class="monitor-value">{{ $metrics['storage']['used'] }} <span class="muted">of {{ $metrics['storage']['total'] }}</span></p>
            @if ($metrics['storage']['usage_percent'] !== null)
                <div class="monitor-progress" role="progressbar" aria-label="Storage used" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $metrics['storage']['usage_percent'] }}" aria-valuetext="{{ $metrics['storage']['usage_percent'] }} percent used">
                    <span style="width: {{ $metrics['storage']['usage_percent'] }}%"></span>
                </div>
            @endif
            <p class="monitor-note">{{ $metrics['storage']['free'] }} available on the app storage volume</p>
        </article>
    </div>

    <section class="card">
        <div class="page-heading">
            <div><p class="eyebrow">Monitoring coverage</p><h2>Health check history</h2></div>
            <strong>{{ $metrics['checks_recorded'] }} / 1,440 checks</strong>
        </div>
        <div class="monitor-progress is-coverage" role="progressbar" aria-label="Health check coverage in the last 24 hours" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $metrics['coverage_percent'] }}" aria-valuetext="{{ $metrics['coverage_percent'] }} percent coverage">
            <span style="width: {{ $metrics['coverage_percent'] }}%"></span>
        </div>
        <p class="muted">Uptime and downtime are estimated from one-minute HTTP, database, and storage checks. Missing checks are shown as reduced coverage, not counted as downtime. Start Laravel's scheduler to collect checks continuously.</p>
    </section>

    <div class="page-heading">
        <div><p class="eyebrow">Library snapshot</p><h2>Collection overview</h2></div>
    </div>
    <div class="grid library-summary">
        @foreach (['users' => 'Registered users', 'books' => 'Books', 'pending' => 'Pending requests', 'requests' => 'Total requests'] as $key => $label)
            <article class="card stat-card">
                <p class="muted">{{ $label }}</p>
                <p class="stat-value">{{ number_format($stats[$key]) }}</p>
            </article>
        @endforeach
    </div>

    <section class="card table-wrap">
        <div class="page-heading">
            <div><p class="eyebrow">Runtime details</p><h2>System information</h2></div>
        </div>
        <table>
            <tbody>
                @foreach ($health as $label => $value)
                    <tr><th scope="row">{{ $label }}</th><td>{{ $value }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <section class="card table-wrap">
        <div class="page-heading">
            <div><p class="eyebrow">Latest updates</p><h2>Recent activity</h2></div>
            <a class="btn secondary" href="{{ route('admin.activities') }}">All logs <svg class="icon"><use href="#icon-arrow" /></svg></a>
        </div>
        <table>
            <thead><tr><th>User</th><th>Action</th><th>Details</th><th>Time</th></tr></thead>
            <tbody>
                @forelse ($recent as $activity)
                    <tr>
                        <td>{{ $activity->user?->name ?? 'System' }}</td>
                        <td>{{ $activity->action }}</td>
                        <td>{{ $activity->details }}</td>
                        <td>{{ $activity->created_at }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">No recent activity.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
