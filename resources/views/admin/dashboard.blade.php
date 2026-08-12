@extends('layouts.panel')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="pecit-page-header">
        <div>
            <h1 class="pecit-page-title">Admin Dashboard</h1>
            <p class="pecit-page-sub">Live overview of today’s queue activity</p>
        </div>
    </div>

    <div class="pecit-stat-grid">
        <div class="pecit-stat" data-tone="gold">
            <div class="pecit-stat-label">Total Queues Today</div>
            <div class="pecit-stat-value">{{ $totalToday }}</div>
        </div>
        <div class="pecit-stat" data-tone="warning">
            <div class="pecit-stat-label">Waiting Queues</div>
            <div class="pecit-stat-value">{{ $waiting }}</div>
        </div>
        <div class="pecit-stat" data-tone="success">
            <div class="pecit-stat-label">Completed Queues</div>
            <div class="pecit-stat-value">{{ $completed }}</div>
        </div>
    </div>

    <div class="pecit-card">
        <div class="pecit-card-head">
            <div>
                <h2>Waiting Queues (Live)</h2>
                <p>Updates automatically every 3 seconds.</p>
            </div>
        </div>
        <div class="pecit-table-wrap">
            <table class="pecit-table">
                <thead>
                    <tr>
                        <th>Queue #</th>
                        <th>Service</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="waiting-queues-body">
                    <tr>
                        <td colspan="3" class="empty">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const endpoint = @json(route('admin.queues.waiting'));
            const tbody = document.getElementById('waiting-queues-body');

            function statusBadge(status) {
                const s = (status || '').toLowerCase();
                let cls = 'pecit-badge';
                if (s === 'waiting') cls += ' pecit-badge-waiting';
                else if (s === 'serving') cls += ' pecit-badge-serving';
                else if (s === 'done') cls += ' pecit-badge-done';
                else if (s === 'cancelled') cls += ' pecit-badge-cancelled';
                return '<span class="' + cls + '">' + (status || '') + '</span>';
            }

            async function loadWaitingQueues() {
                try {
                    const response = await fetch(endpoint, {
                        headers: { 'Accept': 'application/json' },
                        cache: 'no-cache',
                    });

                    if (!response.ok) {
                        console.error('Failed to fetch queues', response.status);
                        return;
                    }

                    const data = await response.json();
                    tbody.innerHTML = '';

                    if (!Array.isArray(data) || data.length === 0) {
                        const tr = document.createElement('tr');
                        tr.innerHTML = '<td colspan="3" class="empty">No waiting queues.</td>';
                        tbody.appendChild(tr);
                        return;
                    }

                    data.forEach(queue => {
                        const tr = document.createElement('tr');
                        const serviceName = queue.service ? queue.service.service_name : '';
                        tr.innerHTML =
                            '<td style="font-weight:700;letter-spacing:0.04em;">' + queue.queue_number + '</td>' +
                            '<td>' + serviceName + '</td>' +
                            '<td>' + statusBadge(queue.status) + '</td>';
                        tbody.appendChild(tr);
                    });
                } catch (e) {
                    console.error('Error loading queues', e);
                }
            }

            loadWaitingQueues();
            setInterval(loadWaitingQueues, 3000);
        });
    </script>
@endpush
