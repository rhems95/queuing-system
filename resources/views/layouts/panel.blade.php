<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'PECIT Queuing System')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="pecit-shell">
    @php
        $isLogin = request()->routeIs('login');
        $showChrome = ! $isLogin;
        $showSidebar = $showChrome && (request()->routeIs('admin.*') || request()->routeIs('window.*'));
    @endphp

    @if ($isLogin)
        @yield('content')
    @else
        <header class="pecit-header">
            <div class="pecit-header-brand">
                @if ($showSidebar)
                    <button type="button" id="sidebarToggle" class="pecit-menu-toggle" aria-label="Toggle menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                @endif
                <img src="{{ asset('logo/logo.png') }}" alt="PECIT Logo">
                <div class="pecit-header-titles">
                    <div class="pecit-org">Philippine Electronics and Communication Institute of Technology Inc.</div>
                    <div class="pecit-app">Queuing System</div>
                </div>
            </div>
            <div class="pecit-header-actions">
                @auth
                    <div class="pecit-user-chip">
                        <span>{{ auth()->user()->name }}</span>
                        <span class="pecit-role-pill">{{ auth()->user()->role }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="pecit-btn-ghost">Logout</button>
                    </form>
                @endauth
            </div>
        </header>

        <div class="pecit-body">
            @if ($showSidebar)
                <div id="sidebarBackdrop" class="pecit-backdrop" aria-hidden="true"></div>
                @if (request()->routeIs('admin.*'))
                    @include('partials.admin-sidebar')
                @elseif (request()->routeIs('window.*'))
                    @include('partials.staff-sidebar')
                @endif
            @endif

            <main class="pecit-main {{ $showSidebar ? 'has-sidebar' : '' }}">
                @yield('content')
            </main>
        </div>

        @if ($showSidebar)
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const toggle = document.getElementById('sidebarToggle');
                    const sidebar = document.getElementById('panelSidebar');
                    const backdrop = document.getElementById('sidebarBackdrop');
                    if (!toggle || !sidebar) return;

                    function closeSidebar() {
                        sidebar.classList.remove('is-open');
                        if (backdrop) backdrop.classList.remove('is-open');
                    }

                    function openSidebar() {
                        sidebar.classList.add('is-open');
                        if (backdrop) backdrop.classList.add('is-open');
                    }

                    toggle.addEventListener('click', function () {
                        if (sidebar.classList.contains('is-open')) {
                            closeSidebar();
                        } else {
                            openSidebar();
                        }
                    });

                    if (backdrop) {
                        backdrop.addEventListener('click', closeSidebar);
                    }
                });
            </script>
        @endif
    @endif

    @stack('scripts')
    @include('partials.secret-about-hotkey')
</body>
</html>
