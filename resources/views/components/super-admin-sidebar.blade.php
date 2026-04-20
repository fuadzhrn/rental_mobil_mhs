<aside class="admin-sidebar" aria-label="Sidebar Super Admin">
    <div class="sidebar-brand">
        <p>VeloraRide</p>
        <span>Super Admin</span>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('super-admin.dashboard') }}" class="{{ request()->routeIs('super-admin.dashboard') ? 'is-active' : '' }}">Dashboard</a>
        <a href="#">Verifikasi Rental</a>
        <a href="#">Semua User</a>
        <a href="#">Laporan</a>
        <a href="#">Komisi</a>
    </nav>

    <form action="{{ route('logout') }}" method="POST" class="sidebar-logout-form">
        @csrf
        <button type="submit" class="sidebar-logout">Logout</button>
    </form>
</aside>
