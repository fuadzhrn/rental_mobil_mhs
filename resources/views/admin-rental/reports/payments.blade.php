@extends('layouts.admin')

@section('title', 'Laporan Pembayaran - Admin Rental')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">💳 Laporan Pembayaran Rental Anda</h1>
            <p class="text-muted small mt-2">Data pembayaran dari customer di rental ini</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin-rental.reports.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
        </div>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label small">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" class="form-control form-control-sm"
                        value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label small">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date" class="form-control form-control-sm"
                        value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="payment_status" class="form-label small">Status Pembayaran</label>
                    <select name="payment_status" id="payment_status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        @foreach ($paymentStatuses as $status => $label)
                            <option value="{{ $status }}" @selected(request('payment_status') == $status)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <a href="{{ route('admin-rental.reports.payments') }}" class="btn btn-secondary btn-sm w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <p class="text-muted small mb-1">Verified</p>
                    <h3 class="mb-0">{{ $summary['total_payments_verified'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <p class="text-muted small mb-1">Uploaded</p>
                    <h3 class="mb-0">{{ $summary['total_payments_uploaded'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <p class="text-muted small mb-1">Rejected</p>
                    <h3 class="mb-0">{{ $summary['total_payments_rejected'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <p class="text-muted small mb-1">Total Nominal Verified</p>
                    <h3 class="mb-0 small">Rp {{ number_format($summary['total_nominal_verified'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3">Booking Code</th>
                            <th class="px-3">Customer</th>
                            <th class="px-3">Metode</th>
                            <th class="px-3 text-end">Nominal</th>
                            <th class="px-3">Status</th>
                            <th class="px-3">Terverifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr>
                                <td class="px-3"><small class="font-monospace">{{ $booking->booking_code }}</small></td>
                                <td class="px-3"><small>{{ $booking->customer->name ?? '-' }}</small></td>
                                <td class="px-3"><small>{{ $booking->payment?->payment_method ?? '-' }}</small></td>
                                <td class="px-3 text-end"><strong>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</strong></td>
                                <td class="px-3">
                                    <span class="badge bg-{{ $booking->payment_status === 'verified' ? 'success' : ($booking->payment_status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ $booking->paymentStatusLabel() }}
                                    </span>
                                </td>
                                <td class="px-3"><small>{{ $booking->payment?->verified_at?->format('d M Y') ?? '-' }}</small></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-4 text-center text-muted">
                                    <small>Belum ada data pembayaran</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $bookings->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection
