@extends('layouts.admin')

@section('title', 'Data Customer | Admin Rental')
@section('page_title', 'Data Customer')

@section('content')
    <p style="margin:0 0 16px 0; color:#6b7280;">Daftar pelanggan yang telah melakukan booking ke rental Anda. Kelompokkan berdasarkan status loyalitas untuk targeting promo yang tepat.</p>

    @if (session('success'))
        <div class="alert alert-success" role="status" style="margin-bottom: 16px; padding: 12px 14px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; border-radius: 10px;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" role="alert" style="margin-bottom: 16px; padding: 12px 14px; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; border-radius: 10px;">
            {{ session('error') }}
        </div>
    @endif

    <form method="GET" action="{{ route('admin-rental.customers.index') }}" style="display:flex; gap:10px; margin-bottom:18px; flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email customer" style="min-width:260px; flex:1; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
        
        <select name="loyal" style="padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
            <option value="">Semua Customer</option>
            <option value="loyal" @selected(request('loyal') === 'loyal')>Pelanggan Setia (2+ Booking)</option>
            <option value="non_loyal" @selected(request('loyal') === 'non_loyal')>Pelanggan Baru/Biasa (&lt;2 Booking)</option>
        </select>

        <button type="submit" style="padding:10px 16px; border:0; border-radius:10px; background:#111827; color:#fff; font-weight:600;">Cari</button>
        @if (request('search') || request('loyal'))
            <a href="{{ route('admin-rental.customers.index') }}" style="padding:10px 16px; border:1px solid #d1d5db; border-radius:10px; color:#111827; text-decoration:none; font-weight:600;">Reset</a>
        @endif
    </form>

    <div style="overflow-x:auto; background:#fff; border:1px solid #e5e7eb; border-radius:14px;">
        <table style="width:100%; border-collapse:collapse; min-width:1300px;">
            <thead style="background:#f9fafb; text-align:left;">
                <tr>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Nama</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Email</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">HP</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Total Booking</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Booking Selesai</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Total Transaksi</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Rata-rata Rating</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Booking Terakhir</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Status Loyalitas</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    @php
                        $isLoyal = $customer->bookings_count >= 2 && $customer->completed_bookings_count > 0;
                    @endphp
                    <tr>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6; font-weight:600;">{{ $customer->name }}</td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6;">{{ $customer->email }}</td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6;">{{ $customer->phone ?? '-' }}</td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6; text-align:center;">{{ $customer->bookings_count ?? 0 }}</td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6; text-align:center;">
                            <span style="background:#ecfdf5; color:#065f46; border-radius:999px; padding:4px 10px; font-size:12px; font-weight:700;">{{ $customer->completed_bookings_count ?? 0 }}</span>
                        </td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6;">Rp {{ number_format($customer->total_transaction_amount ?? 0, 0, ',', '.') }}</td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6; text-align:center;">
                            @if ($customer->average_rating_given)
                                <span style="background:#fef3c7; color:#b45309; border-radius:999px; padding:4px 10px; font-size:12px; font-weight:700;">{{ number_format($customer->average_rating_given, 1) }} ⭐</span>
                            @else
                                <span style="color:#6b7280;">-</span>
                            @endif
                        </td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6; font-size:13px;">
                            @if ($customer->last_booking_at)
                                {{ \Carbon\Carbon::parse($customer->last_booking_at)->format('d M Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6;">
                            @if ($isLoyal)
                                <span style="background:#dbeafe; color:#1e40af; border-radius:999px; padding:6px 12px; font-size:12px; font-weight:700;">🏆 Setia</span>
                            @else
                                <span style="background:#f3f4f6; color:#6b7280; border-radius:999px; padding:6px 12px; font-size:12px;">Baru/Biasa</span>
                            @endif
                        </td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6;">
                            <a href="{{ route('admin-rental.customers.show', $customer) }}" style="display:inline-block; padding:8px 12px; background:#2563eb; color:#fff; text-decoration:none; border-radius:8px; font-size:14px;">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="padding:18px; text-align:center; color:#6b7280;">Belum ada data customer untuk kriteria ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:18px;">
        {{ $customers->links() }}
    </div>
@endsection
