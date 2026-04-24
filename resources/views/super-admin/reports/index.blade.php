@extends('layouts.admin')

@section('title', 'Laporan | Super Admin')
@section('page_title', 'Laporan')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/super-admin-reports.css') }}">
@endpush

@section('content')
    @php
        $bookingsReport = $bookingsReport ?? collect();
        $paymentsReport = $paymentsReport ?? collect();
        $topVehicles = $topVehicles ?? collect();
        $activeCustomers = $activeCustomers ?? collect();
        $revenueReport = $revenueReport ?? collect();
        $commissionsReport = $commissionsReport ?? collect();

        $rentalOptions = $rentalCompanies ?? collect();
        $bookingStatusOptions = $bookingStatuses ?? [
            'waiting_payment' => 'Waiting Payment',
            'waiting_verification' => 'Waiting Verification',
            'confirmed' => 'Confirmed',
            'ongoing' => 'Ongoing',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];
        $paymentStatusOptions = $paymentStatuses ?? [
            'unpaid' => 'Unpaid',
            'uploaded' => 'Uploaded',
            'verified' => 'Verified',
            'rejected' => 'Rejected',
        ];

        $metricTotalBooking = (int) ($totalBookings ?? 0);
        $metricPaymentVerified = (int) ($totalPaymentsVerified ?? 0);
        $metricTotalRevenue = (float) ($totalRevenue ?? 0);
        $metricTotalCommission = (float) ($totalCommission ?? 0);
        $metricTotalRental = isset($totalRentals) ? (int) $totalRentals : null;
        $metricTotalCustomer = (int) ($totalActiveCustomers ?? 0);

        $statusBadgeClass = function (?string $status): string {
            return match (strtolower((string) $status)) {
                'verified', 'completed', 'success' => 'is-success',
                'uploaded', 'ongoing', 'confirmed' => 'is-info',
                'unpaid', 'pending', 'waiting_payment', 'waiting_verification' => 'is-warning',
                'rejected', 'cancelled', 'failed', 'error' => 'is-danger',
                default => 'is-muted',
            };
        };
    @endphp

    <div class="reports-page">
        <div class="reports-breadcrumb">
            <span>Super Admin</span>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <strong>Laporan</strong>
        </div>

        <section class="reports-header-card">
            <h2>Laporan</h2>
            <p>Pantau performa platform rental kendaraan secara menyeluruh melalui laporan booking, pembayaran, customer, dan komisi.</p>
        </section>

        <section class="reports-filter-card">
            <form method="GET" action="{{ route('super-admin.reports.index') }}" class="reports-filter-form">
                <div class="reports-filter-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>

                <div class="reports-filter-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>

                <div class="reports-filter-group">
                    <label for="rental_id">Rental</label>
                    <select id="rental_id" name="rental_id">
                        <option value="">Semua Rental</option>
                        @foreach ($rentalOptions as $rental)
                            <option value="{{ $rental->id ?? '' }}" @selected((string) request('rental_id') === (string) ($rental->id ?? ''))>
                                {{ $rental->company_name ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="reports-filter-group">
                    <label for="booking_status">Booking Status</label>
                    <select id="booking_status" name="booking_status">
                        <option value="">Semua Status</option>
                        @foreach ($bookingStatusOptions as $key => $value)
                            <option value="{{ $key }}" @selected((string) request('booking_status') === (string) $key)>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="reports-filter-group">
                    <label for="payment_status">Payment Status</label>
                    <select id="payment_status" name="payment_status">
                        <option value="">Semua Status</option>
                        @foreach ($paymentStatusOptions as $key => $value)
                            <option value="{{ $key }}" @selected((string) request('payment_status') === (string) $key)>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="reports-filter-actions">
                    <button type="submit" class="reports-btn-primary">
                        <i class="bi bi-funnel-fill" aria-hidden="true"></i>
                        <span>Terapkan Filter</span>
                    </button>
                    <a href="{{ route('super-admin.reports.index') }}" class="reports-btn-secondary">
                        <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                        <span>Reset Filter</span>
                    </a>
                </div>
            </form>
        </section>

        <section class="reports-stats-grid">
            <article class="reports-stat-card">
                <div class="reports-stat-icon"><i class="bi bi-journal-text" aria-hidden="true"></i></div>
                <div>
                    <p>Total Booking</p>
                    <h3>{{ number_format($metricTotalBooking, 0, ',', '.') }}</h3>
                </div>
            </article>

            <article class="reports-stat-card">
                <div class="reports-stat-icon"><i class="bi bi-check2-circle" aria-hidden="true"></i></div>
                <div>
                    <p>Payment Verified</p>
                    <h3>{{ number_format($metricPaymentVerified, 0, ',', '.') }}</h3>
                </div>
            </article>

            <article class="reports-stat-card">
                <div class="reports-stat-icon"><i class="bi bi-cash-stack" aria-hidden="true"></i></div>
                <div>
                    <p>Total Pendapatan Platform</p>
                    <h3>Rp {{ number_format($metricTotalRevenue, 0, ',', '.') }}</h3>
                </div>
            </article>

            <article class="reports-stat-card">
                <div class="reports-stat-icon"><i class="bi bi-percent" aria-hidden="true"></i></div>
                <div>
                    <p>Total Komisi Platform</p>
                    <h3>Rp {{ number_format($metricTotalCommission, 0, ',', '.') }}</h3>
                </div>
            </article>

            <article class="reports-stat-card">
                <div class="reports-stat-icon"><i class="bi bi-building" aria-hidden="true"></i></div>
                <div>
                    <p>Total Rental</p>
                    @if (is_null($metricTotalRental))
                        <h3 class="is-muted">-</h3>
                    @else
                        <h3>{{ number_format($metricTotalRental, 0, ',', '.') }}</h3>
                    @endif
                </div>
            </article>

            <article class="reports-stat-card">
                <div class="reports-stat-icon"><i class="bi bi-people" aria-hidden="true"></i></div>
                <div>
                    <p>Total Customer</p>
                    <h3>{{ number_format($metricTotalCustomer, 0, ',', '.') }}</h3>
                </div>
            </article>
        </section>

        <section class="reports-quicklinks-card">
            <div class="reports-section-head">
                <div>
                    <h3>Quick Links Laporan</h3>
                    <p>Akses cepat ke halaman laporan detail.</p>
                </div>
            </div>
            <div class="reports-quicklinks-grid">
                <a href="{{ route('super-admin.reports.bookings') }}"><i class="bi bi-journal-text" aria-hidden="true"></i><span>Lihat Laporan Booking</span></a>
                <a href="{{ route('super-admin.reports.payments') }}"><i class="bi bi-credit-card-2-front" aria-hidden="true"></i><span>Lihat Laporan Pembayaran</span></a>
                <a href="{{ route('super-admin.reports.top-vehicles') }}"><i class="bi bi-truck" aria-hidden="true"></i><span>Lihat Kendaraan Terlaris</span></a>
                <a href="{{ route('super-admin.reports.active-customers') }}"><i class="bi bi-person-check" aria-hidden="true"></i><span>Lihat Customer Aktif</span></a>
                <a href="{{ route('super-admin.reports.revenue') }}"><i class="bi bi-graph-up-arrow" aria-hidden="true"></i><span>Lihat Pendapatan Rental</span></a>
                <a href="{{ route('super-admin.reports.commissions') }}"><i class="bi bi-receipt" aria-hidden="true"></i><span>Lihat Komisi Platform</span></a>
            </div>
        </section>

        <section class="reports-section-card">
            <div class="reports-section-head">
                <div>
                    <h3>Laporan Booking Per Rental</h3>
                    <p>Ringkasan operasional booking pada setiap rental.</p>
                </div>
                <a href="{{ route('super-admin.reports.bookings') }}" class="reports-detail-link">Lihat Semua</a>
            </div>
            @if ($bookingsReport->count())
                <div class="reports-table-wrap">
                    <table class="reports-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Rental</th>
                                <th>Total Booking</th>
                                <th>Booking Completed</th>
                                <th>Booking Cancelled</th>
                                <th>Booking Ongoing</th>
                                <th>Total Nilai Booking</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookingsReport as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row->rental_name ?? $row->company_name ?? '-' }}</td>
                                    <td>{{ number_format((int) ($row->total_booking ?? 0), 0, ',', '.') }}</td>
                                    <td>{{ number_format((int) ($row->completed_booking ?? 0), 0, ',', '.') }}</td>
                                    <td>{{ number_format((int) ($row->cancelled_booking ?? 0), 0, ',', '.') }}</td>
                                    <td>{{ number_format((int) ($row->ongoing_booking ?? 0), 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format((float) ($row->total_amount ?? $row->total_value ?? 0), 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="reports-empty-state"><i class="bi bi-inbox" aria-hidden="true"></i><p>Belum ada data laporan untuk filter yang dipilih.</p></div>
            @endif
        </section>

        <section class="reports-section-card">
            <div class="reports-section-head">
                <div>
                    <h3>Laporan Pembayaran</h3>
                    <p>Data status pembayaran lintas transaksi.</p>
                </div>
                <a href="{{ route('super-admin.reports.payments') }}" class="reports-detail-link">Lihat Semua</a>
            </div>
            @if ($paymentsReport->count())
                <div class="reports-table-wrap">
                    <table class="reports-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Booking Code</th>
                                <th>Rental</th>
                                <th>Customer</th>
                                <th>Metode Pembayaran</th>
                                <th>Amount</th>
                                <th>Payment Status</th>
                                <th>Paid At / Verified At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($paymentsReport as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row->booking_code ?? '-' }}</td>
                                    <td>{{ $row->rental_name ?? $row->rentalCompany->company_name ?? '-' }}</td>
                                    <td>{{ $row->customer_name ?? $row->customer->name ?? '-' }}</td>
                                    <td>{{ $row->payment_method ?? $row->payment?->payment_method ?? '-' }}</td>
                                    <td>Rp {{ number_format((float) ($row->amount ?? $row->total_amount ?? 0), 0, ',', '.') }}</td>
                                    <td>
                                        @php $paymentStatus = strtolower((string) ($row->payment_status ?? $row->payment?->payment_status ?? '')); @endphp
                                        <span class="reports-status-badge {{ $statusBadgeClass($paymentStatus) }}">{{ ucfirst($paymentStatus ?: '-') }}</span>
                                    </td>
                                    <td>{{ optional($row->paid_at ?? $row->payment?->paid_at ?? $row->verified_at ?? $row->payment?->verified_at)->format('d M Y H:i') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="reports-empty-state"><i class="bi bi-inbox" aria-hidden="true"></i><p>Belum ada data laporan untuk filter yang dipilih.</p></div>
            @endif
        </section>

        <section class="reports-section-card">
            <div class="reports-section-head">
                <div>
                    <h3>Kendaraan Terlaris</h3>
                    <p>Kendaraan dengan performa booking terbaik.</p>
                </div>
                <a href="{{ route('super-admin.reports.top-vehicles') }}" class="reports-detail-link">Lihat Semua</a>
            </div>
            @if ($topVehicles->count())
                <div class="reports-table-wrap">
                    <table class="reports-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kendaraan</th>
                                <th>Rental</th>
                                <th>Kategori</th>
                                <th>Total Booking Valid</th>
                                <th>Total Pendapatan Kendaraan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topVehicles as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row->name ?? '-' }}</td>
                                    <td>{{ $row->rentalCompany->company_name ?? $row->rental_name ?? '-' }}</td>
                                    <td>{{ $row->category ?? '-' }}</td>
                                    <td>{{ number_format((int) ($row->verified_booking_count ?? $row->total_booking_valid ?? 0), 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format((float) ($row->total_revenue ?? $row->vehicle_revenue ?? 0), 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="reports-empty-state"><i class="bi bi-inbox" aria-hidden="true"></i><p>Belum ada data laporan untuk filter yang dipilih.</p></div>
            @endif
        </section>

        <section class="reports-section-card">
            <div class="reports-section-head">
                <div>
                    <h3>Customer Aktif</h3>
                    <p>Customer dengan aktivitas transaksi tertinggi.</p>
                </div>
                <a href="{{ route('super-admin.reports.active-customers') }}" class="reports-detail-link">Lihat Semua</a>
            </div>
            @if ($activeCustomers->count())
                <div class="reports-table-wrap">
                    <table class="reports-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Customer</th>
                                <th>Email</th>
                                <th>Total Booking</th>
                                <th>Booking Completed</th>
                                <th>Total Transaksi</th>
                                <th>Last Booking Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activeCustomers as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row->name ?? '-' }}</td>
                                    <td>{{ $row->email ?? '-' }}</td>
                                    <td>{{ number_format((int) ($row->total_booking_count ?? 0), 0, ',', '.') }}</td>
                                    <td>{{ number_format((int) ($row->completed_booking_count ?? 0), 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format((float) ($row->total_transaction ?? 0), 0, ',', '.') }}</td>
                                    <td>{{ optional($row->last_booking_date)->format('d M Y H:i') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="reports-empty-state"><i class="bi bi-inbox" aria-hidden="true"></i><p>Belum ada data laporan untuk filter yang dipilih.</p></div>
            @endif
        </section>

        <section class="reports-section-card">
            <div class="reports-section-head">
                <div>
                    <h3>Pendapatan Rental</h3>
                    <p>Monitoring pendapatan kotor, komisi, dan estimasi bersih.</p>
                </div>
                <a href="{{ route('super-admin.reports.revenue') }}" class="reports-detail-link">Lihat Semua</a>
            </div>
            @if ($revenueReport->count())
                <div class="reports-table-wrap">
                    <table class="reports-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Rental</th>
                                <th>Total Booking Verified</th>
                                <th>Pendapatan Kotor</th>
                                <th>Komisi Platform</th>
                                <th>Estimasi Pendapatan Bersih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($revenueReport as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row['rental']->company_name ?? $row->rental_name ?? '-' }}</td>
                                    <td>{{ number_format((int) ($row['verified_booking_count'] ?? $row->verified_booking_count ?? 0), 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format((float) ($row['gross_revenue'] ?? $row->gross_revenue ?? 0), 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format((float) ($row['commission'] ?? $row->commission ?? 0), 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format((float) ($row['net_revenue'] ?? $row->net_revenue ?? 0), 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="reports-empty-state"><i class="bi bi-inbox" aria-hidden="true"></i><p>Belum ada data laporan untuk filter yang dipilih.</p></div>
            @endif
        </section>

        <section class="reports-section-card">
            <div class="reports-section-head">
                <div>
                    <h3>Komisi Platform</h3>
                    <p>Detail transaksi komisi dari booking terverifikasi.</p>
                </div>
                <a href="{{ route('super-admin.reports.commissions') }}" class="reports-detail-link">Lihat Semua</a>
            </div>
            @if ($commissionsReport->count())
                <div class="reports-table-wrap">
                    <table class="reports-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Booking Code</th>
                                <th>Rental</th>
                                <th>Customer</th>
                                <th>Total Booking</th>
                                <th>Nilai Komisi</th>
                                <th>Tanggal Transaksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($commissionsReport as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row->booking_code ?? '-' }}</td>
                                    <td>{{ $row->rentalCompany->company_name ?? $row->rental_name ?? '-' }}</td>
                                    <td>{{ $row->customer->name ?? $row->customer_name ?? '-' }}</td>
                                    <td>Rp {{ number_format((float) ($row->total_amount ?? 0), 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format((float) ($row->commission_amount ?? 0), 0, ',', '.') }}</td>
                                    <td>{{ optional($row->created_at ?? $row->transaction_date)->format('d M Y H:i') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="reports-empty-state"><i class="bi bi-inbox" aria-hidden="true"></i><p>Belum ada data laporan untuk filter yang dipilih.</p></div>
            @endif
        </section>
    </div>
@endsection
