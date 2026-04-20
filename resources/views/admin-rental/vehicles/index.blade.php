@extends('layouts.admin')

@section('title', 'Data Kendaraan | Admin Rental')
@section('page_title', 'Data Kendaraan')

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
            <h2 style="margin:0 0 6px;">{{ $rentalCompany->company_name }}</h2>
            <p style="margin:0; color:#6b7280;">Kelola data kendaraan milik rental Anda.</p>
        </div>

        <a href="{{ route('admin-rental.vehicles.create') }}" style="display:inline-flex; align-items:center; padding:10px 16px; background:#2563eb; color:#fff; text-decoration:none; border-radius:10px; font-weight:600;">Tambah Kendaraan</a>
    </div>

    <form method="GET" action="{{ route('admin-rental.vehicles.index') }}" style="display:flex; gap:10px; margin-bottom:18px; flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, brand, atau kategori" style="min-width:260px; flex:1; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">
        <button type="submit" style="padding:10px 16px; border:0; border-radius:10px; background:#111827; color:#fff; font-weight:600;">Cari</button>
        @if (request('search'))
            <a href="{{ route('admin-rental.vehicles.index') }}" style="padding:10px 16px; border:1px solid #d1d5db; border-radius:10px; color:#111827; text-decoration:none; font-weight:600;">Reset</a>
        @endif
    </form>

    <div style="overflow-x:auto; background:#fff; border:1px solid #e5e7eb; border-radius:14px;">
        <table style="width:100%; border-collapse:collapse; min-width:1100px;">
            <thead style="background:#f9fafb; text-align:left;">
                <tr>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Foto</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Nama</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Brand</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Kategori</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Transmisi</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Bahan Bakar</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Kursi</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Harga/Hari</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Status</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Galeri</th>
                    <th style="padding:14px; border-bottom:1px solid #e5e7eb;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($vehicles as $vehicle)
                    <tr>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6; width:110px;">
                            @if ($vehicle->main_image)
                                <img src="{{ asset('storage/' . $vehicle->main_image) }}" alt="{{ $vehicle->name }}" style="width:90px; height:64px; object-fit:cover; border-radius:10px;">
                            @else
                                <div style="width:90px; height:64px; border-radius:10px; background:#e5e7eb; display:flex; align-items:center; justify-content:center; color:#6b7280; font-size:12px;">No Image</div>
                            @endif
                        </td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6; font-weight:600;">{{ $vehicle->name }}</td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6;">{{ $vehicle->brand }}</td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6;">{{ $vehicle->category }}</td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6;">{{ $vehicle->transmission }}</td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6;">{{ $vehicle->fuel_type }}</td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6;">{{ $vehicle->seat_capacity }}</td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6;">Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }}</td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6;">{{ ucfirst($vehicle->status) }}</td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6;">{{ $vehicle->images_count }}</td>
                        <td style="padding:14px; border-bottom:1px solid #f3f4f6; white-space:nowrap;">
                            <a href="{{ route('admin-rental.vehicles.edit', $vehicle) }}" style="display:inline-block; padding:8px 12px; background:#2563eb; color:#fff; text-decoration:none; border-radius:8px; font-size:14px;">Edit</a>
                            <form action="{{ route('admin-rental.vehicles.destroy', $vehicle) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Hapus kendaraan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding:8px 12px; background:#dc2626; color:#fff; border:0; border-radius:8px; font-size:14px;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" style="padding:18px; text-align:center; color:#6b7280;">Belum ada data kendaraan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:18px;">
        {{ $vehicles->links() }}
    </div>
@endsection
