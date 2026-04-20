<aside class="admin-sidebar" aria-label="Sidebar Admin Rental">
    <div class="sidebar-brand">
        <p>VeloraRide</p>
        <span>Admin Rental</span>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('admin-rental.dashboard') }}" class="{{ request()->routeIs('admin-rental.dashboard') ? 'is-active' : '' }}">Dashboard</a>
        <a href="{{ route('admin-rental.vehicles.index') }}" class="{{ request()->routeIs('admin-rental.vehicles.*') ? 'is-active' : '' }}">Data Kendaraan</a>
        <a href="#">Data Booking</a>
        <a href="#">Data Customer</a>
        <a href="#">Promo</a>
    </nav>

    <form action="{{ route('logout') }}" method="POST" class="sidebar-logout-form">
        @csrf
        <button type="submit" class="sidebar-logout">Logout</button>
    </form>
</aside>
