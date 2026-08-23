@extends('layouts.panel')

@section('title', 'Reports & Analytics')

@section('content')
    @php
        $formatDuration = function (?int $seconds): string {
            if ($seconds === null) {
                return '—';
            }
            $seconds = max(0, $seconds);
            $hours = intdiv($seconds, 3600);
            $minutes = intdiv($seconds % 3600, 60);
            $secs = $seconds % 60;
            if ($hours > 0) {
                return $hours.'h '.$minutes.'m '.$secs.'s';
            }

            return $minutes.'m '.$secs.'s';
        };
    @endphp

    <div class="pecit-page-header">
        <div>
            <h1 class="pecit-page-title">Reports &amp; Analytics</h1>
            <p class="pecit-page-sub">Service volume, waiting time, and service time by window for the selected period</p>
        </div>
    </div>

    <form method="GET" class="pecit-filter-bar">
        <label class="pecit-label" style="margin:0;">From</label>
        <input type="date" name="date_from" value="{{ $dateFrom }}" class="pecit-input">
        <label class="pecit-label" style="margin:0;">To</label>
        <input type="date" name="date_to" value="{{ $dateTo }}" class="pecit-input">
        <button type="submit" class="pecit-btn pecit-btn-secondary">Apply</button>
    </form>

    <div class="pecit-stat-grid" style="grid-template-columns:repeat(auto-fit,minmax(14rem,1fr));">
        <div class="pecit-stat" data-tone="gold">
            <div class="pecit-stat-label">Total Served (period)</div>
            <div class="pecit-stat-value">{{ $servedCount }}</div>
        </div>
        <div class="pecit-stat" data-tone="warning">
            <div class="pecit-stat-label">Avg. waiting time</div>
            <div class="pecit-stat-value" style="font-size:1.65rem;">
                {{ $formatDuration($avgWaitingTimeSeconds) }}
            </div>
        </div>
        <div class="pecit-stat" data-tone="success">
            <div class="pecit-stat-label">Avg. service time</div>
            <div class="pecit-stat-value" style="font-size:1.65rem;">
                {{ $formatDuration($avgServiceTimeSeconds) }}
            </div>
        </div>
    </div>

    <div class="pecit-card" style="margin-bottom:1.25rem;">
        <div class="pecit-card-head">
            <div>
                <h2>Averages by Window</h2>
                <p>Waiting = ticket issued → Call Next · Service = Call Next → Complete</p>
            </div>
        </div>
        <div class="pecit-table-wrap">
            <table class="pecit-table">
                <thead>
                    <tr>
                        <th>Window</th>
                        <th>Service</th>
                        <th>Staff</th>
                        <th style="text-align:right;">Served</th>
                        <th style="text-align:right;">Avg. wait</th>
                        <th style="text-align:right;">Avg. service</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($byWindow as $row)
                        <tr>
                            <td style="font-weight:700;">{{ $row->window_name }}</td>
                            <td>{{ $row->service_name }}</td>
                            <td>{{ $row->staff_name ?? '—' }}</td>
                            <td style="text-align:right;font-weight:700;">{{ $row->total }}</td>
                            <td style="text-align:right;">
                                {{ $formatDuration($row->avg_wait_seconds !== null ? (int) round($row->avg_wait_seconds) : null) }}
                            </td>
                            <td style="text-align:right;">
                                {{ $formatDuration($row->avg_service_seconds !== null ? (int) round($row->avg_service_seconds) : null) }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">No data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="pecit-grid-2">
        <div class="pecit-card">
            <div class="pecit-card-head">
                <div><h2>By Service</h2></div>
            </div>
            <div class="pecit-table-wrap">
                <table class="pecit-table">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th style="text-align:right;">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($byService as $row)
                            <tr>
                                <td>{{ $row->service_name }}</td>
                                <td style="text-align:right;font-weight:700;">{{ $row->total }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="empty">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
