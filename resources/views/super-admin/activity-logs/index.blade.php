@extends('layouts.admin')

@section('title', 'Audit Log | Super Admin')
@section('page_title', 'Audit Log')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/super-admin-audit-log.css') }}">
@endpush

@section('content')
    @php
        $logCollection = $activityLogs->getCollection();
        $totalActivities = method_exists($activityLogs, 'total') ? (int) $activityLogs->total() : (int) $activityLogs->count();
        $todayActivities = $logCollection->filter(function ($log) {
            return $log->created_at && \Illuminate\Support\Carbon::parse($log->created_at)->isToday();
        })->count();
        $activeUsersInLogs = $logCollection->pluck('user_id')->filter()->unique()->count();

        $actionGroups = $logCollection->countBy(function ($log) {
            return strtolower((string) $log->action);
        });
        $topAction = $actionGroups->sortDesc()->keys()->first();

        $targetGroups = $logCollection->countBy(function ($log) {
            return strtolower((string) $log->target_type);
        });
        $topTargetType = $targetGroups->sortDesc()->keys()->first();

        $targetTypeOptions = $logCollection
            ->pluck('target_type')
            ->filter()
            ->map(fn($value) => strtolower((string) $value))
            ->unique()
            ->values();

        $actionBadge = function (?string $action): string {
            $normalized = strtolower((string) $action);
            return match (true) {
                str_contains($normalized, 'create'), str_contains($normalized, 'store') => 'is-success',
                str_contains($normalized, 'update'), str_contains($normalized, 'edit') => 'is-info',
                str_contains($normalized, 'delete'), str_contains($normalized, 'destroy') => 'is-danger',
                str_contains($normalized, 'verify'), str_contains($normalized, 'approve') => 'is-verify',
                str_contains($normalized, 'reject'), str_contains($normalized, 'cancel') => 'is-warning',
                str_contains($normalized, 'login'), str_contains($normalized, 'logout'), str_contains($normalized, 'system') => 'is-muted',
                default => 'is-muted',
            };
        };

        $targetBadge = function (?string $targetType): string {
            $normalized = strtolower((string) $targetType);
            return match ($normalized) {
                'booking' => 'is-booking',
                'payment' => 'is-payment',
                'vehicle' => 'is-vehicle',
                'rental', 'rental_company' => 'is-rental',
                'promo' => 'is-promo',
                'review' => 'is-review',
                'user' => 'is-user',
                default => 'is-system',
            };
        };

        $prettyLabel = function (?string $raw): string {
            $normalized = str_replace(['.', '_'], [' / ', ' '], strtolower((string) $raw));
            return ucwords(trim($normalized) === '' ? '-' : trim($normalized));
        };
    @endphp

    <div class="audit-page">
        <div class="audit-breadcrumb">
            <span>Super Admin</span>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <strong>Audit Log</strong>
        </div>

        <section class="audit-header-card">
            <h2>Audit Log</h2>
            <p>Pantau aktivitas penting sistem dan perubahan data yang dilakukan oleh pengguna platform.</p>
        </section>

        @if ($errors->any())
            <section class="audit-inline-alert is-error" role="alert">
                <i class="bi bi-exclamation-octagon" aria-hidden="true"></i>
                <div>
                    <strong>Terjadi kesalahan saat filter data</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </section>
        @endif

        @if (session('success'))
            <section class="audit-inline-alert is-success" role="status">
                <i class="bi bi-check-circle" aria-hidden="true"></i>
                <div>{{ session('success') }}</div>
            </section>
        @endif

        @if (session('error'))
            <section class="audit-inline-alert is-error" role="alert">
                <i class="bi bi-x-circle" aria-hidden="true"></i>
                <div>{{ session('error') }}</div>
            </section>
        @endif

        @if (session('warning'))
            <section class="audit-inline-alert is-warning" role="alert">
                <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
                <div>{{ session('warning') }}</div>
            </section>
        @endif

        @if (session('info'))
            <section class="audit-inline-alert is-info" role="status">
                <i class="bi bi-info-circle" aria-hidden="true"></i>
                <div>{{ session('info') }}</div>
            </section>
        @endif

        <section class="audit-filter-card">
            <form method="GET" action="{{ route('super-admin.activity-logs.index') }}" class="audit-filter-form">
                <div class="audit-filter-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>

                <div class="audit-filter-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>

                <div class="audit-filter-group">
                    <label for="user_id">User</label>
                    <select id="user_id" name="user_id">
                        <option value="">Semua User</option>
                        @foreach ($userOptions as $user)
                            <option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="audit-filter-group">
                    <label for="action">Action</label>
                    <input type="text" id="action" name="action" value="{{ request('action') }}" placeholder="Contoh: booking.create">
                </div>

                <div class="audit-filter-group">
                    <label for="target_type">Target Type</label>
                    <select id="target_type" name="target_type">
                        <option value="">Semua Target</option>
                        @foreach ($targetTypeOptions as $option)
                            <option value="{{ $option }}" @selected((string) request('target_type') === (string) $option)>
                                {{ ucwords(str_replace('_', ' ', $option)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="audit-filter-actions">
                    <button type="submit" class="audit-btn-primary">
                        <i class="bi bi-funnel-fill" aria-hidden="true"></i>
                        <span>Terapkan Filter</span>
                    </button>
                    <a href="{{ route('super-admin.activity-logs.index') }}" class="audit-btn-secondary">
                        <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                        <span>Reset Filter</span>
                    </a>
                </div>
            </form>
        </section>

        <section class="audit-stat-grid">
            <article class="audit-stat-card">
                <div class="audit-stat-icon"><i class="bi bi-list-check" aria-hidden="true"></i></div>
                <div>
                    <p>Total Aktivitas</p>
                    <h3>{{ number_format($totalActivities, 0, ',', '.') }}</h3>
                </div>
            </article>

            <article class="audit-stat-card">
                <div class="audit-stat-icon"><i class="bi bi-calendar-event" aria-hidden="true"></i></div>
                <div>
                    <p>Aktivitas Hari Ini</p>
                    <h3>{{ number_format($todayActivities, 0, ',', '.') }}</h3>
                    <small>berdasarkan halaman ini</small>
                </div>
            </article>

            <article class="audit-stat-card">
                <div class="audit-stat-icon"><i class="bi bi-people" aria-hidden="true"></i></div>
                <div>
                    <p>Total User Aktif di Log</p>
                    <h3>{{ number_format($activeUsersInLogs, 0, ',', '.') }}</h3>
                    <small>berdasarkan halaman ini</small>
                </div>
            </article>

            <article class="audit-stat-card">
                <div class="audit-stat-icon"><i class="bi bi-bar-chart" aria-hidden="true"></i></div>
                <div>
                    <p>Action / Target Terbanyak</p>
                    <h3>{{ $prettyLabel($topAction) }}</h3>
                    <small>{{ $prettyLabel($topTargetType) }}</small>
                </div>
            </article>
        </section>

        <section class="audit-actions-row">
            <a href="{{ route('super-admin.reports.index') }}" class="audit-mini-link">
                <i class="bi bi-graph-up-arrow" aria-hidden="true"></i>
                <span>Buka Laporan Super Admin</span>
            </a>
            <a href="{{ route('super-admin.dashboard') }}" class="audit-mini-link">
                <i class="bi bi-speedometer2" aria-hidden="true"></i>
                <span>Kembali ke Dashboard</span>
            </a>
        </section>

        <section class="audit-table-card">
            <div class="audit-table-head">
                <h3>Data Audit Log</h3>
                <p>Log terbaru ditampilkan di atas untuk membantu investigasi aktivitas sistem.</p>
            </div>

            @if ($activityLogs->count() > 0)
                <div class="audit-table-wrap">
                    <table class="audit-table">
                        <thead>
                            <tr>
                                <th class="is-center">No</th>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Target Type</th>
                                <th class="is-center">Target ID</th>
                                <th>Deskripsi</th>
                                <th>Meta / Detail Singkat</th>
                                <th class="is-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activityLogs as $index => $log)
                                @php
                                    $createdAt = $log->created_at ? \Illuminate\Support\Carbon::parse($log->created_at) : null;
                                    $metaSummary = '-';

                                    if (is_array($log->meta) && count($log->meta) > 0) {
                                        $pairs = collect($log->meta)
                                            ->take(3)
                                            ->map(function ($value, $key) {
                                                $keyText = str_replace('_', ' ', (string) $key);
                                                $valueText = is_scalar($value) ? (string) $value : json_encode($value);
                                                return $keyText . ': ' . $valueText;
                                            })
                                            ->values();
                                        $metaSummary = $pairs->implode(' | ');
                                    }
                                @endphp
                                <tr>
                                    <td class="is-center">{{ ($activityLogs->firstItem() ?? 1) + $index }}</td>
                                    <td>
                                        <strong>{{ $createdAt?->format('d M Y') ?? '-' }}</strong>
                                        <br>
                                        <small>{{ $createdAt?->format('H:i:s') ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @if ($log->user)
                                            <strong>{{ $log->user->name }}</strong>
                                            <br>
                                            <small>{{ $log->user->email }}</small>
                                        @else
                                            <strong>System</strong>
                                            <br>
                                            <small>Unknown User</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="audit-badge {{ $actionBadge($log->action) }}">
                                            {{ $prettyLabel($log->action) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="audit-badge {{ $targetBadge($log->target_type) }}">
                                            {{ $prettyLabel($log->target_type) }}
                                        </span>
                                    </td>
                                    <td class="is-center">{{ $log->target_id ?? '-' }}</td>
                                    <td>
                                        <div class="line-clamp-2" title="{{ $log->description ?? '-' }}">{{ $log->description ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div class="line-clamp-2" title="{{ $metaSummary }}">{{ $metaSummary }}</div>
                                    </td>
                                    <td class="is-center">
                                        @if (Route::has('super-admin.activity-logs.show'))
                                            <a href="{{ route('super-admin.activity-logs.show', $log) }}" class="audit-action-btn">
                                                <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                                                <span>Detail</span>
                                            </a>
                                        @else
                                            <span class="audit-action-btn is-disabled">
                                                <i class="bi bi-lock" aria-hidden="true"></i>
                                                <span>Detail</span>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="audit-pagination-wrap">
                    {{ $activityLogs->links() }}
                </div>
            @else
                <div class="audit-empty-state">
                    <i class="bi bi-inbox" aria-hidden="true"></i>
                    <h4>Belum ada data audit log</h4>
                    <p>Aktivitas sistem akan muncul di halaman ini saat pengguna mulai melakukan aksi pada platform.</p>
                </div>
            @endif
        </section>
    </div>
@endsection
