@extends('layouts.admin')

@section('title', 'Dashboard Admin Rental')
@section('page_title', 'Dashboard Admin Rental')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-rental-dashboard.css') }}">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
    <script src="{{ asset('assets/js/admin-rental-dashboard.js') }}" defer></script>
@endpush

@section('content')
    @php
        $summaryCollection = collect($summary ?? []);

        $summaryValue = function (array $keys, string $default = '0') use ($summaryCollection): string {
            foreach ($keys as $key) {
                $match = $summaryCollection->first(function ($item) use ($key) {
                    return isset($item['label']) && strcasecmp(trim((string) $item['label']), trim($key)) === 0;
                });

                if ($match && array_key_exists('value', $match)) {
                    return (string) $match['value'];
                }
            }

            return $default;
        };

        $stats = [
            ['label' => 'Kendaraan', 'value' => $summaryValue(['Kendaraan', 'Total Kendaraan']), 'hint' => 'Total armada aktif rental', 'icon' => 'bi-car-front'],
            ['label' => 'Customer', 'value' => $summaryValue(['Customer', 'Total Customer']), 'hint' => 'Customer yang pernah booking', 'icon' => 'bi-people'],
            ['label' => 'Total Booking', 'value' => $summaryValue(['Total Booking', 'Booking Aktif', 'Booking Hari Ini']), 'hint' => 'Seluruh booking rental', 'icon' => 'bi-journal-check'],
            ['label' => 'Booking Selesai', 'value' => $summaryValue(['Booking Selesai', 'Total Booking Selesai'], '-'), 'hint' => 'Booking berstatus completed', 'icon' => 'bi-check2-circle'],
            ['label' => 'Revenue', 'value' => $summaryValue(['Revenue', 'Pendapatan Verified Bulan Ini', 'Pendapatan']), 'hint' => 'Pendapatan transaksi valid', 'icon' => 'bi-cash-stack'],
            ['label' => 'Payment Verified', 'value' => $summaryValue(['Payment Verified', 'Payment Menunggu Verifikasi'], '-'), 'hint' => 'Pembayaran terverifikasi', 'icon' => 'bi-patch-check'],
            ['label' => 'Booking Pending', 'value' => $summaryValue(['Booking Pending', 'Pending Booking', 'Booking Aktif'], '-'), 'hint' => 'Booking menunggu proses', 'icon' => 'bi-hourglass-split'],
            ['label' => 'Promo Aktif', 'value' => $summaryValue(['Promo Aktif', 'Total Promo Aktif'], '-'), 'hint' => 'Promo sedang berjalan', 'icon' => 'bi-megaphone'],
        ];

        $monthlyBookings = collect($monthlyBookings ?? []);
        $monthlyBookingLabels = $monthlyBookings->pluck('label')->values()->all();
        $monthlyBookingValues = $monthlyBookings->pluck('value')->map(fn ($value) => (int) $value)->values()->all();
        $hasMonthlyBookingData = collect($monthlyBookingValues)->contains(fn ($value) => $value > 0);

        $quickLinks = [
            ['label' => 'Data Kendaraan', 'route' => route('admin-rental.vehicles.index'), 'icon' => 'bi-car-front', 'hint' => 'Kelola unit kendaraan'],
            ['label' => 'Data Booking', 'route' => route('admin-rental.bookings.index'), 'icon' => 'bi-journal-check', 'hint' => 'Monitoring booking'],
            ['label' => 'Data Pembayaran', 'route' => route('admin-rental.payments.index'), 'icon' => 'bi-wallet2', 'hint' => 'Verifikasi pembayaran'],
            ['label' => 'Data Customer', 'route' => route('admin-rental.customers.index'), 'icon' => 'bi-people', 'hint' => 'Daftar customer rental'],
            ['label' => 'Promo', 'route' => route('admin-rental.promos.index'), 'icon' => 'bi-megaphone', 'hint' => 'Atur promo aktif'],
            ['label' => 'Data Ulasan', 'route' => route('admin-rental.reviews.index'), 'icon' => 'bi-chat-left-text', 'hint' => 'Pantau ulasan pelanggan'],
            ['label' => 'Notifikasi', 'route' => route('notifications.index'), 'icon' => 'bi-bell', 'hint' => 'Notifikasi sistem'],
            ['label' => 'Laporan', 'route' => route('admin-rental.reports.index'), 'icon' => 'bi-graph-up-arrow', 'hint' => 'Laporan operasional'],
        ];
    @endphp

    <div class="admin-rental-dashboard">
        <section class="ard-header-card">
            <div class="ard-breadcrumb">
                <i class="bi bi-speedometer2" aria-hidden="true"></i>
                <span>AREA ADMIN RENTAL</span>
            </div>
            <h2 class="ard-title">Dashboard Admin Rental</h2>
            <p class="ard-description">Dashboard ini menampilkan ringkasan operasional rental untuk memantau armada, booking, pembayaran, promo, dan aktivitas bulanan secara cepat.</p>
        </section>

        <section class="ard-section">
            <div class="ard-section-head">
                <div>
                    <h3>Statistik Utama</h3>
                    <p>Susunan 2 row × 4 kartu agar data penting lebih cepat dipindai saat monitoring harian.</p>
                </div>
            </div>

            <div class="ard-stats-grid">
                @foreach ($stats as $index => $card)
                    <article class="ard-stat-card {{ in_array($index, [4, 7], true) ? 'is-accent' : '' }}">
                        <div class="ard-stat-top">
                            <div class="ard-stat-copy">
                                <p>{{ $card['label'] }}</p>
                                <h3>{{ $card['value'] }}</h3>
                            </div>
                            <div class="ard-stat-icon" aria-hidden="true">
                                <i class="bi {{ $card['icon'] }}"></i>
                            </div>
                        </div>
                        <p class="ard-stat-hint">{{ $card['hint'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="ard-section">
            <div class="ard-chart-card">
                <div class="ard-section-head">
                    <div>
                        <h3>Grafik Booking per Bulan</h3>
                        <p>Tren booking 12 bulan terakhir untuk membantu evaluasi performa rental.</p>
                    </div>
                </div>

                <script id="admin-rental-monthly-bookings-data" type="application/json">
                    @json([
                        'labels' => $monthlyBookingLabels,
                        'values' => $monthlyBookingValues,
                    ])
                </script>

                @if ($hasMonthlyBookingData)
                    <div class="ard-chart-wrap">
                        <canvas id="adminRentalMonthlyBookingsChart" aria-label="Grafik booking per bulan admin rental" role="img"></canvas>
                    </div>
                @else
                    <div class="ard-chart-empty" data-admin-rental-chart-empty>
                        <div>
                            <div class="icon"><i class="bi bi-bar-chart-line" aria-hidden="true"></i></div>
                            <h4>Belum ada data booking bulanan</h4>
                            <p>Grafik akan otomatis tampil ketika data booking per bulan tersedia dari backend.</p>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <section class="ard-section">
            <div class="ard-section-head">
                <div>
                    <h3>Akses Cepat</h3>
                    <p>Arahkan ke menu operasional yang paling sering digunakan admin rental.</p>
                </div>
            </div>

            <div class="ard-quicklinks-card">
                <div class="ard-quicklinks-grid">
                    @foreach ($quickLinks as $item)
                        <a href="{{ $item['route'] }}" class="ard-quicklink">
                            <div class="icon" aria-hidden="true"><i class="bi {{ $item['icon'] }}"></i></div>
                            <strong>{{ $item['label'] }}</strong>
                            <span>{{ $item['hint'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection
