@extends('layouts.panel')

@section('title', 'Transaction History')

@section('content')
    <div class="pecit-page-header">
        <div>
            <h1 class="pecit-page-title">Served Tickets</h1>
            <p class="pecit-page-sub">Transaction history for completed and in-progress calls</p>
        </div>
    </div>

    @if (session('status'))
        <div class="pecit-alert pecit-alert-info">{{ session('status') }}</div>
    @endif

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
                        <th>Window</th>
                        <th>Staff</th>
                        <th>Called Time</th>
                        <th>Finished Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $row)
                        <tr>
                            <td style="font-weight:700;">{{ $row->queue_number }}</td>
                            <td>{{ $row->service_name }}</td>
                            <td>{{ $row->window_name }}</td>
                            <td>{{ $row->staff_name ?? '—' }}</td>
                            <td>{{ $row->called_time }}</td>
                            <td>{{ $row->finished_time ?? '—' }}</td>
                            <td style="white-space:nowrap;">
                                <a href="{{ route('admin.history.edit', $row->queue_call_id) }}" class="pecit-link">Edit</a>
                                <form method="POST" action="{{ route('admin.history.destroy', $row->queue_call_id) }}"
                                      style="display:inline;margin-left:0.65rem;"
                                      onsubmit="return confirm('Delete this record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="pecit-link-danger" style="background:none;border:none;cursor:pointer;padding:0;font:inherit;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty">No records found.</td>
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
