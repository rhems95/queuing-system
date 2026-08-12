@extends('layouts.panel')

@section('title', 'Reports & Analytics')

@section('content')
    <div class="pecit-page-header">
        <div>
            <h1 class="pecit-page-title">Reports &amp; Analytics</h1>
            <p class="pecit-page-sub">Service volume and staff performance for the selected period</p>
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
        <div class="pecit-stat" data-tone="success">
            <div class="pecit-stat-label">Avg. service time</div>
            <div class="pecit-stat-value" style="font-size:1.65rem;">
                @if($avgServiceTimeSeconds !== null)
                    {{ (int)($avgServiceTimeSeconds / 60) }}m {{ $avgServiceTimeSeconds % 60 }}s
                @else
                    —
                @endif
            </div>
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

        <div class="pecit-card">
            <div class="pecit-card-head">
                <div><h2>By Staff / Window</h2></div>
            </div>
            <div class="pecit-table-wrap">
                <table class="pecit-table">
                    <thead>
                        <tr>
                            <th>Staff / Window</th>
                            <th style="text-align:right;">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($byStaff as $row)
                            <tr>
                                <td>{{ $row->staff_or_window ?? '—' }}</td>
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
