<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PECIT Staff Float</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html, body {
            margin: 0;
            padding: 0;
            background: #00005c;
            color: #fff;
            font-family: "Source Sans 3 Variable", "Segoe UI", Tahoma, Arial, sans-serif;
            overflow: hidden;
            user-select: none;
        }
        .sf {
            min-height: 100vh;
            box-sizing: border-box;
            padding: 6px 8px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 6px;
            background: linear-gradient(160deg, #00005c 0%, #000080 55%, #1a1a99 100%);
        }
        .sf-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            border-bottom: 2px solid #c9a227;
            padding-bottom: 4px;
        }
        .sf-head h1 {
            margin: 0;
            font-size: 10px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }
        .sf-head p {
            margin: 1px 0 0;
            font-size: 10px;
            opacity: 0.85;
        }
        .sf-pin {
            font-size: 8px;
            font-weight: 700;
            background: #c9a227;
            color: #1a1400;
            border-radius: 999px;
            padding: 2px 6px;
            white-space: nowrap;
        }
        .sf-head-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 4px;
            flex-shrink: 0;
        }
        .sf-logout-form {
            margin: 0;
        }
        .sf-logout {
            appearance: none;
            font: inherit;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: #fff;
            border-radius: 999px;
            padding: 2px 6px;
            cursor: pointer;
            white-space: nowrap;
        }
        .sf-logout:hover {
            background: rgba(255, 255, 255, 0.12);
        }
        .sf-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
        }
        .sf-meta > div {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 6px;
            padding: 4px 6px;
            text-align: center;
        }
        .sf-meta span {
            display: block;
            font-size: 8px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            opacity: 0.8;
        }
        .sf-meta strong {
            display: block;
            margin-top: 1px;
            font-size: 15px;
            letter-spacing: 0.03em;
            font-variant-numeric: tabular-nums;
        }
        .sf-name {
            display: block;
            margin-top: 2px;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 0;
            line-height: 1.2;
            max-height: 2.4em;
            overflow: hidden;
            opacity: 0.95;
            color: #f5e6a3;
        }
        .sf-timer {
            text-align: center;
            font-size: 10px;
            letter-spacing: 0.04em;
            opacity: 0.9;
            font-variant-numeric: tabular-nums;
            min-height: 14px;
        }
        .sf-timer em {
            font-style: normal;
            font-weight: 800;
            color: #f5e6a3;
        }
        .sf-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5px;
            align-items: stretch;
        }
        .sf-actions form {
            margin: 0;
            display: flex;
        }
        .sf-btn {
            width: 100%;
            border: 0;
            border-radius: 6px;
            font: inherit;
            font-size: 11px;
            font-weight: 800;
            padding: 8px 4px;
            cursor: pointer;
            color: #fff;
            white-space: nowrap;
        }
        .sf-btn:active { transform: translateY(1px); }
        .sf-call { background: #0f7a4b; }
        .sf-recall { background: #d97706; }
        .sf-complete { background: #1d4ed8; }
        .sf-hold { background: #64748b; }
        .sf-toast {
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 6px;
            padding: 4px 6px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="sf">
        <div class="sf-head">
            <div>
                <h1>{{ $window->window_name }}</h1>
                <p>{{ $window->service->service_name ?? 'Window' }}</p>
            </div>
            <div class="sf-head-actions">
                <div class="sf-pin">ON TOP</div>
                <form method="POST" action="{{ route('logout') }}" class="sf-logout-form">
                    @csrf
                    <input type="hidden" name="float_logout" value="1">
                    <button type="submit" class="sf-logout">Log out</button>
                </form>
            </div>
        </div>

        @if (session('status'))
            <div class="sf-toast" id="statusToast">{{ session('status') }}</div>
        @endif

        <div class="sf-meta">
            <div>
                <span>Current</span>
                <strong id="currentQueue">{{ $currentQueue->queue_number ?? '---' }}</strong>
                <span class="sf-name" id="currentStudentName">{{ $currentStudentName ?? '' }}</span>
            </div>
            <div>
                <span>Next</span>
                <strong id="nextQueue">{{ $nextQueue->queue_number ?? '—' }}</strong>
            </div>
        </div>
        <div class="sf-timer" id="serviceTimer" @if(empty($servingStartedAt)) style="visibility:hidden;" @endif>
            Time <em id="serviceTimerValue">00:00</em>
        </div>

        <div class="sf-actions">
            <form method="POST" action="{{ route('window.callNext') }}">
                @csrf
                <button type="submit" id="call-next-btn" class="sf-btn sf-call" title="Alt+N">Call Next</button>
            </form>
            <form method="POST" action="{{ route('window.recall') }}">
                @csrf
                <button type="submit" id="recall-btn" class="sf-btn sf-recall" title="Alt+R">Recall</button>
            </form>
            <form method="POST" action="{{ route('window.complete') }}">
                @csrf
                <button type="submit" id="complete-btn" class="sf-btn sf-complete" title="Alt+C">Complete</button>
            </form>
            <form method="POST" action="{{ route('window.hold') }}">
                @csrf
                <button type="submit" id="hold-btn" class="sf-btn sf-hold">Hold</button>
            </form>
        </div>
    </div>

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
                setTimeout(function () { toast.style.display = 'none'; }, 3000);
            }

            var stateUrl = @json(route('window.state'));
            var servingStartedAt = @json($servingStartedAt ?? null);

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

            function fetchState() {
                fetch(stateUrl, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        var currentEl = document.getElementById('currentQueue');
                        var currentNameEl = document.getElementById('currentStudentName');
                        var nextEl = document.getElementById('nextQueue');
                        if (currentEl) currentEl.textContent = data.current || '---';
                        if (currentNameEl) currentNameEl.textContent = data.current_name || '';
                        if (nextEl) nextEl.textContent = data.next || '—';
                        servingStartedAt = data.serving_started_at || null;
                        paintTimer();
                    })
                    .catch(function () {});
            }

            paintTimer();
            setInterval(paintTimer, 1000);
            setInterval(fetchState, 3000);
        });
    </script>
    @include('partials.secret-about-hotkey')
</body>
</html>
