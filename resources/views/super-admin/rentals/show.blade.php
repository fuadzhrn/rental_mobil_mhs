@extends('layouts.admin')

@section('title', 'Detail Verifikasi Rental')
@section('page_title', 'Detail Verifikasi Rental')

@section('content')
    @if (session('success'))
        <div style="background:#dcfce7; color:#166534; border-radius:12px; padding:12px; margin-bottom:14px;">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div style="background:#fef2f2; color:#991b1b; border-radius:12px; padding:12px; margin-bottom:14px;">{{ session('error') }}</div>
    @endif

    <a href="{{ route('super-admin.rentals.index') }}" style="display:inline-block; margin-bottom:14px; color:#1d4ed8; font-weight:600; text-decoration:none;">&larr; Kembali ke daftar rental</a>

    <div style="display:grid; grid-template-columns:2fr 1fr; gap:14px; align-items:start;">
        <section style="background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:16px;">
            <h3 style="margin:0 0 12px;">{{ $rentalCompany->company_name }}</h3>
            <p style="margin:0 0 10px; color:#6b7280;">{{ $rentalCompany->description ?: 'Tidak ada deskripsi.' }}</p>

            <div style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; margin-bottom:12px;">
                <div>
                    <small style="color:#64748b;">Owner</small>
                    <p style="margin:2px 0 0; font-weight:600;">{{ $rentalCompany->user?->name ?? '-' }}</p>
                </div>
                <div>
                    <small style="color:#64748b;">Status Verifikasi</small>
                    <p style="margin:2px 0 0; font-weight:600; text-transform:capitalize;">{{ $rentalCompany->status_verification }}</p>
                </div>
                <div>
                    <small style="color:#64748b;">Email</small>
                    <p style="margin:2px 0 0;">{{ $rentalCompany->email }}</p>
                </div>
                <div>
                    <small style="color:#64748b;">Telepon</small>
                    <p style="margin:2px 0 0;">{{ $rentalCompany->phone }}</p>
                </div>
                <div>
                    <small style="color:#64748b;">Kota</small>
                    <p style="margin:2px 0 0;">{{ $rentalCompany->city }}</p>
                </div>
                <div>
                    <small style="color:#64748b;">Total Kendaraan / Booking</small>
                    <p style="margin:2px 0 0;">{{ $totalVehicles }} / {{ $totalBookings }}</p>
                </div>
            </div>

            <div style="padding:10px; border-radius:10px; background:#f8fafc; border:1px dashed #cbd5e1; margin-bottom:12px;">
                <small style="color:#64748b;">Alamat</small>
                <p style="margin:4px 0 0;">{{ $rentalCompany->address }}</p>
            </div>

            @if ($rentalCompany->rejection_note)
                <div style="padding:10px; border-radius:10px; background:#fef2f2; color:#7f1d1d; border:1px solid #fecaca;">
                    <strong>Catatan Penolakan Terakhir:</strong>
                    <p style="margin:6px 0 0;">{{ $rentalCompany->rejection_note }}</p>
                </div>
            @endif

            <h4 style="margin:18px 0 8px;">10 Kendaraan Terbaru</h4>
            <div style="overflow:auto; border:1px solid #e5e7eb; border-radius:12px;">
                <table style="width:100%; border-collapse:collapse; min-width:680px;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="text-align:left; padding:10px;">Nama</th>
                            <th style="text-align:left; padding:10px;">Brand</th>
                            <th style="text-align:left; padding:10px;">Plat</th>
                            <th style="text-align:left; padding:10px;">Harga/Hari</th>
                            <th style="text-align:left; padding:10px;">Total Booking</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vehicles as $vehicle)
                            <tr style="border-top:1px solid #e5e7eb;">
                                <td style="padding:10px;">{{ $vehicle->name }}</td>
                                <td style="padding:10px;">{{ $vehicle->brand }}</td>
                                <td style="padding:10px;">{{ $vehicle->plate_number }}</td>
                                <td style="padding:10px;">Rp {{ number_format($vehicle->price_per_day, 0, ',', '.') }}</td>
                                <td style="padding:10px;">{{ $vehicle->bookings_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding:14px; text-align:center; color:#6b7280;">Belum ada kendaraan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <aside style="display:grid; gap:12px;">
            <div style="background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:14px;">
                <h4 style="margin:0 0 10px;">Aksi Verifikasi</h4>

                <form method="POST" action="{{ route('super-admin.rentals.approve', $rentalCompany) }}" style="margin-bottom:10px;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" style="width:100%; padding:10px 12px; border:0; border-radius:10px; background:#16a34a; color:#fff; font-weight:700;">Setujui Rental</button>
                </form>

                <form method="POST" action="{{ route('super-admin.rentals.reject', $rentalCompany) }}">
                    @csrf
                    @method('PATCH')
                    <label for="rejection_note" style="display:block; font-size:13px; color:#475569; margin-bottom:6px;">Alasan Penolakan</label>
                    <textarea name="rejection_note" id="rejection_note" rows="4" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px;">{{ old('rejection_note') }}</textarea>
                    @error('rejection_note')
                        <small style="color:#b91c1c; display:block; margin-top:6px;">{{ $message }}</small>
                    @enderror

                    <button type="submit" style="width:100%; margin-top:10px; padding:10px 12px; border:0; border-radius:10px; background:#dc2626; color:#fff; font-weight:700;">Tolak Rental</button>
                </form>
            </div>

            <div style="background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:14px;">
                <h4 style="margin:0 0 8px;">Audit Verifikasi</h4>
                <p style="margin:0; color:#475569;">Verifier: <strong>{{ $rentalCompany->verifiedBy?->name ?? '-' }}</strong></p>
                <p style="margin:4px 0 0; color:#475569;">Waktu: <strong>{{ $rentalCompany->verified_at?->format('d M Y H:i') ?? '-' }}</strong></p>
            </div>
        </aside>
    </div>
@endsection
