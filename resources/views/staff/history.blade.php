@extends('layouts.app')

@section('title', 'My Service History')

@section('content')
    <h1 class="text-2xl font-bold mb-4">My Service History</h1>
    <p class="text-sm text-gray-600 mb-4">Tickets you have served at your counter. You cannot edit or delete records.</p>

    <form method="GET" class="mb-4 flex items-center gap-2">
        <label class="text-sm text-gray-600">Filter by date:</label>
        <input type="date" name="date" value="{{ request('date') }}" class="border border-gray-300 rounded px-2 py-1">
        <button class="bg-gray-700 text-white px-3 py-1 rounded text-sm">Apply</button>
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2">Queue #</th>
                    <th class="px-3 py-2">Student Name</th>
                    <th class="px-3 py-2">Service</th>
                    <th class="px-3 py-2">Called Time</th>
                    <th class="px-3 py-2">Finished Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $row)
                    <tr class="border-b border-gray-200">
                        <td class="px-3 py-2">{{ $row->queue_number }}</td>
                        <td class="px-3 py-2">{{ $row->student_name }}</td>
                        <td class="px-3 py-2">{{ $row->service_name }}</td>
                        <td class="px-3 py-2">{{ $row->called_time }}</td>
                        <td class="px-3 py-2">{{ $row->finished_time ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-4 text-center text-gray-500">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $records->withQueryString()->links() }}
    </div>

    <div class="mt-4">
        <a href="{{ route('window.dashboard') }}" class="text-blue-600 hover:underline">← Back to Window</a>
    </div>
@endsection
