@extends('layouts.app')

@section('title', 'Manage Users')

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
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">Manage Users</h1>
                <a href="{{ route('admin.users.create') }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded text-sm">
                    + New User
                </a>
            </div>

            @if (session('status'))
                <div class="mb-4 text-sm text-green-700 bg-green-100 border border-green-300 px-3 py-2 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2">ID</th>
                            <th class="px-3 py-2">Name</th>
                            <th class="px-3 py-2">Email</th>
                            <th class="px-3 py-2">Role</th>
                            <th class="px-3 py-2">Window</th>
                            <th class="px-3 py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr class="border-b border-gray-200">
                                <td class="px-3 py-2">{{ $user->id }}</td>
                                <td class="px-3 py-2">{{ $user->name }}</td>
                                <td class="px-3 py-2">{{ $user->email }}</td>
                                <td class="px-3 py-2 capitalize">{{ $user->role }}</td>
                                <td class="px-3 py-2">{{ $user->window->window_name ?? '-' }}</td>
                                <td class="px-3 py-2 text-right space-x-2">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="text-blue-600 text-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                          class="inline"
                                          onsubmit="return confirm('Delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 text-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-4 text-center text-gray-500">
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </section>
    </div>
@endsection

