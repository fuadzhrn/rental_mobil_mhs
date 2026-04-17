<header class="site-header" id="top">
    <div class="container header-inner">
        <a href="{{ route('home') }}" class="brand" aria-label="VeloraRide Home">
            <span class="brand-mark"><i class="fa-solid fa-road"></i></span>
            <span class="brand-text">VeloraRide</span>
        </a>

        <nav class="main-nav" aria-label="Navigasi utama">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'is-active' : '' }}">Home</a>
            <a href="{{ route('katalog') }}" class="{{ request()->routeIs('katalog') || request()->routeIs('detail-mobil') || request()->routeIs('booking') || request()->routeIs('pembayaran') || request()->routeIs('pembayaran.invoice') || request()->routeIs('pembayaran.cetak') ? 'is-active' : '' }}">Katalog</a>
            <a href="{{ route('home') }}#promo">Promo</a>
            <a href="{{ route('home') }}#tentang">Tentang</a>
        </nav>

        <div class="header-actions">
            <a href="#" class="auth-link">Login / Daftar</a>
            <a href="{{ route('katalog') }}" class="btn btn-primary">Cari Kendaraan</a>
        </div>
    </div>
</header>
