<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeloraRide | Katalog Kendaraan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ asset('assets/css/katalog.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/home-mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/katalog-mobile.css') }}">
</head>
<body class="katalog-page">
    @include('home.navbar')

    <main>
        @include('katalog.search-bar')

        <section class="catalog-main" id="katalog">
            <div class="container catalog-layout">
                @include('katalog.filter-kendaraan')

                <section class="catalog-results" aria-label="Hasil pencarian kendaraan">
                    @include('katalog.sorting')
                    @include('katalog.status-ketersediaan')
                    @include('katalog.daftar-kendaraan')
                    @include('katalog.pagination')
                </section>
            </div>
        </section>
    </main>

    <div class="catalog-filter-overlay" data-filter-overlay hidden></div>

    @include('home.footer')

    <script src="{{ asset('assets/js/home-mobile.js') }}"></script>
    <script src="{{ asset('assets/js/katalog.js') }}"></script>
</body>
</html>
