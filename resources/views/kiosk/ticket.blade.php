@extends('layouts.app')

@section('title', 'Your Ticket')

@section('content')
    <div class="max-w-lg mx-auto bg-white p-6 rounded shadow text-center">
        <h1 class="text-2xl font-bold mb-4">Your Queue Ticket</h1>

        <div class="text-gray-600 mb-2">
            {{ $queue->student_name }} ({{ $queue->student_id ?? 'No ID' }})
        </div>

        <div class="text-6xl font-extrabold mb-4">
            {{ $queue->queue_number }}
        </div>

        <p class="text-gray-500 mb-4">
            Please wait for your number to be called on the display.
        </p>

        <a href="{{ route('kiosk') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded">
            Get Another Ticket
        </a>
    </div>
@endsection

