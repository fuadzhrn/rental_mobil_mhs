@extends('layouts.admin')

@section('title', 'Pendapatan Rental - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">💵 Laporan Pendapatan Rental</h1>
            <p class="text-muted small mt-2">Revenue breakdown per rental dan kalkulasi komisi platform</p>
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
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <a href="{{ route('super-admin.reports.revenue') }}" class="btn btn-secondary btn-sm w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">Total Pendapatan (Gross)</p>
                    <h3 class="mb-0">Rp {{ number_format($totalGross, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">Total Komisi Platform (10%)</p>
                    <h3 class="mb-0">Rp {{ number_format($totalCommission, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="text-muted small mb-1">Total Pendapatan Bersih (Net)</p>
                    <h3 class="mb-0">Rp {{ number_format($totalNet, 0, ',', '.') }}</h3>
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
                            <th class="px-3">Nama Rental</th>
                            <th class="px-3 text-center">Booking Verified</th>
                            <th class="px-3 text-end">Pendapatan Gross</th>
                            <th class="px-3 text-end">Komisi (10%)</th>
                            <th class="px-3 text-end">Pendapatan Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($revenues as $item)
                            <tr>
                                <td class="px-3">
                                    <strong>{{ $item['rental']->company_name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $item['rental']->user?->name ?? '-' }}</small>
                                </td>
                                <td class="px-3 text-center">
                                    <span class="badge bg-info">{{ $item['verified_booking_count'] }}</span>
                                </td>
                                <td class="px-3 text-end">
                                    <strong>Rp {{ number_format($item['gross_revenue'], 0, ',', '.') }}</strong>
                                </td>
                                <td class="px-3 text-end">
                                    <strong class="text-danger">Rp {{ number_format($item['commission'], 0, ',', '.') }}</strong>
                                </td>
                                <td class="px-3 text-end">
                                    <strong class="text-success">Rp {{ number_format($item['net_revenue'], 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-4 text-center text-muted">
                                    <small>Belum ada data pendapatan</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3 alert alert-info alert-sm">
        <small>
            <strong>Catatan:</strong> Komisi platform dihitung 10% dari total pendapatan verified.
            Pendapatan Bersih = Pendapatan Gross - Komisi Platform
        </small>
    </div>
</div>
@endsection
