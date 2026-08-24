<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Queue System')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    @unless(request()->routeIs('login') || request()->routeIs('kiosk') || request()->routeIs('kiosk.*'))
        <nav class="bg-blue-700 text-white px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <button id="sidebarToggle" class="mr-2 focus:outline-none">
                    <span class="block w-6 h-0.5 bg-white mb-1"></span>
                    <span class="block w-6 h-0.5 bg-white mb-1"></span>
                    <span class="block w-6 h-0.5 bg-white"></span>
                </button>
                <img src="{{ asset('logo/logo.png') }}" alt="School Logo" class="h-8 w-8 rounded-full object-cover">
                <span class="font-semibold">Queue System</span>
            </div>
            <div class="space-x-4">
                @auth
                    <span>{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button class="bg-blue-900 px-3 py-1 rounded text-sm">Logout</button>
                    </form>
                @endauth
            </div>
        </nav>
    @endunless

    <main class="{{ request()->routeIs('kiosk') || request()->routeIs('kiosk.*') ? 'kiosk-main mx-auto px-3 py-2 relative' : 'container mx-auto px-4 py-6 relative' }}">
        @yield('content')
    </main>

    @unless(request()->routeIs('login') || request()->routeIs('kiosk') || request()->routeIs('kiosk.*'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const toggle = document.getElementById('sidebarToggle');
                const sidebar = document.getElementById('adminSidebar');
                if (!toggle || !sidebar) return;

                toggle.addEventListener('click', function () {
                    sidebar.classList.toggle('-translate-x-full');
                });
            });
        </script>
    @endunless
</body>
</html>
