@extends('layouts.panel')

@section('title', ($window->window_name ?? 'Window') . ' Dashboard')

@section('content')
    <div class="pecit-page-header">
        <div>
            <h1 class="pecit-page-title">
                {{ $window->window_name }}
            </h1>
            <p class="pecit-page-sub">
                {{ $window->service->service_name ?? 'Window' }} · Alt+N Next · Alt+R Recall · Alt+C Complete
            </p>
        </div>
            <form method="POST" action="{{ rtrim(request()->getBasePath(), '/') }}/window/launch-float" id="launchFloatForm">
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
            <div id="currentStudentName" class="pecit-serving-name">{{ $currentStudentName ?? '' }}</div>
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
            <button type="submit" id="call-next-btn" class="pecit-btn pecit-btn-success pecit-btn-lg" title="Alt+N">Call Next</button>
        </form>
        <form method="POST" action="{{ route('window.recall') }}">
            @csrf
            <button type="submit" id="recall-btn" class="pecit-btn pecit-btn-warning pecit-btn-lg" title="Alt+R">Recall</button>
        </form>
        <form method="POST" action="{{ route('window.complete') }}">
            @csrf
            <button type="submit" id="complete-btn" class="pecit-btn pecit-btn-primary pecit-btn-lg" title="Alt+C">Complete</button>
        </form>
        <form method="POST" action="{{ route('window.hold') }}">
            @csrf
            <button type="submit" id="hold-btn" class="pecit-btn pecit-btn-secondary pecit-btn-lg">Hold</button>
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

    <div class="pecit-card">
        <div class="pecit-card-head">
            <div>
                <h2>Held (set aside)</h2>
                <p>Call later without waiting in the 2P→1R line</p>
            </div>
        </div>
        <div class="pecit-table-wrap">
            <table class="pecit-table">
                <thead>
                    <tr>
                        <th>Queue #</th>
                        <th>Name</th>
                        <th>Priority</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="heldTicketsBody">
                    @forelse($heldTickets as $q)
                        <tr>
                            <td style="font-weight:700;">{{ $q->queue_number }}</td>
                            <td>{{ $q->student->name ?? ($q->issued_by ? \App\Services\TicketIssuer::walkInServingLabel($q->issue_reason) : '—') }}</td>
                            <td>
                                @if ($q->priority)
                                    <span class="pecit-badge pecit-badge-priority">Priority</span>
                                @else
                                    <span class="pecit-badge pecit-badge-regular">Regular</span>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('window.callHeld') }}">
                                    @csrf
                                    <input type="hidden" name="queue_id" value="{{ $q->id }}">
                                    <button type="submit" class="pecit-btn pecit-btn-success">Call</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty">No held tickets.</td>
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
            if (e.repeat || !e.altKey || e.ctrlKey || e.metaKey || e.shiftKey) return;
            var tag = e.target && e.target.tagName ? e.target.tagName.toLowerCase() : '';
            if (tag === 'input' || tag === 'textarea' || tag === 'select' || (e.target && e.target.isContentEditable)) return;
            var key = String(e.key || '').toLowerCase();
            var code = String(e.code || '');
            var btnId = null;
            if (key === 'n' || code === 'KeyN') btnId = 'call-next-btn';
            else if (key === 'r' || code === 'KeyR') btnId = 'recall-btn';
            else if (key === 'c' || code === 'KeyC') btnId = 'complete-btn';
            if (!btnId) return;
            e.preventDefault();
            var btn = document.getElementById(btnId);
            if (btn && !btn.disabled) btn.click();
        }, true);

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

            function startLocalFloatBat() {
                var link = document.createElement('a');
                link.href = 'pecit-float:open';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            if (launchForm && launchBtn) {
                launchForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    startLocalFloatBat();
                    launchBtn.disabled = true;
                    launchBtn.textContent = 'Opening...';

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
                                launchMsg.className = 'pecit-alert pecit-alert-info';
                                launchMsg.textContent = (res.data && res.data.message)
                                    ? res.data.message
                                    : 'If the float did not open, run bats\\install-staff-float-protocol.bat once on this PC, then try again.';
                            }
                        })
                        .catch(function () {
                            if (launchMsg) {
                                launchMsg.style.display = 'block';
                                launchMsg.className = 'pecit-alert pecit-alert-info';
                                launchMsg.textContent = 'If the float did not open, run bats\\install-staff-float-protocol.bat once on this PC (or double-click bats\\start-staff-float.bat). Set bats\\staff-float-url.txt to the server IP, e.g. http://192.168.2.100/queue-system/public/window/float';
                            }
                        })
                        .finally(function () {
                            launchBtn.disabled = false;
                            launchBtn.textContent = 'Open System Float';
                        });
                });
            }

            var stateUrl = '{{ route("window.state") }}';
            var callHeldUrl = @json(route('window.callHeld'));
            var csrfToken = @json(csrf_token());
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

            function renderHeldList(rows) {
                var body = document.getElementById('heldTicketsBody');
                if (!body) return;
                if (!rows || !rows.length) {
                    body.innerHTML = '<tr><td colspan="4" class="empty">No held tickets.</td></tr>';
                    return;
                }
                body.innerHTML = rows.map(function (r) {
                    return '<tr>' +
                        '<td style="font-weight:700;">' + escapeHtml(r.queue_number) + '</td>' +
                        '<td>' + escapeHtml(r.student_name || '—') + '</td>' +
                        '<td>' + priorityBadge(r.priority) + '</td>' +
                        '<td>' +
                            '<form method="POST" action="' + callHeldUrl + '">' +
                                '<input type="hidden" name="_token" value="' + escapeHtml(csrfToken) + '">' +
                                '<input type="hidden" name="queue_id" value="' + escapeHtml(r.id) + '">' +
                                '<button type="submit" class="pecit-btn pecit-btn-success">Call</button>' +
                            '</form>' +
                        '</td>' +
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
                var currentNameEl = document.getElementById('currentStudentName');
                var nextEl = document.getElementById('nextQueue');
                if (currentEl) currentEl.textContent = data.current || '---';
                if (currentNameEl) currentNameEl.textContent = data.current_name || '';
                if (nextEl) nextEl.textContent = data.next || 'No waiting';
                if (data.waiting_list) renderWaitingList(data.waiting_list);
                if (data.held_list) renderHeldList(data.held_list);
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
