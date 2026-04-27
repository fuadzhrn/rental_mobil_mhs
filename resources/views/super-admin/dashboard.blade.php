@extends('layouts.admin')

@section('title', 'Dashboard Super Admin')
@section('page_title', 'Dashboard Super Admin')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/super-admin-dashboard.css') }}">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
    <script src="{{ asset('assets/js/super-admin-dashboard.js') }}" defer></script>
@endpush

@section('content')
    @php
        $summary = $summary ?? [];
        $quickLinks = $quickLinks ?? [];
        $monthlyBookings = collect($monthlyBookings ?? []);

        $statCards = collect($summary)->map(function (array $item) {
            return [
                'label' => $item['label'] ?? '-',
                'value' => $item['value'] ?? '-',
                'hint' => $item['hint'] ?? '',
                'icon' => $item['icon'] ?? 'bi-dash-circle',
            ];
        })->values();

        $monthlyBookingLabels = $monthlyBookings->pluck('label')->values()->all();
        $monthlyBookingValues = $monthlyBookings->pluck('value')->map(fn ($value) => (int) $value)->values()->all();
        $hasMonthlyBookingData = collect($monthlyBookingValues)->contains(fn ($value) => $value > 0);
    @endphp

    <div class="super-admin-dashboard">
        <section class="sa-header-card">
            <div class="sa-breadcrumb">
                <i class="bi bi-speedometer2" aria-hidden="true"></i>
                <span>AREA ADMIN</span>
            </div>
            <h2 class="sa-title">Dashboard Super Admin</h2>
            <p class="sa-description">
                Monitoring ringkas performa platform rental kendaraan, mulai dari mitra aktif, customer, armada, pendapatan, hingga booking bulanan.
            </p>
            <div class="sa-meta-row">
                <span class="sa-pill"><i class="bi bi-percent" aria-hidden="true"></i> Komisi platform {{ number_format((float) $commissionRate, 0, ',', '.') }}%</span>
                <span class="sa-pill"><i class="bi bi-graph-up-arrow" aria-hidden="true"></i> Monitoring operasional harian</span>
            </div>
        </section>

        <section class="sa-section">
            <div class="sa-section-head">
                <div>
                    <h3>Statistik Utama</h3>
                    <p>Susunan 2 row × 4 kartu untuk memudahkan monitoring cepat dan jelas.</p>
                </div>
            </div>

            <div class="sa-stats-grid">
                @forelse ($statCards as $index => $card)
                    <article class="sa-stat-card {{ in_array($index, [4, 7], true) ? 'is-accent' : '' }}">
                        <div class="sa-stat-top">
                            <div class="sa-stat-copy">
                                <p>{{ $card['label'] }}</p>
                                <h3>{{ $card['value'] }}</h3>
                            </div>
                            <div class="sa-stat-icon" aria-hidden="true">
                                <i class="bi {{ $card['icon'] }}"></i>
                            </div>
                        </div>
                        @if (!empty($card['hint']))
                            <p class="sa-stat-hint">{{ $card['hint'] }}</p>
                        @endif
                    </article>
                @empty
                    <article class="sa-empty-state" style="grid-column: 1 / -1; min-height: 200px;">
                        <div>
                            <div class="icon"><i class="bi bi-inbox" aria-hidden="true"></i></div>
                            <h4>Statistik belum tersedia</h4>
                            <p>Data ringkasan belum dikirim dari controller.</p>
                        </div>
                    </article>
                @endforelse
            </div>
        </section>

        <section class="sa-section">
            <div class="sa-chart-card">
                <div class="sa-section-head">
                    <div>
                        <h3>Grafik Booking per Bulan</h3>
                        <p>Ringkasan booking 12 bulan terakhir untuk membaca tren aktivitas platform.</p>
                    </div>
                </div>

                <script id="monthly-bookings-data" type="application/json">
                    @json([
                        'labels' => $monthlyBookingLabels,
                        'values' => $monthlyBookingValues,
                    ])
                </script>

                @if ($hasMonthlyBookingData)
                    <div class="sa-chart-wrap">
                        <canvas id="monthlyBookingsChart" aria-label="Grafik booking per bulan" role="img"></canvas>
                    </div>
                @else
                    <div class="sa-chart-empty" data-booking-chart-empty>
                        <div>
                            <div class="icon"><i class="bi bi-bar-chart-line" aria-hidden="true"></i></div>
                            <h4>Belum ada data booking bulanan</h4>
                            <p>Grafik akan muncul otomatis ketika data booking per bulan tersedia.</p>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <section class="sa-section">
            <div class="sa-section-head">
                <div>
                    <h3>Akses Cepat</h3>
                    <p>Tombol pintas untuk ke halaman operasional yang paling sering dipakai.</p>
                </div>
            </div>

            <div class="sa-quicklinks-card">
                <div class="sa-quicklinks-grid">
                    @forelse ($quickLinks as $item)
                        <a href="{{ $item['route'] }}" class="sa-quicklink">
                            <div class="icon" aria-hidden="true"><i class="bi {{ $item['icon'] ?? 'bi-link-45deg' }}"></i></div>
                            <strong>{{ $item['label'] }}</strong>
                            @if (!empty($item['hint']))
                                <span>{{ $item['hint'] }}</span>
                            @endif
                        </a>
                    @empty
                        <div class="sa-empty-state" style="grid-column: 1 / -1; min-height: 180px; box-shadow: none;">
                            <div>
                                <div class="icon"><i class="bi bi-link-45deg" aria-hidden="true"></i></div>
                                <h4>Quick links belum tersedia</h4>
                                <p>Tambahkan tautan operasional di controller bila diperlukan.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
@endsection
