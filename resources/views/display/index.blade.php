<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Now Serving</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#d9d9d9] text-slate-900">
    <style>
        .display-shell {
            width: 100%;
            min-height: 100vh;
            padding: 0 0 1rem 0;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }
        .display-top,
        .now-serving-row {
            flex: 0 0 auto;
            width: 100%;
        }
        .now-serving-row {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            justify-content: space-between;
            gap: 32px;
            margin-bottom: 1rem;
        }
        .now-serving-col {
            flex: 1 1 0;
            min-width: 0;
            text-align: center;
        }
        .cashier-row {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            justify-content: space-between;
            gap: 24px;
            width: 100%;
        }
        .cashier-row > div {
            flex: 1 1 0;
            min-width: 0;
        }
        .now-serving-number {
            min-height: 1.15em;
        }
        .waiting-section {
            width: 100%;
            margin-top: auto;
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }
        .waiting-panel {
            flex: 1 1 auto;
            min-height: 42vh;
            overflow-y: auto;
            background: #fff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        .waiting-table {
            width: 100%;
            table-layout: fixed;
            text-align: center;
            border-collapse: collapse;
        }
        .waiting-table th,
        .waiting-table td {
            width: 25%;
            vertical-align: middle;
        }
        .waiting-cell {
            font-size: 1.45rem;
            letter-spacing: 0.03em;
            line-height: 1.25;
            white-space: nowrap;
        }
        .waiting-num { font-weight: 800; }
        .waiting-tag { font-weight: 400; }
        .waiting-tag.is-priority { color: #dc2626; }
    </style>

    <div class="display-shell">
        <div class="display-top mb-6">
            <div class="shadow rounded-none px-4 py-3 flex items-center justify-between" style="background:#000080;color:#fff;">
                <div class="flex items-center gap-3">
                    <img
                        src="{{ asset('logo/logo.png') }}"
                        alt="School Logo"
                        class="h-10 w-10 object-contain rounded-full bg-white/90 p-1"
                    >
                    <div class="text-left leading-tight">
                        <div class="text-sm font-semibold" style="color:#fff;">Philippine Electronics and</div>
                        <div class="text-sm font-semibold" style="color:#fff;">Communication Institute of Technology Inc.</div>
                    </div>
                </div>
                <div id="dateTimeDisplay" class="text-right text-sm" style="color:#fff;"></div>
            </div>

            <div class="flex justify-center mt-4">
                <h1 class="text-4xl font-extrabold tracking-wide text-center text-slate-900">NOW SERVING</h1>
            </div>
        </div>

        <div id="nowServing" class="now-serving-row">
            @foreach($nowServingGroups as $group)
                <div class="now-serving-col">
                    <div class="inline-block text-sm font-extrabold px-4 py-2 rounded-md mb-3" style="background:#000080;color:#fff;">
                        {{ $group['group_name'] }}
                    </div>
                    <div class="{{ $group['group_name'] === 'Window 1' ? 'cashier-row' : 'space-y-4' }}">
                        @foreach($group['windows'] as $item)
                            <div class="text-center">
                                @php
                                    $hideLabel = $group['group_name'] === 'Window 2' && str_contains(strtolower($item['window_name'] ?? ''), 'promissory');
                                @endphp
                                <div class="text-[11px] font-semibold text-slate-800 mb-2">
                                    {{ $hideLabel ? '' : ($item['window_name'] ?? '') }}
                                </div>
                                <div class="{{ $group['group_name'] === 'Window 1' ? 'text-4xl' : 'text-5xl' }} font-extrabold tracking-widest tabular-nums text-slate-900 leading-none now-serving-number">
                                    {{ $item['queue_number'] ?? '----' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="waiting-section">
            <h2 class="text-xl font-semibold mb-2 text-slate-900">Waiting List</h2>
            <div class="waiting-panel">
                <table class="waiting-table">
                    <thead class="text-white sticky top-0" style="background:#000080;">
                        <tr>
                            @foreach(['Cashier', 'N/A', 'DMO', 'Registrar'] as $header)
                                <th class="px-2 py-3 text-xl font-extrabold tracking-wide">{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody id="waitingList">
                        @php
                            $headers = ['Cashier', 'N/A', 'DMO', 'Registrar'];
                            $hasAny = collect($waitingColumns)->flatten()->isNotEmpty();
                            $maxRows = 10;
                        @endphp
                        @if (! $hasAny)
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-center text-slate-500">
                                    No waiting queues.
                                </td>
                            </tr>
                        @else
                            @for ($i = 0; $i < $maxRows; $i++)
                                <tr class="border-b border-slate-200 bg-white">
                                    @foreach($headers as $header)
                                        @php
                                            $label = $waitingColumns[$header][$i] ?? '';
                                            $isPriority = str_contains($label, '(priority)');
                                            if (preg_match('/^(.+?)(\((?:priority|regular)\))$/', $label, $m)) {
                                                $ticketNum = $m[1];
                                                $ticketTag = $m[2];
                                            } else {
                                                $ticketNum = $label;
                                                $ticketTag = '';
                                            }
                                        @endphp
                                        <td class="waiting-cell px-2 py-2.5 {{ $isPriority ? 'bg-slate-100' : '' }}">
                                            @if ($ticketNum !== '')
                                                <span class="waiting-num">{{ $ticketNum }}</span>@if ($ticketTag !== '')<span class="waiting-tag {{ $isPriority ? 'is-priority' : '' }}">{{ $ticketTag }}</span>@endif
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endfor
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const dataUrl = '{{ route("display.data") }}';
            const pollInterval = 3000;
            let audioEnabled = true;
            let speechUnlocked = false;
            let pendingAnnouncements = [];
            const MAX_PENDING = 3;
            let lastAnnouncedByWindow = {};
            let lastCallTokenByWindow = {};
            let announcementQueue = [];
            let isSpeaking = false;
            let speechWatchdog = null;
            let lastDisplaySnapshot = '';

            function flattenServing(groups) {
                const out = [];
                (groups || []).forEach(function (g) {
                    (g.windows || []).forEach(function (w) {
                        out.push({
                            window_key: (g.group_name || '') + '|' + (w.window_name || ''),
                            window_name: w.window_name,
                            queue_number: w.queue_number,
                            call_token: w.call_token
                        });
                    });
                });
                return out;
            }

            function renderNowServing(groups) {
                if (!groups || !groups.length) return '';
                return groups.map(function (g) {
                    var isWindow1 = String(g.group_name || '') === 'Window 1';
                    const wRows = (g.windows || []).map(function (item) {
                        const num = item.queue_number || '----';
                        var hideLabel = String(g.group_name || '') === 'Window 2' && String(item.window_name || '').toLowerCase().indexOf('promissory') !== -1;
                        var numberClass = String(g.group_name || '') === 'Window 1' ? 'text-4xl' : 'text-5xl';
                        return '<div class="text-center">' +
                            '<div class="text-[11px] font-semibold text-slate-800 mb-2">' + (hideLabel ? '' : escapeHtml(item.window_name)) + '</div>' +
                            '<div class="' + numberClass + ' font-extrabold tracking-widest tabular-nums text-slate-900 leading-none now-serving-number">' + escapeHtml(String(num)) + '</div>' +
                            '</div>';
                    }).join('');
                    return '<div class="now-serving-col text-center">' +
                        '<div class="inline-block text-sm font-extrabold px-4 py-2 rounded-md mb-3" style="background:#000080;color:#fff;">' + escapeHtml(g.group_name) + '</div>' +
                        '<div class="' + (isWindow1 ? 'cashier-row' : 'space-y-4') + '">' + wRows + '</div>' +
                        '</div>';
                }).join('');
            }

            function formatWaitingLabel(label) {
                label = String(label || '');
                if (!label) return '';
                var match = label.match(/^(.+?)(\((?:priority|regular)\))$/);
                if (!match) {
                    return '<span class="waiting-num">' + escapeHtml(label) + '</span>';
                }
                var isPriority = match[2] === '(priority)';
                return '<span class="waiting-num">' + escapeHtml(match[1]) + '</span>' +
                    '<span class="waiting-tag' + (isPriority ? ' is-priority' : '') + '">' + escapeHtml(match[2]) + '</span>';
            }

            function renderWaiting(columns) {
                columns = columns || {};
                var headers = ['Cashier', 'N/A', 'DMO', 'Registrar'];
                var lists = headers.map(function (h) { return columns[h] || []; });
                var hasAny = lists.some(function (list) { return list.length > 0; });
                var maxRows = 10;

                if (!hasAny) {
                    return '<tr><td colspan="4" class="px-4 py-4 text-center text-slate-500">No waiting queues.</td></tr>';
                }

                var html = '';
                for (var i = 0; i < maxRows; i++) {
                    html += '<tr class="border-b border-slate-200 bg-white">';
                    headers.forEach(function (h, idx) {
                        var label = lists[idx][i] || '';
                        var shade = String(label).indexOf('(priority)') !== -1 ? 'bg-slate-100' : '';
                        html += '<td class="waiting-cell px-2 py-2.5 ' + shade + '">' +
                            formatWaitingLabel(label) +
                            '</td>';
                    });
                    html += '</tr>';
                }
                return html;
            }

            function escapeHtml(text) {
                if (text == null) return '';
                var div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function pickVoice(voices) {
                const PREFERRED_VOICE_NAME = '';
                if (!voices || !voices.length) return null;
                if (PREFERRED_VOICE_NAME) {
                    const match = voices.find(v => v.name === PREFERRED_VOICE_NAME);
                    if (match) return match;
                }
                return voices.find(v => (v.lang || '').toLowerCase().startsWith('en')) || voices[0];
            }

            function speakCall(windowName, queueNumber) {
                if (!audioEnabled) return;
                if (!('speechSynthesis' in window) || typeof SpeechSynthesisUtterance === 'undefined') return;
                if (!queueNumber || queueNumber === '---') return;

                const text = 'Queue number ' + queueNumber + ', please proceed to ' + windowName;
                enqueueAnnouncement(text);
            }

            function enqueueAnnouncement(text) {
                if (!text) return;
                announcementQueue.push(text);
                processAnnouncementQueue();
            }

            function processAnnouncementQueue() {
                if (isSpeaking) return;
                if (!announcementQueue.length) return;
                const text = announcementQueue.shift();
                trySpeakText(text);
            }

            function resetSpeakingState() {
                isSpeaking = false;
                if (speechWatchdog) {
                    clearTimeout(speechWatchdog);
                    speechWatchdog = null;
                }
            }

            function unlockSpeech() {
                if (speechUnlocked) return;
                if (!('speechSynthesis' in window) || typeof SpeechSynthesisUtterance === 'undefined') return;
                try {
                    const probe = new SpeechSynthesisUtterance(' ');
                    probe.volume = 0;
                    speechSynthesis.speak(probe);
                    speechUnlocked = true;
                    flushPendingAnnouncements();
                } catch (e) {}
            }

            function flushPendingAnnouncements() {
                while (pendingAnnouncements.length > 0) {
                    const text = pendingAnnouncements.shift();
                    enqueueAnnouncement(text);
                }
            }

            function trySpeakText(text) {
                if (!('speechSynthesis' in window) || typeof SpeechSynthesisUtterance === 'undefined') return;
                if (!speechUnlocked) {
                    if (pendingAnnouncements.length < MAX_PENDING) {
                        pendingAnnouncements.push(text);
                    }
                    return;
                }
                try {
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'en-US';
                    utterance.rate = 1.0;
                    utterance.volume = 1;
                    isSpeaking = true;
                    if (speechWatchdog) clearTimeout(speechWatchdog);
                    speechWatchdog = setTimeout(function () {
                        resetSpeakingState();
                        processAnnouncementQueue();
                    }, 6000);

                    const voices = speechSynthesis.getVoices();
                    const voice = pickVoice(voices);
                    if (voice) utterance.voice = voice;
                    utterance.onstart = function () {
                        speechUnlocked = true;
                    };
                    utterance.onend = function () {
                        resetSpeakingState();
                        processAnnouncementQueue();
                    };
                    utterance.onerror = function () {
                        resetSpeakingState();
                        if (pendingAnnouncements.length < MAX_PENDING) {
                            pendingAnnouncements.push(text);
                        }
                        processAnnouncementQueue();
                    };

                    speechSynthesis.resume();
                    speechSynthesis.speak(utterance);
                } catch (e) {
                    resetSpeakingState();
                    if (pendingAnnouncements.length < MAX_PENDING) {
                        pendingAnnouncements.push(text);
                    }
                    processAnnouncementQueue();
                }
            }

            function updateDateTime() {
                var el = document.getElementById('dateTimeDisplay');
                if (!el) return;
                var now = new Date();
                var date = now.toLocaleDateString(undefined, {
                    weekday: 'short',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
                var time = now.toLocaleTimeString(undefined, {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                el.textContent = date + ' | ' + time;
            }

            function maybeAnnounce(nowServingFlat) {
                if (!nowServingFlat || !nowServingFlat.length) return;
                nowServingFlat.forEach(function (item) {
                    const w = item.window_name;
                    const key = item.window_key || item.window_name;
                    const q = item.queue_number;
                    const callToken = item.call_token || null;
                    if (!w || !q || q === '----') return;

                    if (typeof lastAnnouncedByWindow[key] === 'undefined') {
                        lastAnnouncedByWindow[key] = q;
                        lastCallTokenByWindow[key] = callToken;
                        return;
                    }

                    if (q && (q !== lastAnnouncedByWindow[key] || callToken !== lastCallTokenByWindow[key])) {
                        lastAnnouncedByWindow[key] = q;
                        lastCallTokenByWindow[key] = callToken;
                        speakCall(w, q);
                        return;
                    }

                    lastCallTokenByWindow[key] = callToken;
                });
            }

            function fetchAndUpdate() {
                fetch(dataUrl, { headers: { 'Accept': 'application/json' }, cache: 'no-store' })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        var snapshot = JSON.stringify({
                            serving: (data.now_serving_groups || []).map(function (g) {
                                return {
                                    group_name: g.group_name,
                                    windows: (g.windows || []).map(function (w) {
                                        return {
                                            window_name: w.window_name,
                                            queue_number: w.queue_number
                                        };
                                    })
                                };
                            }),
                            waiting: {
                                Cashier: (data.waiting_columns && data.waiting_columns.Cashier) || [],
                                'N/A': (data.waiting_columns && data.waiting_columns['N/A']) || [],
                                DMO: (data.waiting_columns && data.waiting_columns.DMO) || [],
                                Registrar: (data.waiting_columns && data.waiting_columns.Registrar) || []
                            }
                        });
                        // Avoid rewriting the DOM when nothing changed — full replaces cause visual shuffling.
                        if (snapshot === lastDisplaySnapshot) {
                            maybeAnnounce(flattenServing(data.now_serving_groups || []));
                            return;
                        }
                        lastDisplaySnapshot = snapshot;

                        var servingEl = document.getElementById('nowServing');
                        var waitingEl = document.getElementById('waitingList');
                        const groups = data.now_serving_groups || [];
                        if (servingEl) {
                            servingEl.innerHTML = renderNowServing(groups);
                            servingEl.className = 'now-serving-row';
                        }
                        if (waitingEl) waitingEl.innerHTML = renderWaiting(data.waiting_columns);
                        maybeAnnounce(flattenServing(groups));
                    })
                    .catch(function () {});
            }

            document.addEventListener('DOMContentLoaded', function () {
                audioEnabled = true;
                updateDateTime();
                setInterval(updateDateTime, 1000);
                if ('speechSynthesis' in window) {
                    speechSynthesis.onvoiceschanged = function () {
                        speechSynthesis.getVoices();
                        flushPendingAnnouncements();
                    };
                }

                ['click', 'touchstart', 'keydown'].forEach(function (evt) {
                    document.addEventListener(evt, function () {
                        unlockSpeech();
                    }, { passive: true });
                });

                unlockSpeech();
                setTimeout(unlockSpeech, 300);
                setTimeout(unlockSpeech, 1000);
                setTimeout(flushPendingAnnouncements, 1500);
                setTimeout(flushPendingAnnouncements, 3000);
            });

            fetchAndUpdate();
            setInterval(fetchAndUpdate, pollInterval);
        })();
    </script>
</body>
</html>
