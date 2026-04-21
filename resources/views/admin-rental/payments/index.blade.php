@extends('layouts.admin')

@section('title', 'Pembayaran Booking')
@section('page_title', 'Pembayaran Booking')

@section('content')
    <p class="page-description">
        Daftar pembayaran dari customer untuk rental company Anda.
    </p>

    <div class="stat-grid">
        <article class="stat-card">
            <p>Total Booking</p>
            <h3>{{ $bookings->total() }}</h3>
        </article>
        <article class="stat-card">
            <p>Menunggu Verifikasi</p>
            <h3>{{ $bookings->where('payment.payment_status', \App\Models\Payment::STATUS_UPLOADED)->count() }}</h3>
        </article>
        <article class="stat-card">
            <p>Pending / Unpaid</p>
            <h3>{{ $bookings->where('payment.payment_status', \App\Models\Payment::STATUS_UNPAID)->count() }}</h3>
        </article>
    </div>

    <div class="table-card" style="margin-top:24px; overflow:auto; background:#fff; border-radius:18px; padding:18px;">
        <table class="admin-table" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th align="left">Booking Code</th>
                    <th align="left">Customer</th>
                    <th align="left">Kendaraan</th>
                    <th align="left">Total</th>
                    <th align="left">Status Payment</th>
                    <th align="left">Status Booking</th>
                    <th align="left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $booking)
                    <tr>
                        <td>{{ $booking->booking_code }}</td>
                        <td>{{ $booking->customer_name }}</td>
                        <td>{{ $booking->vehicle->name }}</td>
                        <td>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td>
                        <td>{{ $booking->payment?->payment_status ?? '-' }}</td>
                        <td>{{ $booking->booking_status }}</td>
                        <td>
                            <a href="{{ route('admin-rental.payments.show', $booking) }}" class="btn btn-outline">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:24px; color:#6b7280;">Belum ada pembayaran masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:18px;">{{ $bookings->links() }}</div>
@endsection
