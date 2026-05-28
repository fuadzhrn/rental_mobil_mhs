@extends('layouts.admin')

@section('title', 'Detail Inventory | Admin Rental')
@section('page_title', 'Detail Inventory')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/admin-rental-inventories.css') }}">
@endpush

@section('content')
<div class="inventories-page inventories-detail-page">
    <div class="inventories-breadcrumb">
        <span>Admin Rental</span>
        <i class="bi bi-chevron-right" aria-hidden="true"></i>
        <span>Inventories</span>
        <i class="bi bi-chevron-right" aria-hidden="true"></i>
        <strong>Detail Inventory</strong>
    </div>

    <div class="inventories-header-card">
        <div>
            <h2>{{ $inventory->vehicle->name ?? '—' }}</h2>
            <p>Edit stok kendaraan, cek jumlah reserved, dan update catatan stok di halaman ini.</p>
        </div>
        <div class="inventories-header-meta">
            <span class="inventories-chip">Total: {{ $inventory->total }}</span>
            <span class="inventories-chip is-soft">Available: {{ $inventory->available }}</span>
        </div>
    </div>

    <div class="inventories-form-card">
        <form method="POST" action="{{ route('admin-rental.inventories.update', $inventory) }}" class="inventories-form">
            @csrf
            @method('PUT')

            <div class="inventories-form-grid">
                <div class="inventories-field">
                    <label for="total">Total</label>
                    <input type="number" id="total" name="total" value="{{ $inventory->total }}" min="0">
                </div>

                <div class="inventories-field">
                    <label for="reserved">Reserved</label>
                    <input type="number" id="reserved" name="reserved" value="{{ $inventory->reserved }}" min="0">
                </div>

                <div class="inventories-field">
                    <label for="available">Available</label>
                    <input type="number" id="available" name="available" value="{{ $inventory->available }}" min="0">
                </div>
            </div>

            <div class="inventories-field">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="4">{{ $inventory->notes }}</textarea>
            </div>

            <div class="inventories-form-actions">
                <a href="{{ route('admin-rental.inventories.index') }}" class="inventories-back-btn">Back</a>
                <button type="submit" class="inventories-save-btn">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
