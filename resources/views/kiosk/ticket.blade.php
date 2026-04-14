@extends('layouts.app')

@section('title', 'Your Ticket')

@section('content')
    <div class="max-w-lg mx-auto bg-white p-6 rounded shadow text-center">
        <h1 class="text-2xl font-bold mb-4">Your Queue Ticket</h1>

        <div id="ticketCard" class="border-2 border-dashed border-gray-300 rounded-lg p-5 mb-5">
            <div class="text-gray-600 mb-2">
                {{ $queue->student_name }} ({{ $queue->student_id ?? 'No ID' }})
            </div>

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

        <p class="text-sm text-gray-500 mb-4">
            You may print this ticket, or simply save paper and take a photo of your ticket.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
            <button
                type="button"
                onclick="window.print()"
                class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Print
            </button>

            <a href="{{ route('kiosk') }}"
               class="w-full inline-flex items-center justify-center bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Save Mother Earth
            </a>
        </div>

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

            // Render issued time using kiosk/browser local timezone to avoid server TZ mismatch.
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

