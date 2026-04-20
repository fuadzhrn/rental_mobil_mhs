<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeloraRide | Cetak Bukti Transaksi</title>
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

    <main>
        <section class="payment-layout-section">
            <div class="container" style="display:grid; gap:18px; grid-template-columns:minmax(0, 1fr); max-width:900px;">
                <section class="payment-card invoice-card">
                    <div class="section-heading compact">
                        <p class="eyebrow">Cetak</p>
                        <h2>Bukti Transaksi Siap Cetak</h2>
                    </div>

                    <div class="invoice-list">
                        <div><span>No. Invoice</span><strong>INV-VLR-2026-0417</strong></div>
                        <div><span>Tanggal Transaksi</span><strong>17 Apr 2026</strong></div>
                        <div><span>Nama Penyewa</span><strong>Rendy Saputra</strong></div>
                        <div><span>Kendaraan</span><strong>BMW 320i Sport</strong></div>
                        <div><span>Status Transaksi</span><strong class="status-text">Menunggu Verifikasi</strong></div>
                    </div>

                    <div class="invoice-total">
                        <span>Petunjuk</span>
                        <strong>Gunakan Ctrl + P</strong>
                    </div>

                    <div class="invoice-actions">
                        <button type="button" class="btn btn-primary full-width" onclick="window.print()">Cetak Sekarang</button>
                        <a href="{{ route('pembayaran') }}" class="btn btn-outline full-width">Kembali ke Pembayaran</a>
                    </div>
                </section>
            </div>
        </section>
    </main>

    @include('home.footer')

    <script src="{{ asset('assets/js/home-mobile.js') }}"></script>
</body>
</html>
