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
            font-family: Candara, "Segoe UI", "Trebuchet MS", sans-serif;
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
        .sf-actions {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
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
                <h1>{{ $window->service->service_name ?? 'Window' }}</h1>
                <p>{{ $window->window_name }}</p>
            </div>
            <div class="sf-pin">ON TOP</div>
        </div>

        @if (session('status'))
            <div class="sf-toast" id="statusToast">{{ session('status') }}</div>
        @endif

        <div class="sf-meta">
            <div>
                <span>Current</span>
                <strong id="currentQueue">{{ $currentQueue->queue_number ?? '---' }}</strong>
            </div>
            <div>
                <span>Next</span>
                <strong id="nextQueue">{{ $nextQueue->queue_number ?? '—' }}</strong>
            </div>
        </div>

        <div class="sf-actions">
            <form method="POST" action="{{ route('window.callNext') }}">
                @csrf
                <button type="submit" id="call-next-btn" class="sf-btn sf-call">Call Next</button>
            </form>
            <form method="POST" action="{{ route('window.recall') }}">
                @csrf
                <button type="submit" class="sf-btn sf-recall">Recall</button>
            </form>
            <form method="POST" action="{{ route('window.complete') }}">
                @csrf
                <button type="submit" class="sf-btn sf-complete">Complete</button>
            </form>
        </div>
    </div>

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
                setTimeout(function () { toast.style.display = 'none'; }, 3000);
            }

            var stateUrl = @json(route('window.state'));

            function fetchState() {
                fetch(stateUrl, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        var currentEl = document.getElementById('currentQueue');
                        var nextEl = document.getElementById('nextQueue');
                        if (currentEl) currentEl.textContent = data.current || '---';
                        if (nextEl) nextEl.textContent = data.next || '—';
                    })
                    .catch(function () {});
            }

            setInterval(fetchState, 3000);
        });
    </script>
</body>
</html>
