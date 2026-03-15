@extends('layouts.app')

@section('title', 'Edit Transaction Record')

@section('content')
    <div class="flex gap-6">
        <aside id="adminSidebar"
               class="fixed top-16 left-0 w-60 bg-white shadow-lg h-full transform -translate-x-full transition-transform duration-300 z-40">
            <h2 class="text-lg font-semibold mb-3 p-4">Admin Menu</h2>
            <nav class="space-y-2 text-sm px-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100">📊 Dashboard</a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100">👤 Users</a>
                <a href="{{ route('admin.history') }}" class="flex items-center gap-2 px-3 py-2 rounded bg-blue-600 text-white">📜 Served History</a>
                <a href="{{ route('admin.history.tickets') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100">🎫 All Tickets</a>
                <a href="{{ route('admin.history.reports') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100">📈 Reports</a>
            </nav>
        </aside>

        <section class="flex-1 ml-0 md:ml-64 max-w-xl">
            <h1 class="text-2xl font-bold mb-4">Edit Transaction Record</h1>

            <p class="text-sm text-gray-600 mb-2">
                {{ $record->queue_number }} — {{ $record->student_name }} ({{ $record->service_name }}) — Staff: {{ $record->staff_name ?? '—' }}
            </p>

            <form method="POST" action="{{ route('admin.history.update', $queue_call) }}" class="bg-white rounded shadow p-4">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Called time</label>
                    <input type="datetime-local" name="called_time" step="1"
                           value="{{ \Carbon\Carbon::parse($record->called_time)->format('Y-m-d\TH:i:s') }}"
                           class="w-full border border-gray-300 rounded px-2 py-1">
                    @error('called_time')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Finished time (optional)</label>
                    <input type="datetime-local" name="finished_time" step="1"
                           value="{{ $record->finished_time ? \Carbon\Carbon::parse($record->finished_time)->format('Y-m-d\TH:i:s') : '' }}"
                           class="w-full border border-gray-300 rounded px-2 py-1">
                    @error('finished_time')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
                    <a href="{{ route('admin.history') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded">Cancel</a>
                </div>
            </form>
        </section>
    </div>
@endsection
