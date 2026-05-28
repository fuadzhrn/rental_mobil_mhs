<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VeloraRide | Notifikasi Saya</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/home-mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/customer-notifications.css') }}">
</head>
<body class="customer-notifications-page" style="margin:0; font-family:'Poppins',sans-serif; background:#f4f6fa; color:#111827;">
    @include('home.navbar')

    @php
        $statusFilter = request('status', 'all');
        $typeFilter = request('type');
        $search = request('search');

        $currentItems = $notifications->getCollection();
        $totalNotifications = $notifications->total();
        $unreadCountOnPage = $currentItems->whereNull('read_at')->count();
        $readCountOnPage = $currentItems->whereNotNull('read_at')->count();
        $todayCountOnPage = $currentItems->filter(fn ($item) => optional($item->created_at)->isToday())->count();

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
                'booking', 'booking_new', 'new_booking' => 'fa-regular fa-clipboard',
                'payment', 'payment_uploaded', 'payment_verified', 'payment_rejected' => 'fa-solid fa-credit-card',
                'review', 'review_new' => 'fa-regular fa-comment-dots',
                'promo', 'promo_active', 'promo_expired' => 'fa-solid fa-tags',
                'warning' => 'fa-solid fa-triangle-exclamation',
                'error' => 'fa-solid fa-circle-xmark',
                'success' => 'fa-solid fa-circle-check',
                default => 'fa-regular fa-bell',
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

    <main class="customer-notifications-shell">
        <section class="customer-notifications-hero">
            <div>
                <p class="customer-kicker">Customer Area</p>
                <h1>Notifikasi Saya</h1>
                <p>Pantau update booking, pembayaran, dan aktivitas lain dari rental Anda dalam satu tempat.</p>
            </div>
            <div class="customer-notifications-actions">
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="customer-primary-btn" @disabled($unreadCountOnPage === 0 && $statusFilter === 'read')>
                        <i class="fa-solid fa-check-double"></i>
                        <span>Tandai Semua Dibaca</span>
                    </button>
                </form>
            </div>
        </section>

        <section class="customer-notification-stats">
            <article>
                <span>Total Notifikasi</span>
                <strong>{{ $totalNotifications }}</strong>
            </article>
            <article>
                <span>Belum Dibaca</span>
                <strong>{{ $unreadCountOnPage }}</strong>
            </article>
            <article>
                <span>Sudah Dibaca</span>
                <strong>{{ $readCountOnPage }}</strong>
            </article>
            <article>
                <span>Hari Ini</span>
                <strong>{{ $todayCountOnPage }}</strong>
            </article>
        </section>

        <section class="customer-notifications-toolbar">
            <form method="GET" action="{{ route('notifications.index') }}" class="customer-notifications-form">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul atau isi notifikasi">

                <select name="status">
                    <option value="all" @selected($statusFilter === 'all')>Semua Status</option>
                    <option value="unread" @selected($statusFilter === 'unread')>Belum Dibaca</option>
                    <option value="read" @selected($statusFilter === 'read')>Sudah Dibaca</option>
                </select>

                <select name="type">
                    <option value="" @selected($typeFilter === null || $typeFilter === '')>Semua Tipe</option>
                    <option value="booking" @selected($typeFilter === 'booking')>Booking</option>
                    <option value="payment" @selected($typeFilter === 'payment')>Payment</option>
                    <option value="review" @selected($typeFilter === 'review')>Review</option>
                    <option value="promo" @selected($typeFilter === 'promo')>Promo</option>
                    <option value="system" @selected($typeFilter === 'system')>System</option>
                </select>

                <button type="submit" class="customer-secondary-btn">Terapkan</button>
                <a href="{{ route('notifications.index') }}" class="customer-reset-btn">Reset</a>
            </form>
        </section>

        @if ($notifications->count() > 0)
            <section class="customer-notifications-list">
                @forelse ($filteredNotifications as $index => $notification)
                    @php
                        $isUnread = is_null($notification->read_at);
                        $type = (string) ($notification->type ?? 'system');
                    @endphp

                    <article class="customer-notification-card {{ $isUnread ? 'is-unread' : 'is-read' }}">
                        <div class="customer-notification-icon">
                            <i class="{{ $iconByType($type) }}"></i>
                        </div>

                        <div class="customer-notification-content">
                            <div class="customer-notification-topline">
                                <h3>{{ $notification->title ?? 'Notifikasi' }}</h3>
                                <div class="customer-notification-badges">
                                    <span class="customer-badge-status {{ $isUnread ? 'is-unread' : 'is-read' }}">
                                        {{ $isUnread ? 'Belum Dibaca' : 'Sudah Dibaca' }}
                                    </span>
                                    <span class="customer-badge-type {{ $badgeTypeClass($type) }}">
                                        {{ $typeLabel($type) }}
                                    </span>
                                </div>
                            </div>

                            <p class="customer-notification-message">{{ \Illuminate\Support\Str::limit((string) ($notification->message ?? ''), 180) }}</p>

                            <div class="customer-notification-meta">
                                <span><i class="fa-regular fa-clock"></i> {{ optional($notification->created_at)->format('d M Y H:i') }}</span>
                                <span><i class="fa-solid fa-hashtag"></i> #{{ ($notifications->firstItem() ?? 1) + $index }}</span>
                            </div>
                        </div>

                        <div class="customer-notification-actions">
                            @if (!empty($notification->url))
                                <a href="{{ $notification->url }}" class="customer-action-btn is-view">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    <span>Lihat</span>
                                </a>
                            @else
                                <button type="button" class="customer-action-btn is-disabled" disabled>
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    <span>Lihat</span>
                                </button>
                            @endif

                            @if ($isUnread)
                                <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="customer-action-btn is-read">
                                        <i class="fa-solid fa-check"></i>
                                        <span>Tandai Dibaca</span>
                                    </button>
                                </form>
                            @else
                                <button type="button" class="customer-action-btn is-disabled" disabled>
                                    <i class="fa-solid fa-check"></i>
                                    <span>Sudah Dibaca</span>
                                </button>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="customer-filter-empty">
                        <i class="fa-solid fa-filter"></i>
                        <p>Tidak ada notifikasi yang cocok dengan filter saat ini.</p>
                    </div>
                @endforelse
            </section>

            <div class="customer-notifications-pagination">
                {{ $notifications->links() }}
            </div>
        @else
            <section class="customer-empty-state">
                <div class="customer-empty-icon"><i class="fa-regular fa-envelope"></i></div>
                <h3>Belum ada notifikasi</h3>
                <p>Update booking dan pembayaran Anda akan tampil di halaman ini.</p>
            </section>
        @endif
    </main>

    @include('home.footer')
    <script src="{{ asset('assets/js/home-mobile.js') }}"></script>
</body>
</html>