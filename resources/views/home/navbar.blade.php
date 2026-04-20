<header class="site-header" id="top">
    <div class="container header-inner">
        <a href="{{ route('home') }}" class="brand" aria-label="VeloraRide Home">
            <span class="brand-mark"><i class="fa-solid fa-road"></i></span>
            <span class="brand-text">VeloraRide</span>
        </a>

        <nav class="main-nav desktop-nav" aria-label="Navigasi utama">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'is-active' : '' }}">Home</a>
            <a href="{{ route('katalog') }}" class="{{ request()->routeIs('katalog') || request()->routeIs('detail-mobil') || request()->routeIs('booking') || request()->routeIs('pembayaran') || request()->routeIs('pembayaran.invoice') || request()->routeIs('pembayaran.cetak') ? 'is-active' : '' }}">Katalog</a>
            <a href="{{ route('home') }}#promo">Promo</a>
            <a href="{{ route('home') }}#tentang">Tentang</a>
        </nav>

        <div class="header-actions desktop-actions">
            <a href="{{ route('login') }}" class="auth-link">Login / Daftar</a>
            <a href="{{ route('katalog') }}" class="btn btn-primary">Cari Kendaraan</a>
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
            <a href="{{ route('katalog') }}" class="{{ request()->routeIs('katalog') || request()->routeIs('detail-mobil') || request()->routeIs('booking') || request()->routeIs('pembayaran') || request()->routeIs('pembayaran.invoice') || request()->routeIs('pembayaran.cetak') ? 'is-active' : '' }}">Katalog</a>
            <a href="{{ route('home') }}#promo">Promo</a>
            <a href="{{ route('home') }}#tentang">Tentang</a>
            <a href="{{ route('login') }}" class="mobile-auth-link">Login / Daftar</a>
            <a href="{{ route('katalog') }}" class="btn btn-primary mobile-cta">Cari Kendaraan</a>
        </nav>
    </aside>
</header>
