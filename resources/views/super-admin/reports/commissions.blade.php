@extends('layouts.admin')

@section('title', 'Komisi Platform - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">🔢 Laporan Komisi Platform</h1>
            <p class="text-muted small mt-2">Detail komisi platform dari seluruh transaksi verified</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('super-admin.reports.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
        </div>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <label for="start_date" class="form-label small">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" class="form-control form-control-sm"
                        value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label for="end_date" class="form-label small">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date" class="form-control form-control-sm"
                        value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <label for="rental_id" class="form-label small">Rental</label>
                    <select name="rental_id" id="rental_id" class="form-select form-select-sm">
                        <option value="">Semua Rental</option>
                        @foreach ($rentalCompanies as $rental)
                            <option value="{{ $rental->id }}" @selected(request('rental_id') == $rental->id)>
                                {{ $rental->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <a href="{{ route('super-admin.reports.commissions') }}" class="btn btn-secondary btn-sm w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <p class="text-muted small mb-1">Total Transaksi</p>
                    <h3 class="mb-0">{{ $summary['total_transactions'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <p class="text-muted small mb-1">Gross Revenue</p>
                    <h3 class="mb-0 small">Rp {{ number_format($summary['total_gross_revenue'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <p class="text-muted small mb-1">Total Komisi (10%)</p>
                    <h3 class="mb-0 small">Rp {{ number_format($summary['total_commission'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <p class="text-muted small mb-1">Avg per Booking</p>
                    <h3 class="mb-0 small">Rp {{ number_format($summary['avg_commission_per_booking'], 0, ',', '.') }}</h3>
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
                            <th class="px-3">Rental</th>
                            <th class="px-3">Customer</th>
                            <th class="px-3 text-end">Amount</th>
                            <th class="px-3 text-end">Komisi (10%)</th>
                            <th class="px-3">Status</th>
                            <th class="px-3">Booking Status</th>
                            <th class="px-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($commissions as $booking)
                            <tr>
                                <td class="px-3"><small class="font-monospace">{{ $booking->booking_code }}</small></td>
                                <td class="px-3"><small>{{ $booking->rentalCompany->company_name ?? '-' }}</small></td>
                                <td class="px-3"><small>{{ $booking->customer->name ?? '-' }}</small></td>
                                <td class="px-3 text-end"><strong>Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</strong></td>
                                <td class="px-3 text-end">
                                    <strong class="text-danger">Rp {{ number_format($booking->commission_amount, 0, ',', '.') }}</strong>
                                </td>
                                <td class="px-3">
                                    <span class="badge bg-success">{{ $booking->paymentStatusLabel() }}</span>
                                </td>
                                <td class="px-3">
                                    <span class="badge bg-info">{{ $booking->bookingStatusLabel() }}</span>
                                </td>
                                <td class="px-3"><small>{{ $booking->created_at->format('d M Y') }}</small></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-4 text-center text-muted">
                                    <small>Belum ada data komisi</small>
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
        {{ $commissions->links('pagination::bootstrap-4') }}
    </div>

    <div class="mt-3 alert alert-info alert-sm">
        <small>
            <strong>Komisi Platform:</strong> Dihitung 10% dari nominal transaksi dengan payment_status = verified.
            Hanya transaksi yang terverifikasi yang masuk dalam perhitungan komisi.
        </small>
    </div>
</div>
@endsection
