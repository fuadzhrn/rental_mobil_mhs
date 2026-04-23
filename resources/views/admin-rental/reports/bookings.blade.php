@extends('layouts.admin')

@section('title', 'Laporan Booking - Admin Rental')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">📋 Laporan Booking Rental Anda</h1>
            <p class="text-muted small mt-2">Data booking dari customer di rental ini</p>
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
                    <label for="booking_status" class="form-label small">Status Booking</label>
                    <select name="booking_status" id="booking_status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        @foreach ($bookingStatuses as $status => $label)
                            <option value="{{ $status }}" @selected(request('booking_status') == $status)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <a href="{{ route('admin-rental.reports.bookings') }}" class="btn btn-secondary btn-sm w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <p class="text-muted small mb-1">Total Booking</p>
                    <h3 class="mb-0">{{ $summary['total_bookings'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <p class="text-muted small mb-1">Completed</p>
                    <h3 class="mb-0">{{ $summary['total_completed'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <p class="text-muted small mb-1">Cancelled</p>
                    <h3 class="mb-0">{{ $summary['total_cancelled'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <p class="text-muted small mb-1">Ongoing</p>
                    <h3 class="mb-0">{{ $summary['total_ongoing'] }}</h3>
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
                            <th class="px-3">Kendaraan</th>
                            <th class="px-3 text-center">Pickup</th>
                            <th class="px-3 text-center">Return</th>
                            <th class="px-3 text-end">Total</th>
                            <th class="px-3">Status</th>
                            <th class="px-3">Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr>
                                <td class="px-3"><small class="font-monospace">{{ $booking->booking_code }}</small></td>
                                <td class="px-3"><small>{{ $booking->customer->name ?? '-' }}</small></td>
                                <td class="px-3"><small>{{ $booking->vehicle->name ?? '-' }}</small></td>
                                <td class="px-3 text-center"><small>{{ $booking->pickup_date->format('d M Y') }}</small></td>
                                <td class="px-3 text-center"><small>{{ $booking->return_date->format('d M Y') }}</small></td>
                                <td class="px-3 text-end"><strong>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</strong></td>
                                <td class="px-3">
                                    <span class="badge bg-info">{{ $booking->bookingStatusLabel() }}</span>
                                </td>
                                <td class="px-3">
                                    <span class="badge bg-secondary">{{ $booking->paymentStatusLabel() }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-4 text-center text-muted">
                                    <small>Belum ada data booking</small>
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
