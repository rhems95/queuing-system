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
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#5b6478">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#5b6478">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 11V7a4 4 0 118 0v4M6 11h12v8a2 2 0 01-2 2H8a2 2 0 01-2-2v-8z" />
                                </svg>
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
