@extends('layouts.admin')

@section('title', 'Laporan Platform')
@section('page_title', 'Laporan Platform')

@section('content')
    <p class="page-description">Ringkasan metrik lintas platform untuk monitoring operasional dan pendapatan.</p>

    <form method="GET" action="{{ route('super-admin.reports.index') }}" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(210px,1fr)); gap:10px; margin-bottom:18px;">
        <input type="date" name="start_date" value="{{ request('start_date') }}" style="padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
        <input type="date" name="end_date" value="{{ request('end_date') }}" style="padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
        <button type="submit" style="padding:10px 12px; border:0; border-radius:10px; background:#0f172a; color:#fff; font-weight:600;">Terapkan Filter</button>
    </form>

    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:10px; margin-bottom:16px;">
        <article style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:12px;"><small style="color:#64748b;">Total Rental</small><h4 style="margin:6px 0 0;">{{ $stats['total_rentals'] }}</h4></article>
        <article style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:12px;"><small style="color:#64748b;">Rental Approved</small><h4 style="margin:6px 0 0;">{{ $stats['total_rentals_approved'] }}</h4></article>
        <article style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:12px;"><small style="color:#64748b;">Rental Pending</small><h4 style="margin:6px 0 0;">{{ $stats['total_rentals_pending'] }}</h4></article>
        <article style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:12px;"><small style="color:#64748b;">Total Customer</small><h4 style="margin:6px 0 0;">{{ $stats['total_customers'] }}</h4></article>
        <article style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:12px;"><small style="color:#64748b;">Admin Rental</small><h4 style="margin:6px 0 0;">{{ $stats['total_admin_rental'] }}</h4></article>
        <article style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:12px;"><small style="color:#64748b;">Total Kendaraan</small><h4 style="margin:6px 0 0;">{{ $stats['total_vehicles'] }}</h4></article>
        <article style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:12px;"><small style="color:#64748b;">Total Booking</small><h4 style="margin:6px 0 0;">{{ $stats['total_bookings'] }}</h4></article>
        <article style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:12px;"><small style="color:#64748b;">Booking Selesai</small><h4 style="margin:6px 0 0;">{{ $stats['total_bookings_completed'] }}</h4></article>
        <article style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:12px;"><small style="color:#64748b;">Payment Verified</small><h4 style="margin:6px 0 0;">{{ $stats['total_payment_verified'] }}</h4></article>
        <article style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:12px;"><small style="color:#64748b;">Revenue Verified</small><h4 style="margin:6px 0 0;">Rp {{ number_format($stats['total_transaction_revenue'], 0, ',', '.') }}</h4></article>
        <article style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:12px;"><small style="color:#64748b;">Komisi ({{ $stats['commission_rate'] }}%)</small><h4 style="margin:6px 0 0;">Rp {{ number_format($stats['total_commission'], 0, ',', '.') }}</h4></article>
    </div>

    <div style="display:grid; grid-template-columns:1.4fr 1fr 1fr; gap:12px; align-items:start;">
        <section style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:12px; overflow:auto;">
            <h4 style="margin:0 0 10px;">10 Transaksi Verified Terbaru</h4>
            <table style="width:100%; border-collapse:collapse; min-width:680px;">
                <thead>
                    <tr style="background:#f8fafc;">
                        <th style="text-align:left; padding:10px;">Booking</th>
                        <th style="text-align:left; padding:10px;">Rental</th>
                        <th style="text-align:left; padding:10px;">Customer</th>
                        <th style="text-align:left; padding:10px;">Kendaraan</th>
                        <th style="text-align:left; padding:10px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($latestTransactions as $item)
                        <tr style="border-top:1px solid #e5e7eb;">
                            <td style="padding:10px;">{{ $item->booking_code }}</td>
                            <td style="padding:10px;">{{ $item->rentalCompany?->company_name ?? '-' }}</td>
                            <td style="padding:10px;">{{ $item->customer_name ?: ($item->customer?->name ?? '-') }}</td>
                            <td style="padding:10px;">{{ $item->vehicle?->name ?? '-' }}</td>
                            <td style="padding:10px; font-weight:700;">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="padding:14px; text-align:center; color:#6b7280;">Belum ada data transaksi verified.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:12px;">
            <h4 style="margin:0 0 10px;">Top Rental by Booking</h4>
            <ol style="margin:0; padding-left:18px; display:grid; gap:8px;">
                @forelse ($topRentals as $rental)
                    <li>
                        <strong>{{ $rental->company_name }}</strong><br>
                        <small style="color:#64748b;">{{ $rental->total_bookings }} booking</small>
                    </li>
                @empty
                    <li style="color:#6b7280;">Belum ada data.</li>
                @endforelse
            </ol>
        </section>

        <section style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:12px;">
            <h4 style="margin:0 0 10px;">Top Kendaraan by Booking</h4>
            <ol style="margin:0; padding-left:18px; display:grid; gap:8px;">
                @forelse ($topVehicles as $vehicle)
                    <li>
                        <strong>{{ $vehicle->name }}</strong><br>
                        <small style="color:#64748b;">{{ $vehicle->total_bookings }} booking</small>
                    </li>
                @empty
                    <li style="color:#6b7280;">Belum ada data.</li>
                @endforelse
            </ol>
        </section>
    </div>
@endsection
