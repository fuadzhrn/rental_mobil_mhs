@extends('layouts.admin')

@section('title', 'Data Pembayaran | Admin Rental')
@section('page_title', 'Data Pembayaran')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-rental-payments.css') }}">
@endpush

@section('content')
    @php
        $paymentMethodsConfig = config('payment_methods', []);
        $paymentStatusOptions = [
            \App\Models\Payment::STATUS_UNPAID => 'Unpaid',
            \App\Models\Payment::STATUS_UPLOADED => 'Uploaded',
            \App\Models\Payment::STATUS_VERIFIED => 'Verified',
            \App\Models\Payment::STATUS_REJECTED => 'Rejected',
        ];
        $bookingStatusOptions = [
            \App\Models\Booking::BOOKING_WAITING_PAYMENT => 'Waiting Payment',
            \App\Models\Booking::BOOKING_WAITING_VERIFICATION => 'Waiting Verification',
            \App\Models\Booking::BOOKING_CONFIRMED => 'Confirmed',
            \App\Models\Booking::BOOKING_ONGOING => 'Ongoing',
            \App\Models\Booking::BOOKING_COMPLETED => 'Completed',
            \App\Models\Booking::BOOKING_CANCELLED => 'Cancelled',
        ];
    @endphp

    <div class="payments-page">
        <div class="payments-breadcrumb">
            <span>Admin Rental</span>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <strong>Data Pembayaran</strong>
        </div>

        <div class="payments-header-card">
            <div>
                <h2>Data Pembayaran</h2>
                <p>Pantau dan verifikasi pembayaran booking pelanggan di halaman ini.</p>
                <small>{{ $rentalCompany->company_name ?? 'Rental Company' }}</small>
            </div>
        </div>

        <div class="payments-toolbar-card">
            <form method="GET" action="{{ route('admin-rental.payments.index') }}" class="payments-toolbar-form">
                <div class="payments-input-group payments-search-group">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari booking code, customer, kendaraan">
                </div>

                <div class="payments-input-group">
                    <i class="bi bi-credit-card-2-front" aria-hidden="true"></i>
                    <select name="payment_status">
                        <option value="">Semua Status Pembayaran</option>
                        @foreach ($paymentStatusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(request('payment_status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="payments-input-group">
                    <i class="bi bi-journals" aria-hidden="true"></i>
                    <select name="booking_status">
                        <option value="">Semua Status Booking</option>
                        @foreach ($bookingStatusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(request('booking_status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="payments-input-group">
                    <i class="bi bi-bank" aria-hidden="true"></i>
                    <select name="payment_method">
                        <option value="">Semua Metode</option>
                        @foreach ($paymentMethodsConfig as $methodKey => $method)
                            <option value="{{ $methodKey }}" @selected(request('payment_method') === $methodKey)>{{ $method['label'] ?? $methodKey }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="payments-filter-btn">
                    <i class="bi bi-funnel-fill" aria-hidden="true"></i>
                    <span>Terapkan</span>
                </button>

                <a href="{{ route('admin-rental.payments.index') }}" class="payments-reset-btn">
                    <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                    <span>Reset</span>
                </a>
            </form>
        </div>

        @if ($bookings->count() > 0)
            <div class="payments-table-card">
                <div class="payments-table-wrapper">
                    <table class="payments-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Booking Code</th>
                                <th>Customer</th>
                                <th>Kendaraan</th>
                                <th>Metode Pembayaran</th>
                                <th>Total Bayar</th>
                                <th>Bukti Pembayaran</th>
                                <th>Status Pembayaran</th>
                                <th>Status Booking</th>
                                <th>Tanggal Upload / Paid At</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $index => $booking)
                                @php
                                    $payment = $booking->payment;
                                    $paymentStatus = $payment?->payment_status ?? \App\Models\Payment::STATUS_UNPAID;
                                    $bookingStatus = $booking->booking_status;
                                    $paymentStatusClass = match ($paymentStatus) {
                                        \App\Models\Payment::STATUS_UPLOADED => 'is-uploaded',
                                        \App\Models\Payment::STATUS_VERIFIED => 'is-verified',
                                        \App\Models\Payment::STATUS_REJECTED => 'is-rejected',
                                        default => 'is-unpaid',
                                    };
                                    $bookingStatusClass = match ($bookingStatus) {
                                        \App\Models\Booking::BOOKING_WAITING_PAYMENT => 'is-waiting-payment',
                                        \App\Models\Booking::BOOKING_WAITING_VERIFICATION => 'is-waiting-verification',
                                        \App\Models\Booking::BOOKING_CONFIRMED => 'is-confirmed',
                                        \App\Models\Booking::BOOKING_ONGOING => 'is-ongoing',
                                        \App\Models\Booking::BOOKING_COMPLETED => 'is-completed',
                                        \App\Models\Booking::BOOKING_CANCELLED => 'is-cancelled',
                                        default => 'is-default',
                                    };
                                    $methodLabel = $payment && $payment->payment_method
                                        ? ($paymentMethodsConfig[$payment->payment_method]['label'] ?? $payment->payment_method)
                                        : '-';
                                @endphp
                                <tr>
                                    <td>{{ ($bookings->firstItem() ?? 1) + $index }}</td>
                                    <td class="payments-code">{{ $booking->booking_code }}</td>
                                    <td>{{ $booking->customer_name }}</td>
                                    <td>{{ $booking->vehicle->name ?? '-' }}</td>
                                    <td>{{ $methodLabel }}</td>
                                    <td class="payments-price">Rp {{ number_format((float) $booking->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($payment?->proof_payment)
                                            <a href="{{ asset('storage/' . $payment->proof_payment) }}" target="_blank" rel="noopener" class="payments-proof-link">
                                                <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
                                                <span>Lihat Bukti</span>
                                            </a>
                                        @else
                                            <span class="payments-proof-empty">Belum Upload</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="payments-status {{ $paymentStatusClass }}">
                                            {{ $payment?->status_label ?? 'Belum Bayar' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="payments-status booking {{ $bookingStatusClass }}">
                                            {{ $booking->booking_status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($payment?->paid_at)
                                            {{ $payment->paid_at->format('d M Y H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="payments-actions">
                                            <a href="{{ route('admin-rental.payments.show', $booking) }}" class="payments-action-btn is-detail" title="Lihat detail">
                                                <i class="bi bi-eye" aria-hidden="true"></i>
                                                <span>Detail</span>
                                            </a>

                                            @if ($paymentStatus === \App\Models\Payment::STATUS_UPLOADED)
                                                <form action="{{ route('admin-rental.payments.verify', $booking) }}" method="POST" onsubmit="return confirm('Verifikasi pembayaran ini?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="payments-action-btn is-verify" title="Verifikasi pembayaran">
                                                        <i class="bi bi-check2-circle" aria-hidden="true"></i>
                                                        <span>Verifikasi</span>
                                                    </button>
                                                </form>

                                                <a href="{{ route('admin-rental.payments.show', $booking) }}#reject-form" class="payments-action-btn is-reject" title="Tolak pembayaran">
                                                    <i class="bi bi-x-circle" aria-hidden="true"></i>
                                                    <span>Tolak</span>
                                                </a>
                                            @else
                                                <span class="payments-action-btn is-disabled" aria-disabled="true">
                                                    <i class="bi bi-lock" aria-hidden="true"></i>
                                                    <span>Terkunci</span>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="payments-pagination-wrap">
                {{ $bookings->appends(request()->query())->links() }}
            </div>
        @else
            <div class="payments-empty-state">
                <div class="payments-empty-icon">
                    <i class="bi bi-receipt-cutoff" aria-hidden="true"></i>
                </div>
                <h3>Belum ada data pembayaran</h3>
                <p>Pembayaran dari customer akan muncul di halaman ini setelah booking dibuat.</p>
            </div>
        @endif
    </div>
@endsection
