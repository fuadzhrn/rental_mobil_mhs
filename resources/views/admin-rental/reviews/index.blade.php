@extends('layouts.admin')

@section('title', 'Data Ulasan | Admin Rental')
@section('page_title', 'Data Ulasan')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-rental-reviews.css') }}">
@endpush

@section('content')
    @php
        // Helper function to render star rating
        $renderStars = function ($rating) {
            $fullStars = (int) $rating;
            $hasHalf = ($rating - $fullStars) >= 0.5;
            $emptyStars = 5 - $fullStars - ($hasHalf ? 1 : 0);
            
            $html = '';
            for ($i = 0; $i < $fullStars; $i++) {
                $html .= '<i class="bi bi-star-fill" style="color:#f59e0b;"></i>';
            }
            if ($hasHalf) {
                $html .= '<i class="bi bi-star-half" style="color:#f59e0b;"></i>';
            }
            for ($i = 0; $i < $emptyStars; $i++) {
                $html .= '<i class="bi bi-star" style="color:#d1d5db;"></i>';
            }
            return $html;
        };

        // Calculate summary statistics
        $totalReviews = $reviews->count();
        $averageRating = $reviews->count() > 0 ? round($reviews->avg('rating'), 1) : 0;
        $highRatingCount = $reviews->filter(fn ($r) => $r->rating >= 4)->count();
        $thisMonthCount = $reviews->filter(fn ($r) => \Carbon\Carbon::parse($r->created_at)->isCurrentMonth())->count();
    @endphp

    <div class="reviews-page">
        <!-- Breadcrumb -->
        <div class="reviews-breadcrumb">
            <span>Admin Rental</span>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <strong>Data Ulasan</strong>
        </div>

        <!-- Flash Messages -->
        @if ($message = Session::get('success'))
            <div class="reviews-alert is-success">
                <div class="reviews-alert-icon">
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                </div>
                <div class="reviews-alert-content">
                    <strong>Berhasil</strong>
                    <p>{{ $message }}</p>
                </div>
                <button type="button" class="reviews-alert-close" onclick="this.parentElement.style.display='none';">
                    <i class="bi bi-x" aria-hidden="true"></i>
                </button>
            </div>
        @endif

        @if ($message = Session::get('error'))
            <div class="reviews-alert is-error">
                <div class="reviews-alert-icon">
                    <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                </div>
                <div class="reviews-alert-content">
                    <strong>Gagal</strong>
                    <p>{{ $message }}</p>
                </div>
                <button type="button" class="reviews-alert-close" onclick="this.parentElement.style.display='none';">
                    <i class="bi bi-x" aria-hidden="true"></i>
                </button>
            </div>
        @endif

        <!-- Header Card -->
        <div class="reviews-header-card">
            <div>
                <h2>Data Ulasan</h2>
                <p>Pantau ulasan dan rating dari customer untuk kendaraan dan layanan rental Anda.</p>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="reviews-stats-grid">
            <article class="reviews-stat-card">
                <div class="reviews-stat-icon"><i class="bi bi-chat-left" aria-hidden="true"></i></div>
                <div>
                    <p>Total Ulasan</p>
                    <h3>{{ $totalReviews }}</h3>
                </div>
            </article>
            <article class="reviews-stat-card">
                <div class="reviews-stat-icon"><i class="bi bi-star" aria-hidden="true"></i></div>
                <div>
                    <p>Rata-rata Rating</p>
                    <h3>{{ $averageRating }}/5</h3>
                </div>
            </article>
            <article class="reviews-stat-card">
                <div class="reviews-stat-icon"><i class="bi bi-hand-thumbs-up" aria-hidden="true"></i></div>
                <div>
                    <p>Rating Tinggi (4-5)</p>
                    <h3>{{ $highRatingCount }}</h3>
                </div>
            </article>
            <article class="reviews-stat-card">
                <div class="reviews-stat-icon"><i class="bi bi-calendar3" aria-hidden="true"></i></div>
                <div>
                    <p>Bulan Ini</p>
                    <h3>{{ $thisMonthCount }}</h3>
                </div>
            </article>
        </div>

        <!-- Toolbar -->
        <div class="reviews-toolbar-card">
            <form method="GET" action="{{ route('admin-rental.reviews.index') }}" class="reviews-toolbar-form">
                <div class="reviews-input-group reviews-search-group">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama customer, kendaraan, atau ulasan">
                </div>

                <div class="reviews-input-group">
                    <i class="bi bi-star-fill" aria-hidden="true"></i>
                    <select name="rating">
                        <option value="">Semua Rating</option>
                        <option value="5" @selected(request('rating') === '5')>⭐⭐⭐⭐⭐ (5)</option>
                        <option value="4" @selected(request('rating') === '4')>⭐⭐⭐⭐ (4)</option>
                        <option value="3" @selected(request('rating') === '3')>⭐⭐⭐ (3)</option>
                        <option value="2" @selected(request('rating') === '2')>⭐⭐ (2)</option>
                        <option value="1" @selected(request('rating') === '1')>⭐ (1)</option>
                    </select>
                </div>

                <button type="submit" class="reviews-filter-btn">
                    <i class="bi bi-funnel-fill" aria-hidden="true"></i>
                    <span>Terapkan</span>
                </button>

                <a href="{{ route('admin-rental.reviews.index') }}" class="reviews-reset-btn">
                    <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                    <span>Reset</span>
                </a>
            </form>
        </div>

        <!-- Table / Empty State -->
        @if ($reviews && $reviews->count() > 0)
            <div class="reviews-table-card">
                <div class="reviews-table-wrapper">
                    <table class="reviews-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Customer</th>
                                <th>Kendaraan</th>
                                <th>Booking Code</th>
                                <th>Rating</th>
                                <th>Ulasan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reviews as $index => $review)
                                @php
                                    $ratingClass = match ((int) $review->rating) {
                                        5 => 'is-excellent',
                                        4 => 'is-good',
                                        3 => 'is-average',
                                        2 => 'is-poor',
                                        default => 'is-terrible',
                                    };
                                @endphp
                                <tr>
                                    <td>{{ ($reviews->firstItem() ?? 1) + $index }}</td>
                                    <td class="reviews-date">{{ \Carbon\Carbon::parse($review->created_at)->format('d M Y H:i') }}</td>
                                    <td class="reviews-customer">{{ $review->customer?->name ?? '-' }}</td>
                                    <td class="reviews-vehicle">{{ $review->vehicle?->name ?? '-' }}</td>
                                    <td class="reviews-booking-code">{{ $review->booking?->booking_code ?? '-' }}</td>
                                    <td>
                                        <div class="reviews-rating {{ $ratingClass }}">
                                            <div class="reviews-rating-stars">
                                                {!! $renderStars($review->rating) !!}
                                            </div>
                                            <span class="reviews-rating-text">{{ $review->rating }}/5</span>
                                        </div>
                                    </td>
                                    <td class="reviews-comment">
                                        <div class="reviews-comment-text">{{ \Illuminate\Support\Str::limit($review->review ?? 'Tanpa komentar', 100) }}</div>
                                    </td>
                                    <td>
                                        <div class="reviews-actions">
                                            @if ($review->customer)
                                                <a href="{{ route('admin-rental.customers.show', $review->customer) }}" class="reviews-action-btn is-customer" title="Lihat profil customer">
                                                    <i class="bi bi-person" aria-hidden="true"></i>
                                                    <span>Customer</span>
                                                </a>
                                            @endif
                                            @if ($review->booking)
                                                <a href="{{ route('admin-rental.bookings.show', $review->booking->booking_code) }}" class="reviews-action-btn is-booking" title="Lihat detail booking">
                                                    <i class="bi bi-journal-text" aria-hidden="true"></i>
                                                    <span>Booking</span>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if ($reviews->hasPages())
                <div class="reviews-pagination-wrapper">
                    {{ $reviews->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="reviews-empty-state">
                <div class="reviews-empty-icon">
                    <i class="bi bi-inbox" aria-hidden="true"></i>
                </div>
                <h3>Belum Ada Ulasan</h3>
                <p>Ulasan dari customer akan muncul di halaman ini setelah booking selesai dan review dikirimkan.</p>
            </div>
        @endif
    </div>
@endsection
