<header class="site-header" id="top">
    <div class="container header-inner">
        <a href="{{ route('home') }}" class="brand" aria-label="VeloraRide Home">
            <span class="brand-mark"><i class="fa-solid fa-road"></i></span>
            <span class="brand-text">VeloraRide</span>
        </a>

        <nav class="main-nav desktop-nav" aria-label="Navigasi utama">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'is-active' : '' }}">Home</a>
            <a href="{{ route('katalog.index') }}" class="{{ request()->routeIs('katalog.*') || request()->routeIs('booking') || request()->routeIs('pembayaran') || request()->routeIs('pembayaran.invoice') || request()->routeIs('pembayaran.cetak') ? 'is-active' : '' }}">Katalog</a>
            <a href="{{ route('home') }}#promo">Promo</a>
            <a href="{{ route('home') }}#tentang">Tentang</a>
        </nav>

        <div class="header-actions desktop-actions">
            @auth
                @if (auth()->user()->role === 'customer')
                    <a href="{{ route('customer.bookings.index') }}" class="auth-link">My Booking</a>
                @elseif (auth()->user()->role === 'super_admin')
                    <a href="{{ route('super-admin.dashboard') }}" class="auth-link">Dashboard Admin</a>
                @elseif (auth()->user()->role === 'admin_rental')
                    <a href="{{ route('admin-rental.dashboard') }}" class="auth-link">Dashboard Rental</a>
                @endif

                <form action="{{ route('logout') }}" method="POST" class="auth-inline-form">
                    @csrf
                    <button type="submit" class="auth-link auth-link-btn">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="auth-link">Login / Daftar</a>
            @endauth

            <a href="{{ route('katalog.index') }}" class="btn btn-primary">Cari Kendaraan</a>
        </div>

        <button type="button" class="nav-toggle" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="mobileNavPanel" hidden>
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    <div class="mobile-nav-overlay" data-mobile-nav-overlay hidden></div>

    <aside class="mobile-nav-panel" id="mobileNavPanel" aria-label="Menu navigasi mobile" aria-hidden="true" hidden>
        <div class="mobile-nav-panel-head">
            <a href="{{ route('home') }}" class="brand mobile-brand" aria-label="VeloraRide Home">
                <span class="brand-mark"><i class="fa-solid fa-road"></i></span>
                <span class="brand-text">VeloraRide</span>
            </a>

            <button type="button" class="mobile-nav-close" aria-label="Tutup menu" data-mobile-nav-close>
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <nav class="mobile-nav-links" aria-label="Navigasi mobile">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'is-active' : '' }}">Home</a>
            <a href="{{ route('katalog.index') }}" class="{{ request()->routeIs('katalog.*') || request()->routeIs('booking') || request()->routeIs('pembayaran') || request()->routeIs('pembayaran.invoice') || request()->routeIs('pembayaran.cetak') ? 'is-active' : '' }}">Katalog</a>
            <a href="{{ route('home') }}#promo">Promo</a>
            <a href="{{ route('home') }}#tentang">Tentang</a>

            @auth
                @if (auth()->user()->role === 'customer')
                    <a href="{{ route('customer.bookings.index') }}" class="mobile-auth-link">My Booking</a>
                @elseif (auth()->user()->role === 'super_admin')
                    <a href="{{ route('super-admin.dashboard') }}" class="mobile-auth-link">Dashboard Admin</a>
                @elseif (auth()->user()->role === 'admin_rental')
                    <a href="{{ route('admin-rental.dashboard') }}" class="mobile-auth-link">Dashboard Rental</a>
                @endif

                <form action="{{ route('logout') }}" method="POST" class="mobile-auth-form">
                    @csrf
                    <button type="submit" class="mobile-auth-link mobile-auth-btn">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="mobile-auth-link">Login / Daftar</a>
            @endauth

            <a href="{{ route('katalog.index') }}" class="btn btn-primary mobile-cta">Cari Kendaraan</a>
        </nav>
    </aside>
</header>
