@extends('layouts.admin')

@section('title', 'Laporan Pendapatan - Admin Rental')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">💵 Laporan Pendapatan Rental Anda</h1>
            <p class="text-muted small mt-2">Detail revenue dan kalkulasi komisi platform untuk rental ini</p>
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
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="{{ route('admin-rental.reports.revenue') }}" class="btn btn-secondary btn-sm w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Revenue Cards -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">{{ $rentalCompany->company_name }}</h5>
                    <small class="text-muted">{{ $rentalCompany->user?->name ?? '-' }}</small>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <p class="text-muted small mb-1">Booking Verified</p>
                            <h4 class="mb-0">{{ $verifiedBookingCount }}</h4>
                        </div>
                        <div class="col-6 text-end">
                            <p class="text-muted small mb-1">Rata-rata per booking</p>
                            <h5 class="mb-0">Rp {{ $verifiedBookingCount > 0 ? number_format($grossRevenue / $verifiedBookingCount, 0, ',', '.') : 0 }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Summary Pendapatan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td>Pendapatan Gross:</td>
                            <td class="text-end"><strong>Rp {{ number_format($grossRevenue, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr class="table-danger">
                            <td>Komisi Platform (10%):</td>
                            <td class="text-end"><strong>Rp {{ number_format($commission, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr class="table-success">
                            <td><strong>Pendapatan Net:</strong></td>
                            <td class="text-end"><strong>Rp {{ number_format($netRevenue, 0, ',', '.') }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Breakdown -->
    <div class="row">
        <div class="col">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Detail Perhitungan</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info alert-sm mb-0">
                        <p><strong>Pendapatan Gross (Gross Revenue):</strong></p>
                        <p class="mb-2">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</p>
                        <small class="text-muted">Total dari semua booking dengan payment_status = verified dalam periode {{ request('start_date') ?: '(Semua waktu)' }} hingga {{ request('end_date') ?: '(Sekarang)' }}</small>
                    </div>

                    <hr class="my-3">

                    <div class="alert alert-warning alert-sm mb-0">
                        <p><strong>Komisi Platform (10%):</strong></p>
                        <p class="mb-2">Rp {{ number_format($commission, 0, ',', '.') }}</p>
                        <small class="text-muted">Dihitung dari: Gross Revenue × 10% = Rp {{ number_format($grossRevenue, 0, ',', '.') }} × 10%</small>
                    </div>

                    <hr class="my-3">

                    <div class="alert alert-success alert-sm mb-0">
                        <p><strong>Pendapatan Net (Net Revenue):</strong></p>
                        <p class="mb-2">Rp {{ number_format($netRevenue, 0, ',', '.') }}</p>
                        <small class="text-muted">Dihitung dari: Gross Revenue - Komisi Platform = Rp {{ number_format($grossRevenue, 0, ',', '.') }} - Rp {{ number_format($commission, 0, ',', '.') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3 alert alert-info alert-sm">
        <small>
            <strong>Catatan:</strong>
            <ul class="mb-0 ps-3 mt-2">
                <li>Komisi platform dihitung 10% dari total pendapatan verified</li>
                <li>Pendapatan Bersih = Pendapatan Gross - Komisi Platform</li>
                <li>Hanya transaksi dengan payment_status = verified yang dihitung</li>
                <li>Periode: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d M Y') : '-' }} hingga {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d M Y') : '-' }}</li>
            </ul>
        </small>
    </div>
</div>
@endsection
