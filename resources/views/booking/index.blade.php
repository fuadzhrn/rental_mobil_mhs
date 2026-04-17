<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeloraRide | Booking Kendaraan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
</head>
<body class="booking-page">
    @include('home.navbar')

    <main>
        <section class="booking-layout-section">
            <div class="container booking-layout">
                <div class="booking-main-column">
                    @include('booking.data-kendaraan')
                    @include('booking.form-data-penyewa')
                    @include('booking.detail-pemesanan')
                    @include('booking.promo-voucher')
                </div>

                <aside class="booking-side-column">
                    @include('booking.ringkasan-biaya')
                    @include('booking.tombol-lanjut-pembayaran')
                </aside>
            </div>
        </section>
    </main>

    @include('home.footer')

    <script src="{{ asset('js/booking.js') }}"></script>
</body>
</html>
