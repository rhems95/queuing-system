@extends('layouts.app')

@section('title', $mode === 'create' ? 'Create User' : 'Edit User')

@section('content')
    <div class="flex gap-6">
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
                <a href="{{ route('kiosk') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100">🎟️ Kiosk</a>
                <a href="{{ route('display') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100">🖥️ Display</a>
                <a href="{{ route('admin.history') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100">📜 Served History</a>
                <a href="{{ route('admin.history.tickets') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100">🎫 All Tickets</a>
                <a href="{{ route('admin.history.reports') }}" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100">📈 Reports</a>
            </nav>
        </aside>

        <section class="flex-1 ml-0 md:ml-64">
            <h1 class="text-2xl font-bold mb-4">
                {{ $mode === 'create' ? 'Create User' : 'Edit User' }}
            </h1>

            @if ($errors->any())
                <div class="mb-4 text-red-600 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded shadow p-6 max-w-xl">
                <form method="POST"
                      action="{{ $mode === 'create' ? route('admin.users.store') : route('admin.users.update', $user) }}">
                    @csrf
                    @if($mode === 'edit')
                        @method('PUT')
                    @endif

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">
                            Password
                            @if($mode === 'edit')
                                <span class="text-xs text-gray-500">(leave blank to keep current)</span>
                            @endif
                        </label>
                        <input type="password" name="password"
                               class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Role</label>
                        <select name="role" class="w-full border border-gray-300 rounded px-3 py-2" required>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Window (for staff)</label>
                        <select name="window_id" class="w-full border border-gray-300 rounded px-3 py-2">
                            <option value="">None</option>
                            @foreach($windows as $window)
                                <option value="{{ $window->id }}"
                                    {{ (string) old('window_id', $user->window_id) === (string) $window->id ? 'selected' : '' }}>
                                    {{ $window->window_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm border rounded">
                            Cancel
                        </a>
                        <button class="px-4 py-2 text-sm bg-blue-600 text-white rounded">
                            {{ $mode === 'create' ? 'Create' : 'Update' }}
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection

