<aside class="admin-sidebar" aria-label="Sidebar Admin Rental">
    <div class="sidebar-brand">
        <p>VeloraRide</p>
        <span>Admin Rental</span>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('admin-rental.dashboard') }}" class="{{ request()->routeIs('admin-rental.dashboard') ? 'is-active' : '' }}">Dashboard</a>
        <a href="{{ route('admin-rental.vehicles.index') }}" class="{{ request()->routeIs('admin-rental.vehicles.*') ? 'is-active' : '' }}">Data Kendaraan</a>
        <a href="{{ route('admin-rental.payments.index') }}" class="{{ request()->routeIs('admin-rental.payments.*') ? 'is-active' : '' }}">Data Pembayaran</a>
        <a href="{{ route('admin-rental.bookings.index') }}" class="{{ request()->routeIs('admin-rental.bookings.*') ? 'is-active' : '' }}">Data Booking</a>
        <a href="{{ route('admin-rental.reviews.index') }}" class="{{ request()->routeIs('admin-rental.reviews.*') ? 'is-active' : '' }}">Data Ulasan</a>
        <a href="{{ route('admin-rental.customers.index') }}" class="{{ request()->routeIs('admin-rental.customers.*') ? 'is-active' : '' }}">Data Customer</a>
        <a href="{{ route('admin-rental.promos.index') }}" class="{{ request()->routeIs('admin-rental.promos.*') ? 'is-active' : '' }}">Promo</a>
    </nav>

    <form action="{{ route('logout') }}" method="POST" class="sidebar-logout-form">
        @csrf
        <button type="submit" class="sidebar-logout">Logout</button>
    </form>
</aside>
