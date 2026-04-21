@extends('layouts.admin')

@section('title', 'Data Ulasan Customer')
@section('page_title', 'Data Ulasan Customer')

@section('content')
    <p class="page-description">
        Daftar ulasan customer untuk kendaraan milik {{ $rentalCompany->company_name }}.
    </p>

    <div class="table-card" style="margin-top:20px; overflow:auto; background:#fff; border-radius:18px; padding:18px;">
        <table class="admin-table" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th align="left">Tanggal</th>
                    <th align="left">Kendaraan</th>
                    <th align="left">Customer</th>
                    <th align="left">Booking</th>
                    <th align="left">Rating</th>
                    <th align="left">Ulasan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reviews as $review)
                    <tr>
                        <td>{{ $review->created_at->format('d M Y H:i') }}</td>
                        <td>{{ $review->vehicle?->name ?? '-' }}</td>
                        <td>{{ $review->customer?->name ?? '-' }}</td>
                        <td>{{ $review->booking?->booking_code ?? '-' }}</td>
                        <td>{{ $review->rating }} / 5</td>
                        <td>{{ \Illuminate\Support\Str::limit($review->review ?? 'Tanpa komentar.', 90) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:24px; color:#6b7280;">Belum ada ulasan customer untuk rental Anda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:18px;">{{ $reviews->links() }}</div>
@endsection
