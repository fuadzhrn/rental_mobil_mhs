@extends('layouts.admin')

@section('title', 'Data Komisi')
@section('page_title', 'Data Komisi')

@section('content')
    <p class="page-description">Komisi dihitung dari transaksi verified dengan status booking valid (confirmed/ongoing/completed).</p>

    <form method="GET" action="{{ route('super-admin.commissions.index') }}" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:10px; margin-bottom:16px;">
        <select name="rental_company_id" style="padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
            <option value="">Semua Rental</option>
            @foreach ($rentalOptions as $rentalOption)
                <option value="{{ $rentalOption->id }}" @selected((string) request('rental_company_id') === (string) $rentalOption->id)>
                    {{ $rentalOption->company_name }}
                </option>
            @endforeach
        </select>

        <input type="date" name="start_date" value="{{ request('start_date') }}" style="padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
        <input type="date" name="end_date" value="{{ request('end_date') }}" style="padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">

        <button type="submit" style="padding:10px 12px; border:0; border-radius:10px; background:#0f172a; color:#fff; font-weight:600;">Filter</button>
    </form>

    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:10px; margin-bottom:14px;">
        <article style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:12px;">
            <small style="color:#64748b;">Total Transaksi (Verified)</small>
            <h4 style="margin:6px 0 0;">Rp {{ number_format($totalTransaction, 0, ',', '.') }}</h4>
        </article>
        <article style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:12px;">
            <small style="color:#64748b;">Rate Komisi</small>
            <h4 style="margin:6px 0 0;">{{ $commissionRate }}%</h4>
        </article>
        <article style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:12px;">
            <small style="color:#64748b;">Total Komisi</small>
            <h4 style="margin:6px 0 0;">Rp {{ number_format($totalCommission, 0, ',', '.') }}</h4>
        </article>
    </div>

    <div style="overflow:auto; background:#fff; border-radius:16px; padding:14px; border:1px solid #e5e7eb;">
        <table style="width:100%; border-collapse:collapse; min-width:900px;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="text-align:left; padding:12px;">Booking</th>
                    <th style="text-align:left; padding:12px;">Rental</th>
                    <th style="text-align:left; padding:12px;">Customer</th>
                    <th style="text-align:left; padding:12px;">Status</th>
                    <th style="text-align:left; padding:12px;">Total Transaksi</th>
                    <th style="text-align:left; padding:12px;">Komisi ({{ $commissionRate }}%)</th>
                    <th style="text-align:left; padding:12px;">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $booking)
                    <tr style="border-top:1px solid #e5e7eb;">
                        <td style="padding:12px; font-weight:700;">{{ $booking->booking_code }}</td>
                        <td style="padding:12px;">{{ $booking->rentalCompany?->company_name ?? '-' }}</td>
                        <td style="padding:12px;">{{ $booking->customer_name ?: ($booking->customer?->name ?? '-') }}</td>
                        <td style="padding:12px;">
                            <span style="display:inline-block; margin-right:6px; padding:4px 10px; border-radius:999px; background:#e2e8f0; font-size:12px; font-weight:700; text-transform:capitalize;">{{ $booking->booking_status }}</span>
                            <span style="display:inline-block; padding:4px 10px; border-radius:999px; background:#cffafe; color:#155e75; font-size:12px; font-weight:700; text-transform:capitalize;">{{ $booking->payment_status }}</span>
                        </td>
                        <td style="padding:12px; font-weight:700;">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td>
                        <td style="padding:12px; font-weight:700; color:#1d4ed8;">Rp {{ number_format($booking->total_amount * ($commissionRate / 100), 0, ',', '.') }}</td>
                        <td style="padding:12px;">{{ $booking->created_at->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding:24px; text-align:center; color:#6b7280;">Belum ada transaksi untuk filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:14px;">{{ $bookings->links() }}</div>
@endsection
