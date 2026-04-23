@extends('layouts.admin')

@section('title', 'Laporan Ringkas - Admin Rental')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">📊 Laporan Ringkas Rental Anda</h1>
            <p class="text-muted small mt-2">Dashboard monitoring dan akses cepat ke laporan detail rental</p>
        </div>
    </div>

    <!-- Filter Tanggal -->
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
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('admin-rental.reports.index') }}" class="btn btn-secondary btn-sm w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Total Booking -->
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total Booking</p>
                            <h3 class="mb-0">{{ $totalBookings }}</h3>
                        </div>
                        <div class="ms-auto">
                            <span class="badge bg-primary" style="font-size: 24px;">📦</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Verified -->
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Pembayaran Terverifikasi</p>
                            <h3 class="mb-0">{{ $totalPaymentsVerified }}</h3>
                        </div>
                        <div class="ms-auto">
                            <span class="badge bg-success" style="font-size: 24px;">✓</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Vehicles -->
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total Kendaraan</p>
                            <h3 class="mb-0">{{ $totalVehicles }}</h3>
                        </div>
                        <div class="ms-auto">
                            <span class="badge bg-info" style="font-size: 24px;">🚗</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total Pendapatan</p>
                            <h3 class="mb-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                        </div>
                        <div class="ms-auto">
                            <span class="badge bg-warning" style="font-size: 24px;">💰</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Commission -->
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Komisi Platform (10%)</p>
                            <h3 class="mb-0">Rp {{ number_format($totalCommission, 0, ',', '.') }}</h3>
                        </div>
                        <div class="ms-auto">
                            <span class="badge bg-danger" style="font-size: 24px;">📈</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Active Customers -->
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Customer Aktif</p>
                            <h3 class="mb-0">{{ $totalActiveCustomers }}</h3>
                        </div>
                        <div class="ms-auto">
                            <span class="badge bg-secondary" style="font-size: 24px;">👥</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links Section -->
    <div class="row">
        <div class="col">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="card-title mb-0">⚡ Akses Cepat ke Laporan</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <a href="{{ route('admin-rental.reports.bookings') }}" class="btn btn-outline-primary w-100">
                                📋 Laporan Booking
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin-rental.reports.payments') }}" class="btn btn-outline-success w-100">
                                💳 Laporan Pembayaran
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin-rental.reports.top-vehicles') }}" class="btn btn-outline-info w-100">
                                🏆 Kendaraan Terlaris
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin-rental.reports.active-customers') }}" class="btn btn-outline-warning w-100">
                                ⭐ Customer Aktif
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin-rental.reports.revenue') }}" class="btn btn-outline-danger w-100">
                                💵 Laporan Pendapatan
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin-rental.dashboard') }}" class="btn btn-outline-secondary w-100">
                                ← Kembali Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
