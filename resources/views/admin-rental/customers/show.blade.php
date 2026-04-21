@extends('layouts.admin')

@section('title', 'Detail Customer | Admin Rental')
@section('page_title', 'Detail Customer')

@section('content')
    @if (session('success'))
        <div style="background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46; padding:12px 14px; border-radius:10px; margin-bottom:16px;">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:12px 14px; border-radius:10px; margin-bottom:16px;">{{ session('error') }}</div>
    @endif

    <div style="margin-bottom:18px;">
        <a href="{{ route('admin-rental.customers.index') }}" style="color:#2563eb; text-decoration:none; font-weight:600;">&larr; Kembali ke Daftar Customer</a>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:16px; margin-bottom:20px;">
        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px;">
            <p style="margin:0; color:#6b7280; font-size:13px;">Total Booking</p>
            <h3 style="margin:6px 0 0; font-size:28px;">{{ $bookingCount }}</h3>
        </div>
        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px;">
            <p style="margin:0; color:#6b7280; font-size:13px;">Booking Selesai</p>
            <h3 style="margin:6px 0 0; font-size:28px; color:#059669;">{{ $completedCount }}</h3>
        </div>
        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px;">
            <p style="margin:0; color:#6b7280; font-size:13px;">Total Transaksi</p>
            <h3 style="margin:6px 0 0; font-size:20px;">Rp {{ number_format($totalTransactionAmount, 0, ',', '.') }}</h3>
        </div>
        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:16px;">
            <p style="margin:0; color:#6b7280; font-size:13px;">Rata-rata Rating</p>
            <h3 style="margin:6px 0 0; font-size:20px;">
                -
            </h3>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1.2fr .8fr; gap:20px;">
        <section style="background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:20px;">
            <h2 style="margin-top:0;">Data Pribadi Customer</h2>
            <div style="display:grid; gap:12px;">
                <div>
                    <p style="margin:0; color:#6b7280; font-size:13px; font-weight:600;">Nama Lengkap</p>
                    <p style="margin:4px 0 0; font-size:16px;">{{ $customer->name }}</p>
                </div>
                <div>
                    <p style="margin:0; color:#6b7280; font-size:13px; font-weight:600;">Email</p>
                    <p style="margin:4px 0 0; font-size:16px;">{{ $customer->email }}</p>
                </div>
                <div>
                    <p style="margin:0; color:#6b7280; font-size:13px; font-weight:600;">No. Handphone</p>
                    <p style="margin:4px 0 0; font-size:16px;">{{ $customer->phone ?? '-' }}</p>
                </div>
                <div>
                    <p style="margin:0; color:#6b7280; font-size:13px; font-weight:600;">Terdaftar Sejak</p>
                    <p style="margin:4px 0 0; font-size:16px;">{{ $customer->created_at->format('d M Y') }}</p>
                </div>
            </div>

            <hr style="margin:18px 0; border:0; border-top:1px solid #e5e7eb;">

            <h3 style="margin-top:0;">Riwayat Booking (10 Terbaru)</h3>
            
            @if ($bookings->count() > 0)
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; font-size:14px; min-width:500px;">
                        <thead style="background:#f9fafb;">
                            <tr>
                                <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">Booking Code</th>
                                <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">Kendaraan</th>
                                <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">Tanggal</th>
                                <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">Total</th>
                                <th style="text-align:left; padding:10px; border-bottom:1px solid #e5e7eb;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $booking)
                                @php
                                    $bgColor = $booking->booking_status === 'completed' ? '#ecfdf5' : '#e0e7ff';
                                    $textColor = $booking->booking_status === 'completed' ? '#065f46' : '#3730a3';
                                @endphp
                                <tr>
                                    <td style="padding:10px; border-bottom:1px solid #f3f4f6;">
                                        <a href="{{ route('admin-rental.bookings.show', $booking) }}" style="color:#2563eb; text-decoration:none;">{{ $booking->booking_code }}</a>
                                    </td>
                                    <td style="padding:10px; border-bottom:1px solid #f3f4f6;">{{ $booking->vehicle->name }}</td>
                                    <td style="padding:10px; border-bottom:1px solid #f3f4f6;">{{ $booking->pickup_date->format('d M Y') }}</td>
                                    <td style="padding:10px; border-bottom:1px solid #f3f4f6; font-weight:600;">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td>
                                    <td style="padding:10px; border-bottom:1px solid #f3f4f6;">
                                        <span style="background: {{ $bgColor }}; color: {{ $textColor }}; border-radius:999px; padding:2px 8px; font-size:11px; font-weight:700;">
                                            {{ $booking->booking_status_label }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:14px;">
                    {{ $bookings->links() }}
                </div>
            @else
                <p style="color:#6b7280; text-align:center; padding:20px 0;">Belum ada riwayat booking.</p>
            @endif

            <hr style="margin:18px 0; border:0; border-top:1px solid #e5e7eb;">

            <h3 style="margin-top:0;">Review yang Diberikan ({{ $reviews->count() }})</h3>
            
            @if ($reviews->count() > 0)
                <div style="display:grid; gap:12px;">
                    @foreach ($reviews as $review)
                        <div style="border:1px solid #e5e7eb; border-radius:10px; padding:12px; background:#f9fafb;">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                                <div>
                                    <p style="margin:0; font-weight:600;">{{ $review->vehicle->name }}</p>
                                    <p style="margin:4px 0 0; color:#6b7280; font-size:13px;">{{ $review->booking->booking_code }}</p>
                                </div>
                                <p style="margin:0; color:#f59e0b;">
                                    @for ($i = 0; $i < $review->rating; $i++)
                                        ⭐
                                    @endfor
                                </p>
                            </div>
                            <p style="margin:0; color:#374151; font-size:13px;">{{ $review->review_text }}</p>
                            <p style="margin:8px 0 0; color:#9ca3af; font-size:12px;">{{ $review->created_at->format('d M Y H:i') }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color:#6b7280; text-align:center; padding:20px 0;">Belum memberikan review.</p>
            @endif
        </section>

        <section style="background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:20px;">
            <h3 style="margin-top:0;">Informasi Loyalitas</h3>
            
            @php
                $loyalBg = $isLoyal ? '#dbeafe' : '#f3f4f6';
                $loyalBorder = $isLoyal ? '#0284c7' : '#d1d5db';
                $loyalTextColor = $isLoyal ? '#0c4a6e' : '#6b7280';
            @endphp

            <div style="padding:14px; background: {{ $loyalBg }}; border-radius:10px; border:1px solid {{ $loyalBorder }}; margin-bottom:16px;">
                <p style="margin:0; font-weight:700; color: {{ $loyalTextColor }};">
                    {{ $isLoyal ? '🏆 Pelanggan Setia' : '👤 Pelanggan Baru/Biasa' }}
                </p>
                <p style="margin:6px 0 0; color: {{ $loyalTextColor }}; font-size:13px;">
                    {{ $isLoyal 
                        ? 'Customer ini memenuhi syarat loyalitas: 2+ booking completed' 
                        : 'Customer belum mencapai status loyalitas (minimal 2 booking completed)' 
                    }}
                </p>
            </div>

            <div style="padding:12px; background:#f0fdf4; border-radius:10px; border:1px solid #86efac; margin-bottom:16px;">
                <p style="margin:0; color:#166534; font-size:13px;">
                    <strong>Completed Bookings:</strong> {{ $customer->completed_bookings_count }}/{{ $customer->bookings_count }}
                </p>
            </div>

            <hr style="margin:14px 0; border:0; border-top:1px solid #e5e7eb;">

            <h3 style="margin-top:0;">Kendaraan yang Pernah Dirental</h3>
            
            @if ($vehicles->count() > 0)
                <div style="display:grid; gap:10px;">
                    @foreach ($vehicles as $vehicle)
                        <a href="{{ route('admin-rental.vehicles.edit', $vehicle) }}" style="display:block; padding:10px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; text-decoration:none; color:#111827; transition:all 0.3s;">
                            <p style="margin:0; font-weight:600; color:#111827;">{{ $vehicle->name }}</p>
                            <p style="margin:4px 0 0; color:#6b7280; font-size:13px;">{{ $vehicle->brand }} • {{ $vehicle->category }}</p>
                        </a>
                    @endforeach
                </div>
            @else
                <p style="color:#6b7280; text-align:center; padding:20px 0;">Belum ada data kendaraan.</p>
            @endif

            <hr style="margin:14px 0; border:0; border-top:1px solid #e5e7eb;">

            <h3 style="margin-top:0;">Target Promo</h3>
            @if ($isLoyal)
                <div style="padding:12px; background:#ecfdf5; border-radius:10px; border:1px solid #a7f3d0;">
                    <p style="margin:0; color:#065f46; font-size:13px;">
                        <strong>💡 Saran:</strong> Customer ini cocok untuk promo loyalitas eksklusif dengan diskon lebih besar.
                    </p>
                </div>
            @else
                <div style="padding:12px; background:#fef3c7; border-radius:10px; border:1px solid #fcd34d;">
                    <p style="margin:0; color:#b45309; font-size:13px;">
                        <strong>💡 Saran:</strong> Tawarkan promo khusus untuk meningkatkan loyalitas ke {{ 2 - $customer->completed_bookings_count }} booking lagi.
                    </p>
                </div>
            @endif
        </section>
    </div>
@endsection
