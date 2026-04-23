@extends('layouts.admin')

@section('title', 'Data Customer | Admin Rental')
@section('page_title', 'Data Customer')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-rental-customers.css') }}">
@endpush

@section('content')
    @php
        $activeCustomers = $customers->filter(fn ($customer) => (int) ($customer->booking_count ?? 0) > 0)->count();
        $totalTransactionsListed = $customers->sum(fn ($customer) => (float) ($customer->total_transaction_amount ?? 0));
    @endphp

    <div class="customers-page">
        <div class="customers-breadcrumb">
            <span>Admin Rental</span>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <strong>Data Customer</strong>
        </div>

        <div class="customers-header-card">
            <div>
                <h2>Data Customer</h2>
                <p>Pantau pelanggan rental Anda dan lihat data CRM secara ringkas di halaman ini.</p>
                <small>{{ $rentalCompany->company_name ?? 'Rental Company' }}</small>
            </div>
        </div>

        <div class="customers-stats-grid">
            <article class="customers-stat-card">
                <div class="customers-stat-icon"><i class="bi bi-people" aria-hidden="true"></i></div>
                <div>
                    <p>Total Customer</p>
                    <h3>{{ (int) ($summary['total_customers'] ?? 0) }}</h3>
                </div>
            </article>
            <article class="customers-stat-card">
                <div class="customers-stat-icon"><i class="bi bi-person-check" aria-hidden="true"></i></div>
                <div>
                    <p>Customer Aktif</p>
                    <h3>{{ $activeCustomers }}</h3>
                </div>
            </article>
            <article class="customers-stat-card">
                <div class="customers-stat-icon"><i class="bi bi-award" aria-hidden="true"></i></div>
                <div>
                    <p>Loyal Customer</p>
                    <h3>{{ (int) ($summary['loyal_customers'] ?? 0) }}</h3>
                </div>
            </article>
            <article class="customers-stat-card">
                <div class="customers-stat-icon"><i class="bi bi-cash-stack" aria-hidden="true"></i></div>
                <div>
                    <p>Total Transaksi</p>
                    <h3>Rp {{ number_format($totalTransactionsListed, 0, ',', '.') }}</h3>
                </div>
            </article>
        </div>

        <div class="customers-toolbar-card">
            <form method="GET" action="{{ route('admin-rental.customers.index') }}" class="customers-toolbar-form">
                <div class="customers-input-group customers-search-group">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama customer, email, nomor HP">
                </div>

                <div class="customers-input-group">
                    <i class="bi bi-gem" aria-hidden="true"></i>
                    <select name="loyal">
                        <option value="">Semua Loyalitas</option>
                        <option value="loyal" @selected(request('loyal') === 'loyal')>Loyal Customer</option>
                        <option value="non_loyal" @selected(request('loyal') === 'non_loyal')>Non-Loyal</option>
                    </select>
                </div>

                <div class="customers-input-group">
                    <i class="bi bi-activity" aria-hidden="true"></i>
                    <select name="activity">
                        <option value="">Semua Aktivitas</option>
                        <option value="active" @selected(request('activity') === 'active')>Aktif</option>
                        <option value="low" @selected(request('activity') === 'low')>Rendah</option>
                    </select>
                </div>

                <button type="submit" class="customers-filter-btn">
                    <i class="bi bi-funnel-fill" aria-hidden="true"></i>
                    <span>Terapkan</span>
                </button>

                <a href="{{ route('admin-rental.customers.index') }}" class="customers-reset-btn">
                    <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                    <span>Reset</span>
                </a>
            </form>
        </div>

        @if ($customers->count() > 0)
            <div class="customers-table-card">
                <div class="customers-table-wrapper">
                    <table class="customers-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Customer</th>
                                <th>Email</th>
                                <th>Nomor HP</th>
                                <th>Total Booking</th>
                                <th>Booking Completed</th>
                                <th>Total Transaksi</th>
                                <th>Last Booking</th>
                                <th>Status Loyal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $index => $customer)
                                @php
                                    $totalBooking = (int) ($customer->booking_count ?? 0);
                                    $completedBooking = (int) ($customer->completed_booking_count ?? 0);
                                    $isLoyal = $completedBooking >= (int) ($threshold ?? 2);
                                @endphp
                                <tr>
                                    <td>{{ ($customers->firstItem() ?? 1) + $index }}</td>
                                    <td class="customers-name">{{ $customer->name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone ?? '-' }}</td>
                                    <td>{{ $totalBooking }}</td>
                                    <td>{{ $completedBooking }}</td>
                                    <td class="customers-price">Rp {{ number_format((float) ($customer->total_transaction_amount ?? 0), 0, ',', '.') }}</td>
                                    <td>
                                        @if ($customer->last_booking_at)
                                            {{ \Carbon\Carbon::parse($customer->last_booking_at)->format('d M Y H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="customers-loyal-badge {{ $isLoyal ? 'is-loyal' : 'is-regular' }}">
                                            {{ $isLoyal ? 'Loyal' : 'Non-Loyal' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="customers-actions">
                                            <a href="{{ route('admin-rental.customers.show', $customer) }}" class="customers-action-btn is-detail" title="Lihat detail">
                                                <i class="bi bi-eye" aria-hidden="true"></i>
                                                <span>Detail</span>
                                            </a>
                                            <a href="{{ route('admin-rental.bookings.index', ['search' => $customer->name]) }}" class="customers-action-btn is-bookings" title="Lihat booking">
                                                <i class="bi bi-journal-text" aria-hidden="true"></i>
                                                <span>Booking</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="customers-pagination-wrap">
                {{ $customers->appends(request()->query())->links() }}
            </div>
        @else
            <div class="customers-empty-state">
                <div class="customers-empty-icon">
                    <i class="bi bi-person-x" aria-hidden="true"></i>
                </div>
                <h3>Belum ada data customer</h3>
                <p>Customer yang pernah melakukan booking pada rental Anda akan muncul di halaman ini.</p>
            </div>
        @endif
    </div>
@endsection
