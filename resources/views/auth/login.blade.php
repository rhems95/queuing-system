@extends('layouts.app')

@section('title', 'PECIT Queuing System')

@section('content')
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 px-4">
        <div class="w-full max-w-md">
            <div class="flex flex-col items-center mb-6">
                <img src="{{ asset('logo/logo.png') }}" alt="School Logo" class="h-24 w-24 object-contain mb-3">
                <h1 class="text-2xl font-semibold text-gray-800">PECIT Queuing System</h1>
            </div>

            <div class="bg-white rounded-xl shadow-md px-8 py-7">
                @if ($errors->any())
                    <div class="mb-4 text-red-600 text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ url('/login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <div class="flex items-center border border-gray-300 rounded-lg px-3 py-2 bg-gray-50">
                            <span class="text-gray-400 mr-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </span>
                            <input
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                class="w-full bg-transparent outline-none text-sm"
                                placeholder="Email"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="flex items-center border border-gray-300 rounded-lg px-3 py-2 bg-gray-50">
                            <span class="text-gray-400 mr-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 11V7a4 4 0 118 0v4M6 11h12v8a2 2 0 01-2 2H8a2 2 0 01-2-2v-8z" />
                                </svg>
                            </span>
                            <input
                                type="password"
                                name="password"
                                required
                                class="w-full bg-transparent outline-none text-sm"
                                placeholder="Password"
                            >
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg text-sm transition-colors">
                        Login
                    </button>
                </form>

                <div class="mt-4 text-center">
                    <a href="#"
                       id="forgotPasswordLink"
                       class="text-sm text-gray-500 hover:text-gray-700">
                        Forgot password?
                    </a>
                    <div id="forgotPasswordMsg"
                         class="hidden mt-2 text-xs text-amber-700 bg-amber-50 border border-amber-200 px-3 py-2 rounded">
                        Please contact the administrator to reset your password.
                    </div>
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-gray-400">
                © 2026 PECIT Queuing System
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var link = document.getElementById('forgotPasswordLink');
            var msg = document.getElementById('forgotPasswordMsg');
            if (!link || !msg) return;

            link.addEventListener('click', function (e) {
                e.preventDefault();
                msg.classList.remove('hidden');
            });
        });
    </script>
@endsection

