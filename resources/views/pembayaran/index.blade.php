<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeloraRide | Pembayaran Booking</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ asset('assets/css/pembayaran.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/home-mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pembayaran-mobile.css') }}">
</head>
<body class="pembayaran-page">
    @include('home.navbar')

    @php
        $activePaymentMethod = $selectedMethod ?? $payment->payment_method ?? array_key_first($paymentMethods);
        $activeMethodData = $paymentMethods[$activePaymentMethod] ?? reset($paymentMethods);
    @endphp

    <main>
        <section class="payment-layout-section">
            <div class="container payment-layout">
                <div class="payment-main-column">
                    @if (session('success'))
                        <div class="payment-card" style="border-left:4px solid #16a34a;">
                            <p class="eyebrow" style="color:#16a34a;">Berhasil</p>
                            <p style="margin:0; color:#166534;">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="payment-card" style="border-left:4px solid #dc2626;">
                            <p class="eyebrow" style="color:#dc2626;">Terjadi Kendala</p>
                            <p style="margin:0; color:#991b1b;">{{ session('error') }}</p>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="payment-card" style="border-left:4px solid #dc2626;">
                            <p class="eyebrow" style="color:#dc2626;">Periksa kembali isian Anda</p>
                            <ul style="margin:0; padding-left:18px; color:#991b1b;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('pembayaran.upload', $booking) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mobile-only-block">
                            @include('pembayaran.ringkasan-pesanan')
                        </div>

                        @include('pembayaran.metode-pembayaran')
                        @include('pembayaran.detail-pembayaran')
                        @include('pembayaran.upload-bukti-pembayaran')
                        @include('pembayaran.status-pembayaran')
                    </form>

                    <div class="mobile-only-block">
                        @include('pembayaran.invoice-bukti-transaksi')
                    </div>
                </div>

                <aside class="payment-side-column desktop-only-block">
                    @include('pembayaran.ringkasan-pesanan')
                    @include('pembayaran.invoice-bukti-transaksi')
                </aside>
            </div>
        </section>
    </main>

    @include('pembayaran.footer')

    <script src="{{ asset('assets/js/home-mobile.js') }}"></script>
    <script src="{{ asset('assets/js/pembayaran.js') }}"></script>
</body>
</html>
