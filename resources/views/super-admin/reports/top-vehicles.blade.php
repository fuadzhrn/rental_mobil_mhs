@extends('layouts.admin')

@section('title', 'Kendaraan Terlaris - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">🏆 Kendaraan Terlaris Platform</h1>
            <p class="text-muted small mt-2">Ranking kendaraan berdasarkan jumlah booking verified</p>
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
                    <label for="limit" class="form-label small">Limit</label>
                    <select name="limit" id="limit" class="form-select form-select-sm">
                        <option value="20" @selected(request('limit', 20) == 20)>20</option>
                        <option value="50" @selected(request('limit', 20) == 50)>50</option>
                        <option value="100" @selected(request('limit', 20) == 100)>100</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <a href="{{ route('super-admin.reports.top-vehicles') }}" class="btn btn-secondary btn-sm w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3 text-center" style="width: 60px">#</th>
                            <th class="px-3">Nama Kendaraan</th>
                            <th class="px-3">Brand</th>
                            <th class="px-3">Rental</th>
                            <th class="px-3">Category</th>
                            <th class="px-3 text-center">Total Booking</th>
                            <th class="px-3 text-end">Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topVehicles as $index => $vehicle)
                            <tr>
                                <td class="px-3 text-center">
                                    <strong>{{ $index + 1 }}</strong>
                                </td>
                                <td class="px-3"><strong>{{ $vehicle->name }}</strong></td>
                                <td class="px-3"><small>{{ $vehicle->brand ?? '-' }}</small></td>
                                <td class="px-3"><small>{{ $vehicle->rentalCompany->company_name ?? '-' }}</small></td>
                                <td class="px-3"><small>{{ $vehicle->category ?? '-' }}</small></td>
                                <td class="px-3 text-center">
                                    <span class="badge bg-info">{{ $vehicle->verified_booking_count ?? 0 }}</span>
                                </td>
                                <td class="px-3 text-end">
                                    <strong>Rp {{ number_format($vehicle->total_revenue ?? 0, 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-4 text-center text-muted">
                                    <small>Belum ada data kendaraan</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
