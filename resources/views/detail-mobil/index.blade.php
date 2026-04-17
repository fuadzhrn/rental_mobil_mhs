<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeloraRide | Detail Mobil</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ asset('css/detail-mobil.css') }}">
</head>
<body class="detail-page">
    @include('home.navbar')

    <main>
        <section class="detail-hero-section">
            <div class="container detail-hero-grid">
                @include('detail-mobil.galeri-foto')

                <aside class="detail-right-column">
                    @include('detail-mobil.informasi-utama')
                    @include('detail-mobil.tombol-booking')
                </aside>
            </div>
        </section>

        @include('detail-mobil.spesifikasi')
        @include('detail-mobil.deskripsi')
        @include('detail-mobil.syarat-dan-ketentuan')
        @include('detail-mobil.informasi-rental')
        @include('detail-mobil.rating-dan-ulasan')
    </main>

    @include('home.footer')

    <script src="{{ asset('js/detail-mobil.js') }}"></script>
</body>
</html>
