<aside id="panelSidebar" class="pecit-sidebar">
    <div class="pecit-sidebar-head">
        <h2>Guard Menu</h2>
        <p>Walk-in tickets</p>
    </div>
    <nav class="pecit-nav">
        <a href="{{ route('guard.issue') }}"
           class="pecit-nav-link {{ request()->routeIs('guard.*') ? 'is-active' : '' }}">
            @include('partials.icon', ['name' => 'ticket'])
            <span>Issue Ticket</span>
        </a>

        <div class="pecit-nav-section">Public screens</div>
        <a href="{{ route('display') }}" class="pecit-nav-link" target="_blank" rel="noopener">
            @include('partials.icon', ['name' => 'display'])
            <span>Public Display</span>
        </a>
        <a href="{{ route('kiosk') }}" class="pecit-nav-link" target="_blank" rel="noopener">
            @include('partials.icon', ['name' => 'kiosk'])
            <span>Kiosk Page</span>
        </a>
    </nav>
</aside>
