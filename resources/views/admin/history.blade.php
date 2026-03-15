@extends('layouts.app')

@section('title', 'Transaction History')

@section('content')
    <div class="flex gap-6">
        <!-- Sidebar -->
        <aside
            id="adminSidebar"
            class="fixed top-16 left-0 w-60 bg-white shadow-lg h-full transform -translate-x-full transition-transform duration-300 z-40"
        >
            <h2 class="text-lg font-semibold mb-3 p-4">Admin Menu</h2>
            <nav class="space-y-2 text-sm px-2">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                    <span>📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                    <span>👤</span>
                    <span>Users</span>
                </a>
                <a href="{{ route('kiosk') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100">
                    <span>🎟️</span>
                    <span>Kiosk Page</span>
                </a>
                <a href="{{ route('display') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100">
                    <span>🖥️</span>
                    <span>Public Display</span>
                </a>
                <a href="{{ route('admin.history') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('admin.history') && !request()->routeIs('admin.history.*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                    <span>📜</span>
                    <span>Served History</span>
                </a>
                <a href="{{ route('admin.history.tickets') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('admin.history.tickets') ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                    <span>🎫</span>
                    <span>All Tickets</span>
                </a>
                <a href="{{ route('admin.history.reports') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded {{ request()->routeIs('admin.history.reports') ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                    <span>📈</span>
                    <span>Reports & Analytics</span>
                </a>
            </nav>
        </aside>

        <!-- Main content -->
        <section class="flex-1 ml-0 md:ml-64">
            <h1 class="text-2xl font-bold mb-4">Served Tickets (Transaction History)</h1>

            @if (session('status'))
                <div class="mb-4 text-sm text-blue-700 bg-blue-100 border border-blue-300 px-3 py-2 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <form method="GET" class="mb-4 flex items-center gap-2">
                <label class="text-sm text-gray-600">Filter by date:</label>
                <input type="date" name="date" value="{{ request('date') }}"
                       class="border border-gray-300 rounded px-2 py-1">
                <button class="bg-gray-700 text-white px-3 py-1 rounded text-sm">Apply</button>
            </form>

            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2">Queue #</th>
                            <th class="px-3 py-2">Student Name</th>
                            <th class="px-3 py-2">Service</th>
                            <th class="px-3 py-2">Window</th>
                            <th class="px-3 py-2">Staff</th>
                            <th class="px-3 py-2">Called Time</th>
                            <th class="px-3 py-2">Finished Time</th>
                            <th class="px-3 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $row)
                            <tr class="border-b border-gray-200">
                                <td class="px-3 py-2">{{ $row->queue_number }}</td>
                                <td class="px-3 py-2">{{ $row->student_name }}</td>
                                <td class="px-3 py-2">{{ $row->service_name }}</td>
                                <td class="px-3 py-2">{{ $row->window_name }}</td>
                                <td class="px-3 py-2">{{ $row->staff_name ?? '—' }}</td>
                                <td class="px-3 py-2">{{ $row->called_time }}</td>
                                <td class="px-3 py-2">{{ $row->finished_time ?? '—' }}</td>
                                <td class="px-3 py-2">
                                    <a href="{{ route('admin.history.edit', $row->queue_call_id) }}"
                                       class="text-blue-600 hover:underline mr-2">Edit</a>
                                    <form method="POST" action="{{ route('admin.history.destroy', $row->queue_call_id) }}"
                                          class="inline" onsubmit="return confirm('Delete this record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-4 text-center text-gray-500">
                                    No records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $records->withQueryString()->links() }}
            </div>
        </section>
    </div>
@endsection
