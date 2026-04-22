@extends('layouts.admin')

@section('title', 'Verifikasi Rental')
@section('page_title', 'Verifikasi Rental')

@section('content')
    <p class="page-description">Kelola status verifikasi rental partner dan buka detail untuk persetujuan/penolakan.</p>

    @if (session('success'))
        <div style="background:#dcfce7; color:#166534; border-radius:12px; padding:12px; margin-bottom:14px;">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div style="background:#fef2f2; color:#991b1b; border-radius:12px; padding:12px; margin-bottom:14px;">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('super-admin.rentals.index') }}" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:10px; margin-bottom:18px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama rental, email, kota, owner" style="padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">

        <select name="status" style="padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
            <option value="">Semua Status</option>
            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
            <option value="approved" @selected(request('status') === 'approved')>Approved</option>
            <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
        </select>

        <button type="submit" style="padding:10px 12px; border:0; border-radius:10px; background:#0f172a; color:#fff; font-weight:600;">Filter</button>
    </form>

    <div style="overflow:auto; background:#fff; border-radius:16px; padding:14px; border:1px solid #e5e7eb;">
        <table style="width:100%; border-collapse:collapse; min-width:980px;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="text-align:left; padding:12px;">Nama Rental</th>
                    <th style="text-align:left; padding:12px;">Owner</th>
                    <th style="text-align:left; padding:12px;">Kontak</th>
                    <th style="text-align:left; padding:12px;">Kota</th>
                    <th style="text-align:left; padding:12px;">Kendaraan</th>
                    <th style="text-align:left; padding:12px;">Booking</th>
                    <th style="text-align:left; padding:12px;">Status</th>
                    <th style="text-align:left; padding:12px;">Verifier</th>
                    <th style="text-align:left; padding:12px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rentals as $rental)
                    <tr style="border-top:1px solid #e5e7eb;">
                        <td style="padding:12px; font-weight:700;">{{ $rental->company_name }}</td>
                        <td style="padding:12px;">{{ $rental->user?->name ?? '-' }}</td>
                        <td style="padding:12px;">{{ $rental->email }}<br><span style="color:#6b7280; font-size:12px;">{{ $rental->phone }}</span></td>
                        <td style="padding:12px;">{{ $rental->city }}</td>
                        <td style="padding:12px;">{{ $rental->vehicles_count }}</td>
                        <td style="padding:12px;">{{ $rental->bookings_count }}</td>
                        <td style="padding:12px; text-transform:capitalize;">{{ $rental->status_verification }}</td>
                        <td style="padding:12px;">{{ $rental->verifiedBy?->name ?? '-' }}</td>
                        <td style="padding:12px;"><a href="{{ route('super-admin.rentals.show', $rental) }}" style="padding:8px 10px; border-radius:8px; text-decoration:none; border:1px solid #cbd5e1; color:#0f172a; font-weight:600;">Detail</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="padding:24px; text-align:center; color:#6b7280;">Tidak ada data rental untuk filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:14px;">{{ $rentals->links() }}</div>
@endsection
