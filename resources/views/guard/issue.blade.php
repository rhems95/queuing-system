@extends('layouts.panel')

@section('title', 'Issue Walk-in Ticket')

@section('content')
    <div class="pecit-page-header">
        <div>
            <h1 class="pecit-page-title">Issue Walk-in Ticket</h1>
            <p class="pecit-page-sub">
                Guard ID {{ $guardId }} · {{ auth()->user()->name }}
                · For new enrollees and people who cannot use the kiosk
            </p>
        </div>
    </div>

    @if ($errors->any())
        <div class="pecit-alert pecit-alert-danger">
            <ul class="list-disc list-inside m-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="pecit-card" style="max-width:36rem;margin-bottom:1.25rem;">
        <div class="pecit-card-body">
            <form method="POST" action="{{ route('guard.store') }}">
                @csrf

                <div style="margin-bottom:1rem;">
                    <label class="pecit-label">Service</label>
                    <select name="service_id" class="pecit-select" required>
                        <option value="">Select service</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}" {{ (string) old('service_id') === (string) $service->id ? 'selected' : '' }}>
                                {{ $service->service_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom:1rem;">
                    <label class="pecit-label">Priority</label>
                    <select name="priority" class="pecit-select" required>
                        <option value="regular" {{ old('priority', 'regular') === 'regular' ? 'selected' : '' }}>Regular</option>
                        <option value="priority" {{ old('priority') === 'priority' ? 'selected' : '' }}>Priority</option>
                    </select>
                </div>

                <div style="margin-bottom:1.25rem;">
                    <label class="pecit-label">Reason</label>
                    <select name="issue_reason" class="pecit-select" required>
                        <option value="">Select reason</option>
                        @foreach ($reasons as $value => $label)
                            <option value="{{ $value }}" {{ old('issue_reason') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pecit-actions" style="margin-bottom:0;justify-content:flex-end;">
                    <button type="submit" class="pecit-btn pecit-btn-primary">Issue &amp; Print</button>
                </div>
            </form>
        </div>
    </div>

    <div class="pecit-card">
        <div class="pecit-card-body">
            <h2 class="pecit-page-title" style="font-size:1.05rem;margin:0 0 0.75rem;">Issued today</h2>
            <div class="pecit-table-wrap">
                <table class="pecit-table">
                    <thead>
                        <tr>
                            <th>Ticket</th>
                            <th>Service</th>
                            <th>Priority</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recent as $row)
                            <tr>
                                <td style="font-weight:700;">{{ $row->queue_number }}</td>
                                <td>{{ $row->service->service_name ?? '—' }}</td>
                                <td>{{ $row->priority ? 'Priority' : 'Regular' }}</td>
                                <td>{{ \App\Services\TicketIssuer::reasonLabel($row->issue_reason) ?? '—' }}</td>
                                <td>{{ $row->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty">No walk-in tickets issued yet today.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
