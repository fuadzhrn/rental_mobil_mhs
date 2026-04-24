@extends('layouts.admin')

@section('title', 'Komisi Platform | Super Admin')
@section('page_title', 'Komisi Platform')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/super-admin-report-pages.css') }}">
@endpush

@section('content')
    @php
        $paymentStatusBadge = function (?string $status): string {
            return match (strtolower((string) $status)) {
                'verified' => 'is-success',
                'uploaded' => 'is-info',
                'rejected' => 'is-danger',
                'unpaid' => 'is-muted',
                default => 'is-muted',
            };
        };

        $bookingStatusBadge = function (?string $status): string {
            return match (strtolower((string) $status)) {
                'confirmed', 'ongoing' => 'is-info',
                'completed' => 'is-success',
                'waiting_payment', 'waiting_verification' => 'is-warning',
                'cancelled' => 'is-danger',
                default => 'is-muted',
            };
        };

        $totalContributingRentals = $commissions->getCollection()->pluck('rental_company_id')->filter()->unique()->count();
    @endphp

    <div class="report-page">
        <section class="report-header-card">
            <div class="report-header-top">
                <div>
                    <h2>Komisi Platform</h2>
                    <p>Ringkasan komisi platform dari transaksi valid beserta detail setiap booking.</p>
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
            <form method="GET" action="{{ route('super-admin.reports.commissions') }}" class="report-filter-grid is-compact">
                <div class="report-filter-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="report-filter-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="report-filter-group">
                    <label for="rental_id">Rental</label>
                    <select id="rental_id" name="rental_id">
                        <option value="">Semua Rental</option>
                        @foreach ($rentalCompanies as $rental)
                            <option value="{{ $rental->id }}" @selected((string) request('rental_id') === (string) $rental->id)>
                                {{ $rental->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="report-filter-actions">
                    <button type="submit" class="report-btn-primary">
                        <i class="bi bi-funnel-fill" aria-hidden="true"></i>
                        <span>Terapkan Filter</span>
                    </button>
                    <a href="{{ route('super-admin.reports.commissions') }}" class="report-btn-secondary">
                        <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                        <span>Reset</span>
                    </a>
                </div>
            </form>
        </section>

        <section class="report-stat-grid">
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-cash-coin" aria-hidden="true"></i></div>
                <div>
                    <p>Total Komisi Platform</p>
                    <h3>Rp {{ number_format((float) ($summary['total_commission'] ?? 0), 0, ',', '.') }}</h3>
                </div>
            </article>
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-receipt-cutoff" aria-hidden="true"></i></div>
                <div>
                    <p>Total Transaksi Valid</p>
                    <h3>{{ number_format((int) ($summary['total_transactions'] ?? 0), 0, ',', '.') }}</h3>
                </div>
            </article>
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-building" aria-hidden="true"></i></div>
                <div>
                    <p>Total Rental Berkontribusi</p>
                    <h3>{{ number_format($totalContributingRentals, 0, ',', '.') }}</h3>
                    <small>berdasarkan data halaman ini</small>
                </div>
            </article>
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-calculator" aria-hidden="true"></i></div>
                <div>
                    <p>Rata-rata Komisi per Transaksi</p>
                    <h3>Rp {{ number_format((float) ($summary['avg_commission_per_booking'] ?? 0), 0, ',', '.') }}</h3>
                </div>
            </article>
        </section>

        <section class="report-table-card">
            <div class="report-table-head">
                <h3>Data Komisi</h3>
                <p>Nilai komisi ditampilkan lebih menonjol agar proses monitoring dan validasi lebih cepat.</p>
            </div>

            @if ($commissions->count() > 0)
                <div class="report-table-wrap">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th class="is-center">No</th>
                                <th>Booking Code</th>
                                <th>Rental</th>
                                <th>Customer</th>
                                <th class="is-number">Total Booking</th>
                                <th class="is-number">Nilai Komisi</th>
                                <th>Payment Status</th>
                                <th>Booking Status</th>
                                <th>Tanggal Transaksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($commissions as $index => $booking)
                                @php
                                    $transactionDate = $booking->payment?->verified_at ?? $booking->payment?->paid_at ?? $booking->created_at;
                                @endphp
                                <tr>
                                    <td class="is-center">{{ ($commissions->firstItem() ?? 1) + $index }}</td>
                                    <td class="is-code">{{ $booking->booking_code }}</td>
                                    <td>{{ $booking->rentalCompany?->company_name ?? '-' }}</td>
                                    <td>{{ $booking->customer_name ?: ($booking->customer?->name ?? '-') }}</td>
                                    <td class="is-number">Rp {{ number_format((float) $booking->total_amount, 0, ',', '.') }}</td>
                                    <td class="is-number is-primary-value">Rp {{ number_format((float) ($booking->commission_amount ?? 0), 0, ',', '.') }}</td>
                                    <td>
                                        <span class="report-badge {{ $paymentStatusBadge($booking->payment_status) }}">
                                            {{ $booking->paymentStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="report-badge {{ $bookingStatusBadge($booking->booking_status) }}">
                                            {{ $booking->bookingStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>{{ optional($transactionDate)->format('d M Y H:i') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="report-pagination-wrap">
                    {{ $commissions->links() }}
                </div>
            @else
                <div class="report-empty-state">
                    <i class="bi bi-inbox" aria-hidden="true"></i>
                    <h4>Belum ada data komisi platform</h4>
                    <p>Belum ada data laporan untuk filter yang dipilih.</p>
                </div>
            @endif
        </section>
    </div>
@endsection
