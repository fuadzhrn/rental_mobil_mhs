@extends('layouts.admin')

@section('title', 'Detail User')
@section('page_title', 'Detail User')

@section('content')
    <a href="{{ route('super-admin.users.index') }}" style="display:inline-block; margin-bottom:14px; color:#1d4ed8; font-weight:600; text-decoration:none;">&larr; Kembali ke daftar user</a>

    <section style="background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:16px; margin-bottom:14px;">
        <h3 style="margin:0 0 10px;">{{ $user->name }}</h3>
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:10px;">
            <div>
                <small style="color:#64748b;">Email</small>
                <p style="margin:2px 0 0;">{{ $user->email }}</p>
            </div>
            <div>
                <small style="color:#64748b;">Telepon</small>
                <p style="margin:2px 0 0;">{{ $user->phone ?: '-' }}</p>
            </div>
            <div>
                <small style="color:#64748b;">Role</small>
                <p style="margin:2px 0 0; text-transform:capitalize;">{{ str_replace('_', ' ', $user->role) }}</p>
            </div>
            <div>
                <small style="color:#64748b;">Terdaftar</small>
                <p style="margin:2px 0 0;">{{ $user->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </section>

    @if ($user->role === 'customer')
        <section style="background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:16px;">
            <h4 style="margin:0 0 12px;">Ringkasan Aktivitas Customer</h4>
            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(190px,1fr)); gap:10px;">
                <div style="padding:12px; border-radius:12px; background:#f8fafc; border:1px solid #e2e8f0;">
                    <small style="color:#64748b;">Total Booking</small>
                    <p style="margin:6px 0 0; font-weight:700; font-size:20px;">{{ $detail['booking_count'] ?? 0 }}</p>
                </div>
                <div style="padding:12px; border-radius:12px; background:#f8fafc; border:1px solid #e2e8f0;">
                    <small style="color:#64748b;">Booking Selesai</small>
                    <p style="margin:6px 0 0; font-weight:700; font-size:20px;">{{ $detail['completed_booking_count'] ?? 0 }}</p>
                </div>
                <div style="padding:12px; border-radius:12px; background:#f8fafc; border:1px solid #e2e8f0;">
                    <small style="color:#64748b;">Total Transaksi Verified</small>
                    <p style="margin:6px 0 0; font-weight:700; font-size:20px;">Rp {{ number_format($detail['verified_transaction_total'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div style="padding:12px; border-radius:12px; background:#f8fafc; border:1px solid #e2e8f0;">
                    <small style="color:#64748b;">Total Review</small>
                    <p style="margin:6px 0 0; font-weight:700; font-size:20px;">{{ $detail['review_count'] ?? 0 }}</p>
                </div>
            </div>
        </section>
    @endif

    @if ($user->role === 'admin_rental')
        <section style="background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:16px;">
            <h4 style="margin:0 0 12px;">Ringkasan Akun Admin Rental</h4>

            @php
                $rental = $detail['rental_company'] ?? null;
            @endphp

            @if ($rental)
                <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(190px,1fr)); gap:10px; margin-bottom:12px;">
                    <div style="padding:12px; border-radius:12px; background:#f8fafc; border:1px solid #e2e8f0;">
                        <small style="color:#64748b;">Rental Company</small>
                        <p style="margin:6px 0 0; font-weight:700;">{{ $rental->company_name }}</p>
                    </div>
                    <div style="padding:12px; border-radius:12px; background:#f8fafc; border:1px solid #e2e8f0;">
                        <small style="color:#64748b;">Total Kendaraan</small>
                        <p style="margin:6px 0 0; font-weight:700; font-size:20px;">{{ $detail['vehicle_count'] ?? 0 }}</p>
                    </div>
                    <div style="padding:12px; border-radius:12px; background:#f8fafc; border:1px solid #e2e8f0;">
                        <small style="color:#64748b;">Total Booking</small>
                        <p style="margin:6px 0 0; font-weight:700; font-size:20px;">{{ $detail['booking_count'] ?? 0 }}</p>
                    </div>
                </div>

                <p style="margin:0; color:#475569;">Status verifikasi: <strong style="text-transform:capitalize;">{{ $rental->status_verification }}</strong></p>
            @else
                <p style="margin:0; color:#64748b;">User ini belum memiliki rental company.</p>
            @endif
        </section>
    @endif
@endsection
