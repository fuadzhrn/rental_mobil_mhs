<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeloraRide | Pembayaran</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ asset('css/pembayaran.css') }}">
</head>
<body class="pembayaran-page">
    @include('home.navbar')

    <main>
        <section class="payment-layout-section">
            <div class="container payment-layout">
                <div class="payment-main-column">
                    @include('pembayaran.metode-pembayaran')
                    @include('pembayaran.detail-pembayaran')
                    @include('pembayaran.upload-bukti-pembayaran')
                    @include('pembayaran.status-pembayaran')
                </div>

                <aside class="payment-side-column">
                    @include('pembayaran.ringkasan-pesanan')
                    @include('pembayaran.invoice-bukti-transaksi')
                </aside>
            </div>
        </section>
    </main>

    @include('home.footer')

    <script src="{{ asset('js/pembayaran.js') }}"></script>
</body>
</html>
