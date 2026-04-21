@extends('layouts.admin')

@section('title', 'Detail Booking Admin Rental')
@section('page_title', 'Detail Booking')

@section('content')
    @if (session('success'))
        <div style="background:#dcfce7; color:#166534; border-radius:12px; padding:12px; margin-bottom:14px;">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div style="background:#fef2f2; color:#991b1b; border-radius:12px; padding:12px; margin-bottom:14px;">{{ session('error') }}</div>
    @endif

    <div class="page-description">
        <strong>Booking Code:</strong> {{ $booking->booking_code }}<br>
        <strong>Booking Status:</strong> {{ $booking->booking_status_label }}<br>
        <strong>Payment Status:</strong> {{ $booking->payment_status_label }}
    </div>

    <div style="display:grid; grid-template-columns:1.1fr .9fr; gap:20px; margin-top:18px;">
        <section style="background:#fff; border-radius:16px; padding:16px;">
            <h2 style="margin-top:0;">Data Booking</h2>
            <p><strong>Customer:</strong> {{ $booking->customer_name }} ({{ $booking->customer_email }})</p>
            <p><strong>Kendaraan:</strong> {{ $booking->vehicle->name }}</p>
            <p><strong>Rental:</strong> {{ $booking->vehicle->rentalCompany?->company_name }}</p>
            <p><strong>Pickup:</strong> {{ $booking->pickup_date->format('d M Y') }} {{ $booking->pickup_time ? $booking->pickup_time->format('H:i') : '' }}</p>
            <p><strong>Return:</strong> {{ $booking->return_date->format('d M Y') }}</p>
            <p><strong>Lokasi Pickup:</strong> {{ $booking->pickup_location }}</p>
            <p><strong>Lokasi Return:</strong> {{ $booking->return_location ?? '-' }}</p>
            <p><strong>Durasi:</strong> {{ $booking->duration_days }} Hari</p>
            <p><strong>Total:</strong> Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</p>
            <p><strong>Catatan:</strong> {{ $booking->note ?? '-' }}</p>

            <hr style="margin:14px 0; border:0; border-top:1px solid #e5e7eb;">
            <h3 style="margin-top:0;">Timeline Transaksi</h3>
            @php
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
            <div style="display:grid; gap:8px;">
                @foreach ($timeline as $step => $active)
                    @if ($active)
                        <div style="padding:8px 10px; border-radius:8px; background:#ecfdf5; color:#166534;">{{ $step }}</div>
                    @else
                        <div style="padding:8px 10px; border-radius:8px; background:#f8fafc; color:#64748b;">{{ $step }}</div>
                    @endif
                @endforeach
            </div>
        </section>

        <section style="background:#fff; border-radius:16px; padding:16px;">
            <h2 style="margin-top:0;">Data Pembayaran & Aksi</h2>
            <p><strong>Metode:</strong> {{ $booking->payment?->payment_method ?? '-' }}</p>
            <p><strong>Payment Status:</strong> {{ $booking->payment_status_label }}</p>
            <p><strong>Rejection Note:</strong> {{ $booking->payment?->rejection_note ?? '-' }}</p>

            @if ($booking->payment?->proof_payment)
                @php $proofUrl = asset('storage/' . $booking->payment->proof_payment); @endphp
                <p><strong>Bukti Pembayaran:</strong></p>
                @if (str_ends_with(strtolower($booking->payment->proof_payment), '.pdf'))
                    <a href="{{ $proofUrl }}" target="_blank">Lihat PDF Bukti Pembayaran</a>
                @else
                    <img src="{{ $proofUrl }}" alt="Bukti Pembayaran" style="max-width:100%; border-radius:10px; border:1px solid #e5e7eb;">
                @endif
            @endif

            <hr style="margin:14px 0; border:0; border-top:1px solid #e5e7eb;">
            <h3 style="margin-top:0;">Aksi Status Operasional</h3>

            @if ($booking->booking_status === \App\Models\Booking::BOOKING_CONFIRMED && $booking->payment_status === \App\Models\Booking::PAYMENT_VERIFIED)
                <form method="POST" action="{{ route('admin-rental.bookings.mark-ongoing', $booking) }}" style="margin-bottom:10px;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-primary">Mark Ongoing</button>
                </form>
            @endif

            @if ($booking->booking_status === \App\Models\Booking::BOOKING_ONGOING)
                <form method="POST" action="{{ route('admin-rental.bookings.mark-completed', $booking) }}" style="margin-bottom:10px;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-primary">Mark Completed</button>
                </form>
            @endif

            @if (in_array($booking->booking_status, [\App\Models\Booking::BOOKING_WAITING_PAYMENT, \App\Models\Booking::BOOKING_WAITING_VERIFICATION, \App\Models\Booking::BOOKING_CONFIRMED], true))
                <form method="POST" action="{{ route('admin-rental.bookings.cancel', $booking) }}">
                    @csrf
                    @method('PATCH')
                    <label for="cancel_reason" style="font-weight:600; display:block; margin-bottom:6px;">Alasan Cancel (opsional)</label>
                    <textarea name="cancel_reason" id="cancel_reason" rows="3" style="width:100%; border:1px solid #d1d5db; border-radius:8px; padding:8px;"></textarea>
                    @error('cancel_reason')
                        <small style="color:#dc2626;">{{ $message }}</small>
                    @enderror
                    <button type="submit" class="btn btn-outline" style="margin-top:8px;">Cancel Booking</button>
                </form>
            @endif

            @if ($booking->booking_status === \App\Models\Booking::BOOKING_COMPLETED)
                <p style="background:#ecfeff; color:#155e75; border-radius:10px; padding:10px;">Booking completed. Data ini siap menjadi dasar fitur review pada tahap berikutnya.</p>
            @endif
        </section>
    </div>
@endsection
