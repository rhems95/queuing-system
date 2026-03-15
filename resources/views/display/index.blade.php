<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Now Serving</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto py-6">
        <h1 class="text-4xl font-bold mb-6 text-center">NOW SERVING</h1>

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

        <h2 class="text-2xl font-semibold mb-3">Waiting List</h2>
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
                        <tr class="border-b border-gray-700">
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
                    return '<tr class="border-b border-gray-700">' +
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

            function fetchAndUpdate() {
                fetch(dataUrl, { headers: { 'Accept': 'application/json' } })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        var servingEl = document.getElementById('nowServing');
                        var waitingEl = document.getElementById('waitingList');
                        if (servingEl) servingEl.innerHTML = renderNowServing(data.now_serving);
                        if (waitingEl) waitingEl.innerHTML = renderWaiting(data.waiting);
                    })
                    .catch(function () {});
            }

            setInterval(fetchAndUpdate, pollInterval);
        })();
    </script>
</body>
</html>

