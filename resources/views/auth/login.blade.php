@extends('layouts.panel')

@section('title', 'PECIT Queuing System')

@section('content')
    <div class="pecit-login">
        <div>
            <div class="pecit-login-card">
                <div class="pecit-login-brand">
                    <img src="{{ asset('logo/logo.png') }}" alt="PECIT Logo">
                    <h1>PECIT Queuing System</h1>
                    <p>Staff &amp; Administrator Sign In</p>
                </div>

                <div class="pecit-login-body">
                    @if ($errors->any())
                        <div class="pecit-alert pecit-alert-danger">
                            <ul class="list-disc list-inside m-0 pl-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ url('/login') }}">
                        @csrf

                        <div class="pecit-login-field">
                            <label class="pecit-label">Email</label>
                            <div class="field-shell">
                                @include('partials.icon', ['name' => 'mail', 'class' => 'pecit-field-icon'])
                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    placeholder="Email"
                                    autocomplete="username"
                                >
                            </div>
                        </div>

                        <div class="pecit-login-field">
                            <label class="pecit-label">Password</label>
                            <div class="field-shell">
                                @include('partials.icon', ['name' => 'lock', 'class' => 'pecit-field-icon'])
                                <input
                                    type="password"
                                    name="password"
                                    required
                                    placeholder="Password"
                                    autocomplete="current-password"
                                >
                            </div>
                        </div>

                        <button type="submit" class="pecit-btn pecit-btn-primary" style="width:100%;margin-top:0.35rem;">
                            Login
                        </button>
                    </form>

                    <div style="margin-top:1rem;text-align:center;">
                        <a href="#" id="forgotPasswordLink" class="pecit-link" style="font-size:0.85rem;">
                            Forgot password?
                        </a>
                        <div id="forgotPasswordMsg"
                             class="pecit-alert pecit-alert-warning"
                             style="display:none;margin-top:0.75rem;text-align:left;">
                            Please contact the administrator to reset your password.
                        </div>
                    </div>
                </div>
            </div>
            <p class="pecit-login-footer">© {{ date('Y') }} PECIT Queuing System</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var link = document.getElementById('forgotPasswordLink');
            var msg = document.getElementById('forgotPasswordMsg');
            if (!link || !msg) return;

            link.addEventListener('click', function (e) {
                e.preventDefault();
                msg.style.display = 'block';
            });
        });
    </script>
@endpush
