@extends('layouts.admin')

@section('title', 'Promo | Admin Rental')
@section('page_title', 'Promo')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-rental-promos.css') }}">
@endpush

@section('content')
    @php
        // Helper function to format discount value
        $formatDiscount = function ($value, $type) {
            if ($type === 'percent' || $type === 'percentage') {
                return $value . '%';
            }
            return 'Rp ' . number_format($value, 0, ',', '.');
        };

        // Helper function to format period
        $formatPeriod = function ($startDate, $endDate) {
            if (!$startDate || !$endDate) {
                return '-';
            }
            $start = \Carbon\Carbon::parse($startDate)->format('d M');
            $end = \Carbon\Carbon::parse($endDate)->format('d M Y');
            return $start . ' - ' . $end;
        };

        // Helper function to format quota
        $formatQuota = function ($quota, $usedCount = 0) {
            if (!$quota || $quota === 0) {
                return 'Unlimited';
            }
            return $usedCount . ' / ' . $quota;
        };

        // Calculate summary
        $activeCount = $promos->filter(fn ($p) => $p->status === 'active')->count();
        $inactiveCount = $promos->filter(fn ($p) => $p->status === 'inactive')->count();
        $loyalOnlyCount = $promos->filter(fn ($p) => $p->loyal_only === true)->count();
    @endphp

    <div class="promos-page">
        <!-- Breadcrumb -->
        <div class="promos-breadcrumb">
            <span>Admin Rental</span>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <strong>Promo</strong>
        </div>

        <!-- Flash Messages -->
        @if ($message = Session::get('success'))
            <div class="promos-alert is-success">
                <div class="promos-alert-icon">
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                </div>
                <div class="promos-alert-content">
                    <strong>Berhasil</strong>
                    <p>{{ $message }}</p>
                </div>
                <button type="button" class="promos-alert-close" onclick="this.parentElement.style.display='none';">
                    <i class="bi bi-x" aria-hidden="true"></i>
                </button>
            </div>
        @endif

        @if ($message = Session::get('error'))
            <div class="promos-alert is-error">
                <div class="promos-alert-icon">
                    <i class="bi bi-exclamation-circle-fill" aria-hidden="true"></i>
                </div>
                <div class="promos-alert-content">
                    <strong>Gagal</strong>
                    <p>{{ $message }}</p>
                </div>
                <button type="button" class="promos-alert-close" onclick="this.parentElement.style.display='none';">
                    <i class="bi bi-x" aria-hidden="true"></i>
                </button>
            </div>
        @endif

        <!-- Header Card -->
        <div class="promos-header-card">
            <div>
                <h2>Promo</h2>
                <p>Kelola promo rental Anda untuk menarik pelanggan dan mendukung strategi CRM.</p>
            </div>
            <div class="promos-header-actions">
                <a href="{{ route('admin-rental.promos.create') }}" class="promos-btn-add">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i>
                    <span>Tambah Promo</span>
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="promos-stats-grid">
            <article class="promos-stat-card">
                <div class="promos-stat-icon"><i class="bi bi-tag" aria-hidden="true"></i></div>
                <div>
                    <p>Total Promo</p>
                    <h3>{{ $promos->count() }}</h3>
                </div>
            </article>
            <article class="promos-stat-card">
                <div class="promos-stat-icon"><i class="bi bi-check-circle" aria-hidden="true"></i></div>
                <div>
                    <p>Promo Aktif</p>
                    <h3>{{ $activeCount }}</h3>
                </div>
            </article>
            <article class="promos-stat-card">
                <div class="promos-stat-icon"><i class="bi bi-slash-circle" aria-hidden="true"></i></div>
                <div>
                    <p>Promo Nonaktif</p>
                    <h3>{{ $inactiveCount }}</h3>
                </div>
            </article>
            <article class="promos-stat-card">
                <div class="promos-stat-icon"><i class="bi bi-award" aria-hidden="true"></i></div>
                <div>
                    <p>Loyal Only</p>
                    <h3>{{ $loyalOnlyCount }}</h3>
                </div>
            </article>
        </div>

        <!-- Toolbar -->
        <div class="promos-toolbar-card">
            <form method="GET" action="{{ route('admin-rental.promos.index') }}" class="promos-toolbar-form">
                <div class="promos-input-group promos-search-group">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari judul, kode promo">
                </div>

                <div class="promos-input-group">
                    <i class="bi bi-toggles" aria-hidden="true"></i>
                    <select name="status">
                        <option value="">Semua Status</option>
                        <option value="active" @selected(request('status') === 'active')>Aktif</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                    </select>
                </div>

                <div class="promos-input-group">
                    <i class="bi bi-award" aria-hidden="true"></i>
                    <select name="loyal">
                        <option value="">Semua Tipe</option>
                        <option value="loyal" @selected(request('loyal') === 'loyal')>Loyal Only</option>
                        <option value="general" @selected(request('loyal') === 'general')>General</option>
                    </select>
                </div>

                <div class="promos-input-group">
                    <i class="bi bi-percent" aria-hidden="true"></i>
                    <select name="discount_type">
                        <option value="">Semua Jenis</option>
                        <option value="percent" @selected(request('discount_type') === 'percent')>Persen (%)</option>
                        <option value="fixed" @selected(request('discount_type') === 'fixed')>Nominal (Rp)</option>
                    </select>
                </div>

                <button type="submit" class="promos-filter-btn">
                    <i class="bi bi-funnel-fill" aria-hidden="true"></i>
                    <span>Terapkan</span>
                </button>

                <a href="{{ route('admin-rental.promos.index') }}" class="promos-reset-btn">
                    <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                    <span>Reset</span>
                </a>
            </form>
        </div>

        <!-- Table / Empty State -->
        @if ($promos && $promos->count() > 0)
            <div class="promos-table-card">
                <div class="promos-table-wrapper">
                    <table class="promos-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Promo</th>
                                <th>Kode Promo</th>
                                <th>Jenis Diskon</th>
                                <th>Nilai Diskon</th>
                                <th>Min. Transaksi</th>
                                <th>Periode</th>
                                <th>Kuota</th>
                                <th>Digunakan</th>
                                <th>Tipe</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($promos as $index => $promo)
                                @php
                                    $statusClass = $promo->status === 'active' ? 'is-active' : 'is-inactive';
                                    $loyalBadgeClass = $promo->loyal_only ? 'is-loyal' : 'is-general';
                                    $discountTypeLabel = $promo->discount_type === 'percent' ? 'Persen' : 'Nominal';
                                    $discountValue = $formatDiscount($promo->discount_value, $promo->discount_type);
                                    $period = $formatPeriod($promo->start_date, $promo->end_date);
                                    $quota = $formatQuota($promo->quota, $promo->used_count ?? 0);
                                @endphp
                                <tr>
                                    <td>{{ ($promos->firstItem() ?? 1) + $index }}</td>
                                    <td class="promos-title">{{ $promo->title }}</td>
                                    <td class="promos-code">{{ strtoupper($promo->promo_code) }}</td>
                                    <td>{{ $discountTypeLabel }}</td>
                                    <td class="promos-discount-value">{{ $discountValue }}</td>
                                    <td class="promos-min-transaction">
                                        @if ($promo->min_transaction)
                                            Rp {{ number_format($promo->min_transaction, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="promos-period">{{ $period }}</td>
                                    <td class="promos-quota">{{ $quota }}</td>
                                    <td class="promos-used-count">{{ $promo->used_count ?? 0 }}</td>
                                    <td>
                                        <span class="promos-loyal-badge {{ $loyalBadgeClass }}">
                                            {{ $promo->loyal_only ? 'Loyal Only' : 'General' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="promos-status-badge {{ $statusClass }}">
                                            {{ $promo->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="promos-actions">
                                            <a href="{{ route('admin-rental.promos.edit', $promo->id ?? $promo) }}" class="promos-action-btn is-edit" title="Edit promo">
                                                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                                <span>Edit</span>
                                            </a>

                                            <form action="{{ route('admin-rental.promos.toggle', $promo->id ?? $promo) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="promos-action-btn {{ $promo->status === 'active' ? 'is-deactivate' : 'is-activate' }}" title="{{ $promo->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <i class="bi {{ $promo->status === 'active' ? 'bi-toggle-off' : 'bi-toggle-on' }}" aria-hidden="true"></i>
                                                    <span>{{ $promo->status === 'active' ? 'Nonaktif' : 'Aktif' }}</span>
                                                </button>
                                            </form>

                                            <form action="{{ route('admin-rental.promos.destroy', $promo->id ?? $promo) }}" method="POST" style="display: inline;" onsubmit="return confirm('Hapus promo ini secara permanen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="promos-action-btn is-delete" title="Hapus">
                                                    <i class="bi bi-trash3" aria-hidden="true"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if ($promos->hasPages())
                <div class="promos-pagination-wrapper">
                    {{ $promos->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="promos-empty-state">
                <div class="promos-empty-icon">
                    <i class="bi bi-inbox" aria-hidden="true"></i>
                </div>
                <h3>Belum Ada Promo</h3>
                <p>Tambahkan promo pertama Anda untuk mulai mendukung strategi pemasaran rental.</p>
                <a href="{{ route('admin-rental.promos.create') }}" class="promos-btn-add-empty">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i>
                    <span>Tambah Promo</span>
                </a>
            </div>
        @endif
    </div>
@endsection
