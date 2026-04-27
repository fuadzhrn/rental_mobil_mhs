@extends('layouts.admin')

@section('title', 'Data Kendaraan | Admin Rental')
@section('page_title', 'Data Kendaraan')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-rental-vehicles.css') }}">
@endpush

@section('content')
    @php
        $categoryOptions = $vehicles->pluck('category')->filter()->unique()->sort()->values();
        $statusOptions = [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'maintenance' => 'Maintenance',
        ];
    @endphp

    <div class="vehicles-page">
        <div class="vehicles-breadcrumb">
            <span>Admin Rental</span>
            <i class="bi bi-chevron-right" aria-hidden="true"></i>
            <strong>Data Kendaraan</strong>
        </div>

        @if (session('success'))
            <div class="vehicles-alert vehicles-alert-success" role="status">
                <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="vehicles-alert vehicles-alert-error" role="alert">
                <i class="bi bi-x-circle-fill" aria-hidden="true"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if (session('warning'))
            <div class="vehicles-alert vehicles-alert-warning" role="alert">
                <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        @if (session('info'))
            <div class="vehicles-alert vehicles-alert-info" role="status">
                <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        <div class="vehicles-header-card">
            <div>
                <h2>Data Kendaraan</h2>
                <p>Kelola kendaraan rental Anda di halaman ini.</p>
                <small>{{ $rentalCompany->company_name }}</small>
            </div>

            <a href="{{ route('admin-rental.vehicles.create') }}" class="vehicles-add-btn">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                <span>Tambah Kendaraan</span>
            </a>
        </div>

        <div class="vehicles-toolbar-card">
            <form method="GET" action="{{ route('admin-rental.vehicles.index') }}" class="vehicles-toolbar-form">
                <div class="vehicles-input-group vehicles-search-group">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama kendaraan, brand, atau kategori">
                </div>

                <div class="vehicles-input-group">
                    <i class="bi bi-sliders" aria-hidden="true"></i>
                    <select name="status">
                        <option value="">Semua Status</option>
                        @foreach ($statusOptions as $key => $label)
                            <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="vehicles-input-group">
                    <i class="bi bi-tags" aria-hidden="true"></i>
                    <select name="category">
                        <option value="">Semua Kategori</option>
                        @foreach ($categoryOptions as $category)
                            <option value="{{ $category }}" @selected(request('category') === $category)>{{ ucfirst($category) }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="vehicles-filter-btn">
                    <i class="bi bi-funnel-fill" aria-hidden="true"></i>
                    <span>Terapkan</span>
                </button>

                <a href="{{ route('admin-rental.vehicles.index') }}" class="vehicles-reset-btn">
                    <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                    <span>Reset</span>
                </a>
            </form>
        </div>

        @if ($vehicles->count() > 0)
            <div class="vehicles-table-card">
                <div class="vehicles-table-wrapper">
                    <table class="vehicles-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Foto</th>
                                <th>Nama Kendaraan</th>
                                <th>Brand</th>
                                <th>Kategori</th>
                                <th>Transmisi</th>
                                <th>Bahan Bakar</th>
                                <th>Kapasitas</th>
                                <th>Harga / Hari</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vehicles as $index => $vehicle)
                                <tr>
                                    <td>{{ ($vehicles->firstItem() ?? 1) + $index }}</td>
                                    <td>
                                        @if ($vehicle->main_image)
                                            <img
                                                class="vehicles-thumb"
                                                src="{{ asset('storage/' . $vehicle->main_image) }}"
                                                alt="{{ $vehicle->name }}">
                                        @else
                                            <div class="vehicles-thumb-placeholder">
                                                <i class="bi bi-image" aria-hidden="true"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="vehicles-name">{{ $vehicle->name }}</div>
                                        <div class="vehicles-meta">{{ $vehicle->year }} • {{ $vehicle->type }}</div>
                                    </td>
                                    <td>{{ $vehicle->brand }}</td>
                                    <td>{{ ucfirst($vehicle->category) }}</td>
                                    <td>{{ $vehicle->transmission }}</td>
                                    <td>{{ $vehicle->fuel_type }}</td>
                                    <td>{{ $vehicle->seat_capacity }} kursi</td>
                                    <td class="vehicles-price">Rp {{ number_format((float) $vehicle->price_per_day, 0, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $statusClass = match ($vehicle->status) {
                                                'active' => 'is-active',
                                                'inactive' => 'is-inactive',
                                                default => 'is-maintenance',
                                            };
                                        @endphp
                                        <span class="vehicles-status {{ $statusClass }}">{{ ucfirst($vehicle->status) }}</span>
                                    </td>
                                    <td>
                                        <div class="vehicles-actions">
                                            <a href="{{ route('admin-rental.vehicles.edit', $vehicle) }}" class="vehicles-action-btn is-edit" title="Edit kendaraan">
                                                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                                <span>Edit</span>
                                            </a>

                                            <form action="{{ route('admin-rental.vehicles.destroy', $vehicle) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kendaraan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="vehicles-action-btn is-delete" title="Hapus kendaraan">
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

            <div class="vehicles-pagination-wrap">
                {{ $vehicles->links() }}
            </div>
        @else
            <div class="vehicles-empty-state">
                <div class="vehicles-empty-icon">
                    <i class="bi bi-inboxes-fill" aria-hidden="true"></i>
                </div>
                <h3>Belum ada kendaraan</h3>
                <p>Tambahkan kendaraan pertama Anda untuk mulai mengelola data rental.</p>
                <a href="{{ route('admin-rental.vehicles.create') }}" class="vehicles-add-btn">
                    <i class="bi bi-plus-lg" aria-hidden="true"></i>
                    <span>Tambah Kendaraan</span>
                </a>
            </div>
        @endif
    </div>
@endsection
