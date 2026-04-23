@extends('layouts.admin')

@section('title', 'Customer Aktif - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">⭐ Customer Aktif Platform</h1>
            <p class="text-muted small mt-2">Customer ranking berdasarkan jumlah booking completed</p>
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
                    <a href="{{ route('super-admin.reports.active-customers') }}" class="btn btn-secondary btn-sm w-100">Reset</a>
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
                            <th class="px-3">Nama Customer</th>
                            <th class="px-3">Email</th>
                            <th class="px-3">Phone</th>
                            <th class="px-3 text-center">Total Booking</th>
                            <th class="px-3 text-center">Completed</th>
                            <th class="px-3 text-end">Total Transaksi</th>
                            <th class="px-3">Last Booking</th>
                            <th class="px-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activeCustomers as $index => $customer)
                            <tr>
                                <td class="px-3 text-center">
                                    <strong>{{ $index + 1 }}</strong>
                                </td>
                                <td class="px-3"><strong>{{ $customer->name }}</strong></td>
                                <td class="px-3"><small>{{ $customer->email }}</small></td>
                                <td class="px-3"><small>{{ $customer->phone ?? '-' }}</small></td>
                                <td class="px-3 text-center">
                                    <span class="badge bg-info">{{ $customer->total_booking_count ?? 0 }}</span>
                                </td>
                                <td class="px-3 text-center">
                                    <span class="badge bg-success">{{ $customer->completed_booking_count ?? 0 }}</span>
                                </td>
                                <td class="px-3 text-end">
                                    <strong>Rp {{ number_format($customer->total_transaction ?? 0, 0, ',', '.') }}</strong>
                                </td>
                                <td class="px-3">
                                    <small>{{ $customer->last_booking_date?->format('d M Y') ?? '-' }}</small>
                                </td>
                                <td class="px-3">
                                    @if (($customer->completed_booking_count ?? 0) >= $loyalThreshold)
                                        <span class="badge bg-warning">🏅 Loyal</span>
                                    @else
                                        <span class="badge bg-secondary">Regular</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-3 py-4 text-center text-muted">
                                    <small>Belum ada data customer aktif</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-2 text-muted small">
        <p>🏅 <strong>Loyal Customer:</strong> Customer dengan minimal {{ $loyalThreshold }} booking completed</p>
    </div>
</div>
@endsection
