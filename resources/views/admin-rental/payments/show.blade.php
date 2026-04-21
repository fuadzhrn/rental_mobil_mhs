@extends('layouts.admin')

@section('title', 'Detail Pembayaran')
@section('page_title', 'Detail Pembayaran')

@section('content')
    <div class="page-description">
        <strong>Booking Code:</strong> {{ $booking->booking_code }}<br>
        <strong>Customer:</strong> {{ $booking->customer_name }}<br>
        <strong>Kendaraan:</strong> {{ $booking->vehicle->name }}
    </div>

    <div class="stat-grid" style="margin-top:18px;">
        <article class="stat-card">
            <p>Total Pembayaran</p>
            <h3>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</h3>
        </article>
        <article class="stat-card">
            <p>Status Payment</p>
            <h3>{{ $booking->payment?->payment_status ?? '-' }}</h3>
        </article>
        <article class="stat-card">
            <p>Status Booking</p>
            <h3>{{ $booking->booking_status }}</h3>
        </article>
    </div>

    <div style="display:grid; grid-template-columns: 1.2fr .8fr; gap:24px; margin-top:24px;">
        <section style="background:#fff; border-radius:18px; padding:18px;">
            <h2 style="margin-top:0;">Detail Booking</h2>
            <p><strong>Nama Customer:</strong> {{ $booking->customer_name }}</p>
            <p><strong>Email:</strong> {{ $booking->customer_email }}</p>
            <p><strong>HP:</strong> {{ $booking->customer_phone }}</p>
            <p><strong>Pickup:</strong> {{ $booking->pickup_date->format('d M Y') }} {{ $booking->pickup_time ?? '' }}</p>
            <p><strong>Return:</strong> {{ $booking->return_date->format('d M Y') }}</p>
            <p><strong>Lokasi Pickup:</strong> {{ $booking->pickup_location }}</p>
            <p><strong>Lokasi Return:</strong> {{ $booking->return_location ?? '-' }}</p>
            <p><strong>Metode:</strong> {{ $booking->payment?->payment_method ?? '-' }}</p>
            <p><strong>Nominal:</strong> Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</p>
            <p><strong>Status Bukti:</strong> {{ $booking->payment?->payment_status ?? '-' }}</p>
            <p><strong>Rejection Note:</strong> {{ $booking->payment?->rejection_note ?? '-' }}</p>
        </section>

        <section style="background:#fff; border-radius:18px; padding:18px;">
            <h2 style="margin-top:0;">Bukti Pembayaran</h2>
            @if ($booking->payment?->proof_payment)
                @php $proofUrl = asset('storage/' . $booking->payment->proof_payment); @endphp
                @if (str_ends_with(strtolower($booking->payment->proof_payment), '.pdf'))
                    <p><a href="{{ $proofUrl }}" target="_blank">Lihat file PDF</a></p>
                @else
                    <img src="{{ $proofUrl }}" alt="Bukti Pembayaran" style="max-width:100%; border-radius:14px; border:1px solid #e5e7eb;">
                @endif
            @else
                <p style="color:#6b7280;">Belum ada bukti pembayaran.</p>
            @endif

            <div style="margin-top:20px; display:flex; gap:12px; flex-wrap:wrap;">
                <form action="{{ route('admin-rental.payments.verify', $booking) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-primary" {{ $booking->payment?->payment_status !== \App\Models\Payment::STATUS_UPLOADED ? 'disabled' : '' }}>Verify</button>
                </form>
            </div>

            <form action="{{ route('admin-rental.payments.reject', $booking) }}" method="POST" style="margin-top:18px;">
                @csrf
                @method('PATCH')
                <div class="field-group">
                    <label for="rejection_note">Alasan Penolakan</label>
                    <textarea name="rejection_note" id="rejection_note" rows="4" style="width:100%;"></textarea>
                    @error('rejection_note')
                        <small style="color:#dc2626;">{{ $message }}</small>
                    @enderror
                </div>
                <button type="submit" class="btn btn-outline" {{ $booking->payment?->payment_status !== \App\Models\Payment::STATUS_UPLOADED ? 'disabled' : '' }}>Reject</button>
            </form>
        </section>
    </div>
@endsection
