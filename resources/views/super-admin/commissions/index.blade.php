@extends('layouts.admin')

@section('title', 'Komisi Platform | Super Admin')
@section('page_title', 'Komisi Platform')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/super-admin-report-pages.css') }}">
@endpush

@section('content')
    @php
        $totalValidTransactions = method_exists($bookings, 'total') ? (int) $bookings->total() : (int) $bookings->count();
        $avgCommissionPerTransaction = $totalValidTransactions > 0 ? ($totalCommission / $totalValidTransactions) : 0;
        $pageContributingRentals = $bookings->getCollection()->pluck('rental_company_id')->filter()->unique()->count();

        $bookingStatusBadge = function (?string $status): string {
            return match (strtolower((string) $status)) {
                'confirmed', 'ongoing' => 'is-info',
                'completed' => 'is-success',
                'waiting_payment', 'waiting_verification' => 'is-warning',
                'cancelled' => 'is-danger',
                default => 'is-muted',
            };
        };

        $paymentStatusBadge = function (?string $status): string {
            return match (strtolower((string) $status)) {
                'verified' => 'is-success',
                'uploaded' => 'is-info',
                'unpaid' => 'is-muted',
                'rejected' => 'is-danger',
                default => 'is-muted',
            };
        };

        $prettyLabel = function (?string $raw): string {
            $normalized = str_replace('_', ' ', strtolower((string) $raw));
            return ucwords($normalized === '' ? '-' : $normalized);
        };
    @endphp

    <div class="report-page">
        <section class="report-header-card">
            <div class="report-header-top">
                <div>
                    <h2>Komisi Platform</h2>
                    <p>Pantau komisi platform dari transaksi rental yang valid dan telah terverifikasi.</p>
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
                    <strong>Terjadi kesalahan input filter</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </section>
        @endif

        <section class="report-filter-card">
            <form method="GET" action="{{ route('super-admin.reports.commissions') }}" class="report-filter-grid">
                <div class="report-filter-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>

                <div class="report-filter-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>

                <div class="report-filter-group">
                    <label for="rental_company_id">Rental</label>
                    <select id="rental_company_id" name="rental_company_id">
                        <option value="">Semua Rental</option>
                        @foreach ($rentalOptions as $rentalOption)
                            <option value="{{ $rentalOption->id }}" @selected((string) request('rental_company_id') === (string) $rentalOption->id)>
                                {{ $rentalOption->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="report-filter-group">
                    <label for="payment_status">Payment Status</label>
                    <select id="payment_status" name="payment_status">
                        <option value="">Semua Status</option>
                        <option value="unpaid" @selected((string) request('payment_status') === 'unpaid')>Unpaid</option>
                        <option value="uploaded" @selected((string) request('payment_status') === 'uploaded')>Uploaded</option>
                        <option value="verified" @selected((string) request('payment_status') === 'verified')>Verified</option>
                        <option value="rejected" @selected((string) request('payment_status') === 'rejected')>Rejected</option>
                    </select>
                </div>

                <div class="report-filter-group">
                    <label for="booking_status">Booking Status</label>
                    <select id="booking_status" name="booking_status">
                        <option value="">Semua Status</option>
                        @foreach ($bookingStatuses as $key => $label)
                            <option value="{{ $key }}" @selected((string) request('booking_status') === (string) $key)>
                                {{ $label }}
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
                        <span>Reset Filter</span>
                    </a>
                </div>
            </form>
        </section>

        <section class="report-stat-grid">
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-cash-coin" aria-hidden="true"></i></div>
                <div>
                    <p>Total Komisi Platform</p>
                    <h3>Rp {{ number_format($totalCommission, 0, ',', '.') }}</h3>
                </div>
            </article>

            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-receipt-cutoff" aria-hidden="true"></i></div>
                <div>
                    <p>Total Transaksi Valid</p>
                    <h3>{{ number_format($totalValidTransactions, 0, ',', '.') }}</h3>
                </div>
            </article>

            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-building" aria-hidden="true"></i></div>
                <div>
                    <p>Total Rental Berkontribusi</p>
                    <h3>{{ number_format($pageContributingRentals, 0, ',', '.') }}</h3>
                    <small>(halaman ini)</small>
                </div>
            </article>

            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-calculator" aria-hidden="true"></i></div>
                <div>
                    <p>Rata-rata Komisi per Transaksi</p>
                    <h3>Rp {{ number_format($avgCommissionPerTransaction, 0, ',', '.') }}</h3>
                </div>
            </article>
        </section>

        <section class="report-actions-row">
            <a href="{{ route('super-admin.reports.index') }}" class="report-btn-light">
                <i class="bi bi-bar-chart-line" aria-hidden="true"></i>
                <span>Kembali ke Laporan Ringkas</span>
            </a>
            <a href="{{ route('super-admin.reports.bookings') }}" class="report-btn-light">
                <i class="bi bi-journal-text" aria-hidden="true"></i>
                <span>Lihat Laporan Booking</span>
            </a>
        </section>

        <section class="report-table-card">
            <div class="report-table-head">
                <h3>Data Komisi Platform</h3>
                <p>Komisi dihitung dari transaksi dengan payment verified dan booking valid.</p>
            </div>

            @if ($bookings->count() > 0)
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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $index => $booking)
                                @php
                                    $transactionDate = $booking->payment?->verified_at ?? $booking->payment?->paid_at ?? $booking->created_at;
                                    $commissionAmount = $booking->total_amount * ($commissionRate / 100);
                                @endphp
                                <tr>
                                    <td class="is-center">{{ ($bookings->firstItem() ?? 1) + $index }}</td>
                                    <td class="is-code">{{ $booking->booking_code }}</td>
                                    <td>{{ $booking->rentalCompany?->company_name ?? '-' }}</td>
                                    <td>{{ $booking->customer_name ?: ($booking->customer?->name ?? '-') }}</td>
                                    <td class="is-number">Rp {{ number_format((float) $booking->total_amount, 0, ',', '.') }}</td>
                                    <td class="is-number is-primary-value">Rp {{ number_format((float) $commissionAmount, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="report-badge {{ $paymentStatusBadge($booking->payment_status) }}">
                                            {{ $prettyLabel($booking->payment_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="report-badge {{ $bookingStatusBadge($booking->booking_status) }}">
                                            {{ $prettyLabel($booking->booking_status) }}
                                        </span>
                                    </td>
                                    <td>{{ optional($transactionDate)->format('d M Y H:i') ?? '-' }}</td>
                                    <td class="is-center">
                                        @if (Route::has('super-admin.reports.bookings'))
                                            <a href="{{ route('super-admin.reports.bookings', ['search' => $booking->booking_code]) }}" class="report-btn-light">
                                                <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                                                <span>Lihat Detail</span>
                                            </a>
                                        @else
                                            <span class="report-btn-light is-disabled">
                                                <i class="bi bi-lock" aria-hidden="true"></i>
                                                <span>Detail</span>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="report-pagination-wrap">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="report-empty-state">
                    <i class="bi bi-inbox" aria-hidden="true"></i>
                    <h4>Belum ada data komisi</h4>
                    <p>Komisi platform akan muncul setelah ada transaksi yang valid dan terverifikasi.</p>
                </div>
            @endif
        </section>
    </div>
@endsection
