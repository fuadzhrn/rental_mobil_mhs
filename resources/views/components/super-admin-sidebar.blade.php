<aside class="admin-sidebar" aria-label="Sidebar Super Admin">
    <div class="sidebar-brand">
        <p>VeloraRide</p>
        <span>Super Admin</span>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('super-admin.dashboard') }}" class="{{ request()->routeIs('super-admin.dashboard') ? 'is-active' : '' }}">Dashboard</a>
        <a href="{{ route('super-admin.rentals.index') }}" class="{{ request()->routeIs('super-admin.rentals.*') ? 'is-active' : '' }}">Verifikasi Rental</a>
        <a href="{{ route('super-admin.users.index') }}" class="{{ request()->routeIs('super-admin.users.*') ? 'is-active' : '' }}">Semua User</a>
        <a href="{{ route('super-admin.reports.index') }}" class="{{ request()->routeIs('super-admin.reports.*') ? 'is-active' : '' }}">Laporan</a>
        <a href="{{ route('super-admin.reports.commissions') }}" class="{{ request()->routeIs('super-admin.reports.commissions') || request()->routeIs('super-admin.commissions.*') ? 'is-active' : '' }}">Komisi</a>
        <a href="{{ route('super-admin.activity-logs.index') }}" class="{{ request()->routeIs('super-admin.activity-logs.*') ? 'is-active' : '' }}">Audit Log</a>
        <a href="{{ route('notifications.index') }}" class="{{ request()->routeIs('notifications.*') ? 'is-active' : '' }}">Notifikasi</a>
    </nav>

    <form action="{{ route('logout') }}" method="POST" class="sidebar-logout-form">
        @csrf
        <button type="submit" class="sidebar-logout">Logout</button>
    </form>
</aside>
