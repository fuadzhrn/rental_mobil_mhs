<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeloraRide | Detail Booking {{ $booking->booking_code }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/home-mobile.css') }}">
</head>
<body style="margin:0; font-family:'Poppins',sans-serif; background:#f4f6fa; color:#111827;">
    @include('home.navbar')

    @php
        $payment = $booking->payment;
        $imagePath = $booking->vehicle->main_image
            ? asset('storage/' . $booking->vehicle->main_image)
            : ($booking->vehicle->primaryImage?->image_path ? asset('storage/' . $booking->vehicle->primaryImage->image_path) : null);

        $timeline = [
            'Booking Dibuat' => true,
            'Menunggu Pembayaran' => in_array($booking->booking_status, ['waiting_payment', 'waiting_verification', 'confirmed', 'ongoing', 'completed'], true),
            'Menunggu Verifikasi' => in_array($booking->booking_status, ['waiting_verification', 'confirmed', 'ongoing', 'completed'], true),
            'Dikonfirmasi' => in_array($booking->booking_status, ['confirmed', 'ongoing', 'completed'], true),
            'Sedang Berjalan' => in_array($booking->booking_status, ['ongoing', 'completed'], true),
            'Selesai' => $booking->booking_status === 'completed',
            'Dibatalkan' => $booking->booking_status === 'cancelled',
        ];
    @endphp

    <main style="max-width:1180px; margin:32px auto; padding:0 16px; display:grid; gap:16px;">
        <section style="background:#fff; border-radius:18px; padding:20px; box-shadow:0 10px 24px rgba(15,23,42,.06);">
            <a href="{{ route('customer.bookings.index') }}" style="text-decoration:none; color:#334155; font-weight:600;">&larr; Kembali ke Riwayat Booking</a>
            <h1 style="margin:10px 0 0; font-family:'Montserrat',sans-serif;">Detail Booking {{ $booking->booking_code }}</h1>
            <p style="margin:8px 0 0; color:#6b7280;">Booking status: <strong>{{ $booking->booking_status_label }}</strong> | Payment status: <strong>{{ $booking->payment_status_label }}</strong></p>
        </section>

        @if (session('success'))
            <section style="background:#dcfce7; border-radius:14px; padding:14px; color:#166534;">{{ session('success') }}</section>
        @endif

        @if (session('error'))
            <section style="background:#fef2f2; border-radius:14px; padding:14px; color:#991b1b;">{{ session('error') }}</section>
        @endif

        <section style="display:grid; grid-template-columns:1.2fr .8fr; gap:16px;">
            <article style="background:#fff; border-radius:18px; padding:18px;">
                <h2 style="margin-top:0; font-family:'Montserrat',sans-serif;">Ringkasan Transaksi</h2>

                <div style="display:flex; gap:12px; align-items:center; margin-bottom:14px;">
                    @if ($imagePath)
                        <img src="{{ $imagePath }}" alt="{{ $booking->vehicle->name }}" style="width:86px; height:86px; object-fit:cover; border-radius:12px;">
                    @endif
                    <div>
                        <div style="font-weight:700;">{{ $booking->vehicle->name }}</div>
                        <div style="color:#6b7280;">{{ $booking->vehicle->rentalCompany?->company_name }}</div>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px;">
                    <div><strong>Pickup:</strong><br>{{ $booking->pickup_date->format('d M Y') }} {{ $booking->pickup_time ? $booking->pickup_time->format('H:i') : '' }}</div>
                    <div><strong>Return:</strong><br>{{ $booking->return_date->format('d M Y') }}</div>
                    <div><strong>Lokasi Pickup:</strong><br>{{ $booking->pickup_location }}</div>
                    <div><strong>Lokasi Return:</strong><br>{{ $booking->return_location ?? '-' }}</div>
                    <div><strong>Durasi:</strong><br>{{ $booking->duration_days }} Hari</div>
                    <div><strong>Dengan Driver:</strong><br>{{ $booking->with_driver ? 'Ya' : 'Tidak' }}</div>
                </div>

                <hr style="margin:16px 0; border:0; border-top:1px solid #e5e7eb;">
                <h3 style="margin-top:0;">Rincian Biaya</h3>
                <p>Subtotal: <strong>Rp {{ number_format($booking->subtotal, 0, ',', '.') }}</strong></p>
                <p>Diskon: <strong>Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</strong></p>
                <p>Biaya Tambahan: <strong>Rp {{ number_format($booking->additional_cost, 0, ',', '.') }}</strong></p>
                <p>Total: <strong>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</strong></p>
            </article>

            <article style="background:#fff; border-radius:18px; padding:18px;">
                <h2 style="margin-top:0; font-family:'Montserrat',sans-serif;">Data Pembayaran</h2>
                <p><strong>Metode:</strong> {{ $payment?->payment_method ?? '-' }}</p>
                <p><strong>Status Payment:</strong> {{ $booking->payment_status_label }}</p>
                <p><strong>Status Booking:</strong> {{ $booking->booking_status_label }}</p>

                @if ($payment?->proof_payment)
                    @php $proofUrl = asset('storage/' . $payment->proof_payment); @endphp
                    <p><strong>Bukti Pembayaran:</strong></p>
                    @if (str_ends_with(strtolower($payment->proof_payment), '.pdf'))
                        <a href="{{ $proofUrl }}" target="_blank">Lihat PDF</a>
                    @else
                        <img src="{{ $proofUrl }}" alt="Bukti Pembayaran" style="width:100%; max-width:320px; border-radius:12px; border:1px solid #e5e7eb;">
                    @endif
                @endif

                @if ($payment?->payment_status === \App\Models\Payment::STATUS_REJECTED)
                    <div style="margin-top:14px; background:#fef2f2; border-radius:12px; padding:12px; color:#991b1b;">
                        <strong>Pembayaran ditolak.</strong>
                        <p style="margin:6px 0 0;">{{ $payment->rejection_note ?? 'Silakan upload ulang bukti pembayaran.' }}</p>
                    </div>
                    <a href="{{ route('pembayaran.show', $booking) }}" style="display:inline-block; margin-top:12px; background:#0f172a; color:#fff; text-decoration:none; border-radius:10px; padding:10px 12px;">Upload Ulang Bukti Pembayaran</a>
                @endif

                @if ($booking->booking_status === \App\Models\Booking::BOOKING_COMPLETED)
                    @if ($booking->review)
                        <div style="margin-top:14px; background:#dcfce7; border-radius:12px; padding:12px; color:#166534;">
                            <strong>Sudah Direview:</strong> {{ $booking->review->rating }}/5
                            <p style="margin:6px 0 0;">{{ $booking->review->review ?: 'Tanpa komentar.' }}</p>
                        </div>
                    @else
                        <div style="margin-top:14px; background:#ecfeff; border-radius:12px; padding:12px; color:#155e75;">
                            Booking telah selesai. Anda sudah bisa memberi ulasan untuk kendaraan ini.
                        </div>
                        <a href="{{ route('customer.reviews.create', $booking) }}" style="display:inline-block; margin-top:12px; background:#0f172a; color:#fff; text-decoration:none; border-radius:10px; padding:10px 12px;">Beri Ulasan</a>
                    @endif
                @endif
            </article>
        </section>

        <section style="background:#fff; border-radius:18px; padding:18px;">
            <h2 style="margin-top:0; font-family:'Montserrat',sans-serif;">Timeline Status Transaksi</h2>
            <div style="display:grid; gap:10px;">
                @foreach ($timeline as $step => $active)
                    @if ($active)
                        <div style="padding:10px 12px; border-radius:10px; background:#ecfdf5; color:#166534;">{{ $step }}</div>
                    @else
                        <div style="padding:10px 12px; border-radius:10px; background:#f8fafc; color:#64748b;">{{ $step }}</div>
                    @endif
                @endforeach
            </div>
        </section>

        <section style="background:#fff; border-radius:18px; padding:18px;">
            <h2 style="margin-top:0; font-family:'Montserrat',sans-serif;">Data Penyewa & Catatan</h2>
            <p><strong>Nama:</strong> {{ $booking->customer_name }}</p>
            <p><strong>Email:</strong> {{ $booking->customer_email }}</p>
            <p><strong>Phone:</strong> {{ $booking->customer_phone }}</p>
            <p><strong>Alamat:</strong> {{ $booking->customer_address }}</p>
            <p><strong>Nomor KTP:</strong> {{ $booking->identity_number ?? '-' }}</p>
            <p><strong>Nomor SIM:</strong> {{ $booking->driver_license_number ?? '-' }}</p>
            <p><strong>Catatan:</strong> {{ $booking->note ?? '-' }}</p>
        </section>
    </main>

    @include('home.footer')
    <script src="{{ asset('assets/js/home-mobile.js') }}"></script>
</body>
</html>
