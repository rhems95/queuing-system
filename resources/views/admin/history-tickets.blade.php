@extends('layouts.panel')

@section('title', 'All Generated Tickets')

@section('content')
    <div class="pecit-page-header">
        <div>
            <h1 class="pecit-page-title">All Generated Tickets</h1>
            <p class="pecit-page-sub">Complete ticket log across all services</p>
        </div>
    </div>

    <form method="GET" class="pecit-filter-bar">
        <input type="date" name="date" value="{{ request('date') }}" class="pecit-input">
        <select name="status" class="pecit-select">
            <option value="">All statuses</option>
            <option value="waiting" {{ request('status') === 'waiting' ? 'selected' : '' }}>Waiting</option>
            <option value="serving" {{ request('status') === 'serving' ? 'selected' : '' }}>Serving</option>
            <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Done</option>
            <option value="held" {{ request('status') === 'held' ? 'selected' : '' }}>Held</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        <button type="submit" class="pecit-btn pecit-btn-secondary">Apply</button>
    </form>

    <div class="pecit-card">
        <div class="pecit-table-wrap">
            <table class="pecit-table">
                <thead>
                    <tr>
                        <th>Queue #</th>
                        <th>Service</th>
                        <th>Student</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Queue Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $row)
                        <tr>
                            <td style="font-weight:700;">{{ $row->queue_number }}</td>
                            <td>{{ $row->service_name }}</td>
                            <td>{{ $row->student_name ?: '—' }}</td>
                            <td>
                                @if ($row->priority)
                                    <span class="pecit-badge pecit-badge-priority">Priority</span>
                                @else
                                    <span class="pecit-badge pecit-badge-regular">Regular</span>
                                @endif
                            </td>
                            <td>
                                @php $st = strtolower((string) $row->status); @endphp
                                <span class="pecit-badge pecit-badge-{{ in_array($st, ['waiting','serving','done','cancelled','held'], true) ? $st : 'waiting' }}">
                                    {{ $row->status }}
                                </span>
                            </td>
                            <td>{{ $row->queue_date }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty">No tickets found.</td>
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
