@extends('layouts.app')

@section('title', 'All Generated Tickets')

@section('content')
    <div class="flex gap-6">
        <aside id="adminSidebar"
               class="fixed top-16 left-0 w-60 bg-white shadow-lg h-full transform -translate-x-full transition-transform duration-300 z-40">
            <h2 class="text-lg font-semibold mb-3 p-4">Admin Menu</h2>
            <nav class="space-y-2 text-sm px-2">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">📊 Dashboard</a>
                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">👤 Users</a>
                <a href="{{ route('kiosk') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100">🎟️ Kiosk</a>
                <a href="{{ route('display') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100">🖥️ Display</a>
                <a href="{{ route('admin.history') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('admin.history') && !request()->routeIs('admin.history.*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">📜 Served History</a>
                <a href="{{ route('admin.history.tickets') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('admin.history.tickets') ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">🎫 All Tickets</a>
                <a href="{{ route('admin.history.reports') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('admin.history.reports') ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">📈 Reports</a>
            </nav>
        </aside>

        <section class="flex-1 ml-0 md:ml-64">
            <h1 class="text-2xl font-bold mb-4">All Generated Tickets</h1>

            <form method="GET" class="mb-4 flex flex-wrap items-center gap-2">
                <input type="date" name="date" value="{{ request('date') }}" class="border border-gray-300 rounded px-2 py-1">
                <select name="status" class="border border-gray-300 rounded px-2 py-1">
                    <option value="">All statuses</option>
                    <option value="waiting" {{ request('status') === 'waiting' ? 'selected' : '' }}>Waiting</option>
                    <option value="serving" {{ request('status') === 'serving' ? 'selected' : '' }}>Serving</option>
                    <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Done</option>
                </select>
                <button class="bg-gray-700 text-white px-3 py-1 rounded text-sm">Apply</button>
            </form>

            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2">Queue #</th>
                            <th class="px-3 py-2">Student Name</th>
                            <th class="px-3 py-2">Student ID</th>
                            <th class="px-3 py-2">Service</th>
                            <th class="px-3 py-2">Priority</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Queue Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $row)
                            <tr class="border-b border-gray-200">
                                <td class="px-3 py-2">{{ $row->queue_number }}</td>
                                <td class="px-3 py-2">{{ $row->student_name }}</td>
                                <td class="px-3 py-2">{{ $row->student_id }}</td>
                                <td class="px-3 py-2">{{ $row->service_name }}</td>
                                <td class="px-3 py-2">{{ $row->priority }}</td>
                                <td class="px-3 py-2">{{ $row->status }}</td>
                                <td class="px-3 py-2">{{ $row->queue_date }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-4 text-center text-gray-500">No tickets found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $records->withQueryString()->links() }}</div>
        </section>
    </div>
@endsection
