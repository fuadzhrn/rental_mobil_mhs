@extends('layouts.admin')

@section('title', 'Laporan Booking | Super Admin')
@section('page_title', 'Laporan Booking')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/super-admin-report-pages.css') }}">
@endpush

@section('content')
    @php
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
                'rejected' => 'is-danger',
                'unpaid' => 'is-muted',
                default => 'is-muted',
            };
        };
    @endphp

    <div class="report-page">
        <section class="report-header-card">
            <div class="report-header-top">
                <div>
                    <h2>Laporan Booking</h2>
                    <p>Monitoring aktivitas booking lintas rental dengan status booking dan pembayaran yang mudah dipindai.</p>
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
            <form method="GET" action="{{ route('super-admin.reports.bookings') }}" class="report-filter-grid">
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
                <div class="report-filter-group">
                    <label for="booking_status">Booking Status</label>
                    <select id="booking_status" name="booking_status">
                        <option value="">Semua Status</option>
                        @foreach ($bookingStatuses as $status => $label)
                            <option value="{{ $status }}" @selected((string) request('booking_status') === (string) $status)>
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
                    <a href="{{ route('super-admin.reports.bookings') }}" class="report-btn-secondary">
                        <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                        <span>Reset</span>
                    </a>
                </div>
            </form>
        </section>

        <section class="report-stat-grid">
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-journals" aria-hidden="true"></i></div>
                <div>
                    <p>Total Booking</p>
                    <h3>{{ number_format((int) ($summary['total_bookings'] ?? 0), 0, ',', '.') }}</h3>
                </div>
            </article>
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-check2-circle" aria-hidden="true"></i></div>
                <div>
                    <p>Booking Completed</p>
                    <h3>{{ number_format((int) ($summary['total_completed'] ?? 0), 0, ',', '.') }}</h3>
                </div>
            </article>
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-arrow-repeat" aria-hidden="true"></i></div>
                <div>
                    <p>Booking Ongoing</p>
                    <h3>{{ number_format((int) ($summary['total_ongoing'] ?? 0), 0, ',', '.') }}</h3>
                </div>
            </article>
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-x-circle" aria-hidden="true"></i></div>
                <div>
                    <p>Booking Cancelled</p>
                    <h3>{{ number_format((int) ($summary['total_cancelled'] ?? 0), 0, ',', '.') }}</h3>
                </div>
            </article>
        </section>

        <section class="report-table-card">
            <div class="report-table-head">
                <h3>Data Booking</h3>
                <p>Gunakan scroll horizontal jika tabel melebar agar data tetap terbaca nyaman.</p>
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
                                <th>Kendaraan</th>
                                <th>Pickup Date</th>
                                <th>Return Date</th>
                                <th class="is-center">Duration</th>
                                <th class="is-number">Total Amount</th>
                                <th>Booking Status</th>
                                <th>Payment Status</th>
                                <th>Tanggal Booking</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $index => $booking)
                                @php
                                    $durationDays = $booking->duration_days;
                                    if ($durationDays === null && $booking->pickup_date && $booking->return_date) {
                                        $durationDays = $booking->pickup_date->diffInDays($booking->return_date) + 1;
                                    }
                                @endphp
                                <tr>
                                    <td class="is-center">{{ ($bookings->firstItem() ?? 1) + $index }}</td>
                                    <td class="is-code">{{ $booking->booking_code }}</td>
                                    <td>{{ $booking->rentalCompany?->company_name ?? $booking->vehicle?->rentalCompany?->company_name ?? '-' }}</td>
                                    <td>{{ $booking->customer_name ?: ($booking->customer?->name ?? '-') }}</td>
                                    <td>{{ $booking->vehicle?->name ?? '-' }}</td>
                                    <td>{{ $booking->pickup_date?->format('d M Y') ?? '-' }}</td>
                                    <td>{{ $booking->return_date?->format('d M Y') ?? '-' }}</td>
                                    <td class="is-center">{{ $durationDays ? $durationDays . ' hari' : '-' }}</td>
                                    <td class="is-number">Rp {{ number_format((float) $booking->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="report-badge {{ $bookingStatusBadge($booking->booking_status) }}">
                                            {{ $booking->bookingStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="report-badge {{ $paymentStatusBadge($booking->payment_status) }}">
                                            {{ $booking->paymentStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>{{ $booking->created_at?->format('d M Y H:i') ?? '-' }}</td>
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
                    <h4>Belum ada data laporan</h4>
                    <p>Belum ada data laporan untuk filter yang dipilih.</p>
                </div>
            @endif
        </section>
    </div>
@endsection
