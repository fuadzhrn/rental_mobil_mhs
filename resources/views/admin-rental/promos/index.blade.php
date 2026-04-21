@extends('layouts.admin')

@section('title', 'Data Promo | Admin Rental')
@section('page_title', 'Data Promo')

@section('content')
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

    <div style="display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:20px; align-items:end;">
        <div>
            <h2 style="margin:0 0 6px;">Data Promo</h2>
            <p style="margin:0; color:#6b7280;">Kelola promo dan voucher untuk meningkatkan penjualan rental Anda.</p>
        </div>

        <a href="{{ route('admin-rental.promos.create') }}" style="display:inline-flex; align-items:center; padding:10px 16px; background:#2563eb; color:#fff; text-decoration:none; border-radius:10px; font-weight:600;">Tambah Promo</a>
    </div>

    <form method="GET" action="{{ route('admin-rental.promos.index') }}" style="display:flex; gap:10px; margin-bottom:18px; flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, kode promo, atau deskripsi" style="min-width:260px; flex:1; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
        
        <select name="status" style="padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
            <option value="">Semua Status</option>
            @foreach (['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>

        <button type="submit" style="padding:10px 16px; border:0; border-radius:10px; background:#111827; color:#fff; font-weight:600;">Cari</button>
        @if (request('search') || request('status'))
            <a href="{{ route('admin-rental.promos.index') }}" style="padding:10px 16px; border:1px solid #d1d5db; border-radius:10px; color:#111827; text-decoration:none; font-weight:600;">Reset</a>
        @endif
    </form>

    <div style="overflow-x:auto; background:#fff; border:1px solid #e5e7eb; border-radius:14px;">
        <table style="width:100%; border-collapse:collapse; min-width:1200px;">
            <thead style="background:#f9fafb; text-align:left;">
                <tr>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Kode Promo</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Judul</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Tipe Diskon</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Nilai Diskon</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Min. Transaksi</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Kuota</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Terpakai</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Periode</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Loyal Only</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Status</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($promos as $promo)
                    <tr>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6; font-weight:600;">{{ strtoupper($promo->promo_code) }}</td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6;">{{ $promo->title }}</td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6;">{{ $promo->discount_type === 'percent' ? 'Persen (%)' : 'Nominal (Rp)' }}</td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6;">
                            @if ($promo->discount_type === 'percent')
                                {{ $promo->discount_value }}%
                            @else
                                Rp {{ number_format($promo->discount_value, 0, ',', '.') }}
                            @endif
                        </td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6;">
                            @if ($promo->min_transaction)
                                Rp {{ number_format($promo->min_transaction, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6;">
                            @if ($promo->quota)
                                {{ $promo->quota }}
                            @else
                                Unlimited
                            @endif
                        </td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6;">{{ $promo->used_count }}</td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6; font-size:13px;">
                            {{ $promo->start_date->format('d M') }} - {{ $promo->end_date->format('d M Y') }}
                        </td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6;">
                            @if ($promo->loyal_only)
                                <span style="background:#dbeafe; color:#1e40af; border-radius:999px; padding:4px 8px; font-size:12px; font-weight:600;">Ya</span>
                            @else
                                <span style="background:#f3f4f6; color:#6b7280; border-radius:999px; padding:4px 8px; font-size:12px;">Tidak</span>
                            @endif
                        </td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6;">
                            @php
                                $statusBg = $promo->status === 'active' ? '#ecfdf5' : '#f3f4f6';
                                $statusColor = $promo->status === 'active' ? '#166534' : '#6b7280';
                            @endphp
                            <span style="background: {{ $statusBg }}; color: {{ $statusColor }}; border-radius:999px; padding:4px 10px; font-size:12px; font-weight:700;">
                                {{ ucfirst($promo->status) }}
                            </span>
                        </td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6; white-space:nowrap;">
                            <a href="{{ route('admin-rental.promos.edit', $promo) }}" style="display:inline-block; padding:8px 12px; background:#2563eb; color:#fff; text-decoration:none; border-radius:8px; font-size:14px; margin-right:4px;">Edit</a>
                            <form action="{{ route('admin-rental.promos.destroy', $promo) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Hapus promo ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding:8px 12px; background:#dc2626; color:#fff; border:0; border-radius:8px; font-size:14px;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" style="padding:18px; text-align:center; color:#6b7280;">Belum ada data promo.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:18px;">
        {{ $promos->links() }}
    </div>
@endsection
