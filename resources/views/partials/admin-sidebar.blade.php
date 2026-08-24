<aside id="panelSidebar" class="pecit-sidebar">
    <div class="pecit-sidebar-head">
        <h2>Admin Menu</h2>
        <p>Operations &amp; oversight</p>
    </div>
    <nav class="pecit-nav">
        <a href="{{ route('admin.dashboard') }}"
           class="pecit-nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
            <span class="pecit-nav-icon">DB</span>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="pecit-nav-link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
            <span class="pecit-nav-icon">US</span>
            <span>Users</span>
        </a>

        <div class="pecit-nav-section">Queue records</div>
        <a href="{{ route('admin.history') }}"
           class="pecit-nav-link {{ request()->routeIs('admin.history') || request()->routeIs('admin.history.edit') ? 'is-active' : '' }}">
            <span class="pecit-nav-icon">SH</span>
            <span>Served History</span>
        </a>
        <a href="{{ route('admin.history.tickets') }}"
           class="pecit-nav-link {{ request()->routeIs('admin.history.tickets') ? 'is-active' : '' }}">
            <span class="pecit-nav-icon">TK</span>
            <span>All Tickets</span>
        </a>
        <a href="{{ route('admin.history.reports') }}"
           class="pecit-nav-link {{ request()->routeIs('admin.history.reports') ? 'is-active' : '' }}">
            <span class="pecit-nav-icon">RP</span>
            <span>Reports &amp; Analytics</span>
        </a>

        <div class="pecit-nav-section">Public screens</div>
        <a href="{{ route('kiosk') }}" class="pecit-nav-link" target="_blank" rel="noopener">
            <span class="pecit-nav-icon">KS</span>
            <span>Kiosk Page</span>
        </a>
        <a href="{{ route('display') }}" class="pecit-nav-link" target="_blank" rel="noopener">
            <span class="pecit-nav-icon">DP</span>
            <span>Public Display</span>
        </a>
    </nav>
</aside>
