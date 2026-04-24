@extends('layouts.admin')

@section('title', 'Kendaraan Terlaris | Super Admin')
@section('page_title', 'Kendaraan Terlaris')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/super-admin-report-pages.css') }}">
@endpush

@section('content')
    @php
        $topBookedVehicle = $topVehicles->first();
        $totalValidBookings = (int) $topVehicles->sum('verified_booking_count');
        $totalVehicleRevenue = (float) $topVehicles->sum(fn($vehicle) => (float) ($vehicle->total_revenue ?? 0));
    @endphp

    <div class="report-page">
        <section class="report-header-card">
            <div class="report-header-top">
                <div>
                    <h2>Kendaraan Terlaris</h2>
                    <p>Laporan ranking kendaraan berdasarkan booking valid dan total pendapatan.</p>
                </div>
                <a href="{{ route('super-admin.reports.index') }}" class="report-back-link">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    <span>Kembali ke Laporan</span>
                </a>
            </div>
        </section>

        @if ($errors->any())
            <section class="report-inline-alert is-danger" role="alert">
                <i class="bi bi-exclamation-octagon" aria-hidden="true"></i>
                <div>
                    <strong>Filter tidak valid</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </section>
        @endif

        <section class="report-filter-card">
            <form method="GET" action="{{ route('super-admin.reports.top-vehicles') }}" class="report-filter-grid is-compact">
                <div class="report-filter-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="report-filter-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="report-filter-group">
                    <label for="limit">Jumlah Data</label>
                    <select id="limit" name="limit">
                        <option value="20" @selected((int) request('limit', 20) === 20)>20</option>
                        <option value="50" @selected((int) request('limit', 20) === 50)>50</option>
                        <option value="100" @selected((int) request('limit', 20) === 100)>100</option>
                    </select>
                </div>
                <div class="report-filter-actions">
                    <button type="submit" class="report-btn-primary">
                        <i class="bi bi-funnel-fill" aria-hidden="true"></i>
                        <span>Terapkan Filter</span>
                    </button>
                    <a href="{{ route('super-admin.reports.top-vehicles') }}" class="report-btn-secondary">
                        <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                        <span>Reset</span>
                    </a>
                </div>
            </form>
        </section>

        <section class="report-stat-grid">
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-car-front" aria-hidden="true"></i></div>
                <div>
                    <p>Total Kendaraan Tercatat</p>
                    <h3>{{ number_format($topVehicles->count(), 0, ',', '.') }}</h3>
                    <small>sesuai hasil filter</small>
                </div>
            </article>
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-trophy" aria-hidden="true"></i></div>
                <div>
                    <p>Top Booked Vehicle</p>
                    <h3>{{ $topBookedVehicle?->name ?? '-' }}</h3>
                    <small>{{ $topBookedVehicle?->rentalCompany?->company_name ?? '-' }}</small>
                </div>
            </article>
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-clipboard2-check" aria-hidden="true"></i></div>
                <div>
                    <p>Total Booking Valid</p>
                    <h3>{{ number_format($totalValidBookings, 0, ',', '.') }}</h3>
                </div>
            </article>
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-currency-dollar" aria-hidden="true"></i></div>
                <div>
                    <p>Total Pendapatan Kendaraan</p>
                    <h3>Rp {{ number_format($totalVehicleRevenue, 0, ',', '.') }}</h3>
                </div>
            </article>
        </section>

        <section class="report-table-card">
            <div class="report-table-head">
                <h3>Data Kendaraan Terlaris</h3>
                <p>Bandingkan performa kendaraan antar rental berdasarkan booking valid dan revenue.</p>
            </div>

            @if ($topVehicles->count() > 0)
                <div class="report-table-wrap">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th class="is-center">No</th>
                                <th>Nama Kendaraan</th>
                                <th>Rental</th>
                                <th>Brand</th>
                                <th>Kategori</th>
                                <th class="is-center">Total Booking Valid</th>
                                <th class="is-number">Total Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topVehicles as $index => $vehicle)
                                <tr>
                                    <td class="is-center">{{ $index + 1 }}</td>
                                    <td>{{ $vehicle->name }}</td>
                                    <td>{{ $vehicle->rentalCompany?->company_name ?? '-' }}</td>
                                    <td>{{ $vehicle->brand ?? '-' }}</td>
                                    <td>{{ $vehicle->category ?? '-' }}</td>
                                    <td class="is-center">{{ number_format((int) ($vehicle->verified_booking_count ?? 0), 0, ',', '.') }}</td>
                                    <td class="is-number">Rp {{ number_format((float) ($vehicle->total_revenue ?? 0), 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="report-empty-state">
                    <i class="bi bi-inbox" aria-hidden="true"></i>
                    <h4>Belum ada data kendaraan terlaris</h4>
                    <p>Belum ada data laporan untuk filter yang dipilih.</p>
                </div>
            @endif
        </section>
    </div>
@endsection
