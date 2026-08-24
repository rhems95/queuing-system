@extends('layouts.app')

@section('title', 'Your Ticket')

@section('content')
    <div class="max-w-lg mx-auto bg-white p-6 rounded shadow text-center">
        <h1 class="text-2xl font-bold mb-4">Your Queue Ticket</h1>

        <div id="ticketCard" class="border-2 border-dashed border-gray-300 rounded-lg p-5 mb-5">
            <div class="text-6xl font-extrabold mb-3">
                {{ $queue->queue_number }}
            </div>

            <div class="text-sm text-gray-600 mb-3">
                Issued:
                <span id="issuedAtText">{{ $issuedAt->format('M d, Y h:i A') }}</span>
            </div>

            <p class="text-gray-500">
                Please wait for your number to be called on the display.
            </p>
        </div>

        <button
            type="button"
            onclick="window.print()"
            class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-3">
            Print
        </button>

        <a href="{{ route('kiosk') }}" class="inline-block text-sm text-gray-600 hover:underline">
            Back to Kiosk
        </a>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #ticketCard, #ticketCard * {
                visibility: visible;
            }
            #ticketCard {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                border: 0;
                margin: 0;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('issuedAtText');
            if (!el) return;

            var issuedAt = new Date('{{ $issuedAt->toIso8601String() }}');
            if (isNaN(issuedAt.getTime())) return;

            el.textContent = issuedAt.toLocaleString(undefined, {
                year: 'numeric',
                month: 'short',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
        });
    </script>
@endsection
