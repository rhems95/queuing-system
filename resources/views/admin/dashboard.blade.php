@extends('layouts.app')

@section('title', 'Admin Dashboard')

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
            <h1 class="text-2xl font-bold mb-4">Admin Dashboard</h1>

            <div class="grid md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-4 rounded shadow">
                    <div class="text-sm text-gray-500">Total Queues Today</div>
                    <div class="text-3xl font-bold">{{ $totalToday }}</div>
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <div class="text-sm text-gray-500">Waiting Queues</div>
                    <div class="text-3xl font-bold text-yellow-600">{{ $waiting }}</div>
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <div class="text-sm text-gray-500">Completed Queues</div>
                    <div class="text-3xl font-bold text-green-600">{{ $completed }}</div>
                </div>
            </div>

            <!-- Live waiting queues -->
            <div class="bg-white rounded shadow overflow-x-auto">
                <div class="px-4 py-3 border-b">
                    <h2 class="text-lg font-semibold">Waiting Queues (Live)</h2>
                    <p class="text-xs text-gray-500">Updates automatically every 3 seconds.</p>
                </div>
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2">Queue #</th>
                            <th class="px-3 py-2">Student Name</th>
                            <th class="px-3 py-2">Service</th>
                            <th class="px-3 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody id="waiting-queues-body">
                        <tr>
                            <td colspan="4" class="px-3 py-3 text-center text-gray-500">
                                Loading...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const endpoint = @json(route('admin.queues.waiting'));
            const tbody = document.getElementById('waiting-queues-body');

            async function loadWaitingQueues() {
                try {
                    const response = await fetch(endpoint, {
                        headers: { 'Accept': 'application/json' },
                        cache: 'no-cache',
                    });

                    if (!response.ok) {
                        console.error('Failed to fetch queues', response.status);
                        return;
                    }

                    const data = await response.json();
                    tbody.innerHTML = '';

                    if (!Array.isArray(data) || data.length === 0) {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td colspan="4" class="px-3 py-3 text-center text-gray-500">
                                No waiting queues.
                            </td>
                        `;
                        tbody.appendChild(tr);
                        return;
                    }

                    data.forEach(queue => {
                        const tr = document.createElement('tr');
                        tr.className = 'border-b border-gray-100';
                        const serviceName = queue.service ? queue.service.service_name : '';
                        tr.innerHTML = `
                            <td class="px-3 py-2 font-medium">${queue.queue_number}</td>
                            <td class="px-3 py-2">${queue.student_name}</td>
                            <td class="px-3 py-2">${serviceName}</td>
                            <td class="px-3 py-2 capitalize">${queue.status}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                } catch (e) {
                    console.error('Error loading queues', e);
                }
            }

            loadWaitingQueues();
            setInterval(loadWaitingQueues, 3000);
        });
    </script>
@endsection

