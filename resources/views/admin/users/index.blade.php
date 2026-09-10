@extends('layouts.panel')

@section('title', 'Manage Users')

@section('content')
    <div class="pecit-page-header">
        <div>
            <h1 class="pecit-page-title">Manage Users</h1>
            <p class="pecit-page-sub">Staff: one per window. Walk-in tickets use the kiosk PIN (no Guard login).</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="pecit-btn pecit-btn-primary">+ New User</a>
    </div>

    @if (session('status'))
        <div class="pecit-alert pecit-alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="pecit-alert pecit-alert-danger">
            <ul class="list-disc list-inside m-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="pecit-card">
        <div class="pecit-table-wrap">
            <table class="pecit-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Window</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td style="font-weight:600;">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if ($user->role === 'admin')
                                    <span class="pecit-badge pecit-badge-admin">Admin</span>
                                @elseif ($user->role === 'guard')
                                    <span class="pecit-badge pecit-badge-guard">Kiosk issuer</span>
                                @else
                                    <span class="pecit-badge pecit-badge-staff">Staff</span>
                                @endif
                            </td>
                            <td>{{ $user->window->window_name ?? '—' }}</td>
                            <td style="text-align:right;white-space:nowrap;">
                                <a href="{{ route('admin.users.edit', $user) }}" class="pecit-link">Edit</a>
                                @if ($user->role !== 'guard')
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      style="display:inline;margin-left:0.65rem;"
                                      onsubmit="return confirm('Delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="pecit-link-danger" style="background:none;border:none;cursor:pointer;padding:0;font:inherit;">Delete</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-top:1rem;">
        {{ $users->links() }}
    </div>
@endsection
