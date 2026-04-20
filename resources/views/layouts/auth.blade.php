<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Autentikasi | VeloraRide')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/auth-mobile.css') }}">
</head>
<body class="auth-page">
    <div class="auth-background" aria-hidden="true">
        <span class="bg-orb orb-one"></span>
        <span class="bg-orb orb-two"></span>
        <span class="bg-grid"></span>
    </div>

    <main class="auth-shell">
        <section class="auth-visual" aria-label="Branding VeloraRide">
            <div class="brand-wrap">
                <p class="brand-kicker">Premium Mobility</p>
                <h1>VeloraRide</h1>
                <p class="brand-text">Satu akun untuk jelajahi armada kendaraan, booking cepat, dan pengalaman rental modern.</p>
                <div class="brand-badges">
                    <span>Trusted Fleet</span>
                    <span>Secure Booking</span>
                    <span>Fast Support</span>
                </div>
            </div>
            <div class="visual-card" aria-hidden="true">
                <div class="road-line"></div>
                <div class="car-shape">
                    <span class="wheel left"></span>
                    <span class="wheel right"></span>
                </div>
            </div>
        </section>

        <section class="auth-panel" aria-label="Form autentikasi">
            @yield('content')
        </section>
    </main>

    <script src="{{ asset('assets/js/auth.js') }}" defer></script>
</body>
</html>
