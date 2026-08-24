@extends('layouts.panel')

@section('title', ($window->service->service_name ?? 'Window') . ' Dashboard')

@section('content')
    <div class="pecit-page-header">
        <div>
            <h1 class="pecit-page-title">
                {{ $window->service->service_name ?? 'Window' }} Dashboard
            </h1>
            <p class="pecit-page-sub">
                {{ $window->window_name }} · Press Ctrl + Alt + Space to Call Next
            </p>
        </div>
        <form method="POST" action="{{ route('window.launchFloat') }}" id="launchFloatForm">
            @csrf
            <button type="submit" id="launchFloatBtn" class="pecit-btn pecit-btn-primary">
                Open System Float
            </button>
        </form>
    </div>

    <div id="launchFloatMsg" class="pecit-alert pecit-alert-info" style="display:none;"></div>
    @if (session('status'))
        <div id="statusToast" class="pecit-alert pecit-alert-info">
            {{ session('status') }}
        </div>
    @endif

    <div class="pecit-serving-grid">
        <div class="pecit-serving-card">
            <h2>Current Serving</h2>
            <div id="currentQueue" class="pecit-serving-number">
                {{ $currentQueue->queue_number ?? '---' }}
            </div>
            <div id="serviceTimer" class="pecit-service-timer" @if(empty($servingStartedAt)) style="visibility:hidden;" @endif>
                Service Time: <span id="serviceTimerValue">00:00</span>
            </div>
        </div>
        <div class="pecit-serving-card is-next">
            <h2>Next Queue</h2>
            <div id="nextQueue" class="pecit-serving-number">
                {{ $nextQueue->queue_number ?? 'No waiting' }}
            </div>
        </div>
    </div>

    <div class="pecit-actions">
        <form method="POST" action="{{ route('window.callNext') }}">
            @csrf
            <button type="submit" id="call-next-btn" class="pecit-btn pecit-btn-success pecit-btn-lg">Call Next</button>
        </form>
        <form method="POST" action="{{ route('window.recall') }}">
            @csrf
            <button type="submit" class="pecit-btn pecit-btn-warning pecit-btn-lg">Recall</button>
        </form>
        <form method="POST" action="{{ route('window.complete') }}">
            @csrf
            <button type="submit" class="pecit-btn pecit-btn-primary pecit-btn-lg">Complete</button>
        </form>
    </div>

    <div class="pecit-card">
        <div class="pecit-card-head">
            <div>
                <h2>Waiting (up to 10)</h2>
                <p>Live list for this service window</p>
            </div>
        </div>
        <div class="pecit-table-wrap">
            <table class="pecit-table">
                <thead>
                    <tr>
                        <th>Queue #</th>
                        <th>Priority</th>
                    </tr>
                </thead>
                <tbody id="waitingTicketsBody">
                    @forelse($waitingTickets as $q)
                        <tr>
                            <td style="font-weight:700;">{{ $q->queue_number }}</td>
                            <td>
                                @if ($q->priority)
                                    <span class="pecit-badge pecit-badge-priority">Priority</span>
                                @else
                                    <span class="pecit-badge pecit-badge-regular">Regular</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="empty">No waiting tickets.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('keydown', function (e) {
            if (e.ctrlKey && e.altKey && e.code === 'Space') {
                e.preventDefault();
                var btn = document.getElementById('call-next-btn');
                if (btn) btn.click();
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            var toast = document.getElementById('statusToast');
            if (toast) {
                setTimeout(function () {
                    toast.style.display = 'none';
                }, 3500);
            }

            var launchForm = document.getElementById('launchFloatForm');
            var launchBtn = document.getElementById('launchFloatBtn');
            var launchMsg = document.getElementById('launchFloatMsg');

            if (launchForm && launchBtn) {
                launchForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    launchBtn.disabled = true;
                    launchBtn.textContent = 'Launching...';

                    var tokenInput = launchForm.querySelector('input[name="_token"]');
                    var body = new URLSearchParams();
                    body.set('_token', tokenInput ? tokenInput.value : '');

                    fetch(launchForm.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: body,
                        credentials: 'same-origin',
                    })
                        .then(function (r) { return r.json().then(function (data) { return { ok: r.ok, data: data }; }); })
                        .then(function (res) {
                            if (launchMsg) {
                                launchMsg.style.display = 'block';
                                launchMsg.textContent = (res.data && res.data.message)
                                    ? res.data.message
                                    : (res.ok ? 'System float launched.' : 'Could not launch system float.');
                                launchMsg.className = res.ok
                                    ? 'pecit-alert pecit-alert-success'
                                    : 'pecit-alert pecit-alert-danger';
                            }
                        })
                        .catch(function () {
                            if (launchMsg) {
                                launchMsg.style.display = 'block';
                                launchMsg.className = 'pecit-alert pecit-alert-danger';
                                launchMsg.textContent = 'Could not launch system float. Try bats\\start-staff-float.bat manually.';
                            }
                        })
                        .finally(function () {
                            launchBtn.disabled = false;
                            launchBtn.textContent = 'Open System Float';
                        });
                });
            }

            var stateUrl = '{{ route("window.state") }}';
            var pollInterval = 3000;
            var servingStartedAt = @json($servingStartedAt ?? null);
            var timerInterval = null;

            function priorityBadge(label) {
                var isPriority = String(label).toLowerCase() === 'priority';
                var cls = isPriority ? 'pecit-badge pecit-badge-priority' : 'pecit-badge pecit-badge-regular';
                return '<span class="' + cls + '">' + escapeHtml(label) + '</span>';
            }

            function renderWaitingList(rows) {
                var body = document.getElementById('waitingTicketsBody');
                if (!body) return;
                if (!rows || !rows.length) {
                    body.innerHTML = '<tr><td colspan="2" class="empty">No waiting tickets.</td></tr>';
                    return;
                }
                body.innerHTML = rows.map(function (r) {
                    return '<tr>' +
                        '<td style="font-weight:700;">' + escapeHtml(r.queue_number) + '</td>' +
                        '<td>' + priorityBadge(r.priority) + '</td>' +
                        '</tr>';
                }).join('');
            }

            function escapeHtml(text) {
                if (text == null) return '';
                var div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function formatElapsed(ms) {
                var totalSec = Math.max(0, Math.floor(ms / 1000));
                var m = Math.floor(totalSec / 60);
                var s = totalSec % 60;
                return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            }

            function paintTimer() {
                var wrap = document.getElementById('serviceTimer');
                var valueEl = document.getElementById('serviceTimerValue');
                if (!wrap || !valueEl) return;
                if (!servingStartedAt) {
                    wrap.style.visibility = 'hidden';
                    valueEl.textContent = '00:00';
                    return;
                }
                var started = new Date(servingStartedAt);
                if (isNaN(started.getTime())) {
                    wrap.style.visibility = 'hidden';
                    return;
                }
                wrap.style.visibility = 'visible';
                valueEl.textContent = formatElapsed(Date.now() - started.getTime());
            }

            function setServingStartedAt(iso) {
                servingStartedAt = iso || null;
                paintTimer();
            }

            function updateQueueDisplay(data) {
                var currentEl = document.getElementById('currentQueue');
                var nextEl = document.getElementById('nextQueue');
                if (currentEl) currentEl.textContent = data.current || '---';
                if (nextEl) nextEl.textContent = data.next || 'No waiting';
                if (data.waiting_list) renderWaitingList(data.waiting_list);
                setServingStartedAt(data.serving_started_at || null);
            }

            function fetchState() {
                fetch(stateUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (r) { return r.json(); })
                    .then(updateQueueDisplay)
                    .catch(function () {});
            }

            paintTimer();
            timerInterval = setInterval(paintTimer, 1000);
            setInterval(fetchState, pollInterval);
        });
    </script>
@endpush
