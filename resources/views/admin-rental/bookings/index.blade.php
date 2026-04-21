@extends('layouts.admin')

@section('title', 'Data Booking Admin Rental')
@section('page_title', 'Data Booking')

@section('content')
    <p class="page-description">
        Daftar booking milik rental company Anda.
    </p>

    @if (session('success'))
        <div style="background:#dcfce7; color:#166534; border-radius:12px; padding:12px; margin-bottom:14px;">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div style="background:#fef2f2; color:#991b1b; border-radius:12px; padding:12px; margin-bottom:14px;">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('admin-rental.bookings.index') }}" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(190px,1fr)); gap:10px; margin-bottom:18px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari code / customer / kendaraan" style="padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">

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

        <button type="submit" style="padding:10px 12px; border:0; border-radius:10px; background:#0f172a; color:#fff; font-weight:600;">Filter</button>
    </form>

    <div style="overflow:auto; background:#fff; border-radius:16px; padding:14px;">
        <table style="width:100%; border-collapse:collapse; min-width:940px;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="text-align:left; padding:12px;">Booking Code</th>
                    <th style="text-align:left; padding:12px;">Customer</th>
                    <th style="text-align:left; padding:12px;">Kendaraan</th>
                    <th style="text-align:left; padding:12px;">Pickup</th>
                    <th style="text-align:left; padding:12px;">Return</th>
                    <th style="text-align:left; padding:12px;">Total</th>
                    <th style="text-align:left; padding:12px;">Booking Status</th>
                    <th style="text-align:left; padding:12px;">Payment Status</th>
                    <th style="text-align:left; padding:12px;">Dibuat</th>
                    <th style="text-align:left; padding:12px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $booking)
                    <tr style="border-top:1px solid #e5e7eb;">
                        <td style="padding:12px; font-weight:700;">{{ $booking->booking_code }}</td>
                        <td style="padding:12px;">{{ $booking->customer_name }}</td>
                        <td style="padding:12px;">{{ $booking->vehicle->name }}</td>
                        <td style="padding:12px;">{{ $booking->pickup_date->format('d M Y') }}</td>
                        <td style="padding:12px;">{{ $booking->return_date->format('d M Y') }}</td>
                        <td style="padding:12px; font-weight:700;">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td>
                        <td style="padding:12px;"><span style="background:#e0e7ff; color:#3730a3; border-radius:999px; padding:4px 10px; font-size:12px; font-weight:700;">{{ $booking->booking_status_label }}</span></td>
                        <td style="padding:12px;"><span style="background:#ecfeff; color:#155e75; border-radius:999px; padding:4px 10px; font-size:12px; font-weight:700;">{{ $booking->payment_status_label }}</span></td>
                        <td style="padding:12px;">{{ $booking->created_at->format('d M Y H:i') }}</td>
                        <td style="padding:12px;"><a href="{{ route('admin-rental.bookings.show', $booking) }}" class="btn btn-outline">Detail</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="padding:24px; text-align:center; color:#6b7280;">Belum ada booking untuk kriteria ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:14px;">{{ $bookings->links() }}</div>
@endsection
