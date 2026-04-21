<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeloraRide | {{ $documentTitle ?? 'Dokumen Pembayaran' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ asset('assets/css/pembayaran.css') }}">
</head>
<body class="pembayaran-page">
    <main style="padding:40px 0;">
        <div class="container">
            <div style="max-width:860px; margin:0 auto;">
                <div class="payment-card" style="margin-bottom:20px;">
                    <p class="eyebrow">{{ $documentTitle ?? 'Dokumen' }}</p>
                    <h1 style="margin:0;">{{ $documentTitle ?? 'Dokumen Pembayaran' }}</h1>
                    <p style="color:#6b7280; margin-bottom:0;">{{ $booking->booking_code }} - {{ $booking->vehicle->name }}</p>
                </div>

                @include('pembayaran.invoice-bukti-transaksi')
            </div>
        </div>
    </main>
</body>
</html>
