<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeloraRide | Riwayat Booking Saya</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/home-mobile.css') }}">
</head>
<body style="margin:0; font-family:'Poppins',sans-serif; background:#f4f6fa; color:#111827;">
    @include('home.navbar')

    <main style="max-width:1180px; margin:32px auto; padding:0 16px;">
        <section style="background:#fff; border-radius:18px; padding:20px; box-shadow:0 10px 24px rgba(15,23,42,.06);">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:16px;">
                <div>
                    <p style="margin:0; font-weight:700; color:#ef4444; font-size:13px; text-transform:uppercase; letter-spacing:.08em;">Customer Area</p>
                    <h1 style="margin:4px 0 0; font-family:'Montserrat',sans-serif;">Riwayat Booking Saya</h1>
                </div>
            </div>

            <form method="GET" action="{{ route('customer.bookings.index') }}" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:12px; margin-bottom:16px;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari booking code / kendaraan" style="padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">

                <select name="booking_status" style="padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
                    <option value="">Semua Booking Status</option>
                    @foreach ($bookingStatusOptions as $value => $label)
                        <option value="{{ $value }}" @selected(request('booking_status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>

                <select name="payment_status" style="padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
                    <option value="">Semua Payment Status</option>
                    @foreach ($paymentStatusOptions as $value => $label)
                        <option value="{{ $value }}" @selected(request('payment_status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>

                <button type="submit" style="padding:10px 12px; border:0; border-radius:10px; background:#0f172a; color:#fff; font-weight:600;">Terapkan Filter</button>
            </form>

            <div style="overflow:auto;">
                <table style="width:100%; border-collapse:collapse; min-width:980px;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="text-align:left; padding:12px;">Booking</th>
                            <th style="text-align:left; padding:12px;">Kendaraan</th>
                            <th style="text-align:left; padding:12px;">Jadwal</th>
                            <th style="text-align:left; padding:12px;">Durasi</th>
                            <th style="text-align:left; padding:12px;">Total</th>
                            <th style="text-align:left; padding:12px;">Booking Status</th>
                            <th style="text-align:left; padding:12px;">Payment Status</th>
                            <th style="text-align:left; padding:12px;">Ulasan</th>
                            <th style="text-align:left; padding:12px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            @php
                                $imagePath = $booking->vehicle->main_image
                                    ? asset('storage/' . $booking->vehicle->main_image)
                                    : ($booking->vehicle->primaryImage?->image_path ? asset('storage/' . $booking->vehicle->primaryImage->image_path) : null);
                            @endphp
                            <tr style="border-top:1px solid #e5e7eb;">
                                <td style="padding:12px; font-weight:600;">{{ $booking->booking_code }}</td>
                                <td style="padding:12px;">
                                    <div style="display:flex; gap:10px; align-items:center;">
                                        @if ($imagePath)
                                            <img src="{{ $imagePath }}" alt="{{ $booking->vehicle->name }}" style="width:58px; height:58px; object-fit:cover; border-radius:10px;">
                                        @endif
                                        <div>
                                            <div style="font-weight:600;">{{ $booking->vehicle->name }}</div>
                                            <small style="color:#6b7280;">{{ $booking->vehicle->rentalCompany?->company_name }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:12px;">{{ $booking->pickup_date->format('d M Y') }} - {{ $booking->return_date->format('d M Y') }}</td>
                                <td style="padding:12px;">{{ $booking->duration_days }} Hari</td>
                                <td style="padding:12px; font-weight:700;">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td>
                                <td style="padding:12px;"><span style="background:#e0e7ff; color:#3730a3; border-radius:999px; padding:4px 10px; font-size:12px; font-weight:700;">{{ $booking->booking_status_label }}</span></td>
                                <td style="padding:12px;"><span style="background:#ecfeff; color:#155e75; border-radius:999px; padding:4px 10px; font-size:12px; font-weight:700;">{{ $booking->payment_status_label }}</span></td>
                                <td style="padding:12px;">
                                    @if ($booking->booking_status === \App\Models\Booking::BOOKING_COMPLETED)
                                        @if ($booking->review)
                                            <span style="background:#dcfce7; color:#166534; border-radius:999px; padding:4px 10px; font-size:12px; font-weight:700;">Sudah Direview</span>
                                        @else
                                            <span style="background:#fef3c7; color:#92400e; border-radius:999px; padding:4px 10px; font-size:12px; font-weight:700;">Belum Direview</span>
                                        @endif
                                    @else
                                        <span style="color:#64748b; font-size:13px;">Belum tersedia</span>
                                    @endif
                                </td>
                                <td style="padding:12px; display:flex; gap:8px; flex-wrap:wrap;">
                                    <a href="{{ route('customer.bookings.show', $booking) }}" style="display:inline-block; padding:8px 10px; border:1px solid #0f172a; color:#0f172a; border-radius:8px; text-decoration:none; font-weight:600;">Lihat Detail</a>
                                    @if ($booking->booking_status === \App\Models\Booking::BOOKING_COMPLETED && !$booking->review)
                                        <a href="{{ route('customer.reviews.create', $booking) }}" style="display:inline-block; padding:8px 10px; border:0; background:#0f172a; color:#fff; border-radius:8px; text-decoration:none; font-weight:600;">Beri Ulasan</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align:center; padding:28px; color:#6b7280;">Belum ada booking yang cocok dengan filter saat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top:16px;">{{ $bookings->links() }}</div>
        </section>
    </main>

    @include('home.footer')
    <script src="{{ asset('assets/js/home-mobile.js') }}"></script>
</body>
</html>
