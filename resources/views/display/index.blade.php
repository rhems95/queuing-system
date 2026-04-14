<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Now Serving</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto py-6">
        <div class="relative mb-6">
            <div class="absolute left-0 top-0 flex items-center gap-2">
                <img
                    src="{{ asset('logo/logo.png') }}"
                    alt="School Logo"
                    class="h-11 w-11 object-contain rounded-full shadow"
                >
                <div class="text-left leading-tight">
                    <div class="text-sm font-semibold text-white">Philippine Electronics and</div>
                    <div class="text-sm font-semibold text-white">Communication Institute of Technology Inc.</div>
                </div>
            </div>

            <div id="dateTimeDisplay" class="absolute right-0 top-0 text-right text-sm text-gray-300"></div>
            <div class="flex justify-center pt-14">
                <h1 class="text-4xl font-bold text-center">NOW SERVING</h1>
            </div>
        </div>

        <div id="nowServing" class="grid md:grid-cols-2 gap-6 mb-8">
            @foreach($nowServing as $item)
                <div class="bg-gray-800 p-4 rounded shadow text-center">
                    <div class="text-lg text-gray-300 mb-2">{{ $item['window_name'] }}</div>
                    <div class="text-5xl font-extrabold">
                        {{ $item['queue_number'] ?? '---' }}
                    </div>
                </div>
            @endforeach
        </div>

        <h2 class="text-2xl font-semibold mb-3 text-gray-300">Waiting List</h2>
        <div class="bg-gray-800 rounded shadow max-h-96 overflow-y-auto">
            <table class="min-w-full text-left">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-4 py-2">Queue #</th>
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Priority</th>
                    </tr>
                </thead>
                <tbody id="waitingList">
                    @forelse($waiting as $q)
                        <tr class="border-b border-gray-700 {{ $q->priority === 'Priority' ? 'bg-gray-600' : 'bg-gray-800' }}">
                            <td class="px-4 py-2">{{ $q->queue_number }}</td>
                            <td class="px-4 py-2">{{ $q->student_name }}</td>
                            <td class="px-4 py-2">{{ $q->priority }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-center text-gray-400">
                                No waiting queues.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        (function () {
            const dataUrl = '{{ route("display.data") }}';
            const pollInterval = 3000; // 3 seconds
            let audioEnabled = true;
            let speechUnlocked = false;
            let pendingAnnouncements = [];
            const MAX_PENDING = 3;
            let lastAnnouncedByWindow = {}; // window_name => last queue_number announced
            let lastCallTokenByWindow = {}; // window_name => last call token (changes on recall/new call)

            function renderNowServing(items) {
                if (!items || !items.length) return;
                return items.map(function (item) {
                    const num = item.queue_number || '---';
                    return '<div class="bg-gray-800 p-4 rounded shadow text-center">' +
                        '<div class="text-lg text-gray-300 mb-2">' + escapeHtml(item.window_name) + '</div>' +
                        '<div class="text-5xl font-extrabold">' + escapeHtml(String(num)) + '</div>' +
                        '</div>';
                }).join('');
            }

            function renderWaiting(rows) {
                if (!rows || !rows.length) {
                    return '<tr><td colspan="3" class="px-4 py-4 text-center text-gray-400">No waiting queues.</td></tr>';
                }
                return rows.map(function (q) {
                    var rowShade = q.priority === 'Priority' ? 'bg-gray-600' : 'bg-gray-800';
                    return '<tr class="border-b border-gray-700 ' + rowShade + '">' +
                        '<td class="px-4 py-2">' + escapeHtml(q.queue_number) + '</td>' +
                        '<td class="px-4 py-2">' + escapeHtml(q.student_name) + '</td>' +
                        '<td class="px-4 py-2">' + escapeHtml(q.priority) + '</td>' +
                        '</tr>';
                }).join('');
            }

            function escapeHtml(text) {
                if (text == null) return '';
                var div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function pickVoice(voices) {
                // --- ADJUST VOICE HERE (set to exact voice name on your PC) ---
                const PREFERRED_VOICE_NAME = ''; // e.g. 'Microsoft Zira - English (United States)'

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
                trySpeakText(text);
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
                    trySpeakText(text);
                }
            }

            function trySpeakText(text) {
                if (!('speechSynthesis' in window) || typeof SpeechSynthesisUtterance === 'undefined') return;
                try {
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'en-US';
                    utterance.rate = 1.0;
                    utterance.volume = 1;

                    const voices = speechSynthesis.getVoices();
                    const voice = pickVoice(voices);
                    if (voice) utterance.voice = voice;
                    utterance.onstart = function () {
                        speechUnlocked = true;
                    };
                    utterance.onerror = function () {
                        if (pendingAnnouncements.length < MAX_PENDING) {
                            pendingAnnouncements.push(text);
                        }
                    };

                    speechSynthesis.resume();
                    speechSynthesis.speak(utterance);
                } catch (e) {
                    if (pendingAnnouncements.length < MAX_PENDING) {
                        pendingAnnouncements.push(text);
                    }
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

            function maybeAnnounce(nowServing) {
                if (!nowServing || !nowServing.length) return;
                nowServing.forEach(function (item) {
                    const w = item.window_name;
                    const q = item.queue_number;
                    const callToken = item.call_token || null;
                    if (!w) return;

                    // Initialize without speaking on first load
                    if (typeof lastAnnouncedByWindow[w] === 'undefined') {
                        lastAnnouncedByWindow[w] = q;
                        lastCallTokenByWindow[w] = callToken;
                        return;
                    }

                    // Speak when queue changes OR when recall updates the call token.
                    if (q && (q !== lastAnnouncedByWindow[w] || callToken !== lastCallTokenByWindow[w])) {
                        lastAnnouncedByWindow[w] = q;
                        lastCallTokenByWindow[w] = callToken;
                        speakCall(w, q);
                        return;
                    }

                    // Keep token in sync when there is no announcement.
                    lastCallTokenByWindow[w] = callToken;
                });
            }

            function fetchAndUpdate() {
                fetch(dataUrl, { headers: { 'Accept': 'application/json' } })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        var servingEl = document.getElementById('nowServing');
                        var waitingEl = document.getElementById('waitingList');
                        if (servingEl) servingEl.innerHTML = renderNowServing(data.now_serving);
                        if (waitingEl) waitingEl.innerHTML = renderWaiting(data.waiting);
                        maybeAnnounce(data.now_serving);
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

                // Auto-attempt TTS unlock without any user tap/click.
                // Some browsers still require a user gesture; these retries maximize the chance.
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

