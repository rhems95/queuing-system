@extends('layouts.panel')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="pecit-page-header">
        <div>
            <h1 class="pecit-page-title">Admin Dashboard</h1>
            <p class="pecit-page-sub">Live overview of today’s queue activity · <span id="dashboardClock">—</span></p>
        </div>
        <a href="{{ route('admin.history.reports') }}" class="pecit-btn pecit-btn-primary">View Reports</a>
    </div>

    <div class="pecit-stat-grid pecit-stat-grid-4">
        <div class="pecit-stat" data-tone="gold">
            <div class="pecit-stat-top">
                <div class="pecit-stat-label">Total Today</div>
                @include('partials.icon', ['name' => 'ticket', 'class' => 'pecit-stat-glyph'])
            </div>
            <div class="pecit-stat-value" id="statTotal">{{ $totalToday }}</div>
        </div>
        <div class="pecit-stat" data-tone="warning">
            <div class="pecit-stat-top">
                <div class="pecit-stat-label">Waiting</div>
                @include('partials.icon', ['name' => 'clock', 'class' => 'pecit-stat-glyph'])
            </div>
            <div class="pecit-stat-value" id="statWaiting">{{ $waiting }}</div>
        </div>
        <div class="pecit-stat" data-tone="navy">
            <div class="pecit-stat-top">
                <div class="pecit-stat-label">Now Serving</div>
                @include('partials.icon', ['name' => 'serving', 'class' => 'pecit-stat-glyph'])
            </div>
            <div class="pecit-stat-value" id="statServing">{{ $serving }}</div>
        </div>
        <div class="pecit-stat" data-tone="success">
            <div class="pecit-stat-top">
                <div class="pecit-stat-label">Completed</div>
                @include('partials.icon', ['name' => 'check', 'class' => 'pecit-stat-glyph'])
            </div>
            <div class="pecit-stat-value" id="statCompleted">{{ $completed }}</div>
        </div>
    </div>

    <div class="pecit-quick-grid">
        <a href="{{ route('admin.users.index') }}" class="pecit-quick-card">
            @include('partials.icon', ['name' => 'users', 'class' => 'pecit-quick-icon'])
            <span class="pecit-quick-copy">
                <strong>Manage Users</strong>
                <small>Staff accounts &amp; windows</small>
            </span>
        </a>
        <a href="{{ route('admin.students.index') }}" class="pecit-quick-card">
            @include('partials.icon', ['name' => 'students', 'class' => 'pecit-quick-icon'])
            <span class="pecit-quick-copy">
                <strong>Students</strong>
                <small>Add, delete, or import CSV</small>
            </span>
        </a>
        <a href="{{ route('admin.history') }}" class="pecit-quick-card">
            @include('partials.icon', ['name' => 'history', 'class' => 'pecit-quick-icon'])
            <span class="pecit-quick-copy">
                <strong>Served History</strong>
                <small>Edit completed calls</small>
            </span>
        </a>
        <a href="{{ route('admin.history.reports') }}" class="pecit-quick-card">
            @include('partials.icon', ['name' => 'chart', 'class' => 'pecit-quick-icon'])
            <span class="pecit-quick-copy">
                <strong>Reports</strong>
                <small>Wait &amp; service averages</small>
            </span>
        </a>
        <a href="{{ route('display') }}" class="pecit-quick-card" target="_blank" rel="noopener">
            @include('partials.icon', ['name' => 'display', 'class' => 'pecit-quick-icon'])
            <span class="pecit-quick-copy">
                <strong>Public Display</strong>
                <small>Open now-serving board</small>
            </span>
        </a>
    </div>

    <div class="pecit-card">
        <div class="pecit-card-head">
            <div>
                <h2>Waiting Queues (Live)</h2>
                <p>Updates every 3 seconds · Priority tickets listed first</p>
            </div>
            <span class="pecit-live-pill" id="livePill">Live</span>
        </div>
        <div class="pecit-table-wrap">
            <table class="pecit-table">
                <thead>
                    <tr>
                        <th>Queue #</th>
                        <th>Service</th>
                        <th>Priority</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="waiting-queues-body">
                    <tr>
                        <td colspan="4" class="empty">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var endpoint = @json(route('admin.queues.waiting'));
            var tbody = document.getElementById('waiting-queues-body');
            var livePill = document.getElementById('livePill');
            var clockEl = document.getElementById('dashboardClock');

            function escapeHtml(text) {
                if (text == null) return '';
                var div = document.createElement('div');
                div.textContent = String(text);
                return div.innerHTML;
            }

            function statusBadge(status) {
                var s = (status || '').toLowerCase();
                var cls = 'pecit-badge';
                if (s === 'waiting') cls += ' pecit-badge-waiting';
                else if (s === 'serving') cls += ' pecit-badge-serving';
                else if (s === 'done') cls += ' pecit-badge-done';
                else if (s === 'cancelled') cls += ' pecit-badge-cancelled';
                return '<span class="' + cls + '">' + escapeHtml(status || '') + '</span>';
            }

            function priorityBadge(priority) {
                var isPriority = Number(priority) === 1;
                var cls = isPriority ? 'pecit-badge pecit-badge-priority' : 'pecit-badge pecit-badge-regular';
                return '<span class="' + cls + '">' + (isPriority ? 'Priority' : 'Regular') + '</span>';
            }

            function setCount(id, value) {
                var el = document.getElementById(id);
                if (el) el.textContent = String(value ?? 0);
            }

            function tickClock() {
                if (!clockEl) return;
                var now = new Date();
                clockEl.textContent = now.toLocaleString(undefined, {
                    weekday: 'short',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
            }

            async function loadWaitingQueues() {
                try {
                    var response = await fetch(endpoint, {
                        headers: { 'Accept': 'application/json' },
                        cache: 'no-cache',
                    });

                    if (!response.ok) {
                        if (livePill) livePill.classList.add('is-stale');
                        return;
                    }

                    var payload = await response.json();
                    var data = Array.isArray(payload) ? payload : (payload.queues || []);
                    var counts = payload.counts || null;

                    if (counts) {
                        setCount('statTotal', counts.total);
                        setCount('statWaiting', counts.waiting);
                        setCount('statServing', counts.serving);
                        setCount('statCompleted', counts.completed);
                    }

                    tbody.innerHTML = '';

                    if (!Array.isArray(data) || data.length === 0) {
                        var emptyTr = document.createElement('tr');
                        emptyTr.innerHTML = '<td colspan="4" class="empty">No waiting queues.</td>';
                        tbody.appendChild(emptyTr);
                    } else {
                        data.forEach(function (queue) {
                            var tr = document.createElement('tr');
                            var serviceName = queue.service ? queue.service.service_name : '';
                            tr.innerHTML =
                                '<td style="font-weight:700;letter-spacing:0.04em;">' + escapeHtml(queue.queue_number) + '</td>' +
                                '<td>' + escapeHtml(serviceName) + '</td>' +
                                '<td>' + priorityBadge(queue.priority) + '</td>' +
                                '<td>' + statusBadge(queue.status) + '</td>';
                            tbody.appendChild(tr);
                        });
                    }

                    if (livePill) livePill.classList.remove('is-stale');
                } catch (e) {
                    if (livePill) livePill.classList.add('is-stale');
                    console.error('Error loading queues', e);
                }
            }

            tickClock();
            setInterval(tickClock, 1000);
            loadWaitingQueues();
            setInterval(loadWaitingQueues, 3000);
        });
    </script>
@endpush
