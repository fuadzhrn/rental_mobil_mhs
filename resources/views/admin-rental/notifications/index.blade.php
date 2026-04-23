@extends('layouts.admin')

@section('title', 'Notifikasi | Admin Rental')
@section('page_title', 'Notifikasi')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-rental-notifications.css') }}">
@endpush

@section('content')
    @php
        $currentItems = $notifications->getCollection();
        $statusFilter = request('status', 'all');
        $typeFilter = request('type');
        $search = request('search');

        $totalNotifications = $notifications->total();
        $unreadCountOnPage = $currentItems->whereNull('read_at')->count();
        $readCountOnPage = $currentItems->whereNotNull('read_at')->count();
        $todayCountOnPage = $currentItems->filter(function ($item) {
            return optional($item->created_at)->isToday();
        })->count();

        $filteredNotifications = $currentItems->filter(function ($item) use ($search, $typeFilter) {
            $normalizedType = strtolower(trim((string) ($item->type ?? 'system')));
            $searchText = strtolower(trim((string) $search));
            $haystack = strtolower((string) (($item->title ?? '') . ' ' . ($item->message ?? '')));

            $matchesType = !$typeFilter || $typeFilter === '' || str_contains($normalizedType, strtolower((string) $typeFilter));
            $matchesSearch = $searchText === '' || str_contains($haystack, $searchText);

            return $matchesType && $matchesSearch;
        });

        $iconByType = function (?string $type): string {
            return match (strtolower((string) $type)) {
                'booking', 'booking_new', 'new_booking' => 'bi-journal-text',
                'payment', 'payment_uploaded', 'payment_verified', 'payment_rejected' => 'bi-credit-card-2-front',
                'review', 'review_new' => 'bi-chat-left-text',
                'promo', 'promo_active', 'promo_expired' => 'bi-megaphone',
                'warning' => 'bi-exclamation-triangle',
                'error' => 'bi-x-octagon',
                'success' => 'bi-check-circle',
                default => 'bi-bell',
            };
        };

        $badgeTypeClass = function (?string $type): string {
            return match (strtolower((string) $type)) {
                'booking', 'booking_new', 'new_booking' => 'is-booking',
                'payment', 'payment_uploaded', 'payment_verified', 'payment_rejected' => 'is-payment',
                'review', 'review_new' => 'is-review',
                'promo', 'promo_active', 'promo_expired' => 'is-promo',
                'warning' => 'is-warning',
                'error' => 'is-error',
                'success' => 'is-success',
                default => 'is-system',
            };
        };

        $typeLabel = function (?string $type): string {
            $raw = strtolower(trim((string) $type));
            if ($raw === '') {
                return 'System';
            }

            return ucwords(str_replace(['_', '-'], ' ', $raw));
        };
    @endphp

    <div class="notifications-page">
        <div class="notifications-breadcrumb">
            <span>Admin Rental</span>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <strong>Notifikasi</strong>
        </div>

        @if ($message = session('success'))
            <div class="notifications-alert is-success">
                <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                <div>
                    <strong>Berhasil</strong>
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif

        @if ($message = session('error'))
            <div class="notifications-alert is-error">
                <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                <div>
                    <strong>Gagal</strong>
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif

        @if ($message = session('warning'))
            <div class="notifications-alert is-warning">
                <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                <div>
                    <strong>Peringatan</strong>
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif

        @if ($message = session('info'))
            <div class="notifications-alert is-info">
                <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
                <div>
                    <strong>Informasi</strong>
                    <p>{{ $message }}</p>
                </div>
            </div>
        @endif

        <section class="notifications-header-card">
            <div>
                <h2>Notifikasi</h2>
                <p>Pantau pembaruan penting terkait booking, pembayaran, dan aktivitas rental Anda.</p>
            </div>
            <div class="notifications-header-actions">
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="notifications-btn-primary" @disabled($unreadCountOnPage === 0 && $statusFilter === 'read')>
                        <i class="bi bi-check2-all" aria-hidden="true"></i>
                        <span>Tandai Semua Dibaca</span>
                    </button>
                </form>
            </div>
        </section>

        <section class="notifications-stats-grid">
            <article class="notifications-stat-card">
                <div class="notifications-stat-icon"><i class="bi bi-bell" aria-hidden="true"></i></div>
                <div>
                    <p>Total Notifikasi</p>
                    <h3>{{ $totalNotifications }}</h3>
                </div>
            </article>
            <article class="notifications-stat-card">
                <div class="notifications-stat-icon"><i class="bi bi-bell-fill" aria-hidden="true"></i></div>
                <div>
                    <p>Belum Dibaca (Halaman Ini)</p>
                    <h3>{{ $unreadCountOnPage }}</h3>
                </div>
            </article>
            <article class="notifications-stat-card">
                <div class="notifications-stat-icon"><i class="bi bi-check-circle" aria-hidden="true"></i></div>
                <div>
                    <p>Sudah Dibaca (Halaman Ini)</p>
                    <h3>{{ $readCountOnPage }}</h3>
                </div>
            </article>
            <article class="notifications-stat-card">
                <div class="notifications-stat-icon"><i class="bi bi-calendar-event" aria-hidden="true"></i></div>
                <div>
                    <p>Notifikasi Hari Ini</p>
                    <h3>{{ $todayCountOnPage }}</h3>
                </div>
            </article>
        </section>

        <section class="notifications-toolbar-card">
            <form method="GET" action="{{ route('notifications.index') }}" class="notifications-toolbar-form">
                <div class="notifications-input-group notifications-search-group">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul atau isi notifikasi">
                </div>

                <div class="notifications-input-group">
                    <i class="bi bi-eye" aria-hidden="true"></i>
                    <select name="status">
                        <option value="all" @selected($statusFilter === 'all')>Semua Status</option>
                        <option value="unread" @selected($statusFilter === 'unread')>Belum Dibaca</option>
                        <option value="read" @selected($statusFilter === 'read')>Sudah Dibaca</option>
                    </select>
                </div>

                <div class="notifications-input-group">
                    <i class="bi bi-tags" aria-hidden="true"></i>
                    <select name="type">
                        <option value="" @selected($typeFilter === null || $typeFilter === '')>Semua Tipe</option>
                        <option value="booking" @selected($typeFilter === 'booking')>Booking</option>
                        <option value="payment" @selected($typeFilter === 'payment')>Payment</option>
                        <option value="review" @selected($typeFilter === 'review')>Review</option>
                        <option value="promo" @selected($typeFilter === 'promo')>Promo</option>
                        <option value="system" @selected($typeFilter === 'system')>System</option>
                    </select>
                </div>

                <button type="submit" class="notifications-filter-btn">
                    <i class="bi bi-funnel-fill" aria-hidden="true"></i>
                    <span>Terapkan</span>
                </button>

                <a href="{{ route('notifications.index') }}" class="notifications-reset-btn">
                    <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                    <span>Reset</span>
                </a>
            </form>
        </section>

        @if ($notifications->count() > 0)
            <section class="notifications-list-card">
                <div class="notifications-list">
                    @forelse ($filteredNotifications as $index => $notification)
                        @php
                            $isUnread = is_null($notification->read_at);
                            $type = (string) ($notification->type ?? 'system');
                        @endphp
                            <article class="notification-item {{ $isUnread ? 'is-unread' : 'is-read' }}">
                                <div class="notification-icon">
                                    <i class="bi {{ $iconByType($type) }}" aria-hidden="true"></i>
                                </div>

                                <div class="notification-main">
                                    <div class="notification-topline">
                                        <h3>{{ $notification->title ?? 'Notifikasi' }}</h3>

                                        <div class="notification-badges">
                                            <span class="notification-badge-status {{ $isUnread ? 'is-unread' : 'is-read' }}">
                                                {{ $isUnread ? 'Belum Dibaca' : 'Sudah Dibaca' }}
                                            </span>
                                            <span class="notification-badge-type {{ $badgeTypeClass($type) }}">
                                                {{ $typeLabel($type) }}
                                            </span>
                                        </div>
                                    </div>

                                    <p class="notification-message">{{ \Illuminate\Support\Str::limit((string) ($notification->message ?? ''), 170) }}</p>

                                    <div class="notification-meta">
                                        <span>
                                            <i class="bi bi-clock" aria-hidden="true"></i>
                                            {{ optional($notification->created_at)->format('d M Y H:i') }}
                                        </span>
                                        <span>
                                            <i class="bi bi-hash" aria-hidden="true"></i>
                                            #{{ ($notifications->firstItem() ?? 1) + $index }}
                                        </span>
                                    </div>
                                </div>

                                <div class="notification-actions">
                                    @if (!empty($notification->url))
                                        <a href="{{ $notification->url }}" class="notification-action-btn is-view">
                                            <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                                            <span>Lihat</span>
                                        </a>
                                    @else
                                        <button type="button" class="notification-action-btn is-disabled" disabled>
                                            <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                                            <span>Lihat</span>
                                        </button>
                                    @endif

                                    @if ($isUnread)
                                        <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="notification-action-btn is-read">
                                                <i class="bi bi-check2" aria-hidden="true"></i>
                                                <span>Tandai Dibaca</span>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" class="notification-action-btn is-disabled" disabled>
                                            <i class="bi bi-check2" aria-hidden="true"></i>
                                            <span>Sudah Dibaca</span>
                                        </button>
                                    @endif
                                </div>
                            </article>
                    @empty
                        <div class="notifications-filter-empty">
                            <i class="bi bi-funnel" aria-hidden="true"></i>
                            <p>Tidak ada notifikasi yang cocok dengan filter saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <div class="notifications-pagination-wrapper">
                {{ $notifications->links() }}
            </div>
        @else
            <section class="notifications-empty-state">
                <div class="notifications-empty-icon">
                    <i class="bi bi-inbox" aria-hidden="true"></i>
                </div>
                <h3>Belum ada notifikasi</h3>
                <p>Notifikasi terkait aktivitas rental Anda akan muncul di halaman ini.</p>
            </section>
        @endif
    </div>
@endsection
