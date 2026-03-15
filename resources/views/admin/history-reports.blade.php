@extends('layouts.app')

@section('title', 'Reports & Analytics')

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
            <h1 class="text-2xl font-bold mb-4">Reports & Analytics</h1>

            <form method="GET" class="mb-6 flex flex-wrap items-center gap-2">
                <label class="text-sm text-gray-600">From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="border border-gray-300 rounded px-2 py-1">
                <label class="text-sm text-gray-600">To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="border border-gray-300 rounded px-2 py-1">
                <button class="bg-gray-700 text-white px-3 py-1 rounded text-sm">Apply</button>
            </form>

            <div class="grid md:grid-cols-2 gap-4 mb-6">
                <div class="bg-white p-4 rounded shadow">
                    <h2 class="font-semibold text-gray-600 mb-1">Total Served (period)</h2>
                    <p class="text-3xl font-bold">{{ $servedCount }}</p>
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <h2 class="font-semibold text-gray-600 mb-1">Avg. service time (completed)</h2>
                    <p class="text-3xl font-bold">
                        @if($avgServiceTimeSeconds !== null)
                            {{ (int)($avgServiceTimeSeconds / 60) }}m {{ $avgServiceTimeSeconds % 60 }}s
                        @else
                            —
                        @endif
                    </p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white rounded shadow overflow-hidden">
                    <h2 class="font-semibold p-3 bg-gray-100">By Service</h2>
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50"><tr><th class="px-3 py-2 text-left">Service</th><th class="px-3 py-2 text-right">Count</th></tr></thead>
                        <tbody>
                            @forelse($byService as $row)
                                <tr class="border-b border-gray-200">
                                    <td class="px-3 py-2">{{ $row->service_name }}</td>
                                    <td class="px-3 py-2 text-right">{{ $row->total }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="px-3 py-2 text-gray-500">No data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-white rounded shadow overflow-hidden">
                    <h2 class="font-semibold p-3 bg-gray-100">By Staff / Window</h2>
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50"><tr><th class="px-3 py-2 text-left">Staff / Window</th><th class="px-3 py-2 text-right">Count</th></tr></thead>
                        <tbody>
                            @forelse($byStaff as $row)
                                <tr class="border-b border-gray-200">
                                    <td class="px-3 py-2">{{ $row->staff_or_window ?? '—' }}</td>
                                    <td class="px-3 py-2 text-right">{{ $row->total }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="px-3 py-2 text-gray-500">No data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection
