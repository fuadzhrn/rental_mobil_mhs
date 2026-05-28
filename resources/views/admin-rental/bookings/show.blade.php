@extends('layouts.admin')

@section('title', 'Detail Konfirmasi Booking')
@section('page_title', 'Detail Konfirmasi Booking')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-rental-bookings.css') }}">
@endpush

@section('content')
    <div class="bookings-page bookings-detail-page">
        @if (session('success'))
            <div class="admin-flash admin-flash-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="admin-flash admin-flash-error">{{ session('error') }}</div>
        @endif

        <div class="bookings-breadcrumb">
            <span>Admin Rental</span>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <strong>Detail Konfirmasi Booking</strong>
        </div>

        <div class="bookings-header-card bookings-detail-header">
            <div>
                <h2>Detail Konfirmasi Booking</h2>
                <p>Periksa data booking, cek ketersediaan barang, lalu konfirmasi atau tolak sesuai kondisi stok.</p>
                <small>{{ $booking->booking_code }}</small>
            </div>

            <div class="bookings-detail-badges">
                <span class="bookings-badge">{{ $booking->booking_status_label }}</span>
                <span class="bookings-badge is-soft">{{ $booking->payment_status_label }}</span>
            </div>
        </div>

        <div class="bookings-detail-grid">
            <section class="bookings-detail-card">
                <h3>Data Booking</h3>
                <dl class="bookings-detail-list">
                    <div>
                        <dt>Customer</dt>
                        <dd>
                            {{ $booking->customer_name }}<br>
                            <span>{{ $booking->customer_email }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt>Kendaraan</dt>
                        <dd>{{ $booking->vehicle->name }}</dd>
                    </div>
                    <div>
                        <dt>Rental</dt>
                        <dd>{{ $booking->vehicle->rentalCompany?->company_name }}</dd>
                    </div>
                    <div>
                        <dt>Pickup</dt>
                        <dd>{{ $booking->pickup_date->format('d M Y') }} {{ $booking->pickup_time ? $booking->pickup_time->format('H:i') : '' }}</dd>
                    </div>
                    <div>
                        <dt>Return</dt>
                        <dd>{{ $booking->return_date->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt>Lokasi Pickup</dt>
                        <dd>{{ $booking->pickup_location }}</dd>
                    </div>
                    <div>
                        <dt>Lokasi Return</dt>
                        <dd>{{ $booking->return_location ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt>Durasi</dt>
                        <dd>{{ $booking->duration_days }} Hari</dd>
                    </div>
                    <div>
                        <dt>Total</dt>
                        <dd>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt>Catatan</dt>
                        <dd>{{ $booking->note ?? '-' }}</dd>
                    </div>
                </dl>

                <div class="bookings-section-divider"></div>

                <h4>Timeline Transaksi</h4>
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
                <div class="bookings-timeline">
                    @foreach ($timeline as $step => $active)
                        <div class="bookings-timeline-item {{ $active ? 'is-active' : '' }}">{{ $step }}</div>
                    @endforeach
                </div>
            </section>

            <section class="bookings-detail-card">
                <h3>Data Pembayaran & Aksi</h3>
                <dl class="bookings-detail-list">
                    <div>
                        <dt>Metode</dt>
                        <dd>{{ $booking->payment?->payment_method ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt>Status Pembayaran</dt>
                        <dd>{{ $booking->payment_status_label }}</dd>
                    </div>
                    <div>
                        <dt>Rejection Note</dt>
                        <dd>{{ $booking->payment?->rejection_note ?? '-' }}</dd>
                    </div>
                </dl>

                @if ($booking->payment?->proof_payment)
                    @php $proofUrl = asset('storage/' . $booking->payment->proof_payment); @endphp
                    <div class="bookings-proof-card">
                        <p class="bookings-proof-label">Bukti Pembayaran</p>
                        @if (str_ends_with(strtolower($booking->payment->proof_payment), '.pdf'))
                            <a href="{{ $proofUrl }}" target="_blank" class="bookings-proof-link">Lihat PDF Bukti Pembayaran</a>
                        @else
                            <img src="{{ $proofUrl }}" alt="Bukti Pembayaran">
                        @endif
                    </div>
                @endif

                <div class="bookings-section-divider"></div>

                <h4>Aksi Konfirmasi</h4>

                @if (in_array($booking->booking_status, [\App\Models\Booking::BOOKING_WAITING_PAYMENT, \App\Models\Booking::BOOKING_WAITING_VERIFICATION, \App\Models\Booking::BOOKING_PENDING], true))
                    <div class="bookings-action-stack">
                        <form method="POST" action="{{ route('admin-rental.bookings.confirm', $booking) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bookings-primary-action">Confirm Availability</button>
                        </form>

                        <form method="POST" action="{{ route('admin-rental.bookings.reject-availability', $booking) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="reason" value="Tidak tersedia">
                            <button type="submit" class="bookings-secondary-action">Reject Availability</button>
                        </form>
                    </div>
                @endif

                @if ($booking->booking_status === \App\Models\Booking::BOOKING_CONFIRMED && $booking->payment_status === \App\Models\Booking::PAYMENT_VERIFIED)
                    <form method="POST" action="{{ route('admin-rental.bookings.mark-ongoing', $booking) }}" class="bookings-inline-form">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="bookings-primary-action is-ongoing">Mark Ongoing</button>
                    </form>
                @endif

                @if ($booking->booking_status === \App\Models\Booking::BOOKING_ONGOING)
                    <form method="POST" action="{{ route('admin-rental.bookings.mark-completed', $booking) }}" class="bookings-inline-form">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="bookings-primary-action is-completed">Mark Completed</button>
                    </form>
                @endif

                @if (in_array($booking->booking_status, [\App\Models\Booking::BOOKING_WAITING_PAYMENT, \App\Models\Booking::BOOKING_WAITING_VERIFICATION, \App\Models\Booking::BOOKING_CONFIRMED], true))
                    <form method="POST" action="{{ route('admin-rental.bookings.cancel', $booking) }}" class="bookings-cancel-form">
                        @csrf
                        @method('PATCH')
                        <label for="cancel_reason">Alasan Cancel (opsional)</label>
                        <textarea name="cancel_reason" id="cancel_reason" rows="3" placeholder="Tulis alasan pembatalan di sini..."></textarea>
                        @error('cancel_reason')
                            <small>{{ $message }}</small>
                        @enderror
                        <button type="submit" class="bookings-secondary-action is-cancel">Cancel Booking</button>
                    </form>
                @endif

                @if ($booking->booking_status === \App\Models\Booking::BOOKING_COMPLETED)
                    <div class="bookings-success-note">Booking completed. Data ini siap menjadi dasar fitur review pada tahap berikutnya.</div>
                @endif
            </section>
        </div>
    </div>
@endsection
