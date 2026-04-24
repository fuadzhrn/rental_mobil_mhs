@extends('layouts.admin')

@section('title', 'Laporan Pembayaran | Super Admin')
@section('page_title', 'Laporan Pembayaran')

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
    @endphp

    <div class="report-page">
        <section class="report-header-card">
            <div class="report-header-top">
                <div>
                    <h2>Laporan Pembayaran</h2>
                    <p>Pantau status pembayaran, nominal transaksi, dan proses verifikasi pada level platform.</p>
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
            <form method="GET" action="{{ route('super-admin.reports.payments') }}" class="report-filter-grid">
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
                    <label for="payment_status">Payment Status</label>
                    <select id="payment_status" name="payment_status">
                        <option value="">Semua Status</option>
                        @foreach ($paymentStatuses as $status => $label)
                            <option value="{{ $status }}" @selected((string) request('payment_status') === (string) $status)>
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
                    <a href="{{ route('super-admin.reports.payments') }}" class="report-btn-secondary">
                        <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                        <span>Reset</span>
                    </a>
                </div>
            </form>
        </section>

        <section class="report-stat-grid">
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-patch-check" aria-hidden="true"></i></div>
                <div>
                    <p>Total Payment Verified</p>
                    <h3>{{ number_format((int) ($summary['total_payments_verified'] ?? 0), 0, ',', '.') }}</h3>
                </div>
            </article>
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-cloud-upload" aria-hidden="true"></i></div>
                <div>
                    <p>Total Payment Uploaded</p>
                    <h3>{{ number_format((int) ($summary['total_payments_uploaded'] ?? 0), 0, ',', '.') }}</h3>
                </div>
            </article>
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-x-octagon" aria-hidden="true"></i></div>
                <div>
                    <p>Total Payment Rejected</p>
                    <h3>{{ number_format((int) ($summary['total_payments_rejected'] ?? 0), 0, ',', '.') }}</h3>
                </div>
            </article>
            <article class="report-stat-card">
                <div class="report-stat-icon"><i class="bi bi-cash-stack" aria-hidden="true"></i></div>
                <div>
                    <p>Total Nominal Verified</p>
                    <h3>Rp {{ number_format((float) ($summary['total_nominal_verified'] ?? 0), 0, ',', '.') }}</h3>
                </div>
            </article>
        </section>

        <section class="report-table-card">
            <div class="report-table-head">
                <h3>Data Pembayaran</h3>
                <p>Fokus pada status verifikasi dan catatan penolakan untuk mempercepat audit.</p>
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
                                <th>Metode Pembayaran</th>
                                <th class="is-number">Amount</th>
                                <th>Payment Status</th>
                                <th>Paid At</th>
                                <th>Verified At</th>
                                <th>Rejection Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $index => $booking)
                                <tr>
                                    <td class="is-center">{{ ($bookings->firstItem() ?? 1) + $index }}</td>
                                    <td class="is-code">{{ $booking->booking_code }}</td>
                                    <td>{{ $booking->rentalCompany?->company_name ?? $booking->vehicle?->rentalCompany?->company_name ?? '-' }}</td>
                                    <td>{{ $booking->customer_name ?: ($booking->customer?->name ?? '-') }}</td>
                                    <td>{{ $booking->payment?->payment_method ?? '-' }}</td>
                                    <td class="is-number">Rp {{ number_format((float) $booking->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="report-badge {{ $paymentStatusBadge($booking->payment_status) }}">
                                            {{ $booking->paymentStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>{{ $booking->payment?->paid_at?->format('d M Y H:i') ?? '-' }}</td>
                                    <td>{{ $booking->payment?->verified_at?->format('d M Y H:i') ?? '-' }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit((string) ($booking->payment?->rejection_note ?? '-'), 50) }}</td>
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
                    <h4>Belum ada data pembayaran</h4>
                    <p>Belum ada data laporan untuk filter yang dipilih.</p>
                </div>
            @endif
        </section>
    </div>
@endsection
