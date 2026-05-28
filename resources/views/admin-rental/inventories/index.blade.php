@extends('layouts.admin')

@section('title', 'Inventories | Admin Rental')
@section('page_title', 'Inventories')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-rental-inventories.css') }}">
@endpush

@section('content')
<div class="inventories-page">
    <div class="inventories-breadcrumb">
        <span>Admin Rental</span>
        <i class="bi bi-chevron-right" aria-hidden="true"></i>
        <strong>Inventories</strong>
    </div>

    <div class="inventories-header-card">
        <div>
            <h2>Inventories</h2>
            <p>Kelola stok kendaraan rental Anda. Gunakan halaman ini untuk melihat jumlah total, reserved, available, dan catatan cek terakhir.</p>
        </div>
        <div class="inventories-header-meta">
            <span class="inventories-chip">{{ $inventories->total() }} item</span>
            <span class="inventories-chip is-soft">{{ request()->route('rentalCompany')?->company_name ?? 'Admin Rental' }}</span>
        </div>
    </div>

    @if ($inventories->count() > 0)
        <div class="inventories-table-card">
            <div class="inventories-table-wrapper">
                <table class="inventories-table">
                    <thead>
                        <tr>
                            <th>Vehicle</th>
                            <th>Total</th>
                            <th>Reserved</th>
                            <th>Available</th>
                            <th>Last Checked By</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventories as $inventory)
                            <tr>
                                <td class="inventories-vehicle-cell">{{ $inventory->vehicle->name ?? '-' }}</td>
                                <td>{{ $inventory->total }}</td>
                                <td>{{ $inventory->reserved }}</td>
                                <td>
                                    <span class="inventories-availability {{ $inventory->available <= 0 ? 'is-zero' : ($inventory->available <= 2 ? 'is-low' : 'is-good') }}">
                                        {{ $inventory->available }}
                                    </span>
                                </td>
                                <td>{{ $inventory->lastCheckedBy->name ?? '-' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($inventory->notes, 80) }}</td>
                                <td>
                                    <a href="{{ route('admin-rental.inventories.show', $inventory) }}" class="inventories-view-btn">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                        <span>View</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="inventories-pagination-wrap">
            {{ $inventories->links() }}
        </div>
    @else
        <div class="inventories-empty-state">
            <div class="inventories-empty-icon">
                <i class="bi bi-box-seam" aria-hidden="true"></i>
            </div>
            <h3>Belum ada data inventory</h3>
            <p>Inventory akan muncul setelah kendaraan dan stok terhubung ke rental ini.</p>
        </div>
    @endif
</div>
@endsection
