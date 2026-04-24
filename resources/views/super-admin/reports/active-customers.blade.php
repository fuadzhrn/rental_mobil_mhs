@extends('layouts.admin')

@section('title', 'Customer Aktif | Super Admin')
@section('page_title', 'Customer Aktif')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/super-admin-report-pages.css') }}">
@endpush

@section('content')
    @php
        $totalActiveCustomers = $activeCustomers->count();
        $totalCompletedBookings = (int) $activeCustomers->sum('completed_booking_count');
        $totalTransactions = (float) $activeCustomers->sum('total_transaction');
        $loyalCustomers = $activeCustomers->filter(fn($customer) => ((int) ($customer->completed_booking_count ?? 0)) >= $loyalThreshold)->count();
    @endphp

    <div class="report-page">
        <section class="report-header-card">
            <div class="report-header-top">
                <div>
                    <h2>Customer Aktif</h2>
                    <p>Evaluasi performa customer berdasarkan aktivitas booking dan nilai transaksi.</p>
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
            <form method="GET" action="{{ route('super-admin.reports.active-customers') }}" class="report-filter-grid is-compact">
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
                    <a href="{{ route('super-admin.reports.active-customers') }}" class="report-btn-secondary">
                        <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                        <span>Reset</span>
                    </a>
                </div>
            </form>
        </section>

        <section class="report-stat-grid">
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-people" aria-hidden="true"></i></div>
                <div>
                    <p>Total Customer Aktif</p>
                    <h3>{{ number_format($totalActiveCustomers, 0, ',', '.') }}</h3>
                </div>
            </article>
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-check2-all" aria-hidden="true"></i></div>
                <div>
                    <p>Total Booking Completed</p>
                    <h3>{{ number_format($totalCompletedBookings, 0, ',', '.') }}</h3>
                </div>
            </article>
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-cash-stack" aria-hidden="true"></i></div>
                <div>
                    <p>Total Transaksi</p>
                    <h3>Rp {{ number_format($totalTransactions, 0, ',', '.') }}</h3>
                </div>
            </article>
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-award" aria-hidden="true"></i></div>
                <div>
                    <p>Loyal Customer</p>
                    <h3>{{ number_format($loyalCustomers, 0, ',', '.') }}</h3>
                    <small>Min. {{ $loyalThreshold }} booking completed</small>
                </div>
            </article>
        </section>

        <section class="report-table-card">
            <div class="report-table-head">
                <h3>Data Customer Aktif</h3>
                <p>Loyal customer ditentukan berdasarkan ambang minimal booking selesai.</p>
            </div>

            @if ($activeCustomers->count() > 0)
                <div class="report-table-wrap">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th class="is-center">No</th>
                                <th>Nama Customer</th>
                                <th>Email</th>
                                <th>Nomor HP</th>
                                <th class="is-center">Total Booking</th>
                                <th class="is-center">Booking Completed</th>
                                <th class="is-number">Total Transaksi</th>
                                <th>Last Booking Date</th>
                                <th>Status Loyal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activeCustomers as $index => $customer)
                                @php
                                    $isLoyal = ((int) ($customer->completed_booking_count ?? 0)) >= $loyalThreshold;
                                    $lastBookingDateText = '-';
                                    if ($customer->last_booking_date instanceof \Carbon\CarbonInterface) {
                                        $lastBookingDateText = $customer->last_booking_date->format('d M Y H:i');
                                    } elseif (!empty($customer->last_booking_date)) {
                                        $lastBookingDateText = \Illuminate\Support\Carbon::parse($customer->last_booking_date)->format('d M Y H:i');
                                    }
                                @endphp
                                <tr>
                                    <td class="is-center">{{ $index + 1 }}</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone ?? '-' }}</td>
                                    <td class="is-center">{{ number_format((int) ($customer->total_booking_count ?? 0), 0, ',', '.') }}</td>
                                    <td class="is-center">{{ number_format((int) ($customer->completed_booking_count ?? 0), 0, ',', '.') }}</td>
                                    <td class="is-number">Rp {{ number_format((float) ($customer->total_transaction ?? 0), 0, ',', '.') }}</td>
                                    <td>{{ $lastBookingDateText }}</td>
                                    <td>
                                        @if ($isLoyal)
                                            <span class="report-badge is-accent">Loyal</span>
                                        @else
                                            <span class="report-badge is-muted">Regular</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="report-empty-state">
                    <i class="bi bi-inbox" aria-hidden="true"></i>
                    <h4>Belum ada data customer aktif</h4>
                    <p>Belum ada data laporan untuk filter yang dipilih.</p>
                </div>
            @endif
        </section>
    </div>
@endsection
