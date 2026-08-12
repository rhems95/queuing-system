@extends('layouts.panel')

@section('title', 'My Service History')

@section('content')
    <div class="pecit-page-header">
        <div>
            <h1 class="pecit-page-title">My Service History</h1>
            <p class="pecit-page-sub">Tickets you have served at your counter. You cannot edit or delete records.</p>
        </div>
    </div>

    <form method="GET" class="pecit-filter-bar">
        <label class="pecit-label" style="margin:0;">Filter by date</label>
        <input type="date" name="date" value="{{ request('date') }}" class="pecit-input">
        <button type="submit" class="pecit-btn pecit-btn-secondary">Apply</button>
    </form>

    <div class="pecit-card">
        <div class="pecit-table-wrap">
            <table class="pecit-table">
                <thead>
                    <tr>
                        <th>Queue #</th>
                        <th>Service</th>
                        <th>Called Time</th>
                        <th>Finished Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $row)
                        <tr>
                            <td style="font-weight:700;">{{ $row->queue_number }}</td>
                            <td>{{ $row->service_name }}</td>
                            <td>{{ $row->called_time }}</td>
                            <td>{{ $row->finished_time ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-top:1rem;">
        {{ $records->withQueryString()->links() }}
    </div>
@endsection
