@extends('layouts.app')

@section('title', ($window->service->service_name ?? 'Window') . ' Dashboard')

@section('content')
    <h1 class="text-2xl font-bold mb-4">
        {{ $window->service->service_name ?? 'Window' }} Dashboard
        <span class="text-sm text-gray-500">({{ $window->window_name }})</span>
        <a href="{{ route('window.history') }}" class="text-sm font-normal text-blue-600 hover:underline ml-2">My History</a>
    </h1>

    @if (session('status'))
        <div class="mb-4 text-sm text-blue-700 bg-blue-100 border border-blue-300 px-3 py-2 rounded">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-4 rounded shadow">
            <h2 class="font-semibold text-gray-600 mb-2">Current Serving</h2>
            <div id="currentQueue" class="text-4xl font-bold">
                {{ $currentQueue->queue_number ?? '---' }}
            </div>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h2 class="font-semibold text-gray-600 mb-2">Next Queue</h2>
            <div id="nextQueue" class="text-3xl">
                {{ $nextQueue->queue_number ?? 'No waiting' }}
            </div>
        </div>
    </div>

    <div class="flex gap-3 mb-4">
        <form method="POST" action="{{ route('window.callNext') }}">
            @csrf
            <button class="bg-green-600 text-white px-4 py-2 rounded">Call Next</button>
        </form>
        <form method="POST" action="{{ route('window.recall') }}">
            @csrf
            <button class="bg-yellow-500 text-white px-4 py-2 rounded">Recall</button>
        </form>
        <form method="POST" action="{{ route('window.complete') }}">
            @csrf
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Complete</button>
        </form>
    </div>

    @if (session('called_queue_number') || session('recalled_queue_number'))
        @php
            $announceQueue = session('called_queue_number') ?? session('recalled_queue_number');
        @endphp
        <div id="announce" data-queue="{{ $announceQueue }}"></div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const el = document.getElementById('announce');
            if (el) {
                const queue = el.dataset.queue;
                const text = 'Queue number ' + queue + ' please proceed to the window';
                if ('speechSynthesis' in window) {
                    var utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'en-US';
                    speechSynthesis.speak(utterance);
                }
            }

            // Auto-refresh current & next queue so new tickets show without reload
            var stateUrl = '{{ route("window.state") }}';
            var pollInterval = 3000;

            function updateQueueDisplay(data) {
                var currentEl = document.getElementById('currentQueue');
                var nextEl = document.getElementById('nextQueue');
                if (currentEl) currentEl.textContent = data.current || '---';
                if (nextEl) nextEl.textContent = data.next || 'No waiting';
            }

            function fetchState() {
                fetch(stateUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (r) { return r.json(); })
                    .then(updateQueueDisplay)
                    .catch(function () {});
            }

            setInterval(fetchState, pollInterval);
        });
    </script>
@endsection

