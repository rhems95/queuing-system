<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'PECIT Queuing System')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        (function () {
            try {
                var stored = localStorage.getItem('pecit-theme');
                var theme = stored === 'dark' || stored === 'light' ? stored : 'light';
                document.documentElement.setAttribute('data-theme', theme);
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="pecit-shell">
    @php
        $isLogin = request()->routeIs('login');
        $showChrome = ! $isLogin;
        $showSidebar = $showChrome && (request()->routeIs('admin.*') || request()->routeIs('window.*') || request()->routeIs('guard.*'));
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
                <button type="button" id="pecitThemeToggle" class="pecit-theme-toggle" aria-label="Switch to dark mode" title="Dark mode">
                    <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M21 14.5A8.5 8.5 0 1 1 9.5 3 7 7 0 0 0 21 14.5z"/>
                    </svg>
                    <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="4"/>
                        <path d="M12 2v2"/><path d="M12 20v2"/><path d="M4.93 4.93l1.41 1.41"/><path d="M17.66 17.66l1.41 1.41"/>
                        <path d="M2 12h2"/><path d="M20 12h2"/><path d="M4.93 19.07l1.41-1.41"/><path d="M17.66 6.34l1.41-1.41"/>
                    </svg>
                </button>
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
                @if (request()->routeIs('admin.*') || (request()->routeIs('guard.*') && auth()->user()?->role === 'admin'))
                    @include('partials.admin-sidebar')
                @elseif (request()->routeIs('guard.*'))
                    @include('partials.guard-sidebar')
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var root = document.documentElement;
            var btn = document.getElementById('pecitThemeToggle');

            function currentTheme() {
                return root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
            }

            function applyTheme(theme) {
                var next = theme === 'dark' ? 'dark' : 'light';
                root.setAttribute('data-theme', next);
                try { localStorage.setItem('pecit-theme', next); } catch (e) {}
                if (btn) {
                    var dark = next === 'dark';
                    btn.setAttribute('aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode');
                    btn.setAttribute('title', dark ? 'Light mode' : 'Dark mode');
                }
            }

            applyTheme(currentTheme());
            if (btn) {
                btn.addEventListener('click', function () {
                    applyTheme(currentTheme() === 'dark' ? 'light' : 'dark');
                });
            }
        });
    </script>

    @stack('scripts')
    @include('partials.secret-about-hotkey')
</body>
</html>
