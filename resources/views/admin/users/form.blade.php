@extends('layouts.panel')

@section('title', $mode === 'create' ? 'Create User' : 'Edit User')

@section('content')
    <div class="pecit-page-header">
        <div>
            <h1 class="pecit-page-title">{{ $mode === 'create' ? 'Create User' : 'Edit User' }}</h1>
            <p class="pecit-page-sub">Admin or staff window accounts. Walk-in tickets use the kiosk PIN.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="pecit-alert pecit-alert-danger">
            <ul class="list-disc list-inside m-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="pecit-card" style="max-width:36rem;">
        <div class="pecit-card-body">
            <form method="POST"
                  action="{{ $mode === 'create' ? route('admin.users.store') : route('admin.users.update', $user) }}">
                @csrf
                @if($mode === 'edit')
                    @method('PUT')
                @endif

                <div style="margin-bottom:1rem;">
                    <label class="pecit-label">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="pecit-input">
                </div>

                <div style="margin-bottom:1rem;">
                    <label class="pecit-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="pecit-input">
                </div>

                @if ($user->role !== 'guard')
                <div style="margin-bottom:1rem;">
                    <label class="pecit-label">
                        Password
                        @if($mode === 'edit')
                            <span style="font-weight:500;color:var(--pecit-muted);">(leave blank to keep current)</span>
                        @endif
                    </label>
                    <input type="password" name="password" class="pecit-input">
                </div>
                @endif

                <div style="margin-bottom:1rem;">
                    <label class="pecit-label">Role</label>
                    @if ($user->role === 'guard')
                        <input type="text" value="Kiosk issuer (no login)" class="pecit-input" disabled>
                        <p class="pecit-page-sub" style="margin-top:0.4rem;">
                            This account cannot log in. Change the walk-in PIN under
                            <a href="{{ route('admin.settings.edit') }}">Kiosk PIN</a>.
                        </p>
                    @else
                    <select name="role" id="roleSelect" class="pecit-select" required>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                    @endif
                </div>

                <div id="windowField" style="margin-bottom:1.25rem;{{ $user->role === 'guard' ? 'display:none;' : '' }}">
                    <label class="pecit-label">Window (for staff)</label>
                    <select name="window_id" class="pecit-select">
                        <option value="">None</option>
                        @foreach($windows as $window)
                            <option value="{{ $window->id }}"
                                {{ (string) old('window_id', $user->window_id) === (string) $window->id ? 'selected' : '' }}>
                                {{ $window->window_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pecit-actions" style="margin-bottom:0;justify-content:flex-end;">
                    <a href="{{ route('admin.users.index') }}" class="pecit-btn pecit-btn-outline">Cancel</a>
                    <button type="submit" class="pecit-btn pecit-btn-primary">
                        {{ $mode === 'create' ? 'Create' : 'Update' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var role = document.getElementById('roleSelect');
            var wrap = document.getElementById('windowField');
            if (!role || !wrap) return;
            function sync() {
                wrap.style.opacity = role.value === 'staff' ? '1' : '0.55';
            }
            role.addEventListener('change', sync);
            sync();
        });
    </script>
@endpush
