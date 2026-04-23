@extends('layouts.admin')

@section('title', 'Data Booking | Admin Rental')
@section('page_title', 'Data Booking')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-rental-bookings.css') }}">
@endpush

@section('content')
    <div class="bookings-page">
        <div class="bookings-breadcrumb">
            <span>Admin Rental</span>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <strong>Data Booking</strong>
        </div>

        <div class="bookings-header-card">
            <div>
                <h2>Data Booking</h2>
                <p>Pantau seluruh booking pelanggan dan kelola status transaksi rental Anda di halaman ini.</p>
                <small>{{ $rentalCompany->company_name ?? 'Rental Company' }}</small>
            </div>
        </div>

        <div class="bookings-toolbar-card">
            <form method="GET" action="{{ route('admin-rental.bookings.index') }}" class="bookings-toolbar-form">
                <div class="bookings-input-group bookings-search-group">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari booking code, customer, kendaraan">
                </div>

                <div class="bookings-input-group">
                    <i class="bi bi-journal-text" aria-hidden="true"></i>
                    <select name="booking_status">
                        <option value="">Semua Status Booking</option>
                        @foreach ($bookingStatusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(request('booking_status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="bookings-input-group">
                    <i class="bi bi-credit-card-2-front" aria-hidden="true"></i>
                    <select name="payment_status">
                        <option value="">Semua Status Pembayaran</option>
                        @foreach ($paymentStatusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(request('payment_status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="bookings-input-group">
                    <i class="bi bi-calendar3" aria-hidden="true"></i>
                    <input type="date" name="date" value="{{ request('date') }}">
                </div>

                <button type="submit" class="bookings-filter-btn">
                    <i class="bi bi-funnel-fill" aria-hidden="true"></i>
                    <span>Terapkan</span>
                </button>

                <a href="{{ route('admin-rental.bookings.index') }}" class="bookings-reset-btn">
                    <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                    <span>Reset</span>
                </a>
            </form>
        </div>

        @if ($bookings->count() > 0)
            <div class="bookings-table-card">
                <div class="bookings-table-wrapper">
                    <table class="bookings-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Booking Code</th>
                                <th>Customer</th>
                                <th>Kendaraan</th>
                                <th>Tanggal Sewa</th>
                                <th>Durasi</th>
                                <th>Total Bayar</th>
                                <th>Status Booking</th>
                                <th>Status Pembayaran</th>
                                <th>Tanggal Booking</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $index => $booking)
                                @php
                                    $bookingStatusClass = match ($booking->booking_status) {
                                        \App\Models\Booking::BOOKING_WAITING_PAYMENT => 'is-waiting-payment',
                                        \App\Models\Booking::BOOKING_WAITING_VERIFICATION => 'is-waiting-verification',
                                        \App\Models\Booking::BOOKING_CONFIRMED => 'is-confirmed',
                                        \App\Models\Booking::BOOKING_ONGOING => 'is-ongoing',
                                        \App\Models\Booking::BOOKING_COMPLETED => 'is-completed',
                                        \App\Models\Booking::BOOKING_CANCELLED => 'is-cancelled',
                                        default => 'is-default',
                                    };
                                    $paymentStatusClass = match ($booking->payment_status) {
                                        \App\Models\Booking::PAYMENT_UPLOADED => 'is-uploaded',
                                        \App\Models\Booking::PAYMENT_VERIFIED => 'is-verified',
                                        \App\Models\Booking::PAYMENT_REJECTED => 'is-rejected',
                                        default => 'is-unpaid',
                                    };
                                    $canMarkOngoing = $booking->booking_status === \App\Models\Booking::BOOKING_CONFIRMED && $booking->payment_status === \App\Models\Booking::PAYMENT_VERIFIED;
                                    $canMarkCompleted = $booking->booking_status === \App\Models\Booking::BOOKING_ONGOING;
                                    $canCancel = in_array($booking->booking_status, [
                                        \App\Models\Booking::BOOKING_WAITING_PAYMENT,
                                        \App\Models\Booking::BOOKING_WAITING_VERIFICATION,
                                        \App\Models\Booking::BOOKING_CONFIRMED,
                                    ], true);
                                @endphp
                                <tr>
                                    <td>{{ ($bookings->firstItem() ?? 1) + $index }}</td>
                                    <td class="bookings-code">{{ $booking->booking_code }}</td>
                                    <td>{{ $booking->customer_name }}</td>
                                    <td>{{ $booking->vehicle->name ?? '-' }}</td>
                                    <td>
                                        {{ $booking->pickup_date?->format('d M Y') ?? '-' }}
                                        <span class="bookings-separator">s/d</span>
                                        {{ $booking->return_date?->format('d M Y') ?? '-' }}
                                    </td>
                                    <td>{{ (int) $booking->duration_days }} hari</td>
                                    <td class="bookings-price">Rp {{ number_format((float) $booking->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="bookings-status booking {{ $bookingStatusClass }}">{{ $booking->booking_status_label }}</span>
                                    </td>
                                    <td>
                                        <span class="bookings-status payment {{ $paymentStatusClass }}">{{ $booking->payment_status_label }}</span>
                                    </td>
                                    <td>{{ $booking->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                    <td>
                                        <div class="bookings-actions">
                                            <a href="{{ route('admin-rental.bookings.show', $booking) }}" class="bookings-action-btn is-detail" title="Lihat detail">
                                                <i class="bi bi-eye" aria-hidden="true"></i>
                                                <span>Detail</span>
                                            </a>

                                            @if ($canMarkOngoing)
                                                <form action="{{ route('admin-rental.bookings.mark-ongoing', $booking) }}" method="POST" onsubmit="return confirm('Tandai booking ini sebagai ongoing?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="bookings-action-btn is-ongoing" title="Tandai ongoing">
                                                        <i class="bi bi-play-circle" aria-hidden="true"></i>
                                                        <span>Ongoing</span>
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($canMarkCompleted)
                                                <form action="{{ route('admin-rental.bookings.mark-completed', $booking) }}" method="POST" onsubmit="return confirm('Tandai booking ini sebagai completed?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="bookings-action-btn is-completed" title="Tandai completed">
                                                        <i class="bi bi-check2-circle" aria-hidden="true"></i>
                                                        <span>Completed</span>
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($canCancel)
                                                <form action="{{ route('admin-rental.bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Batalkan booking ini?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="cancel_reason" value="Dibatalkan oleh admin rental melalui halaman Data Booking.">
                                                    <button type="submit" class="bookings-action-btn is-cancel" title="Batalkan booking">
                                                        <i class="bi bi-x-circle" aria-hidden="true"></i>
                                                        <span>Cancel</span>
                                                    </button>
                                                </form>
                                            @endif

                                            @if (!$canMarkOngoing && !$canMarkCompleted && !$canCancel)
                                                <span class="bookings-action-btn is-disabled" aria-disabled="true">
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

            <div class="bookings-pagination-wrap">
                {{ $bookings->appends(request()->query())->links() }}
            </div>
        @else
            <div class="bookings-empty-state">
                <div class="bookings-empty-icon">
                    <i class="bi bi-calendar2-x" aria-hidden="true"></i>
                </div>
                <h3>Belum ada data booking</h3>
                <p>Booking customer akan muncul di halaman ini setelah transaksi dibuat.</p>
            </div>
        @endif
    </div>
@endsection
