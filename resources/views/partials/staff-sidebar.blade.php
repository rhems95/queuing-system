<aside id="panelSidebar" class="pecit-sidebar">
    <div class="pecit-sidebar-head">
        <h2>Staff Menu</h2>
        <p>Counter operations</p>
    </div>
    <nav class="pecit-nav">
        <a href="{{ route('window.dashboard') }}"
           class="pecit-nav-link {{ request()->routeIs('window.dashboard') ? 'is-active' : '' }}">
            <span class="pecit-nav-icon">WN</span>
            <span>Window Dashboard</span>
        </a>
        <a href="{{ route('window.history') }}"
           class="pecit-nav-link {{ request()->routeIs('window.history') ? 'is-active' : '' }}">
            <span class="pecit-nav-icon">HS</span>
            <span>My History</span>
        </a>

        <div class="pecit-nav-section">Public screens</div>
        <a href="{{ route('display') }}" class="pecit-nav-link" target="_blank" rel="noopener">
            <span class="pecit-nav-icon">DP</span>
            <span>Public Display</span>
        </a>
        <a href="{{ route('kiosk') }}" class="pecit-nav-link" target="_blank" rel="noopener">
            <span class="pecit-nav-icon">KS</span>
            <span>Kiosk Page</span>
        </a>
    </nav>
</aside>
