<aside id="panelSidebar" class="pecit-sidebar">
    <div class="pecit-sidebar-head">
        <h2>Admin Menu</h2>
        <p>Operations &amp; oversight</p>
    </div>
    <nav class="pecit-nav">
        <a href="{{ route('admin.dashboard') }}"
           class="pecit-nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
            @include('partials.icon', ['name' => 'dashboard'])
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="pecit-nav-link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
            @include('partials.icon', ['name' => 'users'])
            <span>Users</span>
        </a>
        <a href="{{ route('admin.students.index') }}"
           class="pecit-nav-link {{ request()->routeIs('admin.students.*') ? 'is-active' : '' }}">
            @include('partials.icon', ['name' => 'students'])
            <span>Students</span>
        </a>
        <a href="{{ route('admin.settings.edit') }}"
           class="pecit-nav-link {{ request()->routeIs('admin.settings.*') ? 'is-active' : '' }}">
            @include('partials.icon', ['name' => 'lock'])
            <span>Kiosk PIN</span>
        </a>
        <a href="{{ route('guard.issue') }}"
           class="pecit-nav-link {{ request()->routeIs('guard.*') ? 'is-active' : '' }}">
            @include('partials.icon', ['name' => 'ticket'])
            <span>Walk-in Tickets</span>
        </a>

        <div class="pecit-nav-section">Queue records</div>
        <a href="{{ route('admin.history') }}"
           class="pecit-nav-link {{ request()->routeIs('admin.history') || request()->routeIs('admin.history.edit') ? 'is-active' : '' }}">
            @include('partials.icon', ['name' => 'history'])
            <span>Served History</span>
        </a>
        <a href="{{ route('admin.history.tickets') }}"
           class="pecit-nav-link {{ request()->routeIs('admin.history.tickets') ? 'is-active' : '' }}">
            @include('partials.icon', ['name' => 'ticket'])
            <span>All Tickets</span>
        </a>
        <a href="{{ route('admin.history.reports') }}"
           class="pecit-nav-link {{ request()->routeIs('admin.history.reports') ? 'is-active' : '' }}">
            @include('partials.icon', ['name' => 'chart'])
            <span>Reports &amp; Analytics</span>
        </a>

        <div class="pecit-nav-section">Public screens</div>
        <a href="{{ route('kiosk') }}" class="pecit-nav-link" target="_blank" rel="noopener">
            @include('partials.icon', ['name' => 'kiosk'])
            <span>Kiosk Page</span>
        </a>
        <a href="{{ route('display') }}" class="pecit-nav-link" target="_blank" rel="noopener">
            @include('partials.icon', ['name' => 'display'])
            <span>Public Display</span>
        </a>
    </nav>
</aside>
