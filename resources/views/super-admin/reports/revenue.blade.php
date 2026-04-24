@extends('layouts.admin')

@section('title', 'Pendapatan Rental | Super Admin')
@section('page_title', 'Pendapatan Rental')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/super-admin-report-pages.css') }}">
@endpush

@section('content')
    <div class="report-page">
        <section class="report-header-card">
            <div class="report-header-top">
                <div>
                    <h2>Pendapatan Rental</h2>
                    <p>Ringkasan pendapatan kotor, komisi platform, dan estimasi pendapatan bersih setiap rental.</p>
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
            <form method="GET" action="{{ route('super-admin.reports.revenue') }}" class="report-filter-grid is-compact">
                <div class="report-filter-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="report-filter-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="report-filter-actions">
                    <button type="submit" class="report-btn-primary">
                        <i class="bi bi-funnel-fill" aria-hidden="true"></i>
                        <span>Terapkan Filter</span>
                    </button>
                    <a href="{{ route('super-admin.reports.revenue') }}" class="report-btn-secondary">
                        <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                        <span>Reset</span>
                    </a>
                </div>
            </form>
        </section>

        <section class="report-stat-grid">
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-cash-stack" aria-hidden="true"></i></div>
                <div>
                    <p>Total Pendapatan Kotor</p>
                    <h3>Rp {{ number_format((float) $totalGross, 0, ',', '.') }}</h3>
                </div>
            </article>
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-percent" aria-hidden="true"></i></div>
                <div>
                    <p>Total Komisi Platform</p>
                    <h3>Rp {{ number_format((float) $totalCommission, 0, ',', '.') }}</h3>
                </div>
            </article>
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-wallet2" aria-hidden="true"></i></div>
                <div>
                    <p>Estimasi Pendapatan Bersih</p>
                    <h3>Rp {{ number_format((float) $totalNet, 0, ',', '.') }}</h3>
                </div>
            </article>
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-building" aria-hidden="true"></i></div>
                <div>
                    <p>Total Rental Aktif</p>
                    <h3>{{ number_format($revenues->count(), 0, ',', '.') }}</h3>
                </div>
            </article>
        </section>

        <section class="report-table-card">
            <div class="report-table-head">
                <h3>Data Pendapatan Rental</h3>
                <p>Semua nominal dihitung dari transaksi terverifikasi pada rentang tanggal yang dipilih.</p>
            </div>

            @if ($revenues->count() > 0)
                <div class="report-table-wrap">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th class="is-center">No</th>
                                <th>Nama Rental</th>
                                <th class="is-center">Total Booking Verified</th>
                                <th class="is-number">Pendapatan Kotor</th>
                                <th class="is-number">Komisi Platform</th>
                                <th class="is-number">Estimasi Pendapatan Bersih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($revenues as $index => $item)
                                <tr>
                                    <td class="is-center">{{ $index + 1 }}</td>
                                    <td>
                                        {{ $item['rental']->company_name }}
                                        <br>
                                        <small>{{ $item['rental']->user?->name ?? '-' }}</small>
                                    </td>
                                    <td class="is-center">{{ number_format((int) ($item['verified_booking_count'] ?? 0), 0, ',', '.') }}</td>
                                    <td class="is-number">Rp {{ number_format((float) ($item['gross_revenue'] ?? 0), 0, ',', '.') }}</td>
                                    <td class="is-number">Rp {{ number_format((float) ($item['commission'] ?? 0), 0, ',', '.') }}</td>
                                    <td class="is-number is-primary-value">Rp {{ number_format((float) ($item['net_revenue'] ?? 0), 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="report-empty-state">
                    <i class="bi bi-inbox" aria-hidden="true"></i>
                    <h4>Belum ada data pendapatan rental</h4>
                    <p>Belum ada data laporan untuk filter yang dipilih.</p>
                </div>
            @endif
        </section>

        <section class="report-note-card">
            <strong>Catatan:</strong> Estimasi pendapatan bersih dihitung menggunakan formula pendapatan kotor dikurangi komisi platform.
        </section>
    </div>
@endsection
